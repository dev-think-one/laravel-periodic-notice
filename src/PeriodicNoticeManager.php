<?php

namespace PeriodicNotice;

class PeriodicNoticeManager
{
    /**
     * Indicates if laravel should run migrations for package.
     *
     * @var bool
     */
    public static bool $runsMigrations = true;

    /**
     * Configure laravel to not register current package migrations.
     *
     * @return static
     */
    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }
}
