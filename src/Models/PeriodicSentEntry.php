<?php

namespace PeriodicNotice\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PeriodicNotice\Factories\PeriodicSentEntryFactory;

/**
 * @property \JsonFieldCast\Json\SimpleJsonField $meta
 */
class PeriodicSentEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'meta' => \JsonFieldCast\Casts\SimpleJsonField::class,
    ];

    public function getTable()
    {
        return config('periodic-notice.tables.periodic_sent_entries');
    }

    public function receiver(): MorphTo
    {
        return $this->morphTo();
    }

    public function sendable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeGroup(Builder $query, string|array $group)
    {
        if (is_array($group)) {
            return $query->whereIn('group', $group);
        }

        return $query->where('group', '=', $group);
    }

    public static function factory(...$parameters): PeriodicSentEntryFactory
    {
        return new PeriodicSentEntryFactory();
    }
}
