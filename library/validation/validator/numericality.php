<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;


/***
 * Phalcon\Validation\Validator\Numericality
 *
 * Check for a valid numeric value
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Numericality;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "price",
 *     new Numericality(
 *         [
 *             "message" => ":field is not numeric",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "price",
 *         "amount",
 *     ],
 *     new Numericality(
 *         [
 *             "message" => [
 *                 "price"  => "price is not numeric",
 *                 "amount" => "amount is not numeric",
 *             ]
 *         ]
 *     )
 * );
 * </code>
 **/

class Numericality extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field);

		if ( !preg_match("/^-?\d+\.?\d*$/", value) ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "Numericality"),
				code = $this->prepareCode(field);

			$replacePairs = [":field": label];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"Numericality",
					code
				)
			);

			return false;
		}

		return true;
    }

}