<?php

namespace MasudZaman\Trails\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MasudZaman\Trails\TrackableInterface;

class RegistrationTracked
{
    use Dispatchable, SerializesModels;

    public TrackableInterface $trackable;

    /**
     * Create a new event instance.
     *
     * @param TrackableInterface $trackable
     * @return void
     */
    public function __construct(TrackableInterface $trackable)
    {
        $this->trackable = $trackable;
    }
}
