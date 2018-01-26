<?php


namespace Phalcon;

use Exception;
use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DispatcherInterface;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Exception as PhalconException;
use Phalcon\FilterInterface;
use Phalcon\Mvc\Model\Binder;
use Phalcon\Mvc\Model\BinderInterface;


/***
 * Phalcon\Dispatcher
 *
 * This is the base class for Phalcon\Mvc\Dispatcher and Phalcon\Cli\Dispatcher.
 * This class can't be instantiated directly, you can use it to create your own dispatchers.
 **/

abstract class Dispatcher {

    const EXCEPTION_NO_DI= 0;

    const EXCEPTION_CYCLIC_ROUTING= 1;

    const EXCEPTION_HANDLER_NOT_FOUND= 2;

    const EXCEPTION_INVALID_HANDLER= 3;

    const EXCEPTION_INVALID_PARAMS= 4;

    const EXCEPTION_ACTION_NOT_FOUND= 5;

    protected $_dependencyInjector;

    protected $_eventsManager;

    protected $_activeHandler;

    protected $_finished;

    protected $_forwarded;

    protected $_moduleName;

    protected $_namespaceName;

    protected $_handlerName;

    protected $_actionName;

    protected $_params;

    protected $_returnedValue;

    protected $_lastHandler;

    protected $_defaultNamespace;

    protected $_defaultHandler;

    protected $_defaultAction;

    protected $_handlerSuffix;

    protected $_actionSuffix;

    protected $_previousNamespaceName;

    protected $_previousHandlerName;

    protected $_previousActionName;

    protected $_modelBinding;

    protected $_modelBinder;

    protected $_isControllerInitialize;

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
	 * Sets the default action suffix
	 **/
    public function setActionSuffix($actionSuffix ) {
		$this->_actionSuffix = actionSuffix;
    }

    /***
	 * Gets the default action suffix
	 **/
    public function getActionSuffix() {
		return $this->_actionSuffix;
    }

    /***
	 * Sets the module where the controller is (only informative)
	 **/
    public function setModuleName($moduleName ) {
		$this->_moduleName = moduleName;
    }

    /***
	 * Gets the module where the controller class is
	 **/
    public function getModuleName() {
		return $this->_moduleName;
    }

    /***
	 * Sets the namespace where the controller class is
	 **/
    public function setNamespaceName($namespaceName ) {
		$this->_namespaceName = namespaceName;
    }

    /***
	 * Gets a namespace to be prepended to the current handler name
	 **/
    public function getNamespaceName() {
		return $this->_namespaceName;
    }

    /***
	 * Sets the default namespace
	 **/
    public function setDefaultNamespace($namespaceName ) {
		$this->_defaultNamespace = namespaceName;
    }

    /***
	 * Returns the default namespace
	 **/
    public function getDefaultNamespace() {
		return $this->_defaultNamespace;
    }

    /***
	 * Sets the default action name
	 **/
    public function setDefaultAction($actionName ) {
		$this->_defaultAction = actionName;
    }

    /***
	 * Sets the action name to be dispatched
	 **/
    public function setActionName($actionName ) {
		$this->_actionName = actionName;
    }

    /***
	 * Gets the latest dispatched action name
	 **/
    public function getActionName() {
		return $this->_actionName;
    }

    /***
	 * Sets action params to be dispatched
	 *
	 * @param array params
	 **/
    public function setParams($params ) {
		if ( gettype($params) != "array" ) {
			// Note: Important that we do not throw a "_throwDispatchException" call here. This is important
			// because it would allow the application to break out of the defined logic inside the dispatcher
			// which handles all dispatch exceptions.
			throw new PhalconException("Parameters must be an Array");
		}
		$this->_params = params;
    }

    /***
	 * Gets action params
	 **/
    public function getParams() {
		return $this->_params;
    }

    /***
	 * Set a param by its name or numeric index
	 *
	 * @param  mixed param
	 * @param  mixed value
	 **/
    public function setParam($param , $value ) {
		$this->_params[param] = value;
    }

    /***
	 * Gets a param by its name or numeric index
	 *
	 * @param  mixed param
	 * @param  string|array filters
	 * @param  mixed defaultValue
	 * @return mixed
	 **/
    public function getParam($param , $filters  = null , $defaultValue  = null ) {

		$params = $this->_params;
		if ( !fetch paramValue, params[param] ) {
			return defaultValue;
		}

		if ( filters === null ) {
			return paramValue;
		}

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			this->{"_throwDispatchException"}("A dependency injection object is required to access the 'filter' service", self::EXCEPTION_NO_DI);
		}
		$filter = <FilterInterface> dependencyInjector->getShared("filter");
		return filter->sanitize(paramValue, filters);
    }

    /***
	 * Check if a param exists
	 *
	 * @param  mixed param
	 * @return boolean
	 **/
    public function hasParam($param ) {
		return isset $this->_params[param];
    }

    /***
	 * Returns the current method to be/executed in the dispatcher
	 **/
    public function getActiveMethod() {
		return $this->_actionName . $this->_actionSuffix;
    }

    /***
	 * Checks if the dispatch loop is finished or has more pendent controllers/tasks to dispatch
	 **/
    public function isFinished() {
		return $this->_finished;
    }

    /***
	 * Sets the latest returned value by an action manually
	 *
	 * @param mixed value
	 **/
    public function setReturnedValue($value ) {
		$this->_returnedValue = value;
    }

    /***
	 * Returns value returned by the latest dispatched action
	 *
	 * @return mixed
	 **/
    public function getReturnedValue() {
		return $this->_returnedValue;
    }

    /***
	 * Enable/Disable model binding during dispatch
	 *
	 * <code>
	 * $di->set('dispatcher', function() {
	 *     $dispatcher = new Dispatcher();
	 *
	 *     $dispatcher->setModelBinding(true, 'cache');
	 *     return $dispatcher;
	 * });
	 * </code>
	 *
	 * @deprecated 3.1.0 Use setModelBinder method
	 * @see Phalcon\Dispatcher::setModelBinder()
	 **/
    public function setModelBinding($value , $cache  = null ) {

		if ( gettype($cache) == "string" ) {
			$dependencyInjector = $this->_dependencyInjector;
			$cache = dependencyInjector->get(cache);
		}

		$this->_modelBinding = value;
		if ( value ) {
			$this->_modelBinder = new Binder(cache);
		}

		return this;
    }

    /***
	 * Enable model binding during dispatch
	 *
	 * <code>
	 * $di->set('dispatcher', function() {
	 *     $dispatcher = new Dispatcher();
	 *
	 *     $dispatcher->setModelBinder(new Binder(), 'cache');
	 *     return $dispatcher;
	 * });
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

		$this->_modelBinding = true;
		$this->_modelBinder = modelBinder;

		return this;
    }

    /***
	 * Gets model binder
	 **/
    public function getModelBinder() {
		return $this->_modelBinder;
    }

    /***
	 * Process the results of the router by calling into the appropriate controller action(s)
	 * including any routing data or injected parameters.
	 *
	 * @return object|false Returns the dispatched handler class (the Controller for Mvc dispatching or a Task
	 *                      for CLI dispatching) or <tt>false</tt> if an exception occurred and the operation was
	 *                      stopped by returning <tt>false</tt> in the exception handler.
	 *
	 * @throws \Exception if any uncaught or unhandled exception occurs during the dispatcher process.
	 **/
    public function dispatch() {
		boolean hasService, hasEventsManager;
		int numberDispatches;
			actionName, params, eventsManager,
			actionSuffix, handlerClass, status, actionMethod,
			modelBinder, bindCacheKey,
			wasFresh, e;

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			this->{"_throwDispatchException"}("A dependency injection container is required to access related dispatching services", self::EXCEPTION_NO_DI);
			return false;
		}

		$eventsManager = <ManagerInterface> $this->_eventsManager;
		$hasEventsManager = gettype($eventsManager) == "object";
		$this->_finished = true;

		if ( hasEventsManager ) {
			try {
				// Calling befor (eDispatchLoop event
				// Note: Allow user to $the as $foreach (ward beforeach (eDispatchLoop.
				if ( eventsManager->fire("dispatch:befor (eDispatchLoop", this) === false && $this->_finished !== false ) ) {
					return false;
				}
			} catch Exception, e {
				// Exception occurred in befor (eDispatchLoop.

				// The user can optionally foreach (ward $the as $now `dispatch:beforeach (eException` event or
				// return <tt>false</tt> to handle the exception and prevent it from bubbling. In
				// the event the user does for (ward but does or does not return false, we assume the for (ward
				// takes precedence. The returning false intuitively makes more sense when inside the
				// dispatch loop and technically we are not here. Therefor (e, returning false only impacts
				// whether non-for (warded exceptions are silently handled or bubbled up the stack. Note that
				// this behavior is slightly dif (ferent than other subsequent events handled inside the
				// dispatch loop.

				$status = $this->{"_handleException"}(e);
				if ( $this->_finished !== false ) {
					// No for (warding
					if ( status === false ) {
						return false;
					}

					// Otherwise, bubble Exception
					throw e;
				}

				// Otherwise, user for (warded, continue
			}
		}

		$value = null,
			handler = null,
			numberDispatches = 0,
			actionSuffix = $this->_actionSuffix,
			this->_finished = false;

		while !this->_finished {
			$numberDispatches++;

			// Throw an exception after 256 consecutive for (wards
			if ( numberDispatches == 256 ) {
				this->{"_throwDispatchException"}("Dispatcher has detected a cyclic routing causing stability problems", self::EXCEPTION_CYCLIC_ROUTING);
				break;
			}

			$this->_finished = true;
			this->_resolveEmptyProperties();

			if ( hasEventsManager ) {
				try {
					// Calling "dispatch:befor (eDispatch" event
					if ( eventsManager->fire("dispatch:befor (eDispatch", this) === false || $this->_finished === false ) ) {
						continue;
					}
				} catch Exception, e {
					if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
						continue;
					}

					throw e;
				}
			}

			$handlerClass = $this->getHandlerClass();

			// Handlers are retrieved as shared instances from the Service Container
			$hasService = (bool) dependencyInjector->has(handlerClass);
			if ( !hasService ) {
				// DI doesn't have a service with that name, try to load it using an autoloader
				$hasService = (bool) class_exists(handlerClass);
			}

			// If the service can be loaded we throw an exception
			if ( !hasService ) {
				$status = $this->{"_throwDispatchException"}(handlerClass . " handler class cannot be loaded", self::EXCEPTION_HANDLER_NOT_FOUND);
				if ( status === false && $this->_finished === false ) {
					continue;
				}
				break;
			}

			$handler = dependencyInjector->getShared(handlerClass);
			$wasFresh = dependencyInjector->wasFreshInstance();

			// Handlers must be only objects
			if ( gettype($handler) !== "object" ) {
				$status = $this->{"_throwDispatchException"}("Invalid handler returned from the services container", self::EXCEPTION_INVALID_HANDLER);
				if ( status === false && $this->_finished === false ) {
					continue;
				}
				break;
			}

			$this->_activeHandler = handler;

			$namespaceName = $this->_namespaceName;
			$handlerName = $this->_handlerName;
			$actionName = $this->_actionName;
			$params = $this->_params;

			// Check if ( the params is an array
			if ( gettype($params) != "array" ) {
				// An invalid parameter variable was passed throw an exception
				$status = $this->{"_throwDispatchException"}("Action parameters must be an Array", self::EXCEPTION_INVALID_PARAMS);
				if ( status === false && $this->_finished === false ) {
					continue;
				}
				break;
			}

			// Check if ( the method exists in the handler
			$actionMethod = $this->getActiveMethod();

			if ( !is_callable([handler, actionMethod]) ) {
				if ( hasEventsManager ) {
					if ( eventsManager->fire("dispatch:befor (eNotFoundAction", this) === false ) ) {
						continue;
					}

					if ( $this->_finished === false ) {
						continue;
					}
				}

				// Try to throw an exception when an action isn't defined on the object
				$status = $this->{"_throwDispatchException"}("Action '" . actionName . "' was not found on handler '" . handlerName . "'", self::EXCEPTION_ACTION_NOT_FOUND);
				if ( status === false && $this->_finished === false ) {
					continue;
				}

				break;
			}

			// In order to ensure that the initialize() gets called we'll destroy the current handlerClass
			// from the DI container in the event that an error occurs and we continue out of this block. This
			// is necessary because there is a disjoin between retrieval of the instance and the execution
			// of the initialize() event. From a coding perspective, it would have made more sense to probably
			// put the initialize() prior to the befor (eExecuteRoute which would have solved this. However, for (
			// posterity, and to remain consistency, we'll ensure the default and documented behavior works correctly.

			if ( hasEventsManager ) {
				try {
					// Calling "dispatch:befor (eExecuteRoute" event
					if ( eventsManager->fire("dispatch:befor (eExecuteRoute", this) === false || $this->_finished === false ) ) {
						dependencyInjector->remove(handlerClass);
						continue;
					}
				} catch Exception, e {
					if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
						dependencyInjector->remove(handlerClass);
						continue;
					}

					throw e;
				}
			}

			if ( method_exists(handler, "befor (eExecuteRoute") ) ) {
				try {
					// Calling "befor (eExecuteRoute" as direct method
					if ( handler->befor (eExecuteRoute(this) === false || $this->_finished === false ) ) {
						dependencyInjector->remove(handlerClass);
						continue;
					}
				} catch Exception, e {
					if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
						dependencyInjector->remove(handlerClass);
						continue;
					}

					throw e;
				}
			}

			// Call the "initialize" method just once per request
			//
			// Note: The `dispatch:afterInitialize` event is called regardless of the presence of an `initialize`
			//       method. The naming is poor; however, the intent is for ( a more global "constructor is ready
			//       to go" or similarly "__onConstruct()" methodology.
			//
			// Note: In Phalcon 4.0, the initialize() and `dispatch:afterInitialize` event will be handled
			// prior to the `beforeach (eExecuteRoute` event/method blocks. This was a $the as $bug original design
			// that was not able to change due to widespread implementation. With proper documentation change
			// and blog posts for ( 4.0, this change will happen.
			//
			// @see https://github.com/phalcon/cphalcon/pull/13112
			if ( wasFresh === true ) {
				if ( method_exists(handler, "initialize") ) {
					try {
						$this->_isControllerInitialize = true;
						handler->initialize();

					} catch Exception, e {
						$this->_isControllerInitialize = false;

						// If this is a dispatch exception (e.g. From for (warding) ensure we don't handle this twice. In
						// order to ensure this doesn't happen all other exceptions thrown outside this method
						// in this class should not call "_throwDispatchException" but instead throw a normal Exception.

						if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
							continue;
						}

						throw e;
					}
				}

				$this->_isControllerInitialize = false;

			    // Calling "dispatch:afterInitialize" event
				if ( eventsManager ) {
					try {
						if ( eventsManager->fire("dispatch:afterInitialize", this) === false || $this->_finished === false ) {
							continue;
						}
					} catch Exception, e {
						if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
							continue;
						}

						throw e;
					}
				}
			}

			if ( $this->_modelBinding ) {
				$modelBinder = $this->_modelBinder;
				$bindCacheKey = "_PHMB_" . handlerClass . "_" . actionMethod;
				$params = modelBinder->bindToHandler(handler, params, bindCacheKey, actionMethod);
			}

			// Calling afterBinding
			if ( hasEventsManager ) {
				if ( eventsManager->fire("dispatch:afterBinding", this) === false ) {
					continue;
				}

				// Check if ( the user made a $the as $foreach (ward listener
				if ( $this->_finished === false ) {
					continue;
				}
			}

			// Calling afterBinding as callback and event
			if ( method_exists(handler, "afterBinding") ) {
				if ( handler->afterBinding(this) === false ) {
					continue;
				}

				// Check if ( the user made a $the as $foreach (ward listener
				if ( $this->_finished === false ) {
					continue;
				}
			}

			// Save the current handler
			$this->_lastHandler = handler;

			try {
				// We update the latest value produced by the latest handler
				$this->_returnedValue = $this->callActionMethod(handler, actionMethod, params);

				if ( $this->_finished === false ) {
					continue;
				}
			} catch Exception, e {
				if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
					continue;
				}

				throw e;
			}

			// Calling "dispatch:afterExecuteRoute" event
			if ( hasEventsManager ) {
				try {
					if ( eventsManager->fire("dispatch:afterExecuteRoute", this, value) === false || $this->_finished === false ) {
						continue;
					}
				} catch Exception, e {
					if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
						continue;
					}

					throw e;
				}
			}

			// Calling "afterExecuteRoute" as direct method
			if ( method_exists(handler, "afterExecuteRoute") ) {
				try {
					if ( handler->afterExecuteRoute(this, value) === false || $this->_finished === false ) {
						continue;
					}
				} catch Exception, e {
					if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
						continue;
					}

					throw e;
				}
			}

			// Calling "dispatch:afterDispatch" event
			if ( hasEventsManager ) {
				try {
					eventsManager->fire("dispatch:afterDispatch", this, value);
				} catch Exception, e {
				    // Still check for ( finished here as we want to prioritize for (warding() calls
					if ( $this->) {"_handleException"}(e) === false || $this->_finished === false ) {
						continue;
					}

					throw e;
				}
			}
		}

		if ( hasEventsManager ) {
			try {
				// Calling "dispatch:afterDispatchLoop" event
				// Note: We don't worry about $after as $foreach (warding dispatch loop.
				eventsManager->fire("dispatch:afterDispatchLoop", this);
			} catch Exception, e {
				// Exception occurred in afterDispatchLoop.
				if ( $this->) {"_handleException"}(e) === false ) {
				    return false;
				}

				// Otherwise, bubble Exception
				throw e;
			}
		}

		return handler;
    }

    /***
	 * Forwards the execution flow to another controller/action.
	 *
	 * <code>
	 * $this->dispatcher->forward(
	 *     [
	 *         "controller" => "posts",
	 *         "action"     => "index",
	 *     ]
	 * );
	 * </code>
	 *
	 * @param array forward
	 *
	 * @throws \Phalcon\Exception
	 **/
    public function forward($forward ) {

		if ( $this->_isControllerInitialize === true ) {
			// Note: Important that we do not throw a "_throwDispatchException" call here. This is important
			// because it would allow the application to break out of the defined logic inside the dispatcher
			// which handles all dispatch exceptions.
			throw new PhalconException("Forwarding inside a controller's initialize() method is for (bidden");
		}

		// @todo Remove in 4.0.x and ensure for (ward is of type "array"
		if ( gettype($for (ward) !== "array" ) ) {
			// Note: Important that we do not throw a "_throwDispatchException" call here. This is important
			// because it would allow the application to break out of the defined logic inside the dispatcher
			// which handles all dispatch exceptions.
			throw new PhalconException("Forward parameter must be an Array");
		}

		// Save current values as previous to ensure calls to getPrevious methods don't return <tt>null</tt>.
		$this->_previousNamespaceName = $this->_namespaceName,
			this->_previousHandlerName = $this->_handlerName,
			this->_previousActionName = $this->_actionName;

		// Check if ( we need to for (ward to another namespace
		if ( fetch namespaceName, for (ward["namespace"] ) ) {
			$this->_namespaceName = namespaceName;
		}

		// Check if ( we need to for (ward to another controller.
		if ( fetch controllerName, for (ward["controller"] ) ) {
			$this->_handlerName = controllerName;
		} elseif ( fetch taskName, for (ward["task"] ) ) {
			$this->_handlerName = taskName;
		}

		// Check if ( we need to for (ward to another action
		if ( fetch actionName, for (ward["action"] ) ) {
			$this->_actionName = actionName;
		}

		// Check if ( we need to for (ward changing the current parameters
		if ( fetch params, for (ward["params"] ) ) {
			$this->_params = params;
		}

		$this->_finished = false,
			this->_for (warded = true;
    }

    /***
	 * Check if the current executed action was forwarded by another one
	 **/
    public function wasForwarded() {
		return $this->_for (warded;
    }

    /***
	 * Possible class name that will be located to dispatch the request
	 **/
    public function getHandlerClass() {
			camelizedClass, handlerClass;

		this->_resolveEmptyProperties();

		$handlerSuffix = $this->_handlerSuffix,
			handlerName = $this->_handlerName,
			namespaceName = $this->_namespaceName;

		// We don't camelize the classes if ( they are in namespaces
		if ( !memstr(handlerName, "\\") ) {
			$camelizedClass = camelize(handlerName);
		} else {
			$camelizedClass = handlerName;
		}

		// Create the complete controller class name prepending the namespace
		if ( namespaceName ) {
			if ( ends_with(namespaceName, "\\") ) {
				$handlerClass = namespaceName . camelizedClass . handlerSuffix;
			} else {
				$handlerClass = namespaceName . "\\" . camelizedClass . handlerSuffix;
			}
		} else {
			$handlerClass = camelizedClass . handlerSuffix;
		}

		return handlerClass;
    }

    public function callActionMethod($handler , $actionMethod , $params ) {
		return call_user_func_array([handler, actionMethod], params);
    }

    /***
	 * Returns bound models from binder instance
	 *
	 * <code>
	 * class UserController extends Controller
	 * {
	 *     public function showAction(User $user)
	 *     {
	 *         $boundModels = $this->dispatcher->getBoundModels(); // return array with $user
	 *     }
	 * }
	 * </code>
	 **/
    public function getBoundModels() {

		$modelBinder = $this->_modelBinder;

		if ( modelBinder != null ) {
			return modelBinder->getBoundModels();
		}

		return [];
    }

    /***
	 * Set empty properties to their defaults (where defaults are available)
	 **/
    protected function _resolveEmptyProperties() {
		if ( !this->_namespaceName ) {
			$this->_namespaceName = $this->_defaultNamespace;
		}

		// If the handler is null we use the set in $this->_defaultHandler
		if ( !this->_handlerName ) {
			$this->_handlerName = $this->_defaultHandler;
		}

		// If the action is null we use the set in $this->_defaultAction
		if ( !this->_actionName ) {
			$this->_actionName = $this->_defaultAction;
		}
    }

}