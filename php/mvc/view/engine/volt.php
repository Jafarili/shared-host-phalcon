<?php


namespace Phalcon\Mvc\View\Engine;

use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine;
use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Phalcon\Mvc\View\Exception;


/***
 * Phalcon\Mvc\View\Engine\Volt
 *
 * Designer friendly and fast template engine for PHP written in Zephir/C
 **/

class Volt extends Engine {

    protected $_options;

    protected $_compiler;

    protected $_macros;

    /***
	 * Set Volt's options
	 **/
    public function setOptions($options ) {

    }

    /***
	 * Return Volt's options
	 **/
    public function getOptions() {

    }

    /***
	 * Returns the Volt's compiler
	 **/
    public function getCompiler() {

    }

    /***
	 * Renders a view using the template engine
	 **/
    public function render($templatePath , $params , $mustClean  = false ) {

    }

    /***
	 * Length filter. If an array/object is passed a count is performed otherwise a strlen/mb_strlen
	 **/
    public function length($item ) {

    }

    /***
	 * Checks if the needle is included in the haystack
	 **/
    public function isIncluded($needle , $haystack ) {

    }

    /***
	 * Performs a string conversion
	 **/
    public function convertEncoding($text , $from , $to ) {

    }

    /***
	 * Extracts a slice from a string/array/traversable object value
	 **/
    public function slice($value , $start  = 0 , $end  = null ) {

    }

    /***
	 * Sorts an array
	 **/
    public function sort($value ) {

    }

    /***
	 * Checks if a macro is defined and calls it
	 **/
    public function callMacro($name , $arguments ) {

    }

}