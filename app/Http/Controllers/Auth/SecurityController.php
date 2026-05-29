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
    public function index()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get();

        $agent = new Agent();

        foreach ($sessions as $session) {

            $agent->setUserAgent($session->user_agent);

            $session->browser = $agent->browser();

            $session->platform = $agent->platform();

            $session->is_current_device =
                $session->id === request()->session()->getId();

            $inactiveMinutes = floor(
                (time() - $session->last_activity) / 60
            );

            $session->last_seen = $inactiveMinutes <= 1
                ? 'Online Now'
                : 'Last Seen ' . $inactiveMinutes . ' mins ago';
        }

        $totalDevices = $sessions->count();

        $activities = LoginActivity::where(
                'user_id',
                auth()->id()
            )
            ->latest()
            ->take(10)
            ->get();

        return view(
            'profile.security',
            compact(
                'sessions',
                'activities',
                'totalDevices'
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

        if (!Hash::check(
            $request->password,
            auth()->user()->password
        )) {

            return back()->withErrors([
                'password' => 'Password does not match'
            ]);
        }

        Auth::logoutOtherDevices($request->password);

        return back()->with(
            'success',
            'Logged out from all other devices successfully!'
        );
    }
}