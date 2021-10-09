<?php

return [

	/*
    |-----------------------------------------------------------------------------
    | Should run the cascading behavioud as DB transactional process
    |-----------------------------------------------------------------------------
    | Determine if the cascading (deleting or restoring) of related model recoreds
    | should run as Database transaction process so that if any error arise, no
	| changes should be committed to database.
    |
    */
	'on_database_trabsaction' => true,
	

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
		'delete' => 'deleted',

        
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
		'restore' => 'restored',
	],
];