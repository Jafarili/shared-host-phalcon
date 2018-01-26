<?php


namespace Phalcon\Mvc\View\Engine\Volt;

use Phalcon\Mvc\View\Exception as BaseException;


/***
 * Phalcon\Mvc\View\Exception
 *
 * Class for exceptions thrown by Phalcon\Mvc\View
 **/

class Exception extends BaseException {

    protected $statement;

    public function __construct($message  =  , $statement , $code  = 0 , $previous  = null ) {
		$this->statement = statement;

		parent::__construct(message, code, previous);
    }

    /***
	 * Gets currently parsed statement (if any).
	 **/
    public function getStatement() {

		$statement = $this->statement;
		if ( gettype($statement) !== "array" ) {
			$statement = [];
		}

		return statement;
    }

}