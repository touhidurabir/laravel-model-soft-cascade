<?php

namespace Touhidurabir\ModelSoftCascade\Facades;

use Illuminate\Support\Facades\Facade;

class ModelSoftCascade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {

        return 'model-soft-cascade';
    }
}