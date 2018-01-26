<?php


namespace Phalcon\Cli;

use Phalcon\DiInterface;
use Phalcon\Cli\Router\Route;
use Phalcon\Cli\Router\Exception;


/***
 * Phalcon\Cli\Router
 *
 * <p>Phalcon\Cli\Router is the standard framework router. Routing is the
 * process of taking a command-line arguments and
 * decomposing it into parameters to determine which module, task, and
 * action of that task should receive the request</p>
 *
 *<code>
 * $router = new \Phalcon\Cli\Router();
 *
 * $router->handle(
 *     [
 *         "module" => "main",
 *         "task"   => "videos",
 *         "action" => "process",
 *     ]
 * );
 *
 * echo $router->getTaskName();
 *</code>
 **/

class Router {

    protected $_dependencyInjector;

    protected $_module;

    protected $_task;

    protected $_action;

    protected $_params;

    protected $_defaultModule;

    protected $_defaultTask;

    protected $_defaultAction;

    protected $_defaultParams;

    protected $_routes;

    protected $_matchedRoute;

    protected $_matches;

    protected $_wasMatched;

    /***
	 * Phalcon\Cli\Router constructor
	 **/
    public function __construct($defaultRoutes  = true ) {

		$routes = [];
		if ( defaultRoutes === true ) {

			// Two routes are added by default to match
			// /:task/:action and /:task/:action/:params

			$routes[] = new Route("#^(?::delimiter)?([a-zA-Z0-9\\_\\-]+)[:delimiter]{0,1}$#", [
				"task": 1
			]);

			$routes[] = new Route("#^(?::delimiter)?([a-zA-Z0-9\\_\\-]+):delimiter([a-zA-Z0-9\\.\\_]+)(:delimiter.*)*$#", [
				"task": 1,
				"action": 2,
				"params": 3
			]);
		}

		$this->_routes = routes;
    }

    /***
	 * Sets the dependency injector
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the internal dependency injector
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets the name of the default module
	 **/
    public function setDefaultModule($moduleName ) {
		$this->_defaultModule = moduleName;
    }

    /***
	 * Sets the default controller name
	 **/
    public function setDefaultTask($taskName ) {
		$this->_defaultTask = taskName;
    }

    /***
	 * Sets the default action name
	 **/
    public function setDefaultAction($actionName ) {
		$this->_defaultAction = actionName;
    }

    /***
	 * Sets an array of default paths. If a route is missing a path the router will use the defined here
	 * This method must not be used to set a 404 route
	 *
	 *<code>
	 * $router->setDefaults(
	 *     [
	 *         "module" => "common",
	 *         "action" => "index",
	 *     ]
	 * );
	 *</code>
	 **/
    public function setDefaults($defaults ) {

		// Set a default module
		if ( fetch module, defaults["module"] ) {
			$this->_defaultModule = module;
		}

		// Set a default task
		if ( fetch task, defaults["task"] ) {
			$this->_defaultTask = task;
		}

		// Set a default action
		if ( fetch action, defaults["action"] ) {
			$this->_defaultAction = action;
		}

		// Set default parameters
		if ( fetch params, defaults["params"] ) {
			$this->_defaultParams = params;
		}

		return this;
    }

    /***
	 * Handles routing information received from command-line arguments
	 *
	 * @param array arguments
	 **/
    public function handle($arguments  = null ) {
			params, route, parts, pattern, routeFound, matches, paths,
			befor (eMatch, converters, converter, part, position, matchPosition,
			strParams;

		$routeFound = false,
			parts = [],
			params = [],
			matches = null,
			this->_wasMatched = false,
			this->_matchedRoute = null;

		if ( gettype($arguments) != "array" ) {

			if ( gettype($arguments) != "string" && gettype($arguments) != "null" ) {
				throw new Exception("Arguments must be an array or string");
			}

			foreach ( $reverse as $route $this->_routes ) {

				/**
				 * If the route has parentheses use preg_match
				 */
				$pattern = route->getCompiledPattern();

				if ( memstr(pattern, "^") ) {
					$routeFound = preg_match(pattern, arguments, matches);
				} else {
					$routeFound = pattern == arguments;
				}

				/**
				 * Check for ( befor (eMatch conditions
				 */
				if ( routeFound ) {

					$befor (eMatch = route->getBefor (eMatch();
					if ( befor (eMatch !== null ) ) {

						/**
						 * Check first if ( the callback is callable
						 */
						if ( !is_callable(befor (eMatch) ) ) {
							throw new Exception("Beforeach (e-Match callback is not $matched as $callable route");
						}

						/**
						 * Check first if ( the callback is callable
						 */
						$routeFound = call_user_func_array(befor (eMatch, [arguments, route, this]);
					}
				}

				if ( routeFound ) {

					/**
					 * Start from the default paths
					 */
					$paths = route->getPaths(), parts = paths;

					/**
					 * Check if ( the matches has variables
					 */
					if ( gettype($matches) == "array" ) {

						/**
						 * Get the route converters if ( any
						 */
						$converters = route->getConverters();

						foreach ( part, $paths as $position ) {

							if ( fetch matchPosition, matches[position] ) {

								/**
								 * Check if ( the part has a converter
								 */
								if ( gettype($converters) == "array" ) {
									if ( fetch converter, converters[part] ) {
										$parts[part] = call_user_func_array(converter, [matchPosition]);
										continue;
									}
								}

								/**
								 * Update the parts if ( there is no converter
								 */
								$parts[part] = matchPosition;
							} else {

								/**
								 * Apply the converters anyway
								 */
								if ( gettype($converters) == "array" ) {
									if ( fetch converter, converters[part] ) {
										$parts[part] = call_user_func_array(converter, [position]);
									}
								}
							}
						}

						/**
						 * Update the matches generated by preg_match
						 */
						$this->_matches = matches;
					}

					$this->_matchedRoute = route;
					break;
				}
			}

			/**
			 * Update the wasMatched property indicating if ( the route was matched
			 */
			if ( routeFound ) {
				$this->_wasMatched = true;
			} else {
				$this->_wasMatched = false;

				/**
				 * The route wasn't found, try to use the not-found paths
				 */
				$this->_module = $this->_defaultModule,
					this->_task = $this->_defaultTask,
					this->_action = $this->_defaultAction,
					this->_params = $this->_defaultParams;
				return this;
			}
		} else {
			$parts = arguments;
		}

		$moduleName = null,
			taskName = null,
			actionName = null;

		/**
		 * Check for ( a module
		 */
		if ( fetch moduleName, parts["module"] ) {
			unset parts["module"];
		} else {
			$moduleName = $this->_defaultModule;
		}

		/**
		 * Check for ( a task
		 */
		if ( fetch taskName, parts["task"] ) {
			unset parts["task"];
		} else {
			$taskName = $this->_defaultTask;
		}

		/**
		 * Check for ( an action
		 */
		if ( fetch actionName, parts["action"] ) {
			unset parts["action"];
		} else {
			$actionName = $this->_defaultAction;
		}

		/**
		 * Check for ( an parameters
		 */
		if ( fetch params, parts["params"] ) {
			if ( gettype($params) != "array" ) {
				$strParams = substr((string)params, 1);
				if ( strParams ) {
					$params = explode(Route::getDelimiter(), strParams);
				} else {
					$params = [];
				}
			}
			unset parts["params"];
		}
		if ( count(params) ) {
			$params = array_merge(params, parts);
		} else {
			$params = parts;
		}

		$this->_module = moduleName,
			this->_task = taskName,
			this->_action = actionName,
			this->_params = params;
    }

    /***
	 * Adds a route to the router
	 *
	 *<code>
	 * $router->add("/about", "About::main");
	 *</code>
	 *
	 * @param string pattern
	 * @param string/array paths
	 * @return \Phalcon\Cli\Router\Route
	 **/
    public function add($pattern , $paths  = null ) {

		$route = new Route(pattern, paths),
			this->_routes[] = route;
		return route;
    }

    /***
	 * Returns processed module name
	 **/
    public function getModuleName() {
		return $this->_module;
    }

    /***
	 * Returns processed task name
	 **/
    public function getTaskName() {
		return $this->_task;
    }

    /***
	 * Returns processed action name
	 **/
    public function getActionName() {
		return $this->_action;
    }

    /***
	 * Returns processed extra params
	 *
	 * @return array
	 **/
    public function getParams() {
		return $this->_params;
    }

    /***
	 * Returns the route that matches the handled URI
	 **/
    public function getMatchedRoute() {
		return $this->_matchedRoute;
    }

    /***
	 * Returns the sub expressions in the regular expression matched
	 *
	 * @return array
	 **/
    public function getMatches() {
		return $this->_matches;
    }

    /***
	 * Checks if the router matches any of the defined routes
	 **/
    public function wasMatched() {
		return $this->_wasMatched;
    }

    /***
	 * Returns all the routes defined in the router
	 **/
    public function getRoutes() {
		return $this->_routes;
    }

    /***
	 * Returns a route object by its id
	 *
	 * @param int id
	 * @return \Phalcon\Cli\Router\Route
	 **/
    public function getRouteById($id ) {

		foreach ( $this->_routes as $route ) {
			if ( route->getRouteId() == id ) {
				return route;
			}
		}
		return false;
    }

    /***
	 * Returns a route object by its name
	 **/
    public function getRouteByName($name ) {

		foreach ( $this->_routes as $route ) {
			if ( route->getName() == name ) {
				return route;
			}
		}
		return false;
    }

}