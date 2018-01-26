<?php


namespace Phalcon\Translate\Interpolator;

use Phalcon\Translate\InterpolatorInterface;
class AssociativeArray {

    /***
	 * Replaces placeholders by the values passed
	**/
    public function replacePlaceholders($translation , $placeholders  = null ) {

		if ( gettype($placeholders) === "array" && count(placeholders) ) {
			foreach ( key, $placeholders as $value ) {
				$translation = str_replace("%" . key . "%", value, translation);
			}
		}

		return translation;
    }

}