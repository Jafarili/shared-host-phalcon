<?php


namespace Phalcon\Mvc\Model\Behavior;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\Exception;


/***
 * Phalcon\Mvc\Model\Behavior\Timestampable
 *
 * Allows to automatically update a model’s attribute saving the
 * datetime when a record is created or updated
 **/

class Timestampable extends Behavior {

    /***
	 * Listens for notifications from the models manager
	 **/
    public function notify($type , $model ) {

		/**
		 * Check if ( the developer decided to take action here
		 */
		if ( $this->mustTakeAction(type) !== true ) {
			return null;
		}

		$options = $this->getOptions(type);
		if ( gettype($options) == "array" ) {

			/**
			 * The field name is required in this behavior
			 */
			if ( !fetch field, options["field"] ) {
				throw new Exception("The option 'field' is required");
			}

			$timestamp = null;

			if ( fetch for (mat, options["for (mat"] ) ) {
				/**
				 * Format is a for (mat for ( date()
				 */
				$timestamp = date(for (mat);
			} else {
				if ( fetch generator, options["generator"] ) {

					/**
					 * A generator is a closure that produce the correct timestamp value
					 */
					if ( gettype($generator) == "object" ) {
						if ( generator instanceof \Closure ) {
							$timestamp = call_user_func(generator);
						}
					}
				}
			}

			/**
			 * Last resort call time()
			 */
			if ( timestamp === null ) {
				$timestamp = time();
			}

			/**
			 * Assign the value to the field, use writeattribute if ( the property is protected
			 */
			if ( gettype($field) == "array" ) {
				foreach ( $field as $singleField ) {
					model->writeAttribute(singleField, timestamp);
				}
			} else {
				model->writeAttribute(field, timestamp);
			}
		}
    }

}