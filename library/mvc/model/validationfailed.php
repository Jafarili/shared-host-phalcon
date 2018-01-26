<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\Model;


/***
 * Phalcon\Mvc\Model\ValidationFailed
 *
 * This exception is generated when a model fails to save a record
 * Phalcon\Mvc\Model must be set up to have this behavior
 **/

class ValidationFailed extends \Phalcon\Mvc\Model\Exception {

    protected $_model;

    protected $_messages;

    /***
	 * Phalcon\Mvc\Model\ValidationFailed constructor
	 *
	 * @param Model model
	 * @param Message[] validationMessages
	 **/
    public function __construct($model , $validationMessages ) {

		if ( count(validationMessages) > 0 ) {
			/**
			 * Get the first message in the array
			 */
			$message = validationMessages[0];

			/**
			 * Get the message to use it in the exception
			 */
			$messageStr = message->getMessage();
		} else {
			$messageStr = "Validation failed";
		}

		$this->_model = model;
		$this->_messages = validationMessages;

		parent::__construct(messageStr);
    }

    /***
	 * Returns the model that generated the messages
	 **/
    public function getModel() {
		return $this->_model;
    }

    /***
	 * Returns the complete group of messages produced in the validation
	 **/
    public function getMessages() {
		return $this->_messages;
    }

}