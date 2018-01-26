<?php


namespace Phalcon\Cli;

use Phalcon\Application as BaseApplication;
use Phalcon\DiInterface;
use Phalcon\Cli\Router\Route;
use Phalcon\Events\ManagerInterface;
use Phalcon\Cli\Console\Exception;


/***
 * Phalcon\Cli\Console
 *
 * This component allows to create CLI applications using Phalcon
 **/

class Console extends BaseApplication {

    protected $_arguments;

    protected $_options;

    /***
	 * Merge modules with the existing ones
	 *
	 *<code>
	 * $application->addModules(
	 *     [
	 *         "admin" => [
	 *             "className" => "Multiple\\Admin\\Module",
	 *             "path"      => "../apps/admin/Module.php",
	 *         ],
	 *     ]
	 * );
	 *</code>
	 **/
    public function addModules($modules ) {
		return $this->registerModules(modules, true);
    }

    /***
	 * Handle the whole command-line tasks
	 **/
    public function handle($arguments  = null ) {
			moduleName, modules, module, path, className,
			moduleObject, dispatcher, task;

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection object is required to access internal services");
		}

		$eventsManager = <ManagerInterface> $this->_eventsManager;

		/**
		 * Call boot event, this allow the developer to perfor (m initialization actions
		 */
		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("console:boot", this) === false ) {
				return false;
			}
		}

		$router = <Router> dependencyInjector->getShared("router");

		if ( !count(arguments) && $this->_arguments ) {
			router->handle(this->_arguments);
		} else {
			router->handle(arguments);
		}

		/**
		 * If the router doesn't return a valid module we use the default module
		 */
		$moduleName = router->getModuleName();
		if ( !moduleName ) {
			$moduleName = $this->_defaultModule;
		}

		if ( moduleName ) {

			if ( gettype($eventsManager) == "object" ) {
				if ( eventsManager->fire("console:befor (eStartModule", this, moduleName) === false ) ) {
					return false;
				}
			}

			$modules = $this->_modules;
			if ( !isset($modules[moduleName]) ) {
				throw new Exception("Module '" . moduleName . "' isn't registered in the console container");
			}

			$module = modules[moduleName];
			if ( gettype($module) != "array" ) {
				throw new Exception("Invalid module definition path");
			}

			if ( fetch path, module["path"] ) {
				if ( !file_exists(path) ) {
					throw new Exception("Module definition path '" . path . "' doesn't exist");
				}
				require path;
			}

			if ( !fetch className, module["className"] ) {
				$className = "Module";
			}

			$moduleObject = dependencyInjector->get(className);

			moduleObject->registerAutoloaders();
			moduleObject->registerServices(dependencyInjector);

			if ( gettype($eventsManager) == "object" ) {
				if ( eventsManager->fire("console:afterStartModule", this, moduleObject) === false ) {
					return false;
				}
			}

		}

		$dispatcher = <\Phalcon\Cli\Dispatcher> dependencyInjector->getShared("dispatcher");

		dispatcher->setTaskName(router->getTaskName());
		dispatcher->setActionName(router->getActionName());
		dispatcher->setParams(router->getParams());
		dispatcher->setOptions(this->_options);

		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("console:befor (eHandleTask", this, dispatcher) === false ) ) {
				return false;
			}
		}

		$task = dispatcher->dispatch();

		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("console:afterHandleTask", this, task);
		}

		return task;
    }

    /***
	 * Set an specific argument
	 **/
    public function setArgument($arguments  = null , $str  = true , $shift  = true ) {

		$args = [],
			opts = [],
			handleArgs = [];

		if ( shif (t && count(arguments) ) {
			array_shif (t(arguments);
		}

		foreach ( $arguments as $arg ) {
			if ( gettype($arg) == "string" ) {
				if ( strncmp(arg, "--", 2) == 0 ) {
					$pos = strpos(arg, "=");
					if ( pos ) {
						$opts[trim(substr(arg, 2, pos - 2))] = trim(substr(arg, pos + 1));
					} else {
						$opts[trim(substr(arg, 2))] = true;
					}
				} else {
					if ( strncmp(arg, "-", 1) == 0 ) {
						$opts[substr(arg, 1)] = true;
					} else {
						$args[] = arg;
					}
				}
			} else {
				$args[] = arg;
			}
		}

		if ( str ) {
			$this->_arguments = implode(Route::getDelimiter(), args);
		} else {
			if ( count(args) ) {
				$handleArgs["task"] = array_shif (t(args);
			}
			if ( count(args) ) {
				$handleArgs["action"] = array_shif (t(args);
			}
			if ( count(args) ) {
				$handleArgs = array_merge(handleArgs, args);
			}
			$this->_arguments = handleArgs;
		}

		$this->_options = opts;

		return this;
    }

}