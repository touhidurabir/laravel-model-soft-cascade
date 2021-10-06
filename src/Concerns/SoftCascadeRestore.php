<?php

namespace Touhidurabir\ModelSoftCascade\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait SoftCascadeRestore {

    /**
     * List of allowed events on which the RestoreRelations bootable trait can run
     *
     * @var array
     */
	private $allowedRestoreEvents = ['restored', 'restoring'];


	/**
     * Default event on which the RestoreRelations bootable trait will run
     *
     * @var string
     */
	protected $restoreEvent = 'restoring';


	/**
     * List of the relations to be restored
     *
     * @var array
     */
	protected $restoreRelations = [];


	/**
     * Load the user defined configs and list relations to be deleted
     *
     * @return void
     */
	private function initRestorableRelations() {

		if ( method_exists($this, 'restorableRelations') ) {

			$configs = $this->restorableRelations();

			if ( is_array($configs) ) {

				$this->restoreEvent = isset($configs['event']) && 
									  in_array($configs['event'], $this->allowedRestoreEvents)
									  	? $configs['event']
									 	: $this->restoreEvent;

				$relations = $configs['relations'] ?? NULL;
				
				if ( is_array($relations) && !empty($relations) ) {

					$this->restoreRelations = $relations;

					return true;
				}
			}
		}

		return false;
	}



	/**
     * Restore model relations on Model restore
     *
     * @return void
     */
	public static function bootRestoreRelations() {

		$self = new self;

		if ( ! $self->initRestorableRelations() ) { return; }

		static::{$self->restoreEvent}(function ($model) use ($self) {

			$self->restoreModelRelations($self->restoreRelations, $model);
			
		});
	}


	/**
     * Restore model relations
     *
     * @param  array  $relations
     * @param  Model  $model
     *
     * @return $this
     */
	public function restoreModelRelations(array $relations = [], Model $model = null) {

		$model = $model ?? $this;

		DB::beginTransaction();

		foreach ($relations as $restoreRelation) {

			if ( $model->{$restoreRelation}()->withTrashed()->count() > 0 ) {

				$modelRels = $model->{$restoreRelation}()->withTrashed()->get();

				foreach ($modelRels as $modelRel) {
					
					! ($modelRel instanceof Model) ?: $modelRel->restore();
				}
				
				// $model->{$restoreRelation}()->withTrashed()->restore();
			}

			// !$model->{$restoreRelation} ?: $model->{$restoreRelation}()->withTrashed()->restore();
		}

		DB::commit();

		return $model;
	}
    
}