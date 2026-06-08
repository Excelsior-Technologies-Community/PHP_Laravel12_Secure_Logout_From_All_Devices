<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use App\Models\LoginActivity;

class SecurityController extends Controller
{
    /**
     * Security Page
     */
    public function index(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get();

        $agent = new Agent();

        foreach ($sessions as $session) {

            $agent->setUserAgent($session->user_agent);

            $session->browser = $agent->browser() ?: 'Unknown';
            $session->platform = $agent->platform() ?: 'Unknown';

            $session->is_current_device =
                $session->id === request()->session()->getId();

            $inactiveMinutes = floor(
                (time() - $session->last_activity) / 60
            );

            $session->last_seen = $inactiveMinutes <= 1
                ? 'Online Now'
                : 'Last Seen ' . $inactiveMinutes . ' mins ago';
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $search = strtolower($request->search);

            $sessions = $sessions->filter(function ($session) use ($search) {

                return str_contains(
                    strtolower($session->browser ?? ''),
                    $search
                ) ||
                str_contains(
                    strtolower($session->platform ?? ''),
                    $search
                ) ||
                str_contains(
                    strtolower($session->ip_address ?? ''),
                    $search
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Device Filter
        |--------------------------------------------------------------------------
        */
        if ($request->device_filter === 'current') {

            $sessions = $sessions->where(
                'is_current_device',
                true
            );
        }

        if ($request->device_filter === 'other') {

            $sessions = $sessions->where(
                'is_current_device',
                false
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        $totalDevices = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->count();

        $currentDevices = 1;

        $otherDevices = max(
            0,
            $totalDevices - $currentDevices
        );

        $historyCount = LoginActivity::where(
            'user_id',
            auth()->id()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Login Activities
        |--------------------------------------------------------------------------
        */
        $activities = LoginActivity::where(
            'user_id',
            auth()->id()
        )
        ->latest()
        ->paginate(5);

        return view(
            'profile.security',
            compact(
                'sessions',
                'activities',
                'totalDevices',
                'currentDevices',
                'otherDevices',
                'historyCount'
            )
        );
    }

    /**
     * Logout Other Devices
     */
    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'password' => ['required']
        ]);

        if (
            !Hash::check(
                $request->password,
                auth()->user()->password
            )
        ) {
            return back()->withErrors([
                'password' => 'Password does not match.'
            ]);
        }

        Auth::logoutOtherDevices(
            $request->password
        );

        return back()->with(
            'success',
            'Logged out from all other devices successfully!'
        );
    }

    /**
     * Clear Login History
     */
    public function clearHistory()
    {
        LoginActivity::where(
            'user_id',
            auth()->id()
        )->delete();

        return back()->with(
            'success',
            'Login history cleared successfully.'
        );
    }
}