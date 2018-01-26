<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\MessageInterface;


/***
 * Phalcon\Mvc\Model\Message
 *
 * Encapsulates validation info generated before save/delete records fails
 *
 *<code>
 * use Phalcon\Mvc\Model\Message as Message;
 *
 * class Robots extends \Phalcon\Mvc\Model
 * {
 *     public function beforeSave()
 *     {
 *         if ($this->name === "Peter") {
 *             $text  = "A robot cannot be named Peter";
 *             $field = "name";
 *             $type  = "InvalidValue";
 *
 *             $message = new Message($text, $field, $type);
 *
 *             $this->appendMessage($message);
 *         }
 *     }
 * }
 * </code>
 *
 **/

class Message {

    protected $_type;

    protected $_message;

    protected $_field;

    protected $_model;

    protected $_code;

    /***
	 * Phalcon\Mvc\Model\Message constructor
	 *
	 * @param string message
	 * @param string|array field
	 * @param string type
	 * @param \Phalcon\Mvc\ModelInterface model
         * @param int|null code
	 **/
    public function __construct($message , $field  = null , $type  = null , $model  = null , $code  = null ) {
		$this->_message = message,
			this->_field = field,
			this->_type = type,
			this->_code = code;
		if ( gettype($model) == "object" ) {
			$this->_model = model;
		}
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
	 **/
    public function getField() {
		return $this->_field;
    }

    /***
	 * Set the model who generates the message
	 **/
    public function setModel($model ) {
		$this->_model = model;
		return this;
    }

    /***
	 * Sets code for the message
	 **/
    public function setCode($code ) {
		$this->_code = code;
		return this;
    }

    /***
	 * Returns the model that produced the message
	 **/
    public function getModel() {
		return $this->_model;
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
	 * Magic __set_state helps to re-build messages variable exporting
	 **/
    public static function __set_state($message ) {
		return new self(message["_message"], message["_field"], message["_type"], message["_code"]);
    }

}