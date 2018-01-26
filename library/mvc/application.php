<?php


namespace Phalcon\Mvc;

use Phalcon\Application as BaseApplication;
use Phalcon\DiInterface;
use Phalcon\Mvc\ViewInterface;
use Phalcon\Mvc\RouterInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\DispatcherInterface;
use Phalcon\Mvc\Application\Exception;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;


/***
 * Phalcon\Mvc\Application
 *
 * This component encapsulates all the complex operations behind instantiating every component
 * needed and integrating it with the rest to allow the MVC pattern to operate as desired.
 *
 *<code>
 * use Phalcon\Mvc\Application;
 *
 * class MyApp extends Application
 * {
 *     /**
 *      * Register the services here to make them general or register
 *      * in the ModuleDefinition to make them module-specific
 *      *\/
 *     protected function registerServices()
 *     {
 *
 *     }
 *
 *     /**
 *      * This method registers all the modules in the application
 *      *\/
 *     public function main()
 *     {
 *         $this->registerModules(
 *             [
 *                 "frontend" => [
 *                     "className" => "Multiple\\Frontend\\Module",
 *                     "path"      => "../apps/frontend/Module.php",
 *                 ],
 *                 "backend" => [
 *                     "className" => "Multiple\\Backend\\Module",
 *                     "path"      => "../apps/backend/Module.php",
 *                 ],
 *             ]
 *         );
 *     }
 * }
 *
 * $application = new MyApp();
 *
 * $application->main();
 *</code>
 **/

class Application extends BaseApplication {

    protected $_implicitView;

    protected $_sendHeaders;

    protected $_sendCookies;

    /***
	 * Enables or disables sending headers by each request handling
	 **/
    public function sendHeadersOnHandleRequest($sendHeaders ) {
		$this->_sendHeaders = sendHeaders;
		return this;
    }

    /***
	 * Enables or disables sending cookies by each request handling
	 **/
    public function sendCookiesOnHandleRequest($sendCookies ) {
		$this->_sendCookies = sendCookies;
		return this;
    }

    /***
	 * By default. The view is implicitly buffering all the output
	 * You can full disable the view component using this method
	 **/
    public function useImplicitView($implicitView ) {
		$this->_implicitView = implicitView;
		return this;
    }

    /***
	 * Handles a MVC request
	 **/
    public function handle($uri  = null ) {
			module, moduleObject, moduleName, className, path,
			implicitView, returnedResponse, controller, possibleResponse,
			renderStatus, matchedRoute, match;

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection object is required to access internal services");
		}

		$eventsManager = <ManagerInterface> $this->_eventsManager;

		/**
		 * Call boot event, this allow the developer to perfor (m initialization actions
		 */
		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("application:boot", this) === false ) {
				return false;
			}
		}

		$router = <RouterInterface> dependencyInjector->getShared("router");

		/**
		 * Handle the URI pattern (if ( any)
		 */
		router->handle(uri);

		/**
		 * If a 'match' callback was defined in the matched route
		 * The whole dispatcher+view behavior can be overridden by the developer
		 */
		$matchedRoute = router->getMatchedRoute();
		if ( gettype($matchedRoute) == "object" ) {
			$match = matchedRoute->getMatch();
			if ( match !== null ) {

				if ( match instanceof \Closure ) {
					$match = \Closure::bind(match, dependencyInjector);
				}

				/**
				 * Directly call the match callback
				 */
				$possibleResponse = call_user_func_array(match, router->getParams());

				/**
				 * If the returned value is a string return it as body
				 */
				if ( gettype($possibleResponse) == "string" ) {
					$response = <ResponseInterface> dependencyInjector->getShared("response");
					response->setContent(possibleResponse);
					return response;
				}

				/**
				 * If the returned string is a ResponseInterface use it as response
				 */
				if ( gettype($possibleResponse) == "object" ) {
					if ( possibleResponse instanceof ResponseInterface ) {
						possibleResponse->sendHeaders();
						possibleResponse->sendCookies();
						return possibleResponse;
					}
				}
			}
		}

		/**
		 * If the router doesn't return a valid module we use the default module
		 */
		$moduleName = router->getModuleName();
		if ( !moduleName ) {
			$moduleName = $this->_defaultModule;
		}

		$moduleObject = null;

		/**
		 * Process the module definition
		 */
		if ( moduleName ) {

			if ( gettype($eventsManager) == "object" ) {
				if ( eventsManager->fire("application:befor (eStartModule", this, moduleName) === false ) ) {
					return false;
				}
			}

			/**
			 * Gets the module definition
			 */
			$module = $this->getModule(moduleName);

			/**
			 * A module definition must ne an array or an object
			 */
			if ( gettype($module) != "array" && gettype($module) != "object" ) {
				throw new Exception("Invalid module definition");
			}

			/**
			 * An array module definition contains a path to a module definition class
			 */
			if ( gettype($module) == "array" ) {

				/**
				 * Class name used to load the module definition
				 */
				if ( !fetch className, module["className"] ) {
					$className = "Module";
				}

				/**
				 * If developer specif (y a path try to include the file
				 */
				if ( fetch path, module["path"] ) {
					if ( !class_exists(className, false) ) {
						if ( !file_exists(path) ) {
							throw new Exception("Module definition path '" . path . "' doesn't exist");
						}

						require path;
					}
				}

				$moduleObject = <ModuleDefinitionInterface> dependencyInjector->get(className);

				/**
				 * 'registerAutoloaders' and 'registerServices' are automatically called
				 */
				moduleObject->registerAutoloaders(dependencyInjector);
				moduleObject->registerServices(dependencyInjector);

			} else {

				/**
				 * A module definition object, can be a Closure instance
				 */
				if ( !(module instanceof \Closure) ) {
					throw new Exception("Invalid module definition");
				}

				$moduleObject = call_user_func_array(module, [dependencyInjector]);
			}

			/**
			 * Calling afterStartModule event
			 */
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("application:afterStartModule", this, moduleObject);
			}
		}

		/**
		 * Check whether use implicit views or not
		 */
		$implicitView = $this->_implicitView;

		if ( implicitView === true ) {
			$view = <ViewInterface> dependencyInjector->getShared("view");
		}

		/**
		 * We get the parameters from the router and assign them to the dispatcher
		 * Assign the values passed from the router
		 */
		$dispatcher = <DispatcherInterface> dependencyInjector->getShared("dispatcher");
		dispatcher->setModuleName(router->getModuleName());
		dispatcher->setNamespaceName(router->getNamespaceName());
		dispatcher->setControllerName(router->getControllerName());
		dispatcher->setActionName(router->getActionName());
		dispatcher->setParams(router->getParams());

		/**
		 * Start the view component (start output buffering)
		 */
		if ( implicitView === true ) {
			view->start();
		}

		/**
		 * Calling befor (eHandleRequest
		 */
		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("application:befor (eHandleRequest", this, dispatcher) === false ) ) {
				return false;
			}
		}

		/**
		 * The dispatcher must return an object
		 */
		$controller = dispatcher->dispatch();

		/**
		 * Get the latest value returned by an action
		 */
		$possibleResponse = dispatcher->getReturnedValue();

		/**
		 * Returning false from an action cancels the view
		 */
		if ( gettype($possibleResponse) == "boolean" && possibleResponse === false ) {
			$response = <ResponseInterface> dependencyInjector->getShared("response");
		} else {

			/**
			 * Returning a string makes use it as the body of the response
			 */
			if ( gettype($possibleResponse) == "string" ) {
				$response = <ResponseInterface> dependencyInjector->getShared("response");
				response->setContent(possibleResponse);
			} else {

				/**
				 * Check if ( the returned object is already a response
				 */
				$returnedResponse = ((gettype($possibleResponse) == "object") && (possibleResponse instanceof ResponseInterface));

				/**
				 * Calling afterHandleRequest
				 */
				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("application:afterHandleRequest", this, controller);
				}

				/**
				 * If the dispatcher returns an object we try to render the view in auto-rendering mode
				 */
				if ( returnedResponse === false && implicitView === true ) {
					if ( gettype($controller) == "object" ) {

						$renderStatus = true;

						/**
						 * This allows to make a custom view render
						 */
						if ( gettype($eventsManager) == "object" ) {
							$renderStatus = eventsManager->fire("application:viewRender", this, view);
						}

						/**
						 * Check if ( the view process has been treated by the developer
						 */
						if ( renderStatus !== false ) {

							/**
							 * Automatic render based on the latest controller executed
							 */
							view->render(
								dispatcher->getControllerName(),
								dispatcher->getActionName()
							);
						}
					}
				}

				/**
				 * Finish the view component (stop output buffering)
				 */
				if ( implicitView === true ) {
					view->finish();
				}

				if ( returnedResponse === true ) {

					/**
					 * We don't need to create a response because there is one already created
					 */
					$response = possibleResponse;
				} else {

					$response = <ResponseInterface> dependencyInjector->getShared("response");
					if ( implicitView === true ) {

						/**
						 * The content returned by the view is passed to the response service
						 */
						response->setContent(view->getContent());
					}
				}
			}
		}

		/**
		 * Calling befor (eSendResponse
		 */
		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("application:befor (eSendResponse", this, response);
		}

		/**
		 * Check whether send headers or not (by default yes)
		 */
		if ( $this->_sendHeaders  ) {
			response->sendHeaders();
		}

		/**
		 * Check whether send cookies or not (by default yes)
		 */
		if ( $this->_sendCookies ) {
			response->sendCookies();
		}

		/**
		 * Return the response
		 */
		return response;
    }

}