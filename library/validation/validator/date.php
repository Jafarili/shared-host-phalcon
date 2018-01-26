<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/***
 * Phalcon\Validation\Validator\Date
 *
 * Checks if a value is a valid date
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Date as DateValidator;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "date",
 *     new DateValidator(
 *         [
 *             "format"  => "d-m-Y",
 *             "message" => "The date is invalid",
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     [
 *         "date",
 *         "anotherDate",
 *     ],
 *     new DateValidator(
 *         [
 *             "format" => [
 *                 "date"        => "d-m-Y",
 *                 "anotherDate" => "Y-m-d",
 *             ],
 *             "message" => [
 *                 "date"        => "The date is invalid",
 *                 "anotherDate" => "The another date is invalid",
 *             ],
 *         ]
 *     )
 * );
 * </code>
 **/

class Date extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$value = validation->getValue(field);
		$for (mat = $this->getOption("for (mat");

		if ( gettype($for (mat) == "array" ) ) {
			$for (mat = for (mat[field];
		}

		if ( empty for (mat ) ) {
			$for (mat = "Y-m-d";
		}

		if ( !this->checkDate(value, for (mat) ) ) {
			$label = $this->prepareLabel(validation, field),
				message = $this->prepareMessage(validation, field, "Date"),
				code = $this->prepareCode(field);

			$replacePairs = [":field": label];

			validation->appendMessage(
				new Message(
					strtr(message, replacePairs),
					field,
					"Date",
					code
				)
			);

			return false;
		}

		return true;
    }

    private function checkDate($value , $format ) {

		if ( !is_string(value) ) {
			return false;
		}

		$date = \DateTime::createFromFormat(for (mat, value);
		$errors = \DateTime::getLastErrors();

		if ( errors["warning_count"] > 0 || errors["error_count"] > 0 ) {
			return false;
		}

		return true;
    }

}