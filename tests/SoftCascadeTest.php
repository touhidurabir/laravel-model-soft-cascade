<?php

namespace Touhidurabir\ModelSoftCascade\Tests;

use Orchestra\Testbench\TestCase;
use Touhidurabir\ModelSoftCascade\Tests\App\User;
use Touhidurabir\ModelSoftCascade\Tests\App\Post;
use Touhidurabir\ModelSoftCascade\Tests\App\Profile;
use Touhidurabir\ModelSoftCascade\Tests\App\Comment;
use Touhidurabir\ModelSoftCascade\Tests\App\Address;
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

    
    /**
     * @test
     */
    public function it_cascade_delete() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);

        $user->delete();

        $this->assertNull(User::find($user->id));
        $this->assertNull(Profile::find($profile->id));

        $this->assertIsObject(User::withTrashed()->find($user->id));
        $this->assertIsObject(Profile::withTrashed()->find($profile->id));
    }

}