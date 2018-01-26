<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Exception;


/***
 * Phalcon\Validation\Validator\ExclusionIn
 *
 * Check if a value is not included into a list of values
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\ExclusionIn;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "status",
 *     new ExclusionIn(
 *         [
 *             "message" => "The status must not be A or B",
 *             "domain"  => [
 *                 "A",
 *                 "B",
 *             ],
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "status",
 *         "type",
 *     ],
 *     new ExclusionIn(
 *         [
 *             "message" => [
 *                 "status" => "The status must not be A or B",
 *                 "type"   => "The type must not be 1 or "
 *             ],
 *             "domain" => [
 *                 "status" => [
 *                     "A",
 *                     "B",
 *                 ],
 *                 "type"   => [1, 2],
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class ExclusionIn extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field);

		/**
		 * A domain is an array with a list of valid values
		 */
		$domain = $this->getOption("domain");
		if ( fetch fieldDomain, domain[field] ) {
			if ( gettype($fieldDomain) == "array" ) {
				$domain = fieldDomain;
			}
		}
		if ( gettype($domain) != "array" ) {
			throw new Exception("Option 'domain' must be an array");
		}

		$strict = false;
		if ( $this->hasOption("strict") ) {

			$strict = $this->getOption("strict");

			if ( gettype($strict) == "array" ) {
				$strict = strict[field];
			}

			if ( gettype($strict) != "boolean" ) {
			    throw new Exception("Option 'strict' must be a boolean");
			}
		}

		/**
		 * Check if ( the value is contained by the array
		 */
		if ( in_array(value, domain, strict) ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "ExclusionIn"),
				code = $this->prepareCode(field);

			$replacePairs = [":field": label, ":domain":  join(", ", domain)];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"ExclusionIn",
					code
				)
			);

			return false;
		}

		return true;
    }

}