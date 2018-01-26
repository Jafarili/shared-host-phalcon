<?php


namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\EntityInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;


/***
 * Phalcon\Mvc\Model\Validator\IP
 *
 * Validates that a value is ipv4 address in valid range
 *
 * This validator is only for use with Phalcon\Mvc\Collection. If you are using
 * Phalcon\Mvc\Model, please use the validators provided by Phalcon\Validation.
 *
 *<code>
 * use Phalcon\Mvc\Model\Validator\Ip;
 *
 * class Data extends \Phalcon\Mvc\Collection
 * {
 *     public function validation()
 *     {
 *         // Any pubic IP
 *         $this->validate(
 *             new IP(
 *                 [
 *                     "field"         => "server_ip",
 *                     "version"       => IP::VERSION_4 | IP::VERSION_6, // v6 and v4. The same if not specified
 *                     "allowReserved" => false,   // False if not specified. Ignored for v6
 *                     "allowPrivate"  => false,   // False if not specified
 *                     "message"       => "IP address has to be correct",
 *                 ]
 *             )
 *         );
 *
 *         // Any public v4 address
 *         $this->validate(
 *             new IP(
 *                 [
 *                     "field"   => "ip_4",
 *                     "version" => IP::VERSION_4,
 *                     "message" => "IP address has to be correct",
 *                 ]
 *             )
 *         );
 *
 *         // Any v6 address
 *         $this->validate(
 *             new IP(
 *                 [
 *                     "field"        => "ip6",
 *                     "version"      => IP::VERSION_6,
 *                     "allowPrivate" => true,
 *                     "message"      => "IP address has to be correct",
 *                 ]
 *             )
 *         );
 *
 *         if ($this->validationHasFailed() === true) {
 *             return false;
 *         }
 *     }
 * }
 *</code>
 *
 * @deprecated 3.1.0
 **/

class Ip extends Validator {

    const VERSION_4= FILTER_FLAG_IPV4;

    const VERSION_6= FILTER_FLAG_IPV6;

    /***
	 * Executes the validator
	 **/
    public function validate($record ) {

		$field = $this->getOption("field");

		if ( gettype($field) != "string" ) {
			throw new Exception("Field name must be a string");
		}

		$value = record->readAttribute(field);
		$version = $this->getOption("version", FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
		$allowPrivate = $this->getOption("allowPrivate") ? 0 : FILTER_FLAG_NO_PRIV_RANGE;
		$allowReserved = $this->getOption("allowReserved") ? 0 : FILTER_FLAG_NO_RES_RANGE;

		if ( $this->getOption("allowEmpty", false) && empty value ) {
			return true;
		}

		$options = [
			"options": [
				"default": false
			],
			"flags": version | allowPrivate | allowReserved
		];

		/**
		 * Filters the for (mat using FILTER_VALIDATE_IP
		 */
		if ( !filter_var(value, FILTER_VALIDATE_IP, options) ) {

			$message = $this->getOption("message", "IP address is incorrect");
			this->appendMessage(strtr(message, [":field": field]), field, "IP");

			return false;
		}

		return true;
    }

}