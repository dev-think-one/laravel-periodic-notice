<?php

namespace PeriodicNotice\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use PeriodicNotice\Models\PeriodicSentEntry;

/**
 * Trait related to sendable entity.
 */
trait InPeriodicNotice
{
    public function notificationEntityTitle(): string
    {
        return $this->title ?? '';
    }

    public function notificationEntityWebUrl(): string
    {
        return url($this->slug ?? '');
    }

    public function notificationEntityDescription(): string
    {
        return $this->description ?? '';
    }

    public function periodicSentEntries(): MorphMany
    {
        return $this->morphMany(PeriodicSentEntry::class, 'sendable');
    }

    public function scopeDoesntSentInPeriodicNotice(Builder $query, Model $receiver, string $group)
    {
        $query->whereDoesntHave(
            'periodicSentEntries',
            function (Builder $query) use ($receiver, $group) {
                $query->group($group)
                      ->where('receiver_type', '=', $receiver->getMorphClass())
                      ->where('receiver_id', '=', $receiver->getKey());
            }
        );
    }

    public function scopeReleasedAfter(Builder $query, \DateTimeInterface|string $dateTime, string $group)
    {
        $query->where('created_at', '>=', $dateTime);
    }
}
