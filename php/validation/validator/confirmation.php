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

    }

    /***
	 * Compare strings
	 **/
    protected final function compare($a , $b ) {

    }

}