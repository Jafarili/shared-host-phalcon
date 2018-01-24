<?php


namespace Phalcon\Mvc\Router;

use Phalcon\Mvc\Router\RouteInterface;


/***
 * Phalcon\Mvc\Router\GroupInterface
 *
 *
 *<code>
 * $router = new \Phalcon\Mvc\Router();
 *
 * // Create a group with a common module and controller
 * $blog = new Group(
 *     [
 *         "module"     => "blog",
 *         "controller" => "index",
 *     ]
 * );
 *
 * // All the routes start with /blog
 * $blog->setPrefix("/blog");
 *
 * // Add a route to the group
 * $blog->add(
 *     "/save",
 *     [
 *         "action" => "save",
 *     ]
 * );
 *
 * // Add another route to the group
 * $blog->add(
 *     "/edit/{id}",
 *     [
 *         "action" => "edit",
 *     ]
 * );
 *
 * // This route maps to a controller different than the default
 * $blog->add(
 *     "/blog",
 *     [
 *         "controller" => "about",
 *         "action"     => "index",
 *     ]
 * );
 *
 * // Add the group to the router
 * $router->mount($blog);
 *</code>
 **/

interface GroupInterface {

    /***
	 * Set a hostname restriction for all the routes in the group
	 **/
    public function setHostname($hostname ); 

    /***
	 * Returns the hostname restriction
	 **/
    public function getHostname(); 

    /***
	 * Set a common uri prefix for all the routes in this group
	 **/
    public function setPrefix($prefix ); 

    /***
	 * Returns the common prefix for all the routes
	 **/
    public function getPrefix(); 

    /***
	 * Sets a callback that is called if the route is matched.
	 * The developer can implement any arbitrary conditions here
	 * If the callback returns false the route is treated as not matched
	 **/
    public function beforeMatch($beforeMatch ); 

    /***
	 * Returns the 'before match' callback if any
	 **/
    public function getBeforeMatch(); 

    /***
	 * Set common paths for all the routes in the group
	 *
	 * @param array paths
	 * @return \Phalcon\Mvc\Router\Group
	 **/
    public function setPaths($paths ); 

    /***
	 * Returns the common paths defined for this group
	 **/
    public function getPaths(); 

    /***
	 * Returns the routes added to the group
	 **/
    public function getRoutes(); 

    /***
	 * Adds a route to the router on any HTTP method
	 *
	 *<code>
	 * router->add("/about", "About::index");
	 *</code>
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
	 * Removes all the pre-defined routes
	 **/
    public function clear(); 

}