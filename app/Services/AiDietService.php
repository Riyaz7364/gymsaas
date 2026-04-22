<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDietService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
        $this->model  = config('services.openai.model', 'gpt-4o');
    }

    /**
     * Generate a personalized post-workout motivational message with diet/workout tip.
     * Returns the message string or null on error.
     */
    public function generateAttendanceMessage(Member $member, Attendance $attendance): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('AI: OpenAI API key not configured.');
            return null;
        }

        $name    = $member->name;
        $goal    = str_replace('_', ' ', $member->goal ?? 'fitness');
        $checkin = $attendance->check_in->format('h:i A');
        $day     = now()->format('l');
        $gym     = $member->gym?->name ?? 'your gym';

        $prompt = <<<EOT
You are a friendly and motivating gym AI assistant for {$gym}.
Write a personalized WhatsApp message (max 200 words, warm & encouraging) for {$name} who just checked in at {$checkin} on {$day}.
Their fitness goal is: {$goal}.
Include:
1. A warm greeting using their first name
2. A short motivational line about their goal ({$goal})
3. ONE practical tip â€” either a nutrition tip or workout tip relevant to their goal
4. A positive closing line

Keep it concise, inspiring, and suitable for WhatsApp. Do NOT use markdown, bullet points or headers. Plain conversational text only.
EOT;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => $this->model,
                    'max_tokens'  => 300,
                    'temperature' => 0.8,
                    'messages'    => [
                        ['role' => 'system', 'content' => 'You are a friendly gym AI coach who writes short, personal WhatsApp messages.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API error: ' . $response->body());
                return null;
            }

            return $response->json('choices.0.message.content');
        } catch (\Throwable $e) {
            Log::error('AI generateAttendanceMessage error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Answer a member's question about diet or workout via WhatsApp chat.
     */
    public function answerMemberQuestion(Member $member, string $question): ?string
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $goal = str_replace('_', ' ', $member->goal ?? 'fitness');
        $gym  = $member->gym?->name ?? 'our gym';

        $systemPrompt = "You are a helpful fitness AI coach at {$gym}. The member's name is {$member->name} and their goal is {$goal}. Answer their fitness and diet questions in a friendly, brief WhatsApp style (max 150 words). Plain text only.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => $this->model,
                    'max_tokens'  => 200,
                    'temperature' => 0.7,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $question],
                    ],
                ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json('choices.0.message.content');
        } catch (\Throwable $e) {
            Log::error('AI answerMemberQuestion error: ' . $e->getMessage());
            return null;
        }
    }
}
