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
];