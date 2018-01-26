<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;


/***
 * Phalcon\Validation\Validator\Between
 *
 * Validates that a value is between an inclusive range of two values.
 * For a value x, the test is passed if minimum<=x<=maximum.
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Between;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "price",
 *     new Between(
 *         [
 *             "minimum" => 0,
 *             "maximum" => 100,
 *             "message" => "The price must be between 0 and 100",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "price",
 *         "amount",
 *     ],
 *     new Between(
 *         [
 *             "minimum" => [
 *                 "price"  => 0,
 *                 "amount" => 0,
 *             ],
 *             "maximum" => [
 *                 "price"  => 100,
 *                 "amount" => 50,
 *             ],
 *             "message" => [
 *                 "price"  => "The price must be between 0 and 100",
 *                 "amount" => "The amount must be between 0 and 50",
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class Between extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field),
				minimum = $this->getOption("minimum"),
				maximum = $this->getOption("maximum");

		if ( gettype($minimum) == "array" ) {
			$minimum = minimum[field];
		}

		if ( gettype($maximum) == "array" ) {
			$maximum = maximum[field];
		}

		if ( value < minimum || value > maximum ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "Between"),
				code = $this->prepareCode(field);

			$replacePairs = [":field": label, ":min": minimum, ":max": maximum];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"Between",
					code
				)
			);

			return false;
		}

		return true;
    }

}