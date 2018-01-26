<?php


namespace Phalcon\Db;



/***
 * Phalcon\Db\RawValue
 *
 * This class allows to insert/update raw data without quoting or formatting.
 *
 * The next example shows how to use the MySQL now() function as a field value.
 *
 *<code>
 * $subscriber = new Subscribers();
 *
 * $subscriber->email     = "andres@phalconphp.com";
 * $subscriber->createdAt = new \Phalcon\Db\RawValue("now()");
 *
 * $subscriber->save();
 *</code>
 **/

class RawValue {

    /***
	 * Raw value without quoting or formatting
	 *
	 * @var string
	 **/
    protected $_value;

    /***
	 * Phalcon\Db\RawValue constructor
	 **/
    public function __construct($value ) {
		if ( gettype($value) == "string" && value == "" ) {
			$this->_value = "''";
			return;
		}

		if ( value === null ) {
			$this->_value = "NULL";
			return;
		}

		$this->_value = (string) value;
    }

}