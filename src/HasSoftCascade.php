<?php

namespace Touhidurabir\ModelSoftCascade;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeDelete;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeRestore;
use Touhidurabir\ModelSoftCascade\Exceptions\SoftCascadeException;

trait HasSoftCascade {

    protected $relationships = [];

    protected $runAsDatabaseTransaction = true;

    use SoftCascadeDelete, SoftCascadeRestore;

    abstract public function cascade() : array;

    public static function bootHasSoftCascade() {

        $self = new self;

		$self->initializeHasSoftCascade();

		static::{$self->deleteEvent}(function ($model) use ($self) {

			// if model is force deleting and at model configuration onforceDelete set to true
			if ( $model->isForceDeleting() && $self->forceDeleteOnModelForceDelete ) {

				$self->deleteMethod = 'forceDelete';
			}

			$self->deleteModelRelations($self->deleteRelations, $self->deleteMethod, $model);
		});

        static::{$self->restoreEvent}(function ($model) use ($self) {

			$self->restoreModelRelations($self->restoreRelations, $model);
			
		});
    }


    public function initializeHasSoftCascade() {

        
    }


    /**
     * Validate that the calling model is correctly setup for cascading behaviours.
     *
     * @return void
     * 
     * @throws \Touhidurabir\ModelSoftCascade\Exceptions\SoftCascadeException
     */
    protected function validateCascadingSoftDelete() {

        if ( ! $this->hasSoftDeletes() ) {

            throw SoftCascadeException::softDeleteNotImplementedOnModel(get_class($this));
        }

        if ( $invalidCascadingRelationships = $this->hasInvalidRelationships($this->relationships) ) {
            
            throw SoftCascadeException::relationNotDefined(
                get_class($this),
                $invalidCascadingRelationships
            );
        }
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

}