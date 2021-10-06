<?php

namespace Touhidurabir\ModelSoftCascade\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait SoftCascadeDelete {

    
    /**
     * List of valid relation delete methods
     *
     * @var array
     */
	private $validDeleteMethod = ['delete', 'forceDelete'];


	/**
     * List of allowed events on which the DeleteRelations bootable trait can run
     *
     * @var array
     */
	private $allowedDeleteEvents = ['deleted', 'deleting'];


	/**
     * Default relation delete method
     *
     * @var string
     */
	protected $deleteMethod = 'delete';


	/**
     * Default event on which the DeleteRelations bootable trait will run
     *
     * @var string
     */
	protected $deleteEvent = 'deleting';


	/**
     * Determine relation force delete if model is using force delete
     *
     * @var bool
     */
	protected $forceDeleteOnModelForceDelete = false;


	/**
     * List of the relations to be deleted
     *
     * @var array
     */
	protected $deleteRelations = [];
	

	/**
     * Load the user defined configs and list relations to be deleted
     *
     * @return void
     */
	private function initDelatebaleRelations() {

		if ( method_exists($this, 'deletableRelations') ) {

			$configs = $this->deletableRelations();

			if ( is_array($configs) ) {

				$this->deleteEvent = isset($configs['event']) && 
									 in_array($configs['event'], $this->allowedDeleteEvents)
									 	? $configs['event']
									 	: $this->deleteEvent;

				$this->deleteMethod = $this->validateDeleteMethod($configs['method'] ?? NULL);

				$this->forceDeleteOnModelForceDelete = (bool)($configs['onforceDelete'] ?? NULL);

				$relations = $configs['relations'] ?? NULL;
				
				if ( is_array($relations) && !empty($relations) ) {

					$this->deleteRelations = $relations;

					return true;
				}
			}
		}

		return false;
	}


	/**
     * validate the relation delete method
     *
     * @param  string $method
     * @return void
     */
	protected function validateDeleteMethod($method) {

		return
			in_array($method, $this->validDeleteMethod)
				? $method
				: $this->deleteMethod;
	}


	/**
     * Delete model relations on Model delete
     *
     * @return void
     */
	public static function bootDeleteRelations() {

		$self = new self;

		if ( ! $self->initDelatebaleRelations() ) { return; }

		static::{$self->deleteEvent}(function ($model) use ($self) {

			// if model is force deleting and at model configuration onforceDelete set to true
			if ( $model->isForceDeleting() && $self->forceDeleteOnModelForceDelete ) {

				$self->deleteMethod = 'forceDelete';
			}

			$self->deleteModelRelations($self->deleteRelations, $self->deleteMethod, $model);

		});

	}


	/**
     * Delete model relations
     *
     * @param  array  $relations
     * @param  string $deleteMethod
     * @param  Model  $model
     *
     * @return $this
     */
	public function deleteModelRelations(array $relations = [], string $deleteMethod = 'delete', Model $model = null) {

		$model = $model ?? $this;

		DB::beginTransaction();

		foreach ($relations as $deleteRelation) {

			if ( $model->{$deleteRelation} ) {

				$modelRels = $model->{$deleteRelation};

				if ( $modelRels instanceof Collection ) {
					
					foreach ($modelRels as $modelRel) {

						if ( $modelRel instanceof Model ) {

							$modelRel->{$deleteMethod}();	
						}
					}

					continue;
				} 
				
				! ($modelRels instanceof Model) ?: $modelRels->{$deleteMethod}();
			}
		}

		DB::commit();

		return $model;
	}


    public function deleteModelRelationRecord(Model $relationModel, string $deleteMethod) {

        return $relationModel->pivot ? $relationModel->pivot->{$deleteMethod} : $relationModel->{$deleteMethod};
    }
}