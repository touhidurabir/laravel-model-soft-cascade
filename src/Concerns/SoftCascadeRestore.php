<?php

namespace Touhidurabir\ModelSoftCascade\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait SoftCascadeRestore {

	/**
     * Define should run cascade restore on relations
     *
     * @var bool
     */
	protected $runCascadeRestore = true;


	/**
     * Default event on which the RestoreRelations bootable trait will run
     *
     * @var string
     */
	protected $restoreEvent = 'restoring';


	/**
     * Load the user defined configs and list relations to be deleted
     *
     * @return void
     */
	private function initRestorableRelations() {

		$configs = $this->cascadable();

		$this->runCascadeRestore = $configs['restore']['enable'] ?? true;

		$this->relationships = Arr::wrap($configs['restore']['relations'] ?? $configs);
	}


	/**
     * Restore model relations
     *
     * @param  array  $relations
     * @param  Model  $model
     *
     * @return $this
     */
	protected function restoreModelRelations(array $relations = [], Model $model = null) {

		$model = $model ?? $this;

		if ( $this->runAsDatabaseTransaction ) {

			DB::beginTransaction();
		}

		foreach ($relations as $restoreRelation) {

			if ( $model->{$restoreRelation}()->withTrashed()->count() > 0 ) {

				$modelRels = $model->{$restoreRelation}()->withTrashed()->get();

				foreach ($modelRels as $modelRel) {
					
					if ($this->mapCascadedParentDeleteToChildDelete) {
						!($modelRel instanceof Model) ?: $isUpdatedByCascade = $modelRel->getAttribute($this->mapModelCol);
						if ($isUpdatedByCascade) {
							!($modelRel instanceof Model) ?: $modelRel->restore();
							!($modelRel instanceof Model) ?: $modelRel->update([$this->mapModelCol => false]);
						}
					} else {
						!($modelRel instanceof Model) ?: $modelRel->restore();
					}
				}
				
				// $model->{$restoreRelation}()->withTrashed()->restore();
			}

			// !$model->{$restoreRelation} ?: $model->{$restoreRelation}()->withTrashed()->restore();
		}

		if ( $this->runAsDatabaseTransaction ) {

			DB::commit();
		}

		return $model;
	}
    
}