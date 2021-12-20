<?php

namespace PeriodicNotice\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface SendableEntity
{
    public function notificationEntityTitle(): string;

    public function notificationEntityWebUrl(): string;

    public function notificationEntityDescription(): string;

    public function scopeReleasedAfter(Builder $query, \DateTimeInterface|string $dateTime, string $group);
}
