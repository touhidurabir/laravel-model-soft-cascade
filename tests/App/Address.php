<?php

namespace Touhidurabir\ModelSoftCascade\Tests\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Touhidurabir\ModelSoftCascade\HasSoftCascade;

class Address extends Model {

    use SoftDeletes;

    use HasSoftCascade;

    public function cascadable() : array  {

        return [];
    }

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'addresses';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the user that owns this address.
     *
     * @return object
     */
    public function user() {

        return $this->belongsTo('Touhidurabir\ModelSoftCascade\Tests\App\User');
    }
}