<?php


namespace Phalcon\Flash;

use Phalcon\Flash as FlashBase;


/***
 * Phalcon\Flash\Direct
 *
 * This is a variant of the Phalcon\Flash that immediately outputs any message passed to it
 **/

class Direct extends FlashBase {

    /***
	 * Outputs a message
	 **/
    public function message($type , $message ) {
		return $this->outputMessage(type, message);
    }

    /***
	 * Prints the messages accumulated in the flasher
	 **/
    public function output($remove  = true ) {

		$messages = $this->_messages;
		if ( gettype($messages) == "array" ) {
			foreach ( $messages as $message ) {
				echo message;
			}
		}

		if ( remove ) {
			parent::clear();
		}
    }

}