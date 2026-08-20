<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use App\Models\LoginActivity;
use App\Models\TrustedDevice;
use App\Mail\SuspiciousLoginAlert;
use Jenssegers\Agent\Agent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | LOGIN EVENT
        |--------------------------------------------------------------------------
        */
        Event::listen(Login::class, function ($event) {

            $agent  = new Agent();
            $ip     = request()->ip();
            $browser  = $agent->browser() ?: 'Unknown';
            $platform = $agent->platform() ?: 'Unknown';

            // Location from IP (free API - no key needed)
            $location = $this->getLocation($ip);

            // Check if this is a new/untrusted device
            $deviceToken  = md5($ip . $browser . $platform . $event->user->id);
            $isTrusted    = TrustedDevice::where('user_id', $event->user->id)
                ->where('device_token', $deviceToken)
                ->exists();

            $isNewDevice = !$isTrusted;

            // Log login activity
            LoginActivity::create([
                'user_id'      => $event->user->id,
                'ip_address'   => $ip,
                'browser'      => $browser,
                'platform'     => $platform,
                'location'     => $location,
                'is_suspicious' => $isNewDevice,
                'is_new_device' => $isNewDevice,
                'login_at'     => now(),
            ]);

            // Send suspicious login email alert for new device
            if ($isNewDevice && $event->user->email) {
                try {
                    Mail::to($event->user->email)->send(
                        new SuspiciousLoginAlert(
                            ip: $ip,
                            browser: $browser,
                            platform: $platform,
                            location: $location,
                            loginTime: now()->format('d M Y, h:i A')
                        )
                    );
                } catch (\Exception $e) {
                    // Mail failure should not break login
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | LOGOUT EVENT
        |--------------------------------------------------------------------------
        */
        Event::listen(Logout::class, function ($event) {

            $activity = LoginActivity::where('user_id', $event->user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();

            if ($activity) {
                $activity->update(['logout_at' => now()]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Get Location from IP (ip-api.com - free, no key needed)
    |--------------------------------------------------------------------------
    */
    private function getLocation(string $ip): string
    {
        // Skip for local/private IPs
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return 'Local Network';
        }

        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=city,regionName,country");
            if ($response) {
                $data = json_decode($response, true);
                $parts = array_filter([
                    $data['city'] ?? null,
                    $data['regionName'] ?? null,
                    $data['country'] ?? null,
                ]);
                return implode(', ', $parts) ?: 'Unknown';
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return 'Unknown';
    }
}
