<?php

namespace PeriodicNotice\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PeriodicNotice\Concerns\InPeriodicNotice;
use PeriodicNotice\Contracts\SendableEntity;
use PeriodicNotice\Tests\Fixtures\Factories\PostFactory;

class Post extends Model implements SendableEntity
{
    use InPeriodicNotice;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public static function factory(...$parameters): PostFactory
    {
        return new PostFactory();
    }

    public function scopeReleasedAfter(Builder $query, \DateTimeInterface|string $dateTime, string $group)
    {
        $query->where('published_at', '>=', $dateTime);
    }
}
