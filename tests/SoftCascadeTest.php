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


    /**
     * @test
     */
    public function it_cascade_restore() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);

        $user->delete();
        $user->refresh();
        $user->restore();

        $this->assertIsObject(User::find($user->id));
        $this->assertIsObject(Profile::find($profile->id));
    }


    /**
     * @test
     */
    public function it_cascade_force_delete_on_parent_force_delete() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);
        $post = $user->posts()->create(['title' => 'post title', 'body' => 'post body']);

        $user->forceDelete();

        $this->assertNull(User::withTrashed()->find($user->id));
        $this->assertNull(Profile::withTrashed()->find($profile->id));
        $this->assertNull(Post::withTrashed()->find($post->id));
    }


    /**
     * @test
     */
    public function it_works_on_chain_cascading() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $post = $user->posts()->create(['title' => 'post title', 'body' => 'post body']);
        $comment = $post->comments()->create(['user_id' => $user->id, 'comment' => 'post comment']);

        $user->delete();

        $this->assertNull(User::find($user->id));
        $this->assertNull(Post::find($post->id));
        $this->assertNull(Comment::find($comment->id));

        $this->assertIsObject(User::withTrashed()->find($user->id));
        $this->assertIsObject(Post::withTrashed()->find($post->id));
        $this->assertIsObject(Comment::withTrashed()->find($comment->id));

        $user->restore();

        $this->assertNotNull(User::find($user->id));
        $this->assertNotNull(Post::find($post->id));
        $this->assertNotNull(Comment::find($comment->id));
    }

}