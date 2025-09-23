<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\BroadcastLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalContacts' => Contact::count(),
            'activeContacts' => Contact::active()->count(),
            'totalGroups' => Contact::distinct()->whereNotNull('group')->count('group'),
            'totalBroadcasts' => BroadcastLog::count(),
            'recentBroadcasts' => BroadcastLog::orderBy('created_at', 'desc')->take(5)->get()
        ];

        return view('dashboard', $data);
    }
}
