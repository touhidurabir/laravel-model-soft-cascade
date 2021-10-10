<?php

namespace Touhidurabir\ModelSoftCascade\Exceptions;

use Exception;
use Illuminate\Support\Str;

class SoftCascadeException extends Exception {

    /**
     * Model has not use the SoftDelete trait
     *
     * @param  string $modelClass
     * @return object<\Exception>
     */
    public static function softDeleteNotImplementedOnModel(string $modelClass) {

        return new static(
            sprintf(
                'Model class %s has missing use of Illuminate\Database\Eloquent\SoftDeletes trait', 
                $modelClass
            )
        );
    }

    
    /**
     * Invalid model realtion defined for cascading
     *
     * @param  string $modelClass
     * @param  array  $invalidRelations
     * 
     * @return object<\Exception>
     */
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


    /**
     * Cascading relation not defined
     *
     * @param  string $modelClass
     * @param  array  $targetAction
     * 
     * @return object<\Exception>
     */
    public static function relationNotDefined(string $modelClass, array $targetAction = ['restore', 'delete']) {

        return new static(
            sprintf(
                'Model class %s does not have any relations defined for specificd [%s] actions',
                $modelClass,
                join(', ', $targetAction)
            )
        );
    }


    /**
     * Cascading action[delete, restore] associated invoking event is invalid
     *
     * @param  string $action
     * @param  string $givenEventName
     * @param  array  $allowedEvents
     * 
     * @return object<\Exception>
     */
    public static function invalidActionEvent(string $action, string $givenEventName, array $allowedEvents) {

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