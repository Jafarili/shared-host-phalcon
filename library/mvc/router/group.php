<?php


namespace Phalcon\Mvc\Router;



/***
 * Phalcon\Mvc\Router\Group
 *
 * Helper class to create a group of routes with common attributes
 *
 *<code>
 * $router = new \Phalcon\Mvc\Router();
 *
 * //Create a group with a common module and controller
 * $blog = new Group(
 *     [
 *         "module"     => "blog",
 *         "controller" => "index",
 *     ]
 * );
 *
 * //All the routes start with /blog
 * $blog->setPrefix("/blog");
 *
 * //Add a route to the group
 * $blog->add(
 *     "/save",
 *     [
 *         "action" => "save",
 *     ]
 * );
 *
 * //Add another route to the group
 * $blog->add(
 *     "/edit/{id}",
 *     [
 *         "action" => "edit",
 *     ]
 * );
 *
 * //This route maps to a controller different than the default
 * $blog->add(
 *     "/blog",
 *     [
 *         "controller" => "about",
 *         "action"     => "index",
 *     ]
 * );
 *
 * //Add the group to the router
 * $router->mount($blog);
 *</code>
 **/

class Group {

    protected $_prefix;

    protected $_hostname;

    protected $_paths;

    protected $_routes;

    protected $_beforeMatch;

    /***
	 * Phalcon\Mvc\Router\Group constructor
	 **/
    public function __construct($paths  = null ) {
		if ( gettype($paths) == "array" || gettype($paths) == "string" ) {
			$this->_paths = paths;
		}

		if ( method_exists(this, "initialize") ) {
			this->{"initialize"}(paths);
		}
    }

    /***
	 * Set a hostname restriction for all the routes in the group
	 **/
    public function setHostname($hostname ) {
		$this->_hostname = hostname;
		return this;
    }

    /***
	 * Returns the hostname restriction
	 **/
    public function getHostname() {
		return $this->_hostname;
    }

    /***
	 * Set a common uri prefix for all the routes in this group
	 **/
    public function setPrefix($prefix ) {
		$this->_prefix = prefix;
		return this;
    }

    /***
	 * Returns the common prefix for all the routes
	 **/
    public function getPrefix() {
		return $this->_prefix;
    }

    /***
	 * Sets a callback that is called if the route is matched.
	 * The developer can implement any arbitrary conditions here
	 * If the callback returns false the route is treated as not matched
	 **/
    public function beforeMatch($beforeMatch ) {
		$this->_befor (eMatch = befor (eMatch;
		return this;
    }

    /***
	 * Returns the 'before match' callback if any
	 **/
    public function getBeforeMatch() {
		return $this->_befor (eMatch;
    }

    /***
	 * Set common paths for all the routes in the group
	 **/
    public function setPaths($paths ) {
		$this->_paths = paths;
		return this;
    }

    /***
	 * Returns the common paths defined for this group
	 **/
    public function getPaths() {
		return $this->_paths;
    }

    /***
	 * Returns the routes added to the group
	 **/
    public function getRoutes() {
		return $this->_routes;
    }

    /***
	 * Adds a route to the router on any HTTP method
	 *
	 *<code>
	 * $router->add("/about", "About::index");
	 *</code>
	 **/
    public function add($pattern , $paths  = null , $httpMethods  = null ) {
		return $this->_addRoute(pattern, paths, httpMethods);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is GET
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addGet($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "GET");
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is POST
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addPost($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "POST");
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is PUT
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addPut($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "PUT");
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is PATCH
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addPatch($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "PATCH");
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is DELETE
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addDelete($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "DELETE");
    }

    /***
	 * Add a route to the router that only match if the HTTP method is OPTIONS
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addOptions($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "OPTIONS");
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is HEAD
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Mvc\Router\Route
	 **/
    public function addHead($pattern , $paths  = null ) {
		return $this->_addRoute(pattern, paths, "HEAD");
    }

    /***
	 * Removes all the pre-defined routes
	 **/
    public function clear() {
		$this->_routes = [];
    }

    /***
	 * Adds a route applying the common attributes
	 **/
    protected function _addRoute($pattern , $paths  = null , $httpMethods  = null ) {

		/**
		 * Check if ( the paths need to be merged with current paths
		 */
		$defaultPaths = $this->_paths;

		if ( gettype($defaultPaths) == "array" ) {

			if ( gettype($paths) == "string" ) {
				$processedPaths = Route::getRoutePaths(paths);
			} else {
				$processedPaths = paths;
			}

			if ( gettype($processedPaths) == "array" ) {
				/**
				 * Merge the paths with the default paths
				 */
				$mergedPaths = array_merge(defaultPaths, processedPaths);
			} else {
				$mergedPaths = defaultPaths;
			}
		} else {
			$mergedPaths = paths;
		}

		/**
		 * Every route is internally stored as a Phalcon\Mvc\Router\Route
		 */
		$route = new Route(this->_prefix . pattern, mergedPaths, httpMethods),
			this->_routes[] = route;

		route->setGroup(this);
		return route;
    }

}