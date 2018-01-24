<?php


namespace Phalcon\Mvc\Model\Behavior;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\Exception;


/***
 * Phalcon\Mvc\Model\Behavior\SoftDelete
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