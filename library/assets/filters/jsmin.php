<?php


namespace Phalcon\Assets\Filters;

use Phalcon\Assets\FilterInterface;


/***
 * Phalcon\Assets\Filters\Jsmin
 *
 * Deletes the characters which are insignificant to JavaScript. Comments will be removed. Tabs will be
 * replaced with spaces. Carriage returns will be replaced with linefeeds.
 * Most spaces and linefeeds will be removed.
 **/

class Jsmin {

    /***
	 * Filters the content using JSMIN
	 **/
    public function filter($content ) {

    }

}