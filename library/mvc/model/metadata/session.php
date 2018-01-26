<?php


namespace Phalcon\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData;
use Phalcon\Mvc\Model\Exception;


/***
 * Phalcon\Mvc\Model\MetaData\Session
 *
 * Stores model meta-data in session. Data will erased when the session finishes.
 * Meta-data are permanent while the session is active.
 *
 * You can query the meta-data by printing $_SESSION['$PMM$']
 *
 *<code>
 * $metaData = new \Phalcon\Mvc\Model\Metadata\Session(
 *     [
 *        "prefix" => "my-app-id",
 *     ]
 * );
 *</code>
 **/

class Session extends MetaData {

    protected $_prefix;

    /***
	 * Phalcon\Mvc\Model\MetaData\Session constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {
		if ( gettype($options) == "array" ) {
			if ( fetch prefix, options["prefix"] ) {
				$this->_prefix = prefix;
			}
		}
    }

    /***
	 * Reads meta-data from $_SESSION
	 *
	 * @param string key
	 * @return array
	 **/
    public function read($key ) {

		if ( fetch metaData, _SESSION["$PMM$" . $this->_prefix][key] ) {
			return metaData;
		}

		return null;
    }

    /***
	 * Writes the meta-data to $_SESSION
	 *
	 * @param string key
	 * @param array data
	 **/
    public function write($key , $data ) {
		$_SESSION["$PMM$" . $this->_prefix][key] = data;
    }

}