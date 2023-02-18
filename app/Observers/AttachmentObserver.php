<?php

namespace App\Observers;

use App\FileManager;
use App\Models\Attachment;

class AttachmentObserver
{
    /**
     * Handle the Attachment "created" event.
     *
     * @param Attachment $attachment
     * @return void
     */
    public function created(Attachment $attachment)
    {
    }

    /**
     * Handle the Attachment "updated" event.
     *
     * @param Attachment $attachment
     * @return void
     */
    public function updated(Attachment $attachment)
    {
        //
    }

    /**
     * Handle the Attachment "deleted" event.
     *
     * @param Attachment $attachment
     * @return void
     */
    public function deleted(Attachment $attachment)
    {
        //
    }

    /**
     * Handle the Attachment "restored" event.
     *
     * @param Attachment $attachment
     * @return void
     */
    public function restored(Attachment $attachment)
    {
        //
    }

    /**
     * Handle the Attachment "force deleted" event.
     *
     * @param Attachment $attachment
     * @return void
     */
    public function forceDeleted(Attachment $attachment)
    {
        //
    }
}
