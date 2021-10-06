<?php

namespace Touhidurabir\ModelSoftCascade;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeDelete;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeRestore;

trait HasSoftCascade {

    use SoftCascadeDelete, SoftCascadeRestore;

    abstract public function cascade() : array;

    public static function bootHasSoftCascade() {


    }


    public function initializeHasSoftCascade() {

        
    }
}