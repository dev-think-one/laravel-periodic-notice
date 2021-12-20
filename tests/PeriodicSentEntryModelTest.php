<?php

namespace PeriodicNotice\Tests;

use PeriodicNotice\Models\PeriodicSentEntry;
use PeriodicNotice\Tests\Fixtures\Models\Post;
use PeriodicNotice\Tests\Fixtures\Models\User;

class PeriodicSentEntryModelTest extends TestCase
{

    /** @test */
    public function model_has_table_name_from_config()
    {
        $model = new PeriodicSentEntry();

        $this->assertEquals('custom_periodic_sent_entries', $model->getTable());
    }

    /** @test */
    public function query_scope_group()
    {
        $user = User::factory()->create();

        PeriodicSentEntry::factory()->count(2)->for(
            Post::factory(),
            'sendable'
        )->for($user, 'receiver')->create();

        PeriodicSentEntry::factory()->count(3)->for(
            Post::factory(),
            'sendable'
        )->for($user, 'receiver')->group('foo')->create();

        PeriodicSentEntry::factory()->count(5)->for(
            Post::factory(),
            'sendable'
        )->for($user, 'receiver')->group('bar')->create();

        $this->assertEquals(10, PeriodicSentEntry::count());
        $this->assertEquals(3, PeriodicSentEntry::group('foo')->count());
        $this->assertEquals(8, PeriodicSentEntry::group(['foo', 'bar'])->count());
    }
}
