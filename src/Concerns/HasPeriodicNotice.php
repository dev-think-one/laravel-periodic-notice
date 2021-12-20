<?php

namespace PeriodicNotice\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use PeriodicNotice\Models\PeriodicSentEntry;

/**
 * Trait related to notification receiver.
 *
 * @mixin \PeriodicNotice\Contracts\NotificationReceiver
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasPeriodicNotice
{
    public function periodicSentEntries(): MorphMany
    {
        return $this->morphMany(PeriodicSentEntry::class, 'receiver');
    }

    public function sendNowPeriodicalNotification(string $group = 'default')
    {
        $this->periodicNoticeDirector($group)->sendPeriodicalNotification($this);
    }

    public function sendPeriodicalNotification(string $group = 'default')
    {
        dispatch(function () use ($group) {
            $this->sendNowPeriodicalNotification($group);
        })->onConnection(config('periodic-notice.defaults.connection'))
          ->onQueue(config('periodic-notice.defaults.queue'));
    }
}
