<?php

namespace Touhidurabir\ModelSoftCascade\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

trait SoftCascadeRestore
{
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
    private function initRestorableRelations()
    {

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
    protected function restoreModelRelations(array $relations = [], ?Model $model = null)
    {

        $model = $model ?? $this;

        $cascade = function () use ($relations, $model) {

            foreach ($relations as $restoreRelation) {

                if ($model->{$restoreRelation}()->withTrashed()->count() > 0) {

                    $modelRels = $model->{$restoreRelation}()->withTrashed()->get();

                    foreach ($modelRels as $modelRel) {

                        ! ($modelRel instanceof Model) ?: $modelRel->restore();
                    }

                    // $model->{$restoreRelation}()->withTrashed()->restore();
                }

                // !$model->{$restoreRelation} ?: $model->{$restoreRelation}()->withTrashed()->restore();
            }
        };

        // DB::transaction() rolls back and rethrows if the cascade fails part way
        $this->runAsDatabaseTransaction ? DB::transaction($cascade) : $cascade();

        return $model;
    }

}
