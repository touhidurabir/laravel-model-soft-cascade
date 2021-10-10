<?php

namespace Touhidurabir\ModelSoftCascade\Tests\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Touhidurabir\ModelSoftCascade\HasSoftCascade;

class Comment extends Model {

    use SoftDeletes;

    use HasSoftCascade;

    public function cascadable() {

        return [];
    }

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'comments';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the user that owns the comment.
     *
     * @return object
     */
    public function user() {

        return $this->belongsTo('Touhidurabir\ModelSoftCascade\Tests\App\User');
    }


    /**
     * Get the post that owns the comment.
     *
     * @return object
     */
    public function post() {

        return $this->belongsTo('Touhidurabir\ModelSoftCascade\Tests\App\Post');
    }

}