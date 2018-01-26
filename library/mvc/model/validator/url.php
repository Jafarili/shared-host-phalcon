<?php


namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\EntityInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;


/***
 * Phalcon\Mvc\Model\Validator\Url
 *
 * Allows to validate if a field has a url format
 *
 * This validator is only for use with Phalcon\Mvc\Collection. If you are using
 * Phalcon\Mvc\Model, please use the validators provided by Phalcon\Validation.
 *
 *<code>
 * use Phalcon\Mvc\Model\Validator\Url as UrlValidator;
 *
 * class Posts extends \Phalcon\Mvc\Collection
 * {
 *     public function validation()
 *     {
 *         $this->validate(
 *             new UrlValidator(
 *                 [
 *                     "field" => "source_url",
 *                 ]
 *             )
 *         );
 *
 *         if ($this->validationHasFailed() === true) {
 *             return false;
 *         }
 *     }
 * }
 *</code>
 *
 * @deprecated 3.1.0
 * @see Phalcon\Validation\Validator\Url
 **/

class Url extends Validator {

    /***
	 * Executes the validator
	 **/
    public function validate($record ) {

		$field = $this->getOption("field");
		if ( gettype($field) != "string" ) {
			throw new Exception("Field name must be a string");
		}

		$value = record->readAttribute(field);
		if ( $this->isSetOption("allowEmpty") && empty value ) {
			return true;
		}

		/**
		 * Filters the for (mat using FILTER_VALIDATE_URL
		 */
		if ( !filter_var(value, FILTER_VALIDATE_URL) ) {

			/**
			 * Check if ( the developer has defined a custom message
			 */
			$message = $this->getOption("message");
			if ( empty message ) {
				$message = ":field does not have a valid url for (mat";
			}

			this->appendMessage(strtr(message, [":field": field]), field, "Url");
			return false;
		}

		return true;
    }

}