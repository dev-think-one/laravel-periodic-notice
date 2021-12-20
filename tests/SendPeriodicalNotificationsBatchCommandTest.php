<?php

namespace PeriodicNotice\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use PeriodicNotice\Models\PeriodicSentEntry;
use PeriodicNotice\Notifications\PeriodicPublicationNotification;
use PeriodicNotice\Tests\Fixtures\Models\Post;
use PeriodicNotice\Tests\Fixtures\Models\User;
use Symfony\Component\Console\Exception\RuntimeException;

class SendPeriodicalNotificationsBatchCommandTest extends TestCase
{

    /** @test */
    public function has_required_arguments()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "type, receiver")');
        $this->artisan('periodic-notice:send:batch');
    }


    /** @test */
    public function error_if_not_valid_receiver()
    {
        $type     = 'not_valid_foo';
        $receiver = Post::class;

        $escapedReceiverName = addslashes($receiver);
        $this->artisan("periodic-notice:send:batch {$type} {$escapedReceiverName}")
             ->expectsOutput("Please specify correct receiver. Currently specified: [{$receiver}]")
             ->assertExitCode(1)
             ->assertFailed();
    }


    /** @test */
    public function error_if_not_valid_type()
    {
        $type     = 'not_valid_foo';
        $receiver = User::class;

        $escapedReceiverName      = addslashes($receiver);
        $allowedPeriodTypesString = implode(', ', (new User())->allowedPeriodTypes());
        $this->artisan("periodic-notice:send:batch {$type} {$escapedReceiverName}")
             ->expectsOutput("Please specify correct period type. Currently specified: [{$type}]. Allowed types: [{$allowedPeriodTypesString}]")
             ->assertExitCode(2)
             ->assertFailed();
    }

    /** @test */
    public function successful_send_new_posts()
    {
        /* Prepare users in database */
        User::factory()->count(5)->create();
        $usersExpectNotificationEveryDay = User::factory()
                                               ->periodicNotificationType('every_day')
                                               ->count(6)->create();
        User::factory()
            ->periodicNotificationType('foo_days')
            ->count(7)->create();
        User::factory()
            ->periodicNotificationType('every_week')
            ->count(8)->create();
        User::factory()->count(5)->create();
        $this->assertEquals(31, User::count());
        $this->assertEquals(6, User::withNotificationPeriodType('every_day')->count());

        /* Prepare posts in database */
        $postsPublishedToday = Post::factory()->publishedAt(Carbon::now()->subHours(3))->count(5)->create();
        Post::factory()->publishedAt(Carbon::now()->subDays(3))->count(6)->create();
        $this->assertEquals(11, Post::count());
        $this->assertEquals(5, Post::where('published_at', '>', Carbon::now()->subDay())->count());

        Notification::assertNothingSent();
        $this->assertEquals(0, PeriodicSentEntry::count());

        $type                = 'every_day';
        $receiver            = User::class;
        $escapedReceiverName = addslashes($receiver);
        $this->artisan("periodic-notice:send:batch {$type} {$escapedReceiverName} -G test_gr")
             ->assertExitCode(0)
             ->assertSuccessful();

        $this->assertEquals(6 * 5, PeriodicSentEntry::count());
        $this->assertEquals(6 * 5, PeriodicSentEntry::group('test_gr')->count());

        /** @var PeriodicSentEntry $periodicSentEntry */
        $periodicSentEntry = PeriodicSentEntry::inRandomOrder()->first();

        $this->assertEquals('every_day', $periodicSentEntry->meta->getAttribute('type'));
        $this->assertEquals(PeriodicPublicationNotification::class, $periodicSentEntry->meta->getAttribute('notification'));
        $this->assertTrue(in_array($periodicSentEntry->receiver->id, $usersExpectNotificationEveryDay->pluck('id')->toArray()));
        $this->assertTrue(in_array($periodicSentEntry->sendable->id, $postsPublishedToday->pluck('id')->toArray()));

        Notification::assertSentTo(
            $usersExpectNotificationEveryDay->first(),
            PeriodicPublicationNotification::class
        );
    }
}
