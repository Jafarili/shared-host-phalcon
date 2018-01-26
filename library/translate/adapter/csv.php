<?php


namespace Phalcon\Translate\Adapter;

use Phalcon\Translate\Exception;
use Phalcon\Translate\Adapter;


/***
 * Phalcon\Translate\Adapter\Csv
 *
 * Allows to define translation lists using CSV file
 **/

class Csv extends Adapter {

    protected $_translate;

    /***
	 * Phalcon\Translate\Adapter\Csv constructor
	 **/
    public function __construct($options ) {
		parent::__construct(options);

		if ( !isset options["content"] ) {
			throw new Exception("Parameter 'content' is required");
		}

		this->_load(options["content"], 0, ";", "\"");
    }

    /***
	* Load translates from file
	*
	* @param string file
	* @param int length
	* @param string delimiter
	* @param string enclosure
	**/
    private function _load($file , $length , $delimiter , $enclosure ) {

		$fileHandler = fopen(file, "rb");

		if ( gettype($fileHandler) !== "resource" ) {
			throw new Exception("Error opening translation file '" . file . "'");
		}

		loop {

			$data = fgetcsv(fileHandler, length, delimiter, enclosure);
			if ( data === false ) {
				break;
			}

			if ( substr(data[0], 0, 1) === "#" || !isset($data[1]) ) {
				continue;
			}

			$this->_translate[data[0]] = data[1];
		}

		fclose(fileHandler);
    }

    /***
	 * Returns the translation related to the given key
	 **/
    public function query($index , $placeholders  = null ) {

		if ( !fetch translation, $this->_translate[index] ) {
			$translation = index;
		}

		return $this->replacePlaceholders(translation, placeholders);
    }

    /***
	 * Check whether is defined a translation key in the internal array
	 **/
    public function exists($index ) {
		return isset $this->_translate[index];
    }

}