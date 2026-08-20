<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use App\Models\TrustedDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class SecurityController extends Controller
{
    /**
     * Security Dashboard Page
     */
    public function index(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get();

        $agent = new Agent();

        // Get trusted device tokens for current user
        $trustedTokens = TrustedDevice::where('user_id', auth()->id())
            ->pluck('device_token')
            ->toArray();

        foreach ($sessions as $session) {
            $agent->setUserAgent($session->user_agent);

            $session->browser  = $agent->browser() ?: 'Unknown';
            $session->platform = $agent->platform() ?: 'Unknown';
            $session->is_current_device = ($session->id === request()->session()->getId());

            $inactiveMinutes = floor((time() - $session->last_activity) / 60);
            $session->last_seen = $inactiveMinutes <= 1
                ? 'Online Now'
                : 'Last Seen ' . $inactiveMinutes . ' mins ago';

            // Check if this session's device is trusted
            $deviceToken = md5(
                $session->ip_address . $session->browser . $session->platform . auth()->id()
            );
            $session->is_trusted = in_array($deviceToken, $trustedTokens);
            $session->device_token = $deviceToken;
        }

        // Search filter
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $sessions = $sessions->filter(fn($s) =>
                str_contains(strtolower($s->browser ?? ''), $search) ||
                str_contains(strtolower($s->platform ?? ''), $search) ||
                str_contains(strtolower($s->ip_address ?? ''), $search)
            );
        }

        // Device filter
        if ($request->device_filter === 'current') {
            $sessions = $sessions->where('is_current_device', true);
        } elseif ($request->device_filter === 'other') {
            $sessions = $sessions->where('is_current_device', false);
        } elseif ($request->device_filter === 'trusted') {
            $sessions = $sessions->where('is_trusted', true);
        } elseif ($request->device_filter === 'untrusted') {
            $sessions = $sessions->where('is_trusted', false);
        }

        $totalDevices   = DB::table('sessions')->where('user_id', auth()->id())->count();
        $currentDevices = 1;
        $otherDevices   = max(0, $totalDevices - $currentDevices);
        $trustedCount   = TrustedDevice::where('user_id', auth()->id())->count();
        $historyCount   = LoginActivity::where('user_id', auth()->id())->count();
        $suspiciousCount = LoginActivity::where('user_id', auth()->id())->where('is_suspicious', true)->count();

        $activities = LoginActivity::where('user_id', auth()->id())
            ->latest()
            ->paginate(5);

        return view('profile.security', compact(
            'sessions',
            'activities',
            'totalDevices',
            'currentDevices',
            'otherDevices',
            'trustedCount',
            'historyCount',
            'suspiciousCount'
        ));
    }

    /**
     * Real-time Session Count (AJAX)
     */
    public function sessionCount()
    {
        $count = DB::table('sessions')->where('user_id', auth()->id())->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Logout All Other Devices
     */
    public function logoutAllDevices(Request $request)
    {
        $request->validate(['password' => ['required']]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Password does not match.']);
        }

        // Current session ID save karo
        $currentSessionId = $request->session()->getId();

        // Database ma thi badhaj bija sessions delete karo
        DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $currentSessionId)
            ->delete();

        // Laravel built-in method pan call karo (password hash regenerate kare)
        Auth::logoutOtherDevices($request->password);

        // Current session regenerate karo
        $request->session()->regenerate();

        return back()->with('success', 'Logged out from all other devices successfully!');
    }

    /**
     * Logout Single Specific Session
     */
    public function logoutSession(Request $request, string $sessionId)
    {
        // Prevent logging out current session
        if ($sessionId === $request->session()->getId()) {
            return back()->withErrors(['error' => 'Cannot logout current session from here.']);
        }

        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'Device logged out successfully!');
    }

    /**
     * Trust a Device
     */
    public function trustDevice(Request $request)
    {
        $request->validate(['device_token' => 'required|string']);

        $agent    = new Agent();
        $ip       = $request->ip();
        $browser  = $agent->browser() ?: 'Unknown';
        $platform = $agent->platform() ?: 'Unknown';

        TrustedDevice::firstOrCreate(
            [
                'user_id'      => auth()->id(),
                'device_token' => $request->device_token,
            ],
            [
                'browser'    => $browser,
                'platform'   => $platform,
                'ip_address' => $ip,
                'trusted_at' => now(),
            ]
        );

        return back()->with('success', 'Device marked as trusted!');
    }

    /**
     * Remove Trust from a Device
     */
    public function untrustDevice(Request $request)
    {
        $request->validate(['device_token' => 'required|string']);

        TrustedDevice::where('user_id', auth()->id())
            ->where('device_token', $request->device_token)
            ->delete();

        return back()->with('success', 'Device trust removed!');
    }

    /**
     * Clear Login History
     */
    public function clearHistory()
    {
        LoginActivity::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Login history cleared successfully.');
    }
}
