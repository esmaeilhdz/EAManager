<?php

namespace App\Observers;

use App\Models\Person;

class PersonObserver
{
    /**
     * Handle the Person "created" event.
     *
     * @param Person $person
     * @return void
     */
    public function created(Person $person)
    {
        //
    }

    /**
     * Handle the Person "updated" event.
     *
     * @param Person $person
     * @return void
     */
    public function updated(Person $person)
    {
        //
    }

    /**
     * Handle the Person "deleted" event.
     *
     * @param Person $person
     * @return void
     */
    public function deleted(Person $person)
    {
        //
    }

    /**
     * Handle the Person "restored" event.
     *
     * @param Person $person
     * @return void
     */
    public function restored(Person $person)
    {
        //
    }

    /**
     * Handle the Person "force deleted" event.
     *
     * @param Person $person
     * @return void
     */
    public function forceDeleted(Person $person)
    {
        //
    }
}
