<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/***
 * Phalcon\Validation\Validator\Alnum
 *
 * Check for alphanumeric character(s)
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Alnum as AlnumValidator;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "username",
 *     new AlnumValidator(
 *         [
 *             "message" => ":field must contain only alphanumeric characters",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "username",
 *         "name",
 *     ],
 *     new AlnumValidator(
 *         [
 *             "message" => [
 *                 "username" => "username must contain only alphanumeric characters",
 *                 "name"     => "name must contain only alphanumeric characters",
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class Alnum extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field);

		if ( !ctype_alnum(value) ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "Alnum"),
				code = $this->prepareCode(field);

			$replacePairs = [":field": label];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"Alnum",
					code
				)
			);

			return false;
		}

		return true;
    }

}