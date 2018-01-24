<?php


namespace Phalcon\Logger;



/***
 * Phalcon\Logger\Item
 *
 * Represents each item in a logging transaction
 *
 **/

class Item {

    /***
	 * Log type
	 *
	 * @var integer
	 **/
    protected $_type;

    /***
	 * Log message
	 *
	 * @var string
	 **/
    protected $_message;

    /***
	 * Log timestamp
	 *
	 * @var integer
	 **/
    protected $_time;

    protected $_context;

    /***
	 * Phalcon\Logger\Item constructor
	 *
	 * @param string $message
	 * @param integer $type
	 * @param integer $time
	 * @param array $context
	 **/
    public function __construct($message , $type , $time  = 0 , $context  = null ) {

    }

}