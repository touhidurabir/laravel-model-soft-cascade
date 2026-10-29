<?php

namespace Touhidurabir\ModelSoftCascade\Tests\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Touhidurabir\ModelSoftCascade\HasSoftCascade;

class User extends Model
{
    use SoftDeletes;

    use HasSoftCascade;

    /**
     * Test-only override for the cascade configuration. Reset it after each test.
     *
     * @var array|null
     */
    public static $cascadeConfig = null;

    public function cascadable(): array
    {

        return static::$cascadeConfig ?? [
            'profile', 'posts'
        ];
    }

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'users';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the profile record associated with the user.
     *
     * @return object
     */
    public function profile()
    {

        return $this->hasOne('Touhidurabir\ModelSoftCascade\Tests\App\Profile');
    }


    /**
     * Get all the posts record associated with the user.
     *
     * @return object
     */
    public function posts()
    {

        return $this->hasMany('Touhidurabir\ModelSoftCascade\Tests\App\Post');
    }


    /**
     * Get all the comments record associated with the user.
     *
     * @return object
     */
    public function comments()
    {

        return $this->hasMany('Touhidurabir\ModelSoftCascade\Tests\App\Comment');
    }


    /**
     * Get all the addresses record associated with the user.
     *
     * @return object
     */
    public function addresses()
    {

        return $this->hasMany('Touhidurabir\ModelSoftCascade\Tests\App\Address');
    }


    /**
     * Get all the shared[many to many relation based] posts for this user
     *
     * @return object
     */
    public function sharedPosts()
    {

        return $this->belongsToMany('Touhidurabir\ModelSoftCascade\Tests\App\Post', 'post_user', 'user_id', 'post_id');
    }
}
