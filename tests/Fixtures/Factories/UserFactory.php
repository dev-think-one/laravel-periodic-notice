<?php

namespace PeriodicNotice\Tests\Fixtures\Factories;

use PeriodicNotice\Tests\Fixtures\Models\User;

class UserFactory extends \Orchestra\Testbench\Factories\UserFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    public function periodicNotificationType(string $type)
    {
        return $this->state([
            'periodic_notification_type' => $type,
        ]);
    }
}
