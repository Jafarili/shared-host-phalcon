<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\ReaderInterface;


/***
 * Phalcon\Annotations\Reader
 *
 * Parses docblocks returning an array with the found annotations
 **/

class Reader {

    /***
	 * Reads annotations from the class dockblocks, its methods and/or properties
	 **/
    public function parse($className ) {

    }

    /***
	 * Parses a raw doc block returning the annotations found
	 **/
    public static function parseDocBlock($docBlock , $file  = null , $line  = null ) {

    }

}