<?php

namespace App\Observers;

use App\Models\Notif;

class NotifObserver
{
    /**
     * Handle the Notif "created" event.
     *
     * @param Notif $notif
     * @return void
     */
    public function created(Notif $notif)
    {
        // todo: send sms or email to receiver_user
        // todo: send socket io to receiver_user
    }

    /**
     * Handle the Notif "updated" event.
     *
     * @param Notif $notif
     * @return void
     */
    public function updated(Notif $notif)
    {
        //
    }

    /**
     * Handle the Notif "deleted" event.
     *
     * @param Notif $notif
     * @return void
     */
    public function deleted(Notif $notif)
    {
        //
    }

    /**
     * Handle the Notif "restored" event.
     *
     * @param Notif $notif
     * @return void
     */
    public function restored(Notif $notif)
    {
        //
    }

    /**
     * Handle the Notif "force deleted" event.
     *
     * @param Notif $notif
     * @return void
     */
    public function forceDeleted(Notif $notif)
    {
        //
    }
}
