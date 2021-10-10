<?php

namespace Touhidurabir\ModelSoftCascade;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Touhidurabir\ModelSoftCascade\Concerns\CascadeValidation;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeDelete;
use Touhidurabir\ModelSoftCascade\Concerns\SoftCascadeRestore;

trait HasSoftCascade {

    protected $relationships = [];

    protected $runAsDatabaseTransaction = true;

    use SoftCascadeDelete, SoftCascadeRestore;

    use CascadeValidation;

    abstract public function cascadable() : array;

    public static function bootHasSoftCascade() {

        $self = new self;

		$self->initializeHasSoftCascade();

        $self->validateActionEvent();

		static::{$self->deleteEvent}(function ($model) use ($self) {

            $self->initDelatebaleRelations();

            if ( ! $self->runCascadeDelete ) {

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

            if ( ! $self->runCascadeRestore ) {

                return;
            }

            $self->validateCascading(['restore']);

			$self->restoreModelRelations($self->relationships, $model);
			
		});
    }


    public function initializeHasSoftCascade() {

        $configs = $this->cascadable();

        $this->deleteEvent  = $configs['delete']['event']   ?? config('soft-cascade.events.delete')  ?? 'deleting';
        $this->restoreEvent = $configs['restore']['event']  ?? config('soft-cascade.events.restore') ?? 'restoring';

        $this->runAsDatabaseTransaction = config('soft-cascade.on_database_transaction') ?? true;
    }

}