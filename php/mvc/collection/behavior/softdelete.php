<?php


namespace Phalcon\Mvc\Collection\Behavior;

use Phalcon\Mvc\CollectionInterface;
use Phalcon\Mvc\Collection\Behavior;
use Phalcon\Mvc\Collection\Exception;


/***
 * Phalcon\Mvc\Collection\Behavior\SoftDelete
 *
 * Instead of permanently delete a record it marks the record as
 * deleted changing the value of a flag column
 **/

class SoftDelete extends Behavior {

    /***
	 * Listens for notifications from the models manager
	 **/
    public function notify($type , $model ) {

    }

}