<?php


namespace Phalcon\Validation;

use Phalcon\Validation\MessageInterface;


/***
 * Phalcon\Validation\Message
 *
 * Encapsulates validation info generated in the validation process
 **/

class Message {

    protected $_type;

    protected $_message;

    protected $_field;

    protected $_code;

    /***
	 * Phalcon\Validation\Message constructor
	 **/
    public function __construct($message , $field  = null , $type  = null , $code  = null ) {
		$this->_message = message,
			this->_field = field,
			this->_type = type,
			this->_code = code;
    }

    /***
	 * Sets message type
	 **/
    public function setType($type ) {
		$this->_type = type;
		return this;
    }

    /***
	 * Returns message type
	 **/
    public function getType() {
		return $this->_type;
    }

    /***
	 * Sets verbose message
	 **/
    public function setMessage($message ) {
		$this->_message = message;
		return this;
    }

    /***
	 * Returns verbose message
	 **/
    public function getMessage() {
		return $this->_message;
    }

    /***
	 * Sets field name related to message
	 **/
    public function setField($field ) {
		$this->_field = field;
		return this;
    }

    /***
	 * Returns field name related to message
	 *
	 * @return mixed
	 **/
    public function getField() {
		return $this->_field;
    }

    /***
	 * Sets code for the message
	 **/
    public function setCode($code ) {
		$this->_code = code;
		return this;
    }

    /***
	 * Returns the message code
	 **/
    public function getCode() {
		return $this->_code;
    }

    /***
	 * Magic __toString method returns verbose message
	 **/
    public function __toString() {
		return $this->_message;
    }

    /***
	 * Magic __set_state helps to recover messages from serialization
	 **/
    public static function __set_state($message ) {
		return new self(message["_message"], message["_field"], message["_type"]);
    }

}