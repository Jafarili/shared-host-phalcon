<?php


namespace Phalcon\Assets\Filters;

use Phalcon\Assets\FilterInterface;


/***
 * Phalcon\Assets\Filters\None
 *
 * Returns the content without make any modification to the original source
 **/

class None {

    /***
	 * Returns the content without be touched
	 **/
    public function filter($content ) {
		return content;
    }

}