<?php


namespace Phalcon\Cli\Router;



/***
 * Phalcon\Cli\Router\RouteInterface
 *
 * Interface for Phalcon\Cli\Router\Route
 **/

interface RouteInterface {

    /***
	 * Replaces placeholders from pattern returning a valid PCRE regular expression
	 **/
    public function compilePattern($pattern ); 

    /***
	 * Reconfigure the route adding a new pattern and a set of paths
	 **/
    public function reConfigure($pattern , $paths  = null ); 

    /***
	 * Returns the route's name
	 **/
    public function getName(); 

    /***
	 * Sets the route's name
	 **/
    public function setName($name ); 

    /***
	 * Returns the route's id
	 **/
    public function getRouteId(); 

    /***
	 * Returns the route's pattern
	 **/
    public function getPattern(); 

    /***
	 * Returns the route's pattern
	 **/
    public function getCompiledPattern(); 

    /***
	 * Returns the paths
	 **/
    public function getPaths(); 

    /***
	 * Returns the paths using positions as keys and names as values
	 **/
    public function getReversedPaths(); 

    /***
	 * Resets the internal route id generator
	 **/
    public static function reset(); 

}