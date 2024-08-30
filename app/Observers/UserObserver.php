<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;


class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->clearStatsCache();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->clearStatsCache();
    }

    private function clearStatsCache()
    {
        Cache::forget('stats');
    }
}