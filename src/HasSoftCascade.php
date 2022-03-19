<?php

namespace Touhidurabir\ModelSoftCascade;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Touhidurabir\ModelSoftCascade\Concerns\CascadeValidation;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeDelete;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeRestore;

trait HasSoftCascade {

    use SoftCascadeDelete, SoftCascadeRestore;

    use CascadeValidation;

    /**
     * The action[delete,restore] specific cascadable relations
     *
     * @var array
     */
    protected $relationships = [];


    /**
     * Should run the cascading action as transactionla DB action
     *
     * @var bool
     */
    protected $runAsDatabaseTransaction = true;

    /**
     * Should map the child delete as deleted by cascade
     *
     * @var bool
     */
    protected $mapCascadedParentDeleteToChildDelete = true;

    /**
     * The column in the model table used for mapping if 
     * $this->mapCascadedParentDeleteToChildDelete = true
     *
     * @var string
     */
    protected $mapModelCol = 'deletedByCascade';

    /**
     * The abstract cascade configuration method
     *
     * @return array
     */
    abstract public function cascadable() : array;


    /** 
     * Run cascading action[delete, restore]
     *
     * @return void
     */
    public static function bootHasSoftCascade() {

        $self = new self;

		$self->initializeHasSoftCascade();

        $self->validateActionEvent();

		static::{$self->deleteEvent}(function ($model) use ($self) {

            $self->initDelatebaleRelations();

            if ( ! $self->shouldRunCascade('delete') ) {

                return;
            }

            $self->validateCascading(['delete']);

			// if model is force deleting and at model configuration onforceDelete set to true
			if ( $model->isForceDeleting() && $self->forceDeleteOnModelForceDelete ) {

				$self->deleteMethod = 'forceDelete';
			}

			$self->deleteModelRelations($self->relationships, $self->deleteMethod, $model);
		});

        static::{$self->restoreEvent}(function ($model) use ($self) {

            $self->initRestorableRelations();

            if ( ! $self->shouldRunCascade('restore') ) {

                return;
            }

            $self->validateCascading(['restore']);

			$self->restoreModelRelations($self->relationships, $model);
			
		});
    }


    /** 
     * Init/Set cascading action[delete, restore] specific configurations
     *
     * @return void
     */
    public function initializeHasSoftCascade() {

        $configs = $this->cascadable();

        $this->deleteEvent  = $configs['delete']['event']   ?? config('soft-cascade.events.delete')  ?? 'deleting';
        $this->restoreEvent = $configs['restore']['event']  ?? config('soft-cascade.events.restore') ?? 'restoring';

        $this->runAsDatabaseTransaction = config('soft-cascade.on_database_transaction') ?? true;
        $this->mapCascadedParentDeleteToChildDelete = config('enable_mapping_child_delete_to_parent_delete') ?? true;
        $this->mapModelCol = config('model_delete_mapping_col') ?? 'deletedByCascade';
    }


    /** 
     * Should run relation cascading for specific action[delete, restore]
     *
     * @return void
     */
    protected function shouldRunCascade(string $action) {

        $actionBasedShouldRunProperty = 'runCascade' . ucfirst(strtolower($action));

        if ( ! property_exists($this, $actionBasedShouldRunProperty) ) {

            return true;
        }

        return $this->{$actionBasedShouldRunProperty};
    }

}