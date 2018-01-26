<?php


namespace Phalcon\Translate\Adapter;

use Phalcon\Translate\Exception;
use Phalcon\Translate\Adapter;


/***
 * Phalcon\Translate\Adapter\NativeArray
 *
 * Allows to define translation lists using PHP arrays
 **/

class NativeArray extends Adapter {

    protected $_translate;

    /***
	 * Phalcon\Translate\Adapter\NativeArray constructor
	 **/
    public function __construct($options ) {

		parent::__construct(options);

		if ( !fetch data, options["content"] ) {
			throw new Exception("Translation content was not provided");
		}

		if ( gettype($data) !== "array" ) {
			throw new Exception("Translation data must be an array");
		}

		$this->_translate = data;
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