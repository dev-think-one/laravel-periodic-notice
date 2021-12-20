<?php

namespace PeriodicNotice\Helpers;

use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{
    public static function defaultColumns(Blueprint $table)
    {
        $table->id();
        $table->morphs('receiver');
        $table->morphs('sendable');
        $table->string('group', 30)->default('default')->index();
        $table->dateTime('sent_at')->index();
        $table->json('meta')->nullable();
        $table->timestamps();
    }
}
