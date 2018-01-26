<?php


namespace Phalcon\Mvc;

use Phalcon\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Controller;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro\Exception;
use Phalcon\Di\ServiceInterface;
use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro\LazyLoader;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\BinderInterface;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\Micro\CollectionInterface;


/***
 * Phalcon\Mvc\Micro
 *
 * With Phalcon you can create "Micro-Framework like" applications. By doing this, you only need to
 * write a minimal amount of code to create a PHP application. Micro applications are suitable
 * to small applications, APIs and prototypes in a practical way.
 *
 *<code>
 * $app = new \Phalcon\Mvc\Micro();
 *
 * $app->get(
 *     "/say/welcome/{name}",
 *     function ($name) {
 *         echo "<h1>Welcome $name!</h1>";
 *     }
 * );
 *
 * $app->handle();
 *</code>
 **/

class Micro extends Injectable {

    protected $_dependencyInjector;

    protected $_handlers;

    protected $_router;

    protected $_stopped;

    protected $_notFoundHandler;

    protected $_errorHandler;

    protected $_activeHandler;

    protected $_beforeHandlers;

    protected $_afterHandlers;

    protected $_finishHandlers;

    protected $_returnedValue;

    protected $_modelBinder;

    protected $_afterBindingHandlers;

    /***
	 * Phalcon\Mvc\Micro constructor
	 **/
    public function __construct($dependencyInjector  = null ) {
		if ( gettype($dependencyInjector) == "object" ) {
			if ( dependencyInjector instanceof DiInterface ) {
				this->setDi(dependencyInjector);
			}
		}
    }

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {
		if ( !dependencyInjector->has("application") ) {
			dependencyInjector->set("application", this);
		}

		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Maps a route to a handler without any HTTP method constraint
	 *
	 * @param string routePattern
	 * @param callable handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function map($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router
		 */
		$route = router->add(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is GET
	 *
	 * @param string routePattern
	 * @param callable handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function get($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to GET
		 */
		$route = router->addGet(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is POST
	 *
	 * @param string routePattern
	 * @param callable handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function post($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to POST
		 */
		$route = router->addPost(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is PUT
	 *
	 * @param string $routePattern
	 * @param callable $handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function put($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to PUT
		 */
		$route = router->addPut(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is PATCH
	 *
	 * @param string $routePattern
	 * @param callable $handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function patch($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to PATCH
		 */
		$route = router->addPatch(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is HEAD
	 *
	 * @param string routePattern
	 * @param callable handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function head($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to HEAD
		 */
		$route = router->addHead(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is DELETE
	 *
	 * @param string routePattern
	 * @param callable handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function delete($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to DELETE
		 */
		$route = router->addDelete(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Maps a route to a handler that only matches if the HTTP method is OPTIONS
	 *
	 * @param string routePattern
	 * @param callable handler
	 * @return \Phalcon\Mvc\Router\RouteInterface
	 **/
    public function options($routePattern , $handler ) {

		/**
		 * We create a router even if ( there is no one in the DI
		 */
		$router = $this->getRouter();

		/**
		 * Routes are added to the router restricting to OPTIONS
		 */
		$route = router->addOptions(routePattern);

		/**
		 * Using the id produced by the router we store the handler
		 */
		$this->_handlers[route->getRouteId()] = handler;

		/**
		 * The route is returned, the developer can add more things on it
		 */
		return route;
    }

    /***
	 * Mounts a collection of handlers
	 **/
    public function mount($collection ) {
			subHandler, realHandler, prefixedPattern, route, handler, name;

		/**
		 * Get the main handler
		 */
		$mainHandler = collection->getHandler();
		if ( empty mainHandler ) {
			throw new Exception("Collection requires a main handler");
		}

		$handlers = collection->getHandlers();
		if ( !count(handlers) ) {
			throw new Exception("There are no handlers to mount");
		}

		if ( gettype($handlers) == "array" ) {

			/**
			 * Check if ( handler is lazy
			 */
			if ( collection->isLazy() ) {
				$lazyHandler = new LazyLoader(mainHandler);
			} else {
				$lazyHandler = mainHandler;
			}

			/**
			 * Get the main prefix for ( the collection
			 */
			$prefix = collection->getPrefix();

			foreach ( $handlers as $handler ) {

				if ( gettype($handler) != "array" ) {
					throw new Exception("One of the registered handlers is invalid");
				}

				$methods    = handler[0];
				$pattern    = handler[1];
				$subHandler = handler[2];
				$name       = handler[3];

				/**
				 * Create a real handler
				 */
				$realHandler = [lazyHandler, subHandler];

				if ( !empty prefix ) {
					if ( pattern == "/" ) {
						$prefixedPattern = prefix;
					} else {
						$prefixedPattern = prefix . pattern;
					}
				} else {
					$prefixedPattern = pattern;
				}

				/**
				 * Map the route manually
				 */
				$route = $this->map(prefixedPattern, realHandler);

				if ( (gettype($methods) == "string" && methods != "") || gettype($methods) == "array" ) {
					route->via(methods);
				}

				if ( gettype($name) == "string" ) {
					route->setName(name);
				}
			}
		}

		return this;
    }

    /***
	 * Sets a handler that will be called when the router doesn't match any of the defined routes
	 *
	 * @param callable handler
	 * @return \Phalcon\Mvc\Micro
	 **/
    public function notFound($handler ) {
		$this->_notFoundHandler = handler;
		return this;
    }

    /***
	 * Sets a handler that will be called when an exception is thrown handling the route
	 *
	 * @param callable handler
	 * @return \Phalcon\Mvc\Micro
	 **/
    public function error($handler ) {
		$this->_errorHandler = handler;
		return this;
    }

    /***
	 * Returns the internal router used by the application
	 **/
    public function getRouter() {

		$router = $this->_router;
		if ( gettype($router) != "object" ) {

			$router = $this->getSharedService("router");

			/**
			 * Clear the set routes if ( any
			 */
			router->clear();

			/**
			 * Automatically remove extra slashes
			 */
			router->removeExtraSlashes(true);

			/**
			 * Update the internal router
			 */
			$this->_router = router;
		}

		return router;
    }

    /***
	 * Sets a service from the DI
	 *
	 * @param string  serviceName
	 * @param mixed   definition
	 * @param boolean shared
	 * @return \Phalcon\Di\ServiceInterface
	 **/
    public function setService($serviceName , $definition , $shared  = false ) {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = new FactoryDefault();
			$this->_dependencyInjector = dependencyInjector;
		}

		return dependencyInjector->set(serviceName, definition, shared);
    }

    /***
	 * Checks if a service is registered in the DI
	 **/
    public function hasService($serviceName ) {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = new FactoryDefault();
			$this->_dependencyInjector = dependencyInjector;
		}

		return dependencyInjector->has(serviceName);
    }

    /***
	 * Obtains a service from the DI
	 *
	 * @param string serviceName
	 * @return object
	 **/
    public function getService($serviceName ) {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = new FactoryDefault();
			$this->_dependencyInjector = dependencyInjector;
		}

		return dependencyInjector->get(serviceName);
    }

    /***
	 * Obtains a shared service from the DI
	 *
	 * @param string serviceName
	 * @return mixed
	 **/
    public function getSharedService($serviceName ) {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = new FactoryDefault();
			$this->_dependencyInjector = dependencyInjector;
		}

		return dependencyInjector->getShared(serviceName);
    }

    /***
	 * Handle the whole request
	 *
	 * @param string uri
	 * @return mixed
	 **/
    public function handle($uri  = null ) {
			handler, befor (eHandlers, params, returnedValue, e, errorHandler,
			afterHandlers, notFoundHandler, finishHandlers, finish, befor (e, after,
			response, modelBinder, bindCacheKey, routeName, realHandler = null, methodName, lazyReturned,
			afterBindingHandlers, afterBinding;

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection container is required to access required micro services");
		}

		try {

			$returnedValue = null;

			/**
			 * Calling befor (eHandle routing
			 */
			$eventsManager = $this->_eventsManager;
			if ( gettype($eventsManager) == "object" ) {
				if ( eventsManager->fire("micro:befor (eHandleRoute", this) === false ) ) {
					return false;
				}
			}

			/**
			 * Handling routing infor (mation
			 */
			$router = <RouterInterface> dependencyInjector->getShared("router");

			/**
			 * Handle the URI as normal
			 */
			router->handle(uri);

			/**
			 * Check if ( one route was matched
			 */
			$matchedRoute = router->getMatchedRoute();
			if ( gettype($matchedRoute) == "object" ) {

				if ( !fetch handler, $this->_handlers[matchedRoute->getRouteId()] ) {
					throw new Exception("Matched route doesn't have an associated handler");
				}

				/**
				 * Updating active handler
				 */
				$this->_activeHandler = handler;

				/**
				 * Calling befor (eExecuteRoute event
				 */
				if ( gettype($eventsManager) == "object" ) {
					if ( eventsManager->fire("micro:befor (eExecuteRoute", this) === false ) ) {
						return false;
					} else {
						$handler = $this->_activeHandler;
					}
				}

				$befor (eHandlers = $this->_befor (eHandlers;
				if ( gettype($befor (eHandlers) == "array" ) ) {

					$this->_stopped = false;

					/**
					 * Calls the befor (e handlers
					 */
					foreach ( $beforeach (eHandlers as $beforeach (e ) {

						if ( gettype($befor (e) == "object" ) ) {
							if ( befor (e instanceof MiddlewareInterface ) ) {

								/**
								 * Call the middleware
								 */
								$status = befor (e->call(this);

								/**
								 * Reload the status
								 * break the execution if ( the middleware was stopped
								 */
								if ( $this->_stopped ) {
									break;
								}

								continue;
							}
						}

						if ( !is_callable(befor (e) ) ) {
							throw new Exception("'befor (e' handler is not callable");
						}

						/**
						 * Call the befor (e handler
						 */
						$status = call_user_func(befor (e);

						/**
						 * break the execution if ( the middleware was stopped
						 */
						if (  $this->_stopped ) {
							break;
						}
					}
					/**
					 * Reload the 'stopped' status
					 */
					if ( $this->_stopped ) {
						return status;
					}
				}

				$params = router->getParams();

				$modelBinder = $this->_modelBinder;

				/**
				 * Bound the app to the handler
				 */
				if ( gettype($handler) == "object" && handler instanceof \Closure ) {
					$handler = \Closure::bind(handler, this);
					if ( modelBinder != null ) {
						$routeName = matchedRoute->getName();
						if ( routeName != null ) {
							$bindCacheKey = "_PHMB_" . routeName;
						} else {
							$bindCacheKey = "_PHMB_" . matchedRoute->getPattern();
						}
						$params = modelBinder->bindToHandler(handler, params, bindCacheKey);
					}
				}

				/**
				 * Calling the Handler in the PHP userland
				 */

				 if ( gettype($handler) == "array" ) {

					$realHandler = handler[0];

					if ( realHandler instanceof Controller && modelBinder != null ) {
						$methodName = handler[1];
						$bindCacheKey = "_PHMB_" . get_class(realHandler) . "_" . methodName;
						$params = modelBinder->bindToHandler(realHandler, params, bindCacheKey, methodName);
					}
				}

				/**
				 * Instead of double call_user_func_array when lazy loading we will just call method
				 */
				if ( realHandler != null && realHandler instanceof LazyLoader ) {
					$methodName = handler[1];
					/**
					 * There is seg fault if ( we try set directly value of method to returnedValue
					 */
					$lazyReturned = realHandler->callMethod(methodName, params, modelBinder);
					$returnedValue = lazyReturned;
				} else {
					$returnedValue = call_user_func_array(handler, params);
				}

				/**
				 * Calling afterBinding event
				 */
				if ( gettype($eventsManager) == "object" ) {
					if ( eventsManager->fire("micro:afterBinding", this) === false ) {
						return false;
					}
				}

				$afterBindingHandlers = $this->_afterBindingHandlers;
				if ( gettype($afterBindingHandlers) == "array" ) {
					$this->_stopped = false;

					/**
					 * Calls the after binding handlers
					 */
					foreach ( $afterBindingHandlers as $afterBinding ) {

						if ( gettype($afterBinding) == "object" && afterBinding instanceof MiddlewareInterface ) {

							/**
							 * Call the middleware
							 */
							$status = afterBinding->call(this);

							/**
							 * Reload the status
							 * break the execution if ( the middleware was stopped
							 */
							if ( $this->_stopped ) {
								break;
							}

							continue;
						}

						if ( !is_callable(afterBinding) ) {
							throw new Exception("'afterBinding' handler is not callable");
						}

						/**
						 * Call the afterBinding handler
						 */
						$status = call_user_func(afterBinding);

						/**
						 * break the execution if ( the middleware was stopped
						 */
						if ( $this->_stopped ) {
						    break;
						}
					}
					/**
					* Reload the 'stopped' status
					 */
					if ( $this->_stopped ) {
						return status;
					}
				}

				/**
				 * Update the returned value
				 */
				$this->_returnedValue = returnedValue;

				/**
				 * Calling afterExecuteRoute event
				 */
				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("micro:afterExecuteRoute", this);
				}

				$afterHandlers = $this->_afterHandlers;
				if ( gettype($afterHandlers) == "array" ) {

					$this->_stopped = false;

					/**
					 * Calls the after handlers
					 */
					foreach ( $afterHandlers as $after ) {

						if ( gettype($after) == "object" ) {
							if ( after instanceof MiddlewareInterface ) {

								/**
								 * Call the middleware
								 */
								$status = after->call(this);

								/**
								 * break the execution if ( the middleware was stopped
								 */
								if ( $this->_stopped ) {
									break;
								}

								continue;
							}
						}

						if ( !is_callable(after) ) {
							throw new Exception("One of the 'after' handlers is not callable");
						}

						$status = call_user_func(after);

						/**
						 * break the execution if ( the middleware was stopped
						 */
						if ( $this->_stopped ) {
							break;
						}
					}
				}

			} else {

				/**
				 * Calling befor (eNotFound event
				 */
				$eventsManager = $this->_eventsManager;
				if ( gettype($eventsManager) == "object" ) {
					if ( eventsManager->fire("micro:befor (eNotFound", this) === false ) ) {
						return false;
					}
				}

				/**
				 * Check if ( a notfoundhandler is defined and it's callable
				 */
				$notFoundHandler = $this->_notFoundHandler;
				if ( !is_callable(notFoundHandler) ) {
					throw new Exception("Not-Found handler is not callable or is not defined");
				}

				/**
				 * Call the Not-Found handler
				 */
				$returnedValue = call_user_func(notFoundHandler);
			}

			/**
			 * Calling afterHandleRoute event
			 */
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("micro:afterHandleRoute", this, returnedValue);
			}

			$finishHandlers = $this->_finishHandlers;
			if ( gettype($finishHandlers) == "array" ) {

				$this->_stopped = false;

				$params = null;

				/**
				 * Calls the finish handlers
				 */
				foreach ( $finishHandlers as $finish ) {

					/**
					 * Try to execute middleware as plugins
					 */
					if ( gettype($finish) == "object" ) {

						if ( finish instanceof MiddlewareInterface ) {

							/**
							 * Call the middleware
							 */
							$status = finish->call(this);

							/**
							 * break the execution if ( the middleware was stopped
							 */
							if ( $this->_stopped ) {
								break;
							}

							continue;
						}
					}

					if ( !is_callable(finish) ) {
						throw new Exception("One of the 'finish' handlers is not callable");
					}

					if ( params === null ) {
						$params = [this];
					}

					/**
					 * Call the 'finish' middleware
					 */
					$status = call_user_func_array(finish, params);

					/**
					 * break the execution if ( the middleware was stopped
					 */
					if ( $this->_stopped ) {
						break;
					}
				}
			}

		} catch \Exception, e {

			/**
			 * Calling befor (eNotFound event
			 */
			$eventsManager = $this->_eventsManager;
			if ( gettype($eventsManager) == "object" ) {
				$returnedValue = eventsManager->fire("micro:befor (eException", this, e);
			}

			/**
			 * Check if ( an errorhandler is defined and it's callable
			 */
			$errorHandler = $this->_errorHandler;
			if ( errorHandler ) {

				if ( !is_callable(errorHandler) ) {
					throw new Exception("Error handler is not callable");
				}

				/**
				 * Call the Error handler
				 */
				$returnedValue = call_user_func_array(errorHandler, [e]);
				if ( gettype($returnedValue) == "object" ) {
					if ( !(returnedValue instanceof ResponseInterface) ) {
						throw e;
					}
				} else {
					if ( returnedValue !== false ) {
						throw e;
					}
				}

			} else {
				if ( returnedValue !== false ) {
					throw e;
				}
			}
		}

		/**
		 * Check if ( the returned value is a string and take it as response body
		 */
		if ( gettype($returnedValue) == "string" ) {
			$response = <ResponseInterface> dependencyInjector->getShared("response");
			if ( !response->isSent() ) {
				response->setContent(returnedValue);
				response->send();
			}
		}

		/**
		 * Check if ( the returned object is already a response
		 */
		if ( gettype($returnedValue) == "object" ) {
			if ( returnedValue instanceof ResponseInterface ) {
				/**
				 * Automatically send the response
				 */
				 if ( !returnedValue->isSent() ) {
				 	returnedValue->send();
				 }
			}
		}

		return returnedValue;
    }

    /***
	 * Stops the middleware execution avoiding than other middlewares be executed
	 **/
    public function stop() {
		$this->_stopped = true;
    }

    /***
	 * Sets externally the handler that must be called by the matched route
	 *
	 * @param callable activeHandler
	 **/
    public function setActiveHandler($activeHandler ) {
		$this->_activeHandler = activeHandler;
    }

    /***
	 * Return the handler that will be called for the matched route
	 *
	 * @return callable
	 **/
    public function getActiveHandler() {
		return $this->_activeHandler;
    }

    /***
	 * Returns the value returned by the executed handler
	 *
	 * @return mixed
	 **/
    public function getReturnedValue() {
		return $this->_returnedValue;
    }

    /***
	 * Check if a service is registered in the internal services container using the array syntax
	 *
	 * @param string alias
	 * @return boolean
	 **/
    public function offsetExists($alias ) {
		return $this->hasService(alias);
    }

    /***
	 * Allows to register a shared service in the internal services container using the array syntax
	 *
	 *<code>
	 *	$app["request"] = new \Phalcon\Http\Request();
	 *</code>
	 *
	 * @param string alias
	 * @param mixed definition
	 **/
    public function offsetSet($alias , $definition ) {
		this->setService(alias, definition);
    }

    /***
	 * Allows to obtain a shared service in the internal services container using the array syntax
	 *
	 *<code>
	 * var_dump(
	 *     $app["request"]
	 * );
	 *</code>
	 *
	 * @param string alias
	 * @return mixed
	 **/
    public function offsetGet($alias ) {
		return $this->getService(alias);
    }

    /***
	 * Removes a service from the internal services container using the array syntax
	 *
	 * @param string alias
	 **/
    public function offsetUnset($alias ) {
		return alias;
    }

    /***
	 * Appends a before middleware to be called before execute the route
	 *
	 * @param callable handler
	 * @return \Phalcon\Mvc\Micro
	 **/
    public function before($handler ) {
		$this->_befor (eHandlers[] = handler;
		return this;
    }

    /***
	 * Appends a afterBinding middleware to be called after model binding
	 *
	 * @param callable handler
	 * @return \Phalcon\Mvc\Micro
	 **/
    public function afterBinding($handler ) {
		$this->_afterBindingHandlers[] = handler;
		return this;
    }

    /***
	 * Appends an 'after' middleware to be called after execute the route
	 *
	 * @param callable handler
	 * @return \Phalcon\Mvc\Micro
	 **/
    public function after($handler ) {
		$this->_afterHandlers[] = handler;
		return this;
    }

    /***
	 * Appends a 'finish' middleware to be called when the request is finished
	 *
	 * @param callable handler
	 * @return \Phalcon\Mvc\Micro
	 **/
    public function finish($handler ) {
		$this->_finishHandlers[] = handler;
		return this;
    }

    /***
	 * Returns the internal handlers attached to the application
	 **/
    public function getHandlers() {
		return $this->_handlers;
    }

    /***
	 * Gets model binder
	 **/
    public function getModelBinder() {
		return $this->_modelBinder;
    }

    /***
	 * Sets model binder
	 *
	 * <code>
	 * $micro = new Micro($di);
	 * $micro->setModelBinder(new Binder(), 'cache');
	 * </code>
	 **/
    public function setModelBinder($modelBinder , $cache  = null ) {

		if ( gettype($cache) == "string" ) {
			$dependencyInjector = $this->_dependencyInjector;
			$cache = dependencyInjector->get(cache);
		}

		if ( cache != null ) {
			modelBinder->setCache(cache);
		}

		$this->_modelBinder = modelBinder;

		return this;
    }

    /***
	 * Returns bound models from binder instance
	 **/
    public function getBoundModels() {

		$modelBinder = $this->_modelBinder;

		if ( modelBinder != null ) {
			return modelBinder->getBoundModels();
		}

		return [];
    }

}