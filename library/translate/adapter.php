<?php


namespace Phalcon\Translate;

use Phalcon\Translate\Exception;
use Phalcon\Translate\InterpolatorInterface;
use Phalcon\Translate\Interpolator\AssociativeArray;


/***
 * Phalcon\Translate\Adapter
 *
 * Base class for Phalcon\Translate adapters
 **/

abstract class Adapter {

    /***
	* @var Phalcon\Translate\InterpolatorInterface
	**/
    protected $_interpolator;

    public function __construct($options ) {

		if ( !fetch interpolator, options["interpolator"] ) {
			$interpolator = new AssociativeArray();
		}
		this->setInterpolator(interpolator);
    }

    public function setInterpolator($interpolator ) {
		$this->_interpolator = interpolator;
		return this;
    }

    /***
	 * Returns the translation string of the given key
	 *
	 * @param string  translateKey
	 * @param array   placeholders
	 * @return string
	 **/
    public function t($translateKey , $placeholders  = null ) {
		return $this->{"query"}(translateKey, placeholders);
    }

    /***
	 * Returns the translation string of the given key (alias of method 't')
	 *
	 * @param string  translateKey
	 * @param array   placeholders
	 * @return string
	 **/
    public function _($translateKey , $placeholders  = null ) {
		return $this->{"query"}(translateKey, placeholders);
    }

    /***
	 * Sets a translation value
	 *
	 * @param string offset
	 * @param string value
	 **/
    public function offsetSet($offset , $value ) {
		throw new Exception("Translate is an immutable ArrayAccess object");
    }

    /***
	 * Check whether a translation key exists
	 **/
    public function offsetExists($translateKey ) {
		return $this->{"exists"}(translateKey);
    }

    /***
	 * Unsets a translation from the dictionary
	 *
	 * @param string offset
	 **/
    public function offsetUnset($offset ) {
		throw new Exception("Translate is an immutable ArrayAccess object");
    }

    /***
	 * Returns the translation related to the given key
	 *
	 * @param  string translateKey
	 * @return string
	 **/
    public function offsetGet($translateKey ) {
		return $this->{"query"}(translateKey, null);
    }

    /***
	 * Replaces placeholders by the values passed
	 **/
    protected function replacePlaceholders($translation , $placeholders  = null ) {
		return $this->_interpolator->{"replacePlaceholders"}(translation, placeholders);
    }

}