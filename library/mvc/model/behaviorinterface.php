<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\ModelInterface;


/***
 * Phalcon\Mvc\Model\BehaviorInterface
 *
 * Interface for Phalcon\Mvc\Model\Behavior
 **/

interface BehaviorInterface {

    /***
	 * This method receives the notifications from the EventsManager
	 **/
    public function notify($type , $model ); 

    /***
	 * Calls a method when it's missing in the model
	 *
	 * @param \Phalcon\Mvc\ModelInterface model
	 * @param string method
	 * @param array arguments
	 **/
    public function missingMethod($model , $method , $arguments  = null ); 

}