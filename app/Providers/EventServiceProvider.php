<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

use Illuminate\Support\Facades\Event;

use App\Models\LoginActivity;

use Jenssegers\Agent\Agent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings.
     */
    protected $listen = [];

    /**
     * Register any events.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | LOGIN EVENT
        |--------------------------------------------------------------------------
        */

        Event::listen(Login::class, function ($event) {

            $agent = new Agent();

            LoginActivity::create([

                'user_id' => $event->user->id,

                'ip_address' => request()->ip(),

                'browser' => $agent->browser(),

                'platform' => $agent->platform(),

                'login_at' => now(),
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | LOGOUT EVENT
        |--------------------------------------------------------------------------
        */

        Event::listen(Logout::class, function ($event) {

            $activity = LoginActivity::where(
                    'user_id',
                    $event->user->id
                )
                ->latest()
                ->first();

            if ($activity) {

                $activity->update([

                    'logout_at' => now()
                ]);
            }
        });
    }
}