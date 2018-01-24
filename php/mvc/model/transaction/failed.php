<?php


namespace Phalcon\Mvc\Model\Transaction;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Transaction\Exception;
use Phalcon\Mvc\Model\MessageInterface;


/***
 * Phalcon\Mvc\Model\Transaction\Failed
 *
 * This class will be thrown to exit a try/catch block for isolated transactions
 **/

class Failed extends Exception {

    protected $_record;

    /***
	 * Phalcon\Mvc\Model\Transaction\Failed constructor
	 **/
    public function __construct($message , $record  = null ) {

    }

    /***
	 * Returns validation record messages which stop the transaction
	 **/
    public function getRecordMessages() {

    }

    /***
	 * Returns validation record messages which stop the transaction
	 **/
    public function getRecord() {

    }

}