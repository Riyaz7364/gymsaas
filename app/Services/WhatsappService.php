<?php

namespace App\Services;

use App\Models\Member;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private string $token;
    private string $phoneNumberId;
    private string $apiUrl;
    private bool $logOnly;

    public function __construct()
    {
        $this->token         = config('services.whatsapp.token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
        $this->apiUrl        = config('services.whatsapp.api_url');
        $this->logOnly       = (bool) config('services.whatsapp.log_only', false);
    }

    /**
     * Send a plain-text message to a phone number.
     * Returns the WhatsApp message ID on success, null on failure.
     */
    public function sendText(string $phone, string $body, ?Member $member = null, string $trigger = 'manual'): ?string
    {
        // Normalize: remove spaces/dashes, ensure + prefix
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        if (!str_starts_with($phone, '+')) {
            $phone = '+91' . ltrim($phone, '0'); // default country code India
        }

        if ($this->logOnly) {
            $mockMessageId = 'log_only_' . now()->timestamp . '_' . substr(md5($phone . $body), 0, 8);
            $this->log(
                $member,
                $phone,
                $body,
                $trigger,
                'queued',
                $mockMessageId,
                ['mode' => 'log_only', 'note' => 'WhatsApp API call skipped by configuration'],
                null
            );
            return $mockMessageId;
        }

        if (empty($this->token) || empty($this->phoneNumberId)) {
            Log::warning('WhatsApp: token or phone_number_id not configured.');
            $this->log(
                $member,
                $phone,
                $body,
                $trigger,
                'failed',
                null,
                ['mode' => 'live'],
                'Missing WhatsApp credentials'
            );
            return null;
        }

        try {
            $response = Http::withToken($this->token)
                ->timeout(15)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $phone,
                    'type'              => 'text',
                    'text'              => ['body' => $body],
                ]);

            $data      = $response->json();
            $messageId = $data['messages'][0]['id'] ?? null;
            $status    = $response->successful() ? 'sent' : 'failed';

            $this->log($member, $phone, $body, $trigger, $status, $messageId, $data, $response->failed() ? ($data['error']['message'] ?? 'Unknown error') : null);

            return $messageId;
        } catch (\Throwable $e) {
            Log::error('WhatsApp sendText error: ' . $e->getMessage());
            $this->log($member, $phone, $body, $trigger, 'failed', null, null, $e->getMessage());
            return null;
        }
    }

    /**
     * Parse an inbound webhook message payload from Meta.
     * Returns ['from' => phone, 'body' => text] or null if not a text message.
     */
    public function parseInboundMessage(array $payload): ?array
    {
        try {
            $entry   = $payload['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value   = $changes['value'] ?? null;
            $message = $value['messages'][0] ?? null;

            if (!$message || $message['type'] !== 'text') {
                return null;
            }

            return [
                'from' => $message['from'],
                'body' => trim($message['text']['body'] ?? ''),
                'id'   => $message['id'],
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function log(?Member $member, string $phone, string $body, string $trigger, string $status, ?string $messageId, ?array $metaResponse, ?string $error): void
    {
        $gymId = $member?->gym_id ?? auth()->user()?->gym_id;

        if (!$gymId) {
            return;
        }

        WhatsappLog::create([
            'gym_id'              => $gymId,
            'member_id'           => $member?->id,
            'to_phone'            => $phone,
            'direction'           => 'outbound',
            'trigger'             => $trigger,
            'body'                => $body,
            'status'              => $status,
            'whatsapp_message_id' => $messageId,
            'meta_response'       => $metaResponse,
            'error_message'       => $error,
            'sent_at'             => now(),
        ]);
    }
}
