<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;


/***
 * Phalcon\Validation\Validator\Digit
 *
 * Check for numeric character(s)
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Digit as DigitValidator;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "height",
 *     new DigitValidator(
 *         [
 *             "message" => ":field must be numeric",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "height",
 *         "width",
 *     ],
 *     new DigitValidator(
 *         [
 *             "message" => [
 *                 "height" => "height must be numeric",
 *                 "width"  => "width must be numeric",
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class Digit extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field);

		if ( is_int(value) || ctype_digit(value) ) {
			return true;
		}

		$label = $this->prepareLabel(validation, field),
			message = $this->prepareMessage(validation, field, "Digit"),
			code = $this->prepareCode(field);

		$replacePairs = [":field": label];

		validation->appendMessage(
			new Message(
				strtr(message, replacePairs),
				field,
				"Digit",
				code
			)
		);

		return false;
    }

}