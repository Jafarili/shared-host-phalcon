<?php


namespace Phalcon\Mvc\Router;

use Phalcon\DiInterface;
use Phalcon\Mvc\Router;
use Phalcon\Annotations\Annotation;
use Phalcon\Mvc\Router\Exception;


/***
 * Phalcon\Mvc\Router\Annotations
 *
 * A router that reads routes annotations from classes/resources
 *
 * <code>
 * use Phalcon\Mvc\Router\Annotations;
 *
 * $di->setShared(
 *     "router",
 *     function() {
 *         // Use the annotations router
 *         $router = new Annotations(false);
 *
 *         // This will do the same as above but only if the handled uri starts with /robots
 *         $router->addResource("Robots", "/robots");
 *
 *         return $router;
 *     }
 * );
 * </code>
 **/

class Annotations extends Router {

    protected $_handlers;

    protected $_controllerSuffix;

    protected $_actionSuffix;

    protected $_routePrefix;

    /***
	 * Adds a resource to the annotations handler
	 * A resource is a class that contains routing annotations
	 **/
    public function addResource($handler , $prefix  = null ) {
		$this->_handlers[] = [prefix, handler];

		return this;
    }

    /***
	 * Adds a resource to the annotations handler
	 * A resource is a class that contains routing annotations
	 * The class is located in a module
	 **/
    public function addModuleResource($module , $handler , $prefix  = null ) {
		$this->_handlers[] = [prefix, handler, module];

		return this;
    }

    /***
	 * Produce the routing parameters from the rewrite information
	 **/
    public function handle($uri  = null ) {
			scope, prefix, dependencyInjector, handler, controllerName,
			lowerControllerName, namespaceName, moduleName, sufixed, handlerAnnotations,
			classAnnotations, annotations, annotation, methodAnnotations, method,
			collection;

		if ( !uri ) {
			/**
			 * If 'uri' isn't passed as parameter it reads $_GET["_url"]
			 */
			$realUri = $this->getRewriteUri();
		} else {
			$realUri = uri;
		}

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection container is required to access the 'annotations' service");
		}

		$annotationsService = dependencyInjector->getShared("annotations");

		$handlers = $this->_handlers;

		$controllerSuffix = $this->_controllerSuffix;

		foreach ( $handlers as $scope ) {

			if ( gettype($scope) != "array" ) {
				continue;
			}

			/**
			 * A prefix (if ( any) must be in position 0
			 */
			$prefix = scope[0];

			if ( !empty prefix && !starts_with(realUri, prefix) ) {
				continue;
			}

			/**
			 * The controller must be in position 1
			 */
			$handler = scope[1];

			if ( memstr(handler, "\\") ) {

				/**
				 * Extract the real class name from the namespaced class
				 * The lowercased class name is used as controller
				 * Extract the namespace from the namespaced class
				 */
				$controllerName = get_class_ns(handler),
					namespaceName = get_ns_class(handler);
			} else {
				$controllerName = handler;
			}

			$this->_routePrefix = null;

			/**
			 * Check if ( the scope has a module associated
			 */

			$sufixed = controllerName . controllerSuffix;

			/**
			 * Add namespace to class if ( one is set
			 */
			if ( namespaceName !== null ) {
				$sufixed = namespaceName . "\\" . sufixed;
			}

			/**
			 * Get the annotations from the class
			 */
			$handlerAnnotations = annotationsService->get(sufixed);

			if ( gettype($handlerAnnotations) != "object" ) {
				continue;
			}

			/**
			 * Process class annotations
			 */
			$classAnnotations = handlerAnnotations->getClassAnnotations();
			if ( gettype($classAnnotations) == "object" ) {
				$annotations = classAnnotations->getAnnotations();
				if ( gettype($annotations) == "array" ) {
					foreach ( $annotations as $annotation ) {
						this->processControllerAnnotation(controllerName, annotation);
					}
				}
			}

			/**
			 * Process method annotations
			 */
			$methodAnnotations = handlerAnnotations->getMethodsAnnotations();
			if ( gettype($methodAnnotations) == "array" ) {
				$lowerControllerName = uncamelize(controllerName);

				foreach ( method, $methodAnnotations as $collection ) {
					if ( gettype($collection) == "object" ) {
						foreach ( $collection->getAnnotations() as $annotation ) {
							this->processActionAnnotation(moduleName, namespaceName, lowerControllerName, method, annotation);
						}
					}
				}
			}
		}

		/**
		 * Call the parent handle method()
		 */
		parent::handle(realUri);
    }

    /***
	 * Checks for annotations in the controller docblock
	 **/
    public function processControllerAnnotation($handler , $annotation ) {
		if ( annotation->getName() == "RoutePrefix" ) {
			$this->_routePrefix = annotation->getArgument(0);
		}
    }

    /***
	 * Checks for annotations in the public methods of the controller
	 **/
    public function processActionAnnotation($module , $namespaceName , $controller , $action , $annotation ) {
			route, methods, converts, param, convert, conversorParam, routeName,
			befor (eMatch;

		$isRoute = false,
			methods = null,
			name = annotation->getName();

		/**
		 * Find if ( the route is for ( adding routes
		 */
		switch name {

			case "Route":
				$isRoute = true;
				break;

			case "Get":
				$isRoute = true, methods = "GET";
				break;

			case "Post":
				$isRoute = true, methods = "POST";
				break;

			case "Put":
				$isRoute = true, methods = "PUT";
				break;

			case "Patch":
				$isRoute = true, methods = "PATCH";
				break;

			case "Delete":
				$isRoute = true, methods = "DELETE";
				break;

			case "Options":
				$isRoute = true, methods = "OPTIONS";
				break;
		}

		if ( isRoute === true ) {

			$actionName = strtolower(str_replace(this->_actionSuffix, "", action)),
				routePrefix = $this->_routePrefix;

			/**
			 * Check foreach ( existing $the as $paths annotation
			 */
			$paths = annotation->getNamedArgument("paths");
			if ( gettype($paths) != "array" ) {
				$paths = [];
			}

			/**
			 * Update the module if ( any
			 */
			if ( !empty module ) {
				$paths["module"] = module;
			}

			/**
			 * Update the namespace if ( any
			 */
			if ( !empty namespaceName ) {
				$paths["namespace"] = namespaceName;
			}

			$paths["controller"] = controller,
				paths["action"] = actionName;

			$value = annotation->getArgument(0);

			/**
			 * Create the route using the prefix
			 */
			if ( gettype($value) !== "null" ) {
				if ( value != "/" ) {
					$uri = routePrefix . value;
				} else {
					if ( gettype($routePrefix) !== "null" ) {
						$uri = routePrefix;
					} else {
						$uri = value;
					}
				}
			} else {
				$uri = routePrefix . actionName;
			}

			/**
			 * Add the route to the router
			 */
			$route = $this->add(uri, paths);

			/**
			 * Add HTTP constraint methods
			 */
			if ( methods !== null ) {
				route->via(methods);
			} else {
				$methods = annotation->getNamedArgument("methods");
				if ( gettype($methods) == "array" || gettype($methods) == "string" ) {
					route->via(methods);
				}
			}

			/**
			 * Add the converters
			 */
			$converts = annotation->getNamedArgument("converts");
			if ( gettype($converts) == "array" ) {
				foreach ( param, $converts as $convert ) {
					route->convert(param, convert);
				}
			}

			/**
			 * Add the conversors
			 */
			$converts = annotation->getNamedArgument("conversors");
			if ( gettype($converts) == "array" ) {
				foreach ( conversorParam, $converts as $convert ) {
					route->convert(conversorParam, convert);
				}
			}

			/**
			 * Add the conversors
			 */
			$befor (eMatch = annotation->getNamedArgument("befor (eMatch");
			if ( gettype($befor (eMatch) == "array" || gettype($befor (eMatch) == "string" ) ) {
				route->befor (eMatch(befor (eMatch);
			}

			$routeName = annotation->getNamedArgument("name");
			if ( gettype($routeName) == "string" ) {
				route->setName(routeName);
			}

			return true;
		}
    }

    /***
	 * Changes the controller class suffix
	 **/
    public function setControllerSuffix($controllerSuffix ) {
		$this->_controllerSuffix = controllerSuffix;
    }

    /***
	 * Changes the action method suffix
	 **/
    public function setActionSuffix($actionSuffix ) {
		$this->_actionSuffix = actionSuffix;
    }

    /***
	 * Return the registered resources
	 **/
    public function getResources() {
		return $this->_handlers;
    }

}