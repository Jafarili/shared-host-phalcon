<?php


namespace Phalcon\Cli;

use Phalcon\Cli\Router\RouteInterface;


/***
 * Phalcon\Cli\RouterInterface
 *
 * Interface for Phalcon\Cli\Router
 **/

interface RouterInterface {

    /***
	 * Sets the name of the default module
	 **/
    public function setDefaultModule($moduleName ); 

    /***
	 * Sets the default task name
	 **/
    public function setDefaultTask($taskName ); 

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
	 *
	 * @param array arguments
	 **/
    public function handle($arguments  = null ); 

    /***
	 * Adds a route to the router on any HTTP method
	 **/
    public function add($pattern , $paths  = null ); 

    /***
	 * Returns processed module name
	 **/
    public function getModuleName(); 

    /***
	 * Returns processed task name
	 **/
    public function getTaskName(); 

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