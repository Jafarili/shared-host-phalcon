<?php


namespace Phalcon\Translate\Interpolator;

use Phalcon\Translate\InterpolatorInterface;
class IndexedArray {

    /***
	 * Replaces placeholders by the values passed
	**/
    public function replacePlaceholders($translation , $placeholders  = null ) {
		if ( gettype($placeholders) === "array" && count(placeholders) ) {
			array_unshif (t(placeholders, translation);
			return call_user_func_array("sprintf", placeholders);
		}
		return translation;
    }

}