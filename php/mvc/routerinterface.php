<?php


namespace Phalcon\Mvc;

use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Mvc\Router\GroupInterface;


/***
 * Phalcon\Mvc\RouterInterface
 *
 * Interface for Phalcon\Mvc\Router
 **/

interface RouterInterface {

    /***
	 * Sets the name of the default module
	 **/
    public function setDefaultModule($moduleName ); 

    /***
	 * Sets the default controller name
	 **/
    public function setDefaultController($controllerName ); 

    /***
	 * Sets the default action name
	 **/
    public function setDefaultAction($actionName ); 

    /***
	 * Sets an array of default paths
	 **/
    public function setDefaults($defaults ); 

    /***
	 * Handles routing information received from the rewrite engine
	 **/
    public function handle($uri  = null ); 

    /***
	 * Adds a route to the router on any HTTP method
	 **/
    public function add($pattern , $paths  = null , $httpMethods  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is GET
	 **/
    public function addGet($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is POST
	 **/
    public function addPost($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is PUT
	 **/
    public function addPut($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is PATCH
	 **/
    public function addPatch($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is DELETE
	 **/
    public function addDelete($pattern , $paths  = null ); 

    /***
	 * Add a route to the router that only match if the HTTP method is OPTIONS
	 **/
    public function addOptions($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is HEAD
	 **/
    public function addHead($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is PURGE (Squid and Varnish support)
	 **/
    public function addPurge($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is TRACE
	 **/
    public function addTrace($pattern , $paths  = null ); 

    /***
	 * Adds a route to the router that only match if the HTTP method is CONNECT
	 **/
    public function addConnect($pattern , $paths  = null ); 

    /***
	 * Mounts a group of routes in the router
	 **/
    public function mount($group ); 

    /***
	 * Removes all the defined routes
	 **/
    public function clear(); 

    /***
	 * Returns processed module name
	 **/
    public function getModuleName(); 

    /***
	 * Returns processed namespace name
	 **/
    public function getNamespaceName(); 

    /***
	 * Returns processed controller name
	 **/
    public function getControllerName(); 

    /***
	 * Returns processed action name
	 **/
    public function getActionName(); 

    /***
	 * Returns processed extra params
	 **/
    public function getParams(); 

    /***
	 * Returns the route that matches the handled URI
	 **/
    public function getMatchedRoute(); 

    /***
	 * Return the sub expressions in the regular expression matched
	 **/
    public function getMatches(); 

    /***
	 * Check if the router matches any of the defined routes
	 **/
    public function wasMatched(); 

    /***
	 * Return all the routes defined in the router
	 **/
    public function getRoutes(); 

    /***
	 * Returns a route object by its id
	 **/
    public function getRouteById($id ); 

    /***
	 * Returns a route object by its name
	 **/
    public function getRouteByName($name ); 

}