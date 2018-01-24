<?php


namespace Phalcon\Mvc\Router;



/***
 * Phalcon\Mvc\Router\RouteInterface
 *
 * Interface for Phalcon\Mvc\Router\Route
 **/

interface RouteInterface {

    /***
	 * Sets a hostname restriction to the route
	 **/
    public function setHostname($hostname ); 

    /***
	 * Returns the hostname restriction if any
	 **/
    public function getHostname(); 

    /***
	 * Replaces placeholders from pattern returning a valid PCRE regular expression
	 **/
    public function compilePattern($pattern ); 

    /***
	 * Set one or more HTTP methods that constraint the matching of the route
	 **/
    public function via($httpMethods ); 

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
	 * Sets a set of HTTP methods that constraint the matching of the route
	 **/
    public function setHttpMethods($httpMethods ); 

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
	 * Returns the HTTP methods that constraint matching the route
	 **/
    public function getHttpMethods(); 

    /***
	 * Resets the internal route id generator
	 **/
    public static function reset(); 

}