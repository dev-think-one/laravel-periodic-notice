<?php

namespace PeriodicNotice\Tests;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use PeriodicNotice\PeriodicNoticeDirector;
use PeriodicNotice\Tests\Fixtures\Models\Post;
use PeriodicNotice\Tests\Fixtures\Models\User;

class PeriodicNoticeDirectorTest extends TestCase
{
    /** @test */
    public function use_query_to_get_entries_as_callback()
    {
        $user = User::factory()
                    ->periodicNotificationType('every_day')
                    ->create();

        Post::factory()->count(10)->create();
        $post = Post::factory()->publishedAt(Carbon::now()->subHours(3))->create();
        Post::factory()->count(10)->create();

        $director = PeriodicNoticeDirector::defaults()
                                          ->usePeriodType('every_day')
                                          ->usePeriodTypesMap([
                                              'every_day' => 60 * 60 * 24,
                                          ])
                                          ->useQueryToGetEntries(fn () => Post::query()->where('title', $post->title));

        $result = $director->findEntries($user);

        $this->assertCount(1, $result);
        $this->assertEquals($post->id, $result->first()->id);
    }

    /** @test */
    public function find_entrieS_return_empty()
    {
        $director = PeriodicNoticeDirector::defaults()
                                          ->usePeriodType('every_day')
                                          ->useQueryToGetEntries(Post::class);

        $user = User::factory()
                    ->periodicNotificationType('every_day')
                    ->create();
        $result = $director->findEntries($user);

        $this->assertCount(0, $result);
    }

    /** @test */
    public function use_custom_notification()
    {
        $director = PeriodicNoticeDirector::defaults()
                                          ->useNotificationClass(ResetPassword::class);

        $this->assertEquals(ResetPassword::class, $director->notificationClass());


        $director = PeriodicNoticeDirector::defaults('foo')
                                          ->usePeriodType('bar')
                                          ->useNotificationClass(function ($periodType, $group) {
                                              $this->assertEquals('bar', $periodType);
                                              $this->assertEquals('foo', $group);

                                              return VerifyEmail::class;
                                          });

        $this->assertEquals(VerifyEmail::class, $director->notificationClass());
    }
}
