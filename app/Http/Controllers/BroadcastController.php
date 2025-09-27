<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\BroadcastLog;
use App\Services\WaboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BroadcastController extends Controller
{
    private WaboxService $waboxService;

    public function __construct(WaboxService $waboxService)
    {
        $this->waboxService = $waboxService;
    }

    /**
     * Show broadcast form
     */
    public function index()
    {
        $contacts = Contact::active()->orderBy('name')->get();
        $groups = Contact::distinct()->pluck('group')->filter();
        $recentBroadcasts = BroadcastLog::orderBy('created_at', 'desc')->take(5)->get();

        return view('broadcast.index', compact('contacts', 'groups', 'recentBroadcasts'));
    }

    /**
     * Send broadcast message
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'exists:contacts,id',
            'use_personalization' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check API connection first
        if (!$this->waboxService->testConnection()) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke API WaboxApp. Periksa konfigurasi Anda.'])->withInput();
        }

        $recipients = Contact::whereIn('id', $request->recipients)->active()->get();

        if ($recipients->isEmpty()) {
            return back()->withErrors(['recipients' => 'Tidak ada penerima yang valid.'])->withInput();
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('broadcast-images', 'public');
        }

        // Create broadcast log
        $broadcastLog = BroadcastLog::create([
            'message' => $request->message,
            'image_path' => $imagePath,
            'recipients' => $request->recipients,
            'total_sent' => 0,
            'total_success' => 0,
            'total_failed' => 0,
            'status' => 'pending',
            'response_data' => []
        ]);

        try {
            // Update status to processing
            $broadcastLog->update(['status' => 'processing']);

            // Check if personalization is enabled
            $usePersonalization = $request->boolean('use_personalization', false);

            // Send broadcast with or without personalization and images
            if ($usePersonalization) {
                // Note: Personalized broadcasts with images not yet implemented
                $result = $this->waboxService->sendPersonalizedBroadcast($recipients, $request->message);
            } else {
                // Prepare phone numbers for regular broadcast
                $phoneNumbers = $recipients->pluck('formatted_phone')->toArray();
                
                // Check if image is included
                if ($imagePath) {
                    // Generate full URL for the image
                    $imageUrl = asset('storage/' . $imagePath);
                    $result = $this->waboxService->sendBroadcastWithImage($phoneNumbers, $request->message, $imageUrl);
                } else {
                    $result = $this->waboxService->sendBroadcast($phoneNumbers, $request->message);
                }
            }

            // Update broadcast log with results
            $broadcastLog->update([
                'total_sent' => $result['summary']['total_sent'],
                'total_success' => $result['summary']['total_success'],
                'total_failed' => $result['summary']['total_failed'],
                'status' => 'completed',
                'response_data' => $result['results']
            ]);

            $successMessage = "Broadcast berhasil dikirim! ";
            $successMessage .= "Total: {$result['summary']['total_sent']}, ";
            $successMessage .= "Berhasil: {$result['summary']['total_success']}, ";
            $successMessage .= "Gagal: {$result['summary']['total_failed']}";

            return redirect()->route('broadcast.index')->with('success', $successMessage);

        } catch (\Exception $e) {
            // Update status to failed
            $broadcastLog->update([
                'status' => 'failed',
                'response_data' => ['error' => $e->getMessage()]
            ]);

            return back()->withErrors(['broadcast' => 'Error saat mengirim broadcast: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Send message to all contacts
     */
    public function sendToAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'group' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query = Contact::active();

        if ($request->group) {
            $query->byGroup($request->group);
        }

        $contacts = $query->get();

        if ($contacts->isEmpty()) {
            return back()->withErrors(['recipients' => 'Tidak ada kontak yang tersedia.'])->withInput();
        }

        // Convert to recipient IDs and send
        $request->merge(['recipients' => $contacts->pluck('id')->toArray()]);

        return $this->send($request);
    }

    /**
     * Send message to specific group
     */
    public function sendToGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'group' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $contacts = Contact::active()->byGroup($request->group)->get();

        if ($contacts->isEmpty()) {
            return back()->withErrors(['recipients' => "Tidak ada kontak dalam grup '{$request->group}'."])->withInput();
        }

        // Convert to recipient IDs and send
        $request->merge(['recipients' => $contacts->pluck('id')->toArray()]);

        return $this->send($request);
    }

    /**
     * Show broadcast history
     */
    public function history()
    {
        $broadcasts = BroadcastLog::orderBy('created_at', 'desc')->paginate(20);

        return view('broadcast.history', compact('broadcasts'));
    }

    /**
     * Show broadcast details
     */
    public function show(BroadcastLog $broadcastLog)
    {
        $contacts = $broadcastLog->contacts()->get();

        return view('broadcast.show', compact('broadcastLog', 'contacts'));
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $accountInfo = $this->waboxService->getAccountInfo();

            if ($accountInfo['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi API berhasil!',
                    'data' => $accountInfo['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal terhubung ke API: ' . ($accountInfo['error'] ?? 'Unknown error')
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
     * Get contacts by group (AJAX)
     */
    public function getContactsByGroup(Request $request)
    {
        $group = $request->get('group');

        $query = Contact::active();

        if ($group) {
            $query->byGroup($group);
        }

        $contacts = $query->orderBy('name')->get(['id', 'name', 'phone_number']);

        return response()->json($contacts);
    }
}
