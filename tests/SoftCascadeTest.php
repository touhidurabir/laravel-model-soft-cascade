<?php

namespace Touhidurabir\ModelSoftCascade\Tests;

use RuntimeException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Orchestra\Testbench\TestCase;
use Touhidurabir\ModelSoftCascade\Tests\App\User;
use Touhidurabir\ModelSoftCascade\Tests\App\Post;
use Touhidurabir\ModelSoftCascade\Tests\App\Profile;
use Touhidurabir\ModelSoftCascade\Tests\App\Comment;
use Touhidurabir\ModelSoftCascade\Tests\Traits\LaravelTestBootstrapping;

class SoftCascadeTest extends TestCase
{
    use LaravelTestBootstrapping;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {

        User::$cascadeConfig = null;

        parent::setUp();
    }


    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {

        User::$cascadeConfig = null;

        parent::tearDown();
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {

        $this->loadMigrationsFrom(__DIR__ . '/App/database/migrations');

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }


    /**
     * @test
     */
    #[Test]
    public function it_cascade_delete()
    {

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
    #[Test]
    public function it_cascade_restore()
    {

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
    #[Test]
    public function it_cascade_force_delete_on_parent_force_delete()
    {

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
    #[Test]
    public function it_works_on_chain_cascading()
    {

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


    /**
     * @test
     */
    #[Test]
    public function it_does_not_leak_force_delete_into_later_soft_deletes()
    {

        $forceDeletedUser = User::create(['email' => 'a@m.test', 'password' => '123']);
        $forceDeletedUser->profile()->create(['first_name' => 'first', 'last_name' => 'last']);

        $user = User::create(['email' => 'b@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);
        $post = $user->posts()->create(['title' => 'post title', 'body' => 'post body']);

        $forceDeletedUser->forceDelete();
        $user->delete();

        $this->assertIsObject(User::withTrashed()->find($user->id));
        $this->assertIsObject(Profile::withTrashed()->find($profile->id));
        $this->assertIsObject(Post::withTrashed()->find($post->id));
        $this->assertTrue(Profile::withTrashed()->find($profile->id)->trashed());
        $this->assertTrue(Post::withTrashed()->find($post->id)->trashed());
    }


    /**
     * @test
     */
    #[Test]
    public function it_rolls_back_the_cascade_delete_when_a_relation_fails()
    {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);
        $post = $user->posts()->create(['title' => 'post title', 'body' => 'post body']);

        Post::deleting(function () {
            throw new RuntimeException('Post delete failed');
        });

        try {
            $user->delete();
            $this->fail('The cascade delete exception was not rethrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Post delete failed', $exception->getMessage());
        }

        $this->assertSame(0, DB::transactionLevel());
        $this->assertNotNull(User::find($user->id));
        $this->assertNotNull(Profile::find($profile->id));
        $this->assertNotNull(Post::find($post->id));
    }


    /**
     * @test
     */
    #[Test]
    public function it_rolls_back_the_cascade_restore_when_a_relation_fails()
    {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);
        $post = $user->posts()->create(['title' => 'post title', 'body' => 'post body']);

        $user->delete();
        $user->refresh();

        Post::restoring(function () {
            throw new RuntimeException('Post restore failed');
        });

        try {
            $user->restore();
            $this->fail('The cascade restore exception was not rethrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Post restore failed', $exception->getMessage());
        }

        $this->assertSame(0, DB::transactionLevel());
        $this->assertNull(User::find($user->id));
        $this->assertNull(Profile::find($profile->id));
        $this->assertNull(Post::find($post->id));
    }


    /**
     * @test
     */
    #[Test]
    public function it_deletes_the_pivot_record_or_the_model_with_delete_model_relation_record()
    {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = $user->profile()->create(['first_name' => 'first', 'last_name' => 'last']);
        $post = $user->posts()->create(['title' => 'post title', 'body' => 'post body']);
        $user->sharedPosts()->attach($post->id);

        $user->deleteModelRelationRecord($user->sharedPosts()->first(), 'delete');

        $this->assertSame(0, DB::table('post_user')->count());
        $this->assertNotNull(Post::find($post->id));

        $user->deleteModelRelationRecord($profile, 'delete');

        $this->assertNull(Profile::find($profile->id));
        $this->assertIsObject(Profile::withTrashed()->find($profile->id));
    }

}
