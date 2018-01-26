<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;


/***
 * Phalcon\Validation\Validator\Callback
 *
 * Calls user function for validation
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Callback as CallbackValidator;
 * use Phalcon\Validation\Validator\Numericality as NumericalityValidator;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     ["user", "admin"],
 *     new CallbackValidator(
 *         [
 *             "message" => "There must be only an user or admin set",
 *             "callback" => function($data) {
 *                 if (!empty($data->getUser()) && !empty($data->getAdmin())) {
 *                     return false;
 *                 }
 *
 *                 return true;
 *             }
 *         ]
 *     )
 * );
 *
 * $validator->add(
 *     "amount",
 *     new CallbackValidator(
 *         [
 *             "callback" => function($data) {
 *                 if (!empty($data->getProduct())) {
 *                     return new NumericalityValidator(
 *                         [
 *                             "message" => "Amount must be a number."
 *                         ]
 *                     );
 *                 }
 *             }
 *         ]
 *     )
 * );
 * </code>
 **/

class Callback extends Validator {

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		$callback = $this->getOption("callback");

		if ( is_callable(callback) ) {
			$data = validation->getEntity();
			if ( empty data ) {
				$data = validation->getData();
			}
			$returnedValue = call_user_func(callback, data);
			if ( gettype($returnedValue) == "boolean" ) {
				if ( !returnedValue ) {
					$label = $this->prepareLabel(validation, field),
						message = $this->prepareMessage(validation, field, "Callback"),
						code = $this->prepareCode(field);

					$replacePairs = [":field": label];

					validation->appendMessage(
						new Message(
							strtr(message, replacePairs),
							field,
							"Callback",
							code
						)
					);

					return false;
				}

				return true;
			}
			elseif ( gettype($returnedValue) == "object" && returnedValue instanceof Validator ) {
				return returnedValue->validate(validation, field);
			}
			throw new Exception("Callback must return boolean or Phalcon\\Validation\\Validator object");
		}

		return true;
    }

}