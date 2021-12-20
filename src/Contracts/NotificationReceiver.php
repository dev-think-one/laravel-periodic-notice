<?php

namespace PeriodicNotice\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use PeriodicNotice\PeriodicNoticeDirector;

interface NotificationReceiver
{
    public function periodicSentEntries(): MorphMany;

    public function sendNowPeriodicalNotification(string $group = 'default');
    
    public function sendPeriodicalNotification(string $group = 'default');

    public function periodicNoticeDirector(string $group = 'default'): PeriodicNoticeDirector;

    public function scopeWithNotificationPeriodType(Builder $query, string $type, string $group = 'default');
}
