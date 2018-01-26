<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Exception;
use Phalcon\Validation\Validator;


/***
 * Phalcon\Validation\Validator\Confirmation
 *
 * Checks that two values have the same value
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Confirmation;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "password",
 *     new Confirmation(
 *         [
 *             "message" => "Password doesn't match confirmation",
 *             "with"    => "confirmPassword",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "password",
 *         "email",
 *     ],
 *     new Confirmation(
 *         [
 *             "message" => [
 *                 "password" => "Password doesn't match confirmation",
 *                 "email"    => "Email doesn't match confirmation",
 *             ],
 *             "with" => [
 *                 "password" => "confirmPassword",
 *                 "email"    => "confirmEmail",
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class Confirmation extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$fieldWith = $this->getOption("with");

		if ( gettype($fieldWith) == "array" ) {
			$fieldWith = fieldWith[field];
		}

		$value = validation->getValue(field),
			valueWith = validation->getValue(fieldWith);

		if ( !this->compare(value, valueWith) ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "Confirmation"),
				code = $this->prepareCode(field);

			$labelWith = $this->getOption("labelWith");
			if ( gettype($labelWith) == "array" ) {
				$labelWith = labelWith[fieldWith];
			}
			if ( empty labelWith ) {
				$labelWith = validation->getLabel(fieldWith);
			}

			$replacePairs = [":field": label, ":with":  labelWith];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"Confirmation",
					code
				)
			);

			return false;
		}

		return true;
    }

    /***
	 * Compare strings
	 **/
    protected final function compare($a , $b ) {
		if ( $this->getOption("ignoreCase", false) ) {

			/**
			 * mbstring is required here
			 */
			if ( !function_exists("mb_strtolower") ) {
				throw new Exception("Extension 'mbstring' is required");
			}

			$a = mb_strtolower(a, "utf-8");
			$b = mb_strtolower(b, "utf-8");
		}

		return a == b;
    }

}