<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;


/***
 * Phalcon\Validation\Validator\Identical
 *
 * Checks if a value is identical to other
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Identical;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "terms",
 *     new Identical(
 *         [
 *             "accepted" => "yes",
 *             "message" => "Terms and conditions must be accepted",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "terms",
 *         "anotherTerms",
 *     ],
 *     new Identical(
 *         [
 *             "accepted" => [
 *                 "terms"        => "yes",
 *                 "anotherTerms" => "yes",
 *             ],
 *             "message" => [
 *                 "terms"        => "Terms and conditions must be accepted",
 *                 "anotherTerms" => "Another terms  must be accepted",
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class Identical extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field);

		if ( $this->hasOption("accepted") ) {
			$accepted = $this->getOption("accepted");
			if ( gettype($accepted) == "array" ) {
				$accepted = accepted[field];
			}
			$valid = value == accepted;
		} else {
			if ( $this->hasOption("value") ) {
				$valueOption = $this->getOption("value");
				if ( gettype($valueOption) == "array" ) {
					$valueOption = valueOption[field];
				}
				$valid = value == valueOption;
			}
		}

		if ( !valid ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "Identical"),
				code = $this->prepareCode(field);

			$replacePairs = [":field": label];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"Identical",
					code
				)
			);

			return false;
		}

		return true;
    }

}