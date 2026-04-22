<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\WhatsappLog;
use App\Services\AiDietService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request, WhatsappService $whatsapp, AiDietService $ai)
    {
        $payload = $request->json()->all();
        $msg = $whatsapp->parseInboundMessage($payload);

        if (!$msg) {
            return response('EVENT_RECEIVED', 200);
        }

        $from = $msg['from'];
        $body = strtoupper(trim($msg['body']));

        $phoneSearch = preg_replace('/^\+91|^0/', '', $from);
        $member = Member::where(function ($q) use ($from, $phoneSearch) {
            $q->where('phone', $from)
              ->orWhere('phone', $phoneSearch)
              ->orWhere('phone', '+' . ltrim($from, '+'));
        })->first();

        if (!$member) {
            return response('EVENT_RECEIVED', 200);
        }

        WhatsappLog::create([
            'gym_id'              => $member->gym_id,
            'member_id'           => $member->id,
            'to_phone'            => $from,
            'direction'           => 'inbound',
            'trigger'             => 'member_reply',
            'body'                => $msg['body'],
            'status'              => 'received',
            'whatsapp_message_id' => $msg['id'],
            'sent_at'             => now(),
        ]);

        match (true) {
            $body === 'STOP'  => $this->handleStop($member, $whatsapp),
            $body === 'START' => $this->handleStart($member, $whatsapp),
            in_array($body, ['GOAL', 'DIET', 'WORKOUT', 'HELP']) => $this->handleHelp($member, $body, $whatsapp),
            default => $this->handleAiQuestion($member, $msg['body'], $ai, $whatsapp),
        };

        return response('EVENT_RECEIVED', 200);
    }

    private function handleStop(Member $member, WhatsappService $whatsapp): void
    {
        $member->update(['whatsapp_optin' => false]);
        $whatsapp->sendText($member->phone, "You have been unsubscribed from GymHub messages. Reply START to re-subscribe anytime.", $member, 'stop_command');
    }

    private function handleStart(Member $member, WhatsappService $whatsapp): void
    {
        $member->update(['whatsapp_optin' => true]);
        $whatsapp->sendText($member->phone, "Welcome back, {$member->name}! You have re-subscribed to GymHub AI messages. You will receive motivational messages after each check-in.", $member, 'start_command');
    }

    private function handleHelp(Member $member, string $command, WhatsappService $whatsapp): void
    {
        $goal = str_replace('_', ' ', $member->goal ?? 'fitness');
        $tips = [
            'GOAL'    => "Your current goal is: {$goal}. Stay consistent and you will reach it! Reply DIET or WORKOUT for tips.",
            'DIET'    => "Nutrition tip for {$goal}: Focus on whole foods, adequate protein (0.8-1.2g per kg body weight), and stay hydrated.",
            'WORKOUT' => "Workout tip for {$goal}: Consistency beats intensity. Aim for 3-5 workouts per week. Rest days are essential for recovery!",
            'HELP'    => "GymHub AI Commands:\nDIET - nutrition tip\nWORKOUT - exercise tip\nGOAL - your fitness goal\nSTOP - unsubscribe\nSTART - re-subscribe",
        ];

        $whatsapp->sendText($member->phone, $tips[$command], $member, 'help_command');
    }

    private function handleAiQuestion(Member $member, string $question, AiDietService $ai, WhatsappService $whatsapp): void
    {
        $answer = $ai->answerMemberQuestion($member, $question);
        if ($answer) {
            $whatsapp->sendText($member->phone, $answer, $member, 'ai_reply');
        }
    }
}
