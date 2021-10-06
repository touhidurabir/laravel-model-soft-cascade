<?php

namespace Touhidurabir\ModelSoftCascade\Exceptions;

use Exception;
use Illuminate\Support\Str;

class SoftCascadeException extends Exceptions {

    public static function softDeleteNotImplementedOnModel(string $modelClass) {

        return new static(
            sprintf(
                'Model class %s has missing use of Illuminate\Database\Eloquent\SoftDeletes trait', 
                $modelClass
            )
        );
    }

    
    public static function relationNotDefined(string $modelClass, array $invalidRelations) {

        return new static(
            sprintf(
                'Model class %s does not have relations [%s] defined or does not return an instance of Illuminate\Database\Eloquent\Relations\Relation',
                Str::plural('Relationship', count($invalidRelations)),
                join(', ', $invalidRelations)
            )
        );
    }
}