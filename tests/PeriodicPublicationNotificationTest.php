<?php

namespace PeriodicNotice\Tests;

use PeriodicNotice\Notifications\PeriodicPublicationNotification;
use PeriodicNotice\Tests\Fixtures\Models\Post;
use PeriodicNotice\Tests\Fixtures\Models\User;

class PeriodicPublicationNotificationTest extends TestCase
{
    /** @test */
    public function build_default_notification()
    {
        $user    = User::factory()->create();
        $entries = Post::factory()->count(2)->create();

        $notification = new PeriodicPublicationNotification($entries);

        $mailMessage = $notification->toMail($user);

        $mailString = $mailMessage->render();

        /** @var \PeriodicNotice\Contracts\SendableEntity $entry */
        foreach ($entries as $entry) {
            $this->assertTrue(str_contains($mailString, $entry->notificationEntityWebUrl()));
        }
    }
}
