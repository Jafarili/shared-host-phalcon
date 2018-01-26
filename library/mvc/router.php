<?php


namespace Phalcon\Mvc;

use Phalcon\DiInterface;
use Phalcon\Mvc\Router\Route;
use Phalcon\Mvc\Router\Exception;
use Phalcon\Http\RequestInterface;
use Phalcon\Mvc\Router\GroupInterface;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Events\EventsAwareInterface;


/***
 * Phalcon\Mvc\Router
 *
 * Phalcon\Mvc\Router is the standard framework router. Routing is the
 * process of taking a URI endpoint (that part of the URI which comes after the base URL) and
 * decomposing it into parameters to determine which module, controller, and
 * action of that controller should receive the request
 *
 * <code>
 * use Phalcon\Mvc\Router;
 *
 * $router = new Router();
 *
 * $router->add(
 *     "/documentation/{chapter}/{name}\.{type:[a-z]+}",
 *     [
 *         "controller" => "documentation",
 *         "action"     => "show",
 *     ]
 * );
 *
 * $router->handle();
 *
 * echo $router->getControllerName();
 * </code>
 **/

class Router {

    const URI_SOURCE_GET_URL= 0;

    const URI_SOURCE_SERVER_REQUEST_URI= 1;

    const POSITION_FIRST= 0;

    const POSITION_LAST= 1;

    protected $_dependencyInjector;

    protected $_eventsManager;

    protected $_uriSource;

    protected $_namespace;

    protected $_module;

    protected $_controller;

    protected $_action;

    protected $_params;

    protected $_routes;

    protected $_matchedRoute;

    protected $_matches;

    protected $_wasMatched;

    protected $_defaultNamespace;

    protected $_defaultModule;

    protected $_defaultController;

    protected $_defaultAction;

    protected $_defaultParams;

    protected $_removeExtraSlashes;

    protected $_notFoundPaths;

    /***
	 * Phalcon\Mvc\Router constructor
	 **/
    public function __construct($defaultRoutes  = true ) {
		array routes = [];

		if ( defaultRoutes ) {

			// Two routes are added by default to match /:controller/:action and
			// /:controller/:action/:params

			$routes[] = new Route("#^/([\\w0-9\\_\\-]+)[/]{0,1}$#u", [
				"controller": 1
			]);

			$routes[] = new Route("#^/([\\w0-9\\_\\-]+)/([\\w0-9\\.\\_]+)(/.*)*$#u", [
				"controller": 1,
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
	 * Sets the events manager
	 **/
    public function setEventsManager($eventsManager ) {
		$this->_eventsManager = eventsManager;
    }

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager() {
		return $this->_eventsManager;
    }

    /***
	 * Get rewrite info. This info is read from $_GET["_url"]. This returns '/' if the rewrite information cannot be read
	 **/
    public function getRewriteUri() {

		/**
		 * By default we use $_GET["url"] to obtain the rewrite infor (mation
		 */
		if ( !this->_uriSource ) {
			if ( fetch url, _GET["_url"] ) {
				if ( !empty url ) {
					return url;
				}
			}
		} else {
			/**
			 * Otherwise use the standard $_SERVER["REQUEST_URI"]
			 */
			if ( fetch url, _SERVER["REQUEST_URI"] ) {
				$urlParts = explode("?", url),
					realUri = urlParts[0];
				if ( !empty realUri ) {
					return realUri;
				}
			}
		}
		return "/";
    }

    /***
	 * Sets the URI source. One of the URI_SOURCE_* constants
	 *
	 * <code>
	 * $router->setUriSource(
	 *     Router::URI_SOURCE_SERVER_REQUEST_URI
	 * );
	 * </code>
	 **/
    public function setUriSource($uriSource ) {
		$this->_uriSource = uriSource;
		return this;
    }

    /***
	 * Set whether router must remove the extra slashes in the handled routes
	 **/
    public function removeExtraSlashes($remove ) {
		$this->_removeExtraSlashes = remove;
		return this;
    }

    /***
	 * Sets the name of the default namespace
	 **/
    public function setDefaultNamespace($namespaceName ) {
		$this->_defaultNamespace = namespaceName;
		return this;
    }

    /***
	 * Sets the name of the default module
	 **/
    public function setDefaultModule($moduleName ) {
		$this->_defaultModule = moduleName;
		return this;
    }

    /***
	 * Sets the default controller name
	 **/
    public function setDefaultController($controllerName ) {
		$this->_defaultController = controllerName;
		return this;
    }

    /***
	 * Sets the default action name
	 **/
    public function setDefaultAction($actionName ) {
		$this->_defaultAction = actionName;
		return this;
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

		// Set a default namespace
		if ( fetch namespaceName, defaults["namespace"] ) {
			$this->_defaultNamespace = namespaceName;
		}

		// Set a default module
		if ( fetch module, defaults["module"] ) {
			$this->_defaultModule = module;
		}

		// Set a default controller
		if ( fetch controller, defaults["controller"] ) {
			$this->_defaultController = controller;
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
	 * Returns an array of default parameters
	 **/
    public function getDefaults() {
			"namespace":  $this->_defaultNamespace,
			"module":     $this->_defaultModule,
			"controller": $this->_defaultController,
			"action":     $this->_defaultAction,
			"params":     $this->_defaultParams
		];
    }

    /***
	 * Handles routing information received from the rewrite engine
	 *
	 *<code>
	 * // Read the info from the rewrite engine
	 * $router->handle();
	 *
	 * // Manually passing an URL
	 * $router->handle("/posts/edit/1");
	 *</code>
	 **/
    public function handle($uri  = null ) {
			params, matches, notFoundPaths,
			vnamespace, module,  controller, action, paramsStr, strParams,
			route, methods, dependencyInjector,
			hostname, regexHostName, matched, pattern, handledUri, befor (eMatch,
			paths, converters, part, position, matchPosition, converter, eventsManager;

		if ( !uri ) {
			/**
			 * If 'uri' isn't passed as parameter it reads _GET["_url"]
			 */
			$realUri = $this->getRewriteUri();
		} else {
			$realUri = uri;
		}

		/**
		 * Remove extra slashes in the route
		 */
		if ( $this->_removeExtraSlashes && realUri != "/" ) {
			$handledUri = rtrim(realUri, "/");
		} else {
			$handledUri = realUri;
		}

		$request = null,
			currentHostName = null,
			routeFound = false,
			parts = [],
			params = [],
			matches = null,
			this->_wasMatched = false,
			this->_matchedRoute = null;

		$eventsManager = $this->_eventsManager;

		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("router:befor (eCheckRoutes", this);
		}

		/**
		 * Routes are traversed in reversed order
		 */
		foreach ( $reverse as $route $this->_routes ) {
			$params = [],
				matches = null;

			/**
			 * Look for ( HTTP method constraints
			 */
			$methods = route->getHttpMethods();
			if ( methods !== null ) {

				/**
				 * Retrieve the request service from the container
				 */
				if ( request === null ) {

					$dependencyInjector = <DiInterface> $this->_dependencyInjector;
					if ( gettype($dependencyInjector) != "object" ) {
						throw new Exception("A dependency injection container is required to access the 'request' service");
					}

					$request = <RequestInterface> dependencyInjector->getShared("request");
				}

				/**
				 * Check if ( the current method is allowed by the route
				 */
				if ( request->isMethod(methods, true) === false ) {
					continue;
				}
			}

			/**
			 * Look for ( hostname constraints
			 */
			$hostname = route->getHostName();
			if ( hostname !== null ) {

				/**
				 * Retrieve the request service from the container
				 */
				if ( request === null ) {

					$dependencyInjector = <DiInterface> $this->_dependencyInjector;
					if ( gettype($dependencyInjector) != "object" ) {
						throw new Exception("A dependency injection container is required to access the 'request' service");
					}

					$request = <RequestInterface> dependencyInjector->getShared("request");
				}

				/**
				 * Check if ( the current hostname is the same as the route
				 */
				if ( gettype($currentHostName) == "null" ) {
					$currentHostName = request->getHttpHost();
				}

				/**
				 * No HTTP_HOST, maybe in CLI mode?
				 */
				if ( !currentHostName ) {
					continue;
				}

				/**
				 * Check if ( the hostname restriction is the same as the current in the route
				 */
				if ( memstr(hostname, "(") ) {
					if ( !memstr(hostname, "#") ) {
						$regexHostName = "#^" . hostname;
						if ( !memstr(hostname, ":") ) {
							$regexHostName .= "(:[[:digit:]]+)?";
						}
						$regexHostName .= "$#i";
					} else {
						$regexHostName = hostname;
					}
					$matched = preg_match(regexHostName, currentHostName);
				} else {
					$matched = currentHostName == hostname;
				}

				if ( !matched ) {
					continue;
				}
			}

			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("router:befor (eCheckRoute", this, route);
			}

			/**
			 * If the route has parentheses use preg_match
			 */
			$pattern = route->getCompiledPattern();

			if ( memstr(pattern, "^") ) {
				$routeFound = preg_match(pattern, handledUri, matches);
			} else {
				$routeFound = pattern == handledUri;
			}

			/**
			 * Check for ( befor (eMatch conditions
			 */
			if ( routeFound ) {

				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("router:matchedRoute", this, route);
				}

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
					$routeFound = call_user_func_array(befor (eMatch, [handledUri, route, this]);
				}

			} else {
				if ( gettype($eventsManager) == "object" ) {
					$routeFound = eventsManager->fire("router:notMatchedRoute", this, route);
				}
			}

			if ( routeFound ) {

				/**
				 * Start from the default paths
				 */
				$paths = route->getPaths(),
					parts = paths;

				/**
				 * Check if ( the matches has variables
				 */
				if ( gettype($matches) == "array" ) {

					/**
					 * Get the route converters if ( any
					 */
					$converters = route->getConverters();

					foreach ( part, $paths as $position ) {

						if ( gettype($part) != "string" ) {
							throw new Exception("Wrong key in paths: " . part);
						}

						if ( gettype($position) != "string" && gettype($position) != "integer" ) {
							continue;
						}

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
							} else {

								/**
								 * Remove the path if ( the parameter was not matched
								 */
								if ( gettype($position) == "integer" ) {
									unset parts[part];
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
		}

		/**
		 * The route wasn't found, try to use the not-found paths
		 */
		if ( !routeFound ) {
			$notFoundPaths = $this->_notFoundPaths;
			if ( notFoundPaths !== null ) {
				$parts = Route::getRoutePaths(notFoundPaths),
					routeFound = true;
			}
		}

		/**
		 * Use default values befor (e we overwrite them if ( the route is matched
		 */
		$this->_namespace = $this->_defaultNamespace,
			this->_module = $this->_defaultModule,
			this->_controller = $this->_defaultController,
			this->_action = $this->_defaultAction,
			this->_params = $this->_defaultParams;

		if ( routeFound ) {

			/**
			 * Check for ( a namespace
			 */
			if ( fetch vnamespace, parts["namespace"] ) {
				if ( !is_numeric(vnamespace) ) {
					$this->_namespace = vnamespace;
				}
				unset parts["namespace"];
			}

			/**
			 * Check for ( a module
			 */
			if ( fetch module, parts["module"] ) {
				if ( !is_numeric(module) ) {
					$this->_module = module;
				}
				unset parts["module"];
			}

			/**
			 * Check for ( a controller
			 */
			if ( fetch controller, parts["controller"] ) {
				if ( !is_numeric(controller) ) {
					$this->_controller = controller;
				}
				unset parts["controller"];
			}

			/**
			 * Check for ( an action
			 */
			if ( fetch action, parts["action"] ) {
				if ( !is_numeric(action) ) {
					$this->_action = action;
				}
				unset parts["action"];
			}

			/**
			 * Check for ( parameters
			 */
			if ( fetch paramsStr, parts["params"] ) {
				if ( gettype($paramsStr) == "string" ) {
					$strParams = trim(paramsStr, "/");
					if ( strParams !== "" ) {
						$params = explode("/", strParams);
					}
				}

				unset parts["params"];
			}

			if ( count(params) ) {
				$this->_params = array_merge(params, parts);
			} else {
				$this->_params = parts;
			}
		}

		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("router:afterCheckRoutes", this);
		}
    }

    /***
	 * Adds a route to the router without any HTTP constraint
	 *
	 *<code>
	 * use Phalcon\Mvc\Router;
	 *
	 * $router->add("/about", "About::index");
	 * $router->add("/about", "About::index", ["GET", "POST"]);
	 * $router->add("/about", "About::index", ["GET", "POST"], Router::POSITION_FIRST);
	 *</code>
	 **/
    public function add($pattern , $paths  = null , $httpMethods  = null , $position ) {

		/**
		 * Every route is internally stored as a Phalcon\Mvc\Router\Route
		 */
		$route = new Route(pattern, paths, httpMethods);

		switch position {

			case self::POSITION_LAST:
				$this->_routes[] = route;
				break;

			case self::POSITION_FIRST:
				$this->_routes = array_merge([route], $this->_routes);
				break;

			default:
				throw new Exception("Invalid route position");
		}

		return route;
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is GET
	 **/
    public function addGet($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "GET", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is POST
	 **/
    public function addPost($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "POST", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is PUT
	 **/
    public function addPut($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "PUT", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is PATCH
	 **/
    public function addPatch($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "PATCH", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is DELETE
	 **/
    public function addDelete($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "DELETE", position);
    }

    /***
	 * Add a route to the router that only match if the HTTP method is OPTIONS
	 **/
    public function addOptions($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "OPTIONS", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is HEAD
	 **/
    public function addHead($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "HEAD", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is PURGE (Squid and Varnish support)
	 **/
    public function addPurge($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "PURGE", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is TRACE
	 **/
    public function addTrace($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "TRACE", position);
    }

    /***
	 * Adds a route to the router that only match if the HTTP method is CONNECT
	 **/
    public function addConnect($pattern , $paths  = null , $position ) {
		return $this->add(pattern, paths, "CONNECT", position);
    }

    /***
	 * Mounts a group of routes in the router
	 **/
    public function mount($group ) {

		$eventsManager = $this->_eventsManager;

		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("router:befor (eMount", this, group);
		}

		$groupRoutes = group->getRoutes();
		if ( !count(groupRoutes) ) {
			throw new Exception("The group of routes does not contain any routes");
		}

		/**
		 * Get the befor (e-match condition
		 */
		$befor (eMatch = group->getBefor (eMatch();

		if ( befor (eMatch !== null ) ) {
			foreach ( $groupRoutes as $route ) {
				route->befor (eMatch(befor (eMatch);
			}
		}

		// Get the hostname restriction
		$hostname = group->getHostName();

		if ( hostname !== null ) {
			foreach ( $groupRoutes as $route ) {
				route->setHostName(hostname);
			}
		}

		$routes = $this->_routes;

		if ( gettype($routes) == "array" ) {
			$this->_routes = array_merge(routes, groupRoutes);
		} else {
			$this->_routes = groupRoutes;
		}

		return this;
    }

    /***
	 * Set a group of paths to be returned when none of the defined routes are matched
	 **/
    public function notFound($paths ) {
		if ( gettype($paths) != "array" && gettype($paths) != "string" ) {
			throw new Exception("The not-found paths must be an array or string");
		}
		$this->_notFoundPaths = paths;
		return this;
    }

    /***
	 * Removes all the pre-defined routes
	 **/
    public function clear() {
		$this->_routes = [];
    }

    /***
	 * Returns the processed namespace name
	 **/
    public function getNamespaceName() {
		return $this->_namespace;
    }

    /***
	 * Returns the processed module name
	 **/
    public function getModuleName() {
		return $this->_module;
    }

    /***
	 * Returns the processed controller name
	 **/
    public function getControllerName() {
		return $this->_controller;
    }

    /***
	 * Returns the processed action name
	 **/
    public function getActionName() {
		return $this->_action;
    }

    /***
	 * Returns the processed parameters
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

    /***
	 * Returns whether controller name should not be mangled
	 **/
    public function isExactControllerName() {
		return true;
    }

}