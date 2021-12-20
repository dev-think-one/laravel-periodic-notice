<?php

namespace PeriodicNotice\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use PeriodicNotice\Concerns\HasPeriodicNotice;
use PeriodicNotice\Contracts\NotificationReceiver;
use PeriodicNotice\PeriodicNoticeDirector;
use PeriodicNotice\Tests\Fixtures\Factories\UserFactory;

class User extends \Illuminate\Foundation\Auth\User implements NotificationReceiver
{
    use Notifiable;
    use HasFactory;
    use HasPeriodicNotice;

    public static function factory(...$parameters): UserFactory
    {
        return new UserFactory();
    }

    public function allowedPeriodTypes()
    {
        return [
            'every_day',
            'every_week',
        ];
    }

    public function periodicNoticeDirector(string $group = 'default'): PeriodicNoticeDirector
    {
        $dayInSeconds = 60 * 60 * 24;

        return PeriodicNoticeDirector::defaults($group)
                                     ->usePeriodType($this->periodic_notification_type)
                                     ->usePeriodTypesMap([
                                         'every_day'  => round($dayInSeconds),
                                         'every_week' => round($dayInSeconds * 7),
                                     ])
                                     ->useQueryToGetEntries(Post::class);
    }

    public function scopeWithNotificationPeriodType(Builder $query, string $type, string $group = 'default')
    {
        $query->where('periodic_notification_type', '=', $type);
    }
}
