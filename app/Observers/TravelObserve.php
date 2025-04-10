<?php

namespace App\Observers;

use App\Models\Travel;
use Illuminate\Support\Facades\Log;

class TravelObserve
{
    //
    public function creating(Travel $travel): void
    {
        //
        $travel->slug = str($travel->name)->slug();
        Log::info("<br>Travel {$travel->slug} model in creating (before)...<br>");
    }

    /**
     * Handle the Travel "created" event.
     */
    public function created(Travel $travel): void
    {
        //
        Log::info("<br>Travel model {$travel->slug} in creating (after)...<br>");
    }

    /**
     * Handle the Travel "updated" event.
     */
    public function updated(Travel $travel): void
    {
        //
        Log::info("<br>Travel model {$travel->slug} just updated...<br>");
    }

    /**
     * Handle the Travel "deleted" event.
     */
    public function deleted(Travel $travel): void
    {
        //
        Log::info("<br>Travel model {$travel->slug} just deleted...<br>");
    }

    /**
     * Handle the Travel "restored" event.
     */
    public function restored(Travel $travel): void
    {
        //
        Log::info("<br>Travel model {$travel->slug} just restored...<br>");
    }

    /**
     * Handle the Travel "force deleted" event.
     */
    public function forceDeleted(Travel $travel): void
    {
        //
    }
}
