<?php

namespace Tests;

trait CustomDatabaseMigrations
{
    /**
     * Define hooks to migrate   the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->artisan('migrate');

//        $this->app[Kernel::class]->setArtisan(null);

//        $this->beforeApplicationDestroyed(function () {
//            $this->artisan('migrate:rollback');
//        });
    }
}
