<?php

return [

	/*
    |-----------------------------------------------------------------------------
    | Should run the cascading behaviour run as DB transactional process
    |-----------------------------------------------------------------------------
    | Determine if the cascading (deleting or restoring) of related model recoreds
    | should run as Database transaction process so that if any error arise, no
	| changes should be committed to database.
    |
    */
	'on_database_transaction' => true,
	

	/*
    |-----------------------------------------------------------------------------
    | The event on which cascading will take place
    |-----------------------------------------------------------------------------
    | The delete/restore model cascading to invloke on model's delete/restore
    | event .
    |
    */
	'events' => [
		/*
	    |--------------------------------------------------------------------------
	    | The cascade invoking model delete event
	    |--------------------------------------------------------------------------
	    | Apply the delete action on relation models sequentially on given model
		| event . 
		|
	    | Applicable model delete associated events : deleting, deleted
		|
	    */
		'delete' => 'deleting',

        
		/*
	    |--------------------------------------------------------------------------
	    | The cascade invoking model restore event
	    |--------------------------------------------------------------------------
	    | Apply the restore action on relation models sequentially on given model
		| event . 
		|
	    | Applicable model delete associated events : restoring, restored
		|
	    */
		'restore' => 'restoring',
	],


	/*
    |-----------------------------------------------------------------------------
    | Should force delete relation records on model force delete
    |-----------------------------------------------------------------------------
    | This define should the relations be force delete on model force delete . By
	| the very defination of cascade, associated models should ne force delete if
	| if top models got force deleted . But in some cases that is not the desired 
	| case . And for such cases, it should come handy .
    |
    */
	'force_delete_on_model_force_delete' => true,

	/*
    |-----------------------------------------------------------------------------
    | Should cascade delete keep track of cascaded models deleted at time of parent delete
    |-----------------------------------------------------------------------------
    | If a model alredy has child records soft deleted and that model is deleted via this package,
    | then all child models will be sodt deleted with no true method to determine, during restore, 
    | if the child was part of parent soft delete or not.  Enabling this config and adding column to any/all
    | child models will solve this issue.
    |
    */
	'enable_mapping_child_delete_to_parent_delete' => true,	
	
	/*
    |-----------------------------------------------------------------------------
    | Column in model to map child delete to parent delete
    |-----------------------------------------------------------------------------
    | If enable_mapping_child_delete_to_parent_delete is true, this column needs to be
    | in the model as defined below in a migration
    |
    |
    |   $table->softDeletes();
    |   $table->boolean('deletedByCascade')->default(false);
    */
	'model_delete_mapping_col' => 'deletedByCascade',	
];