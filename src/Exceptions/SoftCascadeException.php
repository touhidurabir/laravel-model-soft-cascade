<?php

namespace Touhidurabir\ModelSoftCascade\Exceptions;

use Exception;
use Illuminate\Support\Str;

class SoftCascadeException extends Exception {

    public static function softDeleteNotImplementedOnModel(string $modelClass) {

        return new static(
            sprintf(
                'Model class %s has missing use of Illuminate\Database\Eloquent\SoftDeletes trait', 
                $modelClass
            )
        );
    }

    
    public static function invalidRelationDefined(string $modelClass, array $invalidRelations) {

        return new static(
            sprintf(
                'Model class %s does not have %s [%s] defined or does not return an instance of Illuminate\Database\Eloquent\Relations\Relation',
                $modelClass,
                Str::plural('relationship', count($invalidRelations)),
                join(', ', $invalidRelations)
            )
        );
    }

    public static function relationNotDefined(string $modelClass, $targetAction = ['restore', 'delete']) {

        return new static(
            sprintf(
                'Model class %s does not have any relations defined for specificd [%s] actions',
                $modelClass,
                join(', ', $targetAction)
            )
        );
    }


    public static function invalidActionEvent(string $action, $givenEventName, $allowedEvents) {

        return new static(
            sprintf(
                'Given invalid %s event for %s action, acceptable events are [%s]',
                $action,
                $givenEventName,
                join(', ', $allowedEvents)
            )
        );
    }
}