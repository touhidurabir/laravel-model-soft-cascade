<?php

namespace Touhidurabir\ModelSoftCascade\Tests\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Touhidurabir\ModelSoftCascade\HasSoftCascade;

class Post extends Model {

    use SoftDeletes;

    use HasSoftCascade;


    /**
     * The cascade custom configurations
     *
     * @return array
     */
    public function cascadable() : array  {

        return [
            'delete' => [
                'event'     => 'deleted',
                'relations' => ['comments'],
                'force'     => true,
            ],
            'restore' => [
                'event'     => 'restored',
                'relations' => ['comments'],
            ]
        ];
    }
    

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'posts';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the user that owns the post.
     *
     * @return object
     */
    public function user() {

        return $this->belongsTo('Touhidurabir\ModelSoftCascade\Tests\App\User');
    }


    /**
     * Get all the comments associated with this post
     *
     * @return object
     */
    public function comments() {

        return $this->hasMany('Touhidurabir\ModelSoftCascade\Tests\App\Comment');
    }


    /**
     * Get all the shared[many to many relation based] users associated with this posts
     * 
     * @return object
     */
    public function sharedUsers() {

        return $this->belongsToMany('Touhidurabir\ModelSoftCascade\Tests\App\User', 'post_user', 'post_id', 'user_id');
    }

}