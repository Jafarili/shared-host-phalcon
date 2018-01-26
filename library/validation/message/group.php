<?php


namespace Phalcon\Validation\Message;

use Phalcon\Validation\Message;
use Phalcon\Validation\Exception;
use Phalcon\Validation\MessageInterface;
use Phalcon\Validation\Message\Group;


/***
 * Phalcon\Validation\Message\Group
 *
 * Represents a group of validation messages
 **/

class Group {

    protected $_position;

    protected $_messages;

    /***
	 * Phalcon\Validation\Message\Group constructor
	 *
	 * @param array messages
	 **/
    public function __construct($messages  = null ) {
		if ( gettype($messages) == "array" ) {
			$this->_messages = messages;
		}
    }

    /***
	 * Gets an attribute a message using the array syntax
	 *
	 *<code>
	 * print_r(
	 *     $messages[0]
	 * );
	 *</code>
	 *
	 * @param int index
	 * @return \Phalcon\Validation\Message
	 **/
    public function offsetGet($index ) {
		if ( fetch message, $this->_messages[index] ) {
			return message;
		}
		return false;
    }

    /***
	 * Sets an attribute using the array-syntax
	 *
	 *<code>
	 * $messages[0] = new \Phalcon\Validation\Message("This is a message");
	 *</code>
	 *
	 * @param int index
	 * @param \Phalcon\Validation\Message message
	 **/
    public function offsetSet($index , $message ) {
		if ( gettype($message) != "object" ) {
			throw new Exception("The message must be an object");
		}
		$this->_messages[index] = message;
    }

    /***
	 * Checks if an index exists
	 *
	 *<code>
	 * var_dump(
	 *     isset($message["database"])
	 * );
	 *</code>
	 *
	 * @param int index
	 * @return boolean
	 **/
    public function offsetExists($index ) {
		return isset $this->_messages[index];
    }

    /***
	 * Removes a message from the list
	 *
	 *<code>
	 * unset($message["database"]);
	 *</code>
	 **/
    public function offsetUnset($index ) {
		if ( isset($this->_messages[index]) ) {
			array_splice(this->_messages, index, 1);
		}
    }

    /***
	 * Appends a message to the group
	 *
	 *<code>
	 * $messages->appendMessage(
	 *     new \Phalcon\Validation\Message("This is a message")
	 * );
	 *</code>
	 **/
    public function appendMessage($message ) {
		$this->_messages[] = message;
    }

    /***
	 * Appends an array of messages to the group
	 *
	 *<code>
	 * $messages->appendMessages($messagesArray);
	 *</code>
	 *
	 * @param \Phalcon\Validation\MessageInterface[] messages
	 **/
    public function appendMessages($messages ) {

		if ( gettype($messages) != "array" && gettype($messages) != "object" ) {
			throw new Exception("The messages must be array or object");
		}

		$currentMessages = $this->_messages;
		if ( gettype($messages) == "array" ) {

			/**
			 * An array of messages is simply merged into the current one
			 */
			if ( gettype($currentMessages) == "array" ) {
				$finalMessages = array_merge(currentMessages, messages);
			} else {
				$finalMessages = messages;
			}
			$this->_messages = finalMessages;

		} else {

			/**
			 * A group of messages is iterated and appended one-by-one to the current list
			 */
			//foreach ( $iterator(messages) as $message ) {
			//	this->appendMessage(message);
			//}

			messages->rewind();
			while messages->valid() {
				$message = messages->current();
				this->appendMessage(message);
    			messages->next();
			}
		}
    }

    /***
	 * Filters the message group by field name
	 *
	 * @param string fieldName
	 * @return array
	 **/
    public function filter($fieldName ) {

		$filtered = [],
			messages = $this->_messages;
		if ( gettype($messages) == "array" ) {

			/**
			 * A group of messages is iterated and appended one-by-one to the current list
			 */
			foreach ( $messages as $message ) {

				/**
				 * Get the field name
				 */
				if ( method_exists(message, "getField") ) {
					if ( fieldName == message->getField() ) {
						$filtered[] = message;
					}
				}
			}
		}

		return filtered;
    }

    /***
	 * Returns the number of messages in the list
	 **/
    public function count() {
		return count(this->_messages);
    }

    /***
	 * Rewinds the internal iterator
	 **/
    public function rewind() {
		$this->_position = 0;
    }

    /***
	 * Returns the current message in the iterator
	 **/
    public function current() {
		return $this->_messages[this->_position];
    }

    /***
	 * Returns the current position/key in the iterator
	 **/
    public function key() {
		return $this->_position;
    }

    /***
	 * Moves the internal iteration pointer to the next position
	 **/
    public function next() {
		$this->_position++;
    }

    /***
	 * Check if the current message in the iterator is valid
	 **/
    public function valid() {
		return isset $this->_messages[this->_position];
    }

    /***
	 * Magic __set_state helps to re-build messages variable when exporting
	 *
	 * @param array group
	 * @return \Phalcon\Validation\Message\Group
	 **/
    public static function __set_state($group ) {
		return new self(group["_messages"]);
    }

}