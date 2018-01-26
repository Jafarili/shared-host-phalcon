<?php


namespace Phalcon\Annotations\Adapter;

use Phalcon\Annotations\Adapter;
use Phalcon\Annotations\Reflection;
use Phalcon\Annotations\Exception;


/***
 * Phalcon\Annotations\Adapter\Files
 *
 * Stores the parsed annotations in files. This adapter is suitable for production
 *
 *<code>
 * use Phalcon\Annotations\Adapter\Files;
 *
 * $annotations = new Files(
 *     [
 *         "annotationsDir" => "app/cache/annotations/",
 *     ]
 * );
 *</code>
 **/

class Files extends Adapter {

    protected $_annotationsDir;

    /***
	 * Phalcon\Annotations\Adapter\Files constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {
		if ( gettype($options) == "array" ) {
			if ( fetch annotationsDir, options["annotationsDir"] ) {
				$this->_annotationsDir = annotationsDir;
			}
		}
    }

    /***
	 * Reads parsed annotations from files
	 *
	 * @param string key
	 * @return \Phalcon\Annotations\Reflection
	 **/
    public function read($key ) {

		/**
		 * Paths must be normalized befor (e be used as keys
		 */
		$path = $this->_annotationsDir . prepare_virtual_path(key, "_") . ".php";

		if ( file_exists(path) ) {
			return require path;
		}

		return false;
    }

    /***
	 * Writes parsed annotations to files
	 **/
    public function write($key , $data ) {

		/**
		 * Paths must be normalized befor (e be used as keys
		 */
		$path = $this->_annotationsDir . prepare_virtual_path(key, "_") . ".php";

		if ( (file_put_contents(path, "<?php return " . var_export(data, true) . "; ") === false) ) {
	  		throw new Exception("Annotations directory cannot be written");
		}
    }

}