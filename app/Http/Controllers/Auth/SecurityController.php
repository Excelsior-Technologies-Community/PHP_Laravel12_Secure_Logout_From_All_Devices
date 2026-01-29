<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecurityController extends Controller
{
    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Password does not match']);
        }

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Logged out from all other devices successfully!');
    }
}
