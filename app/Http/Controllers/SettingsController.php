<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\WaboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show WaboxApp settings page
     */
    public function waboxApp()
    {
        $settings = Setting::getWaboxSettings();

        return view('settings.waboxapp', compact('settings'));
    }

    /**
     * Update WaboxApp settings
     */
    public function updateWaboxApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wabox_token' => 'required|string|min:10',
            'wabox_uid' => 'required|string|min:3',
            'wabox_api_url' => 'required|url',
            'wabox_broadcast_delay' => 'required|integer|min:1|max:60',
            'wabox_batch_size' => 'required|integer|min:10|max:1000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Save settings to database
            Setting::set('wabox_token', $request->wabox_token, 'string', 'WaboxApp API Token', false);
            Setting::set('wabox_uid', $request->wabox_uid, 'string', 'WaboxApp User ID', false);
            Setting::set('wabox_api_url', $request->wabox_api_url, 'string', 'WaboxApp API URL', false);
            Setting::set('wabox_broadcast_delay', $request->wabox_broadcast_delay, 'string', 'Broadcast Delay (seconds)', false);
            Setting::set('wabox_batch_size', $request->wabox_batch_size, 'string', 'Batch Size for Broadcasting', false);

            // Test connection with new settings
            $waboxService = new WaboxService();
            $testResult = $waboxService->testConnection();

            if ($testResult['success']) {
                return redirect()->route('settings.waboxapp')->with('success', 'Pengaturan WaboxApp berhasil disimpan dan koneksi berhasil ditest!');
            } else {
                return redirect()->route('settings.waboxapp')->with('warning', 'Pengaturan disimpan, namun test koneksi gagal: ' . ($testResult['error'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Test WaboxApp connection
     */
    public function testWaboxConnection()
    {
        try {
            $waboxService = new WaboxService();
            $result = $waboxService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'custom_uid' => $result['custom_uid'] ?? null,
                    'data' => $result['response'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Test koneksi gagal'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check WaboxApp account status
     */
    public function checkAccountStatus()
    {
        try {
            // First, let's check if settings are being retrieved correctly
            $settings = \App\Models\Setting::getWaboxSettings();
            Log::info('Settings Retrieved: ', $settings);

            $waboxService = new WaboxService();
            $result = $waboxService->getAccountStatus();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Gagal mengambil status akun',
                    'debug_info' => $result['response'] ?? null,
                    'settings_debug' => $settings
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Show general settings page
     */
    public function index()
    {
        return view('settings.index');
    }
}
