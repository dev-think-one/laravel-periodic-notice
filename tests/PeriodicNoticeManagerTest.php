<?php

namespace PeriodicNotice\Tests;

use PeriodicNotice\PeriodicNoticeManager;

class PeriodicNoticeManagerTest extends TestCase
{

    /** @test */
    public function possible_to_ignore_migration()
    {
        $this->assertTrue(PeriodicNoticeManager::$runsMigrations);

        PeriodicNoticeManager::ignoreMigrations();

        $this->assertFalse(PeriodicNoticeManager::$runsMigrations);

        PeriodicNoticeManager::$runsMigrations = true;

        $this->assertTrue(PeriodicNoticeManager::$runsMigrations);
    }
}
