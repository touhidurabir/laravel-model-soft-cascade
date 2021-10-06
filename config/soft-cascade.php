<?php

return [

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