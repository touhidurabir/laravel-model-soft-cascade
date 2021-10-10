<?php

namespace Touhidurabir\ModelSoftCascade\Tests;

use Orchestra\Testbench\TestCase;
use Touhidurabir\ModelHashid\Tests\App\User;
use Touhidurabir\ModelHashid\Tests\App\Post;
use Touhidurabir\ModelHashid\Tests\App\Profile;
use Touhidurabir\ModelHashid\Tests\App\Comment;
use Touhidurabir\ModelHashid\Tests\App\Address;
use Touhidurabir\ModelSoftCascade\Tests\Traits\LaravelTestBootstrapping;

class SoftCascadeTest extends TestCase {
    
    use LaravelTestBootstrapping;

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations() {

        $this->loadMigrationsFrom(__DIR__ . '/App/database/migrations');
        
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }

    
}