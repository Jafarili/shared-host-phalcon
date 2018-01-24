<?php


namespace Phalcon\Mvc\Collection\Behavior;

use Phalcon\Mvc\CollectionInterface;
use Phalcon\Mvc\Collection\Behavior;
use Phalcon\Mvc\Collection\Exception;


/***
 * Phalcon\Mvc\Collection\Behavior\Timestampable
 *
 * Allows to automatically update a model’s attribute saving the
 * datetime when a record is created or updated
 **/

class Timestampable extends Behavior {

    /***
	 * Listens for notifications from the models manager
	 **/
    public function notify($type , $model ) {

    }

}