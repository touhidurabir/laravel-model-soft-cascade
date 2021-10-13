<?php

namespace Touhidurabir\ModelSoftCascade\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait SoftCascadeDelete {

	/**
     * Define should run cascade delete on relations
     *
     * @var bool
     */
	protected $runCascadeDelete = true;


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
     * Load the user defined configs and list relations to be deleted
     *
     * @return void
     */
	private function initDelatebaleRelations() {

		$configs = $this->cascadable();

		$this->runCascadeDelete = $configs['delete']['enable'] ?? true;

		$this->relationships = Arr::wrap($configs['delete']['relations'] ?? $configs);

		$this->forceDeleteOnModelForceDelete = $configs['delete']['force'] ?? config('soft-cascade.force_delete_on_model_force_delete');
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

		if ( $this->runAsDatabaseTransaction ) {

			DB::beginTransaction();
		}

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

				if ( $modelRels instanceof Model ) {

					$modelRels->{$deleteMethod}();
				}
			}
		}

		if ( $this->runAsDatabaseTransaction ) {
			
			DB::commit();
		}

		return $model;
	}


    public function deleteModelRelationRecord(Model $relationModel, string $deleteMethod) {

        return $relationModel->pivot ? $relationModel->pivot->{$deleteMethod} : $relationModel->{$deleteMethod};
    }
}