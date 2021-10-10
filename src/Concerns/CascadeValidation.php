<?php

namespace Touhidurabir\ModelSoftCascade\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;
use Touhidurabir\ModelSoftCascade\Exceptions\SoftCascadeException;

trait CascadeValidation {

    /**
     * List of allowed events on which the Cascade bootable trait can run
     *
     * @var array
     */
    protected $allowedEvent = [
        'delete'    => ['deleted', 'deleting'],
        'restore'   => ['restored', 'restoring']
    ];


    /**
     * Validate that the calling model is correctly setup for cascading behaviours.
     *
     * @return void
     * 
     * @throws \Touhidurabir\ModelSoftCascade\Exceptions\SoftCascadeException
     */
    protected function validateCascading(array $actions = ['delete', 'restore']) {

        $modelClass = get_class($this);

        if ( ! $this->hasCascadeRelationDefined() ) {

            throw SoftCascadeException::relationNotDefined($modelClass, $actions);
        }

        if ( ! $this->hasSoftDeletes() ) {

            throw SoftCascadeException::softDeleteNotImplementedOnModel($modelClass);
        }

        if ( $invalidCascadingRelationships = $this->hasInvalidRelationships($this->relationships) ) {
            
            throw SoftCascadeException::invalidRelationDefined(
                $modelClass,
                $invalidCascadingRelationships
            );
        }
    }


    /**
     * Determine if any relations has defined or not
     *
     * @return bool
     */
    protected function hasCascadeRelationDefined() {

        if ( ! is_array($this->relationships) || empty($this->relationships)  ) {

            return false;
        }

        return true;
    }


    /**
     * Determine if the current model has soft deletes trait define/use it.
     *
     * @return bool
     */
    protected function hasSoftDeletes() {

        return method_exists($this, 'runSoftDelete');
    }


    /**
     * Determine if the current model has any invalid cascading relationships defined.
     *
     * A relationship is considered invalid when the method does not exist, or the relationship
     * method does not return an instance of Illuminate\Database\Eloquent\Relations\Relation.
     *
     * @param  array $relationships
     * @return array
     */
    protected function hasInvalidRelationships(array $relationships = []) {

        return array_filter($relationships, function ($relationship) {
            return ! method_exists($this, $relationship) || ! $this->{$relationship}() instanceof Relation;
        });
    }


    /**
     * Validate given actions[delete,restore] cascade event
     *
     * @param  array $actions
     * @return void
     * 
     * @throws \Touhidurabir\ModelSoftCascade\Exceptions\SoftCascadeException
     */
    protected function validateActionEvent(array $actions = ['delete', 'restore']) {

        foreach($actions as $action) {

            $eventPropertyName = strtolower($action) . 'Event';

            if ( ! in_array($this->{$eventPropertyName}, $this->allowedEvent[$action]) ) {

                throw SoftCascadeException::invalidActionEvent(
                    $action, 
                    $this->{$eventPropertyName}, 
                    $this->allowedEvent[$action]
                );
            }
        }
    }

}