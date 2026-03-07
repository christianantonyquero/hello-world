<?php

namespace App\Listeners;

use App\User;

class UserEventSubscriber
{
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@handleUserLogin'
        );
    }

    // User Login
    public function handleUserLogin($event) {
        $this->logUserLogin($event->user);
    }

    protected function logUserLogin(User $user)
    {
        // increase login count and setup timestamp
        $user->increment('login_count', 1, [
            'last_login_at' => now(),
        ]);
    }
}