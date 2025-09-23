<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaboxService
{
    private string $apiUrl;
    private string $token;
    private string $uid;

    public function __construct()
    {
        // Try to get settings from database first, fallback to config
        if (class_exists(\App\Models\Setting::class)) {
            $this->apiUrl = \App\Models\Setting::get('wabox_api_url') ?: config('services.wabox.api_url', 'https://www.waboxapp.com/api');
            $this->token = \App\Models\Setting::get('wabox_token') ?: config('services.wabox.token');
            $this->uid = \App\Models\Setting::get('wabox_uid') ?: config('services.wabox.uid');
        } else {
            $this->apiUrl = config('services.wabox.api_url', 'https://www.waboxapp.com/api');
            $this->token = config('services.wabox.token');
            $this->uid = config('services.wabox.uid');
        }
    }

    /**
     * Send WhatsApp message to a single recipient
     */
    public function sendMessage(string $phoneNumber, string $message, string $customUid = null): array
    {
        try {
            // Generate custom_uid if not provided
            if (!$customUid) {
                $customUid = 'msg_' . time() . '_' . rand(1000, 9999);
            }

            // Use GET method as per official documentation
            $response = Http::timeout(30)->get("{$this->apiUrl}/send/chat", [
                'token' => $this->token,
                'uid' => $this->uid,
                'to' => $this->formatPhoneNumber($phoneNumber),
                'custom_uid' => $customUid,
                'text' => $message,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'message_id' => $result['custom_uid'] ?? $customUid,
                    'custom_uid' => $result['custom_uid'] ?? $customUid,
                    'response' => $result
                ];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? $result['error'] ?? 'Unknown error',
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('WaboxApp API Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Send broadcast message to multiple recipients
     */
    public function sendBroadcast(array $phoneNumbers, string $message): array
    {
        $results = [];
        $totalSent = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        // Get broadcast delay setting (default 3 seconds)
        $broadcastDelay = \App\Models\Setting::get('wabox_broadcast_delay', 3);
        $batchSize = \App\Models\Setting::get('wabox_batch_size', 50);

        foreach ($phoneNumbers as $index => $phoneNumber) {
            // Generate unique custom_uid for each message
            $customUid = 'broadcast_' . time() . '_' . $totalSent . '_' . rand(1000, 9999);
            $result = $this->sendMessage($phoneNumber, $message, $customUid);

            $totalSent++;

            if ($result['success']) {
                $totalSuccess++;
            } else {
                $totalFailed++;
            }

            $results[] = [
                'phone' => $phoneNumber,
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'custom_uid' => $result['custom_uid'] ?? $customUid,
                'error' => $result['error'] ?? null,
            ];

            // Add configurable delay between messages to avoid rate limiting
            if ($index < count($phoneNumbers) - 1) { // Don't delay after last message
                sleep((int)$broadcastDelay);
            }

            // Add longer pause every batch to prevent being flagged as spam
            if ($totalSent % $batchSize === 0 && $index < count($phoneNumbers) - 1) {
                Log::info("Batch completed ({$totalSent} messages sent), pausing for " . ($broadcastDelay * 3) . " seconds...");
                sleep((int)$broadcastDelay * 3); // Longer pause between batches
            }
        }

        return [
            'results' => $results,
            'summary' => [
                'total_sent' => $totalSent,
                'total_success' => $totalSuccess,
                'total_failed' => $totalFailed,
                'delay_used' => $broadcastDelay . ' seconds',
                'batch_size' => $batchSize,
            ]
        ];
    }

    /**
     * Send personalized broadcast message to multiple recipients
     * Replaces placeholders like {name}, {phone}, {group} with actual contact data
     */
    public function sendPersonalizedBroadcast($contacts, string $messageTemplate): array
    {
        $results = [];
        $totalSent = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        // Get broadcast delay setting (default 3 seconds)
        $broadcastDelay = \App\Models\Setting::get('wabox_broadcast_delay', 3);
        $batchSize = \App\Models\Setting::get('wabox_batch_size', 50);

        foreach ($contacts as $index => $contact) {
            // Replace placeholders with actual contact data
            $personalizedMessage = $this->replacePlaceholders($messageTemplate, $contact);

            // Generate unique custom_uid for each message
            $customUid = 'personalized_' . time() . '_' . $totalSent . '_' . rand(1000, 9999);
            $result = $this->sendMessage($contact->formatted_phone, $personalizedMessage, $customUid);

            $totalSent++;

            if ($result['success']) {
                $totalSuccess++;
            } else {
                $totalFailed++;
            }

            $results[] = [
                'phone' => $contact->formatted_phone,
                'name' => $contact->name,
                'personalized_message' => $personalizedMessage,
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'custom_uid' => $result['custom_uid'] ?? $customUid,
                'error' => $result['error'] ?? null,
            ];

            // Add configurable delay between messages to avoid rate limiting
            if ($index < count($contacts) - 1) { // Don't delay after last message
                sleep((int)$broadcastDelay);
            }

            // Add longer pause every batch to prevent being flagged as spam
            if ($totalSent % $batchSize === 0 && $index < count($contacts) - 1) {
                Log::info("Personalized batch completed ({$totalSent} messages sent), pausing for " . ($broadcastDelay * 3) . " seconds...");
                sleep((int)$broadcastDelay * 3); // Longer pause between batches
            }
        }

        return [
            'results' => $results,
            'summary' => [
                'total_sent' => $totalSent,
                'total_success' => $totalSuccess,
                'total_failed' => $totalFailed,
                'delay_used' => $broadcastDelay . ' seconds',
                'batch_size' => $batchSize,
                'personalization' => 'enabled'
            ]
        ];
    }

    /**
     * Replace placeholders in message template with contact data
     */
    private function replacePlaceholders(string $messageTemplate, $contact): string
    {
        $placeholders = [
            '{name}' => $contact->name ?? 'Pelanggan',
            '{nama}' => $contact->name ?? 'Pelanggan',
            '{phone}' => $contact->phone ?? '',
            '{telepon}' => $contact->phone ?? '',
            '{group}' => $contact->group ?? 'Umum',
            '{grup}' => $contact->group ?? 'Umum',
            '{email}' => $contact->email ?? '',
            '{alamat}' => $contact->address ?? '',
            '{address}' => $contact->address ?? '',
        ];

        $message = $messageTemplate;
        foreach ($placeholders as $placeholder => $value) {
            $message = str_replace($placeholder, $value, $message);
        }

        return $message;
    }

    /**
     * Get account status using official API
     */
    public function getAccountStatus(): array
    {
        try {
            // Check if we have the required credentials
            if (empty($this->token) || empty($this->uid)) {
                return [
                    'success' => false,
                    'error' => 'Token atau UID tidak tersedia. Mohon lengkapi pengaturan terlebih dahulu.',
                    'response' => null
                ];
            }

            // Log credentials for debugging (hide token for security)
            Log::info('WaboxApp Credentials Check: ', [
                'api_url' => $this->apiUrl,
                'token_length' => strlen($this->token),
                'token_preview' => substr($this->token, 0, 10) . '...',
                'uid' => $this->uid
            ]);

            // Use POST method with token as query parameter exactly as per documentation
            // URL encode the token to handle special characters
            $encodedToken = urlencode($this->token);
            $url = "{$this->apiUrl}/status/{$this->uid}?token={$encodedToken}";
            Log::info('WaboxApp Status Request URL: ' . $url);

            $response = Http::timeout(30)->post($url);

            $result = $response->json();

            // Log the response for debugging
            Log::info('WaboxApp Status Response: ', [
                'status_code' => $response->status(),
                'response_body' => $result
            ]);

            if ($response->successful() && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Status akun berhasil diambil'
                ];
            }

            // If status endpoint fails, provide more helpful error message
            $errorMessage = $result['error'] ?? $result['message'] ?? 'API mengembalikan response yang tidak diharapkan';

            if ($response->status() === 403 || $response->status() === 401) {
                $errorMessage .= '. Kemungkinan token tidak valid, expired, atau tidak memiliki akses ke endpoint status.';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
                'response' => $result,
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('WaboxApp Account Status Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Gagal mengambil status akun: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Get account information (legacy method)
     */
    public function getAccountInfo(): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/status", [
                'token' => $this->token,
                'uid' => $this->uid,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('WaboxApp Account Info Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for WaboxApp API
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // If doesn't start with +, assume Indonesian number
        if (!str_starts_with($phone, '+')) {
            // Remove leading 0 if present and add +62
            $phone = '+62' . ltrim($phone, '0');
        }

        return $phone;
    }

    /**
     * Test API connection using official chat endpoint
     */
    public function testConnection(): array
    {
        try {
            // Check if we have the required credentials
            if (empty($this->token) || empty($this->uid)) {
                return [
                    'success' => false,
                    'error' => 'Token atau UID tidak tersedia. Mohon lengkapi pengaturan terlebih dahulu.',
                    'response' => null
                ];
            }

            // Generate unique custom_uid for test
            $customUid = 'test_' . time() . '_' . rand(1000, 9999);

            // Use GET method as per documentation, send to self (UID)
            $response = Http::timeout(30)->get("{$this->apiUrl}/send/chat", [
                'token' => $this->token,
                'uid' => $this->uid,
                'to' => $this->uid, // Send test message to self
                'custom_uid' => $customUid,
                'text' => 'Test connection from WA Broadcast System - ' . date('Y-m-d H:i:s')
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'message' => 'Koneksi berhasil! Test message terkirim.',
                    'custom_uid' => $result['custom_uid'] ?? $customUid,
                    'response' => $result
                ];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? $result['error'] ?? 'API mengembalikan response yang tidak diharapkan',
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('WaboxApp Test Connection Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Koneksi gagal: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }
}
