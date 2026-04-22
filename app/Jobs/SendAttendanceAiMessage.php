<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MemberAiMessage;
use App\Services\AiDietService;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendAttendanceAiMessage implements ShouldQueue
{
    use Queueable;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        public readonly int $attendanceId,
        public readonly int $memberId,
    ) {}

    public function handle(AiDietService $aiService, WhatsappService $whatsapp): void
    {
        $attendance = Attendance::find($this->attendanceId);
        $member     = Member::with('gym')->find($this->memberId);

        if (!$attendance || !$member || !$member->whatsapp_optin || !$member->phone) {
            return;
        }

        // Generate AI message
        $message = $aiService->generateAttendanceMessage($member, $attendance);

        if (!$message) {
            Log::warning("AI message generation failed for member {$member->id}");
            return;
        }

        // Send via WhatsApp
        $messageId = $whatsapp->sendText($member->phone, $message, $member, 'check_in');

        // Persist AI message record
        MemberAiMessage::create([
            'gym_id'             => $member->gym_id,
            'member_id'          => $member->id,
            'attendance_id'      => $attendance->id,
            'ai_context'         => 'check_in_motivation',
            'ai_response'        => $message,
            'whatsapp_body'      => $message,
            'sent_via_whatsapp'  => $messageId !== null,
        ]);
    }
}
