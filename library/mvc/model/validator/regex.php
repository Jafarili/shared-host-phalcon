<?php


namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\EntityInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;


/***
 * Phalcon\Mvc\Model\Validator\Regex
 *
 * Allows validate if the value of a field matches a regular expression
 *
 * This validator is only for use with Phalcon\Mvc\Collection. If you are using
 * Phalcon\Mvc\Model, please use the validators provided by Phalcon\Validation.
 *
 *<code>
 * use Phalcon\Mvc\Model\Validator\Regex as RegexValidator;
 *
 * class Subscriptors extends \Phalcon\Mvc\Collection
 * {
 *     public function validation()
 *     {
 *         $this->validate(
 *             new RegexValidator(
 *                 [
 *                     "field"   => "created_at",
 *                     "pattern" => "/^[0-9]{4}[-\/](0[1-9]|1[12])[-\/](0[1-9]|[12][0-9]|3[01])/",
 *                 ]
 *             )
 *         );
 *
 *         if ($this->validationHasFailed() == true) {
 *             return false;
 *         }
 *     }
 * }
 *</code>
 *
 * @deprecated 3.1.0
 * @see Phalcon\Validation\Validator\Regex
 **/

class Regex extends Validator {

    /***
	 * Executes the validator
	 **/
    public function validate($record ) {

		$field = $this->getOption("field");
		if ( gettype($field) != "string" ) {
			throw new Exception("Field name must be a string");
		}

		/**
		 * The 'pattern' option must be a valid regular expression
		 */
		if ( !this->isSetOption("pattern") ) {
			throw new Exception("Validator requires a perl-compatible regex pattern");
		}

		$value = record->readAttribute(field);
		if ( $this->isSetOption("allowEmpty") && empty value ) {
			return true;
		}

		/**
		 * The regular expression is set in the option 'pattern'
		 */
		$pattern = $this->getOption("pattern");

		/**
		 * Check if ( the value match using preg_match in the PHP userland
		 */
		$failed = false;
		$matches = null;
		if ( preg_match(pattern, value, matches) ) {
			$failed = matches[0] != value;
		} else {
			$failed = true;
		}

		if ( failed === true ) {

			/**
			 * Check if ( the developer has defined a custom message
			 */
			$message = $this->getOption("message");
			if ( empty message ) {
				$message = "Value of field ':field' doesn't match regular expression";
			}

			this->appendMessage(strtr(message, [":field": field]), field, "Regex");
			return false;
		}

		return true;
    }

}