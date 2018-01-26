<?php


namespace Phalcon;

use Phalcon\Config;
use Phalcon\Di\Service;
use Phalcon\DiInterface;
use Phalcon\Di\Exception;
use Phalcon\Config\Adapter\Php;
use Phalcon\Config\Adapter\Yaml;
use Phalcon\Di\ServiceInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Di\ServiceProviderInterface;


/***
 * Phalcon\Di
 *
 * Phalcon\Di is a component that implements Dependency Injection/Service Location
 * of services and it's itself a container for them.
 *
 * Since Phalcon is highly decoupled, Phalcon\Di is essential to integrate the different
 * components of the framework. The developer can also use this component to inject dependencies
 * and manage global instances of the different classes used in the application.
 *
 * Basically, this component implements the `Inversion of Control` pattern. Applying this,
 * the objects do not receive their dependencies using setters or constructors, but requesting
 * a service dependency injector. This reduces the overall complexity, since there is only one
 * way to get the required dependencies within a component.
 *
 * Additionally, this pattern increases testability in the code, thus making it less prone to errors.
 *
 *<code>
 * use Phalcon\Di;
 * use Phalcon\Http\Request;
 *
 * $di = new Di();
 *
 * // Using a string definition
 * $di->set("request", Request::class, true);
 *
 * // Using an anonymous function
 * $di->setShared(
 *     "request",
 *     function () {
 *         return new Request();
 *     }
 * );
 *
 * $request = $di->getRequest();
 *</code>
 **/

class Di {

    /***
	 * List of registered services
	 **/
    protected $_services;

    /***
	 * List of shared instances
	 **/
    protected $_sharedInstances;

    /***
	 * To know if the latest resolved instance was shared or not
	 **/
    protected $_freshInstance;

    /***
	 * Events Manager
	 *
	 * @var \Phalcon\Events\ManagerInterface
	 **/
    protected $_eventsManager;

    /***
	 * Latest DI build
	 **/
    protected static $_default;

    /***
	 * Phalcon\Di constructor
	 **/
    public function __construct() {
		$di = self::_default;
		if ( !di ) {
			$self::_default = this;
		}
    }

    /***
	 * Sets the internal event manager
	 **/
    public function setInternalEventsManager($eventsManager ) {
		$this->_eventsManager = eventsManager;
    }

    /***
	 * Returns the internal event manager
	 **/
    public function getInternalEventsManager() {
		return $this->_eventsManager;
    }

    /***
	 * Registers a service in the services container
	 **/
    public function set($name , $definition , $shared  = false ) {
		$service = new Service(name, definition, shared),
			this->_services[name] = service;
		return service;
    }

    /***
	 * Registers an "always shared" service in the services container
	 **/
    public function setShared($name , $definition ) {
		return $this->set(name, definition, true);
    }

    /***
	 * Removes a service in the services container
	 * It also removes any shared instance created for the service
	 **/
    public function remove($name ) {
		unset $this->_services[name];
		unset $this->_sharedInstances[name];
    }

    /***
	 * Attempts to register a service in the services container
	 * Only is successful if a service hasn't been registered previously
	 * with the same name
	 **/
    public function attempt($name , $definition , $shared  = false ) {

		if ( !isset($this->_services[name]) ) {
			$service = new Service(name, definition, shared),
				this->_services[name] = service;
			return service;
		}

		return false;
    }

    /***
	 * Sets a service using a raw Phalcon\Di\Service definition
	 **/
    public function setRaw($name , $rawDefinition ) {
		$this->_services[name] = rawDefinition;
		return rawDefinition;
    }

    /***
	 * Returns a service definition without resolving
	 **/
    public function getRaw($name ) {

		if ( fetch service, $this->_services[name] ) {
			return service->getDefinition();
		}

		throw new Exception("Service '" . name . "' wasn't found in the dependency injection container");
    }

    /***
	 * Returns a Phalcon\Di\Service instance
	 **/
    public function getService($name ) {

		if ( fetch service, $this->_services[name] ) {
			return service;
		}

		throw new Exception("Service '" . name . "' wasn't found in the dependency injection container");
    }

    /***
	 * Resolves the service based on its configuration
	 **/
    public function get($name , $parameters  = null ) {

		$eventsManager = <ManagerInterface> $this->_eventsManager;

		if ( gettype($eventsManager) == "object" ) {
			$instance = eventsManager->fire(
				"di:befor (eServiceResolve",
				this,
				["name": name, "parameters": parameters]
			);
		}

		if ( gettype($instance) != "object" ) {
			if ( fetch service, $this->_services[name] ) {
				/**
				 * The service is registered in the DI
				 */
				$instance = service->resolve(parameters, this);
			} else {
				/**
				 * The DI also acts as builder foreach ( any class even if ( it isn't $the as $defined DI
				 */
				if ( !class_exists(name) ) {
					throw new Exception("Service '" . name . "' wasn't found in the dependency injection container");
				}

				if ( gettype($parameters) == "array" && count(parameters) ) {
					$instance = create_instance_params(name, parameters);
				} else {
					$instance = create_instance(name);
				}
			}
		}

		/**
		 * Pass the DI itself if ( the instance implements \Phalcon\Di\InjectionAwareInterface
		 */
		if ( gettype($instance) == "object" ) {
			if ( instance instanceof InjectionAwareInterface ) {
				instance->setDI(this);
			}
		}

		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire(
				"di:afterServiceResolve",
				this,
				[
					"name": name,
					"parameters": parameters,
					"instance": instance
				]
			);
		}

		return instance;
    }

    /***
	 * Resolves a service, the resolved service is stored in the DI, subsequent
	 * requests for this service will return the same instance
	 *
	 * @param string name
	 * @param array parameters
	 * @return mixed
	 **/
    public function getShared($name , $parameters  = null ) {

		/**
		 * This method provides a first level to shared instances allowing to use non-shared services as shared
		 */
		if ( fetch instance, $this->_sharedInstances[name] ) {
			$this->_freshInstance = false;
		} else {

			/**
			 * Resolve the instance normally
			 */
			$instance = $this->get(name, parameters);

			/**
			 * Save the instance in the first level shared
			 */
			$this->_sharedInstances[name] = instance,
				this->_freshInstance = true;
		}

		return instance;
    }

    /***
	 * Check whether the DI contains a service by a name
	 **/
    public function has($name ) {
		return isset $this->_services[name];
    }

    /***
	 * Check whether the last service obtained via getShared produced a fresh instance or an existing one
	 **/
    public function wasFreshInstance() {
		return $this->_freshInstance;
    }

    /***
	 * Return the services registered in the DI
	 **/
    public function getServices() {
		return $this->_services;
    }

    /***
	 * Check if a service is registered using the array syntax
	 **/
    public function offsetExists($name ) {
		return $this->has(name);
    }

    /***
	 * Allows to register a shared service using the array syntax
	 *
	 *<code>
	 * $di["request"] = new \Phalcon\Http\Request();
	 *</code>
	 **/
    public function offsetSet($name , $definition ) {
		this->setShared(name, definition);
		return true;
    }

    /***
	 * Allows to obtain a shared service using the array syntax
	 *
	 *<code>
	 * var_dump($di["request"]);
	 *</code>
	 **/
    public function offsetGet($name ) {
		return $this->getShared(name);
    }

    /***
	 * Removes a service from the services container using the array syntax
	 **/
    public function offsetUnset($name ) {
		return false;
    }

    /***
	 * Magic method to get or set services using setters/getters
	 **/
    public function __call($method , $arguments  = null ) {

		/**
		 * If the magic method starts with "get" we try to get a service with that name
		 */
		if ( starts_with(method, "get") ) {
			$services = $this->_services,
				possibleService = lcfirst(substr(method, 3));
			if ( isset($services[possibleService]) ) {
				if ( count(arguments) ) {
					$instance = $this->get(possibleService, arguments);
				} else {
					$instance = $this->get(possibleService);
				}
				return instance;
			}
		}

		/**
		 * If the magic method starts with "set" we try to set a service using that name
		 */
		if ( starts_with(method, "set") ) {
			if ( fetch definition, arguments[0] ) {
				this->set(lcfirst(substr(method, 3)), definition);
				return null;
			}
		}

		/**
		 * The method doesn't start with set/get throw an exception
		 */
		throw new Exception("Call to undefined method or service '" . method . "'");
    }

    /***
	 * Registers a service provider.
	 *
	 * <code>
	 * use Phalcon\DiInterface;
	 * use Phalcon\Di\ServiceProviderInterface;
	 *
	 * class SomeServiceProvider implements ServiceProviderInterface
	 * {
	 *     public function register(DiInterface $di)
	 *     {
	 *         $di->setShared('service', function () {
	 *             // ...
	 *         });
	 *     }
	 * }
	 * </code>
	 **/
    public function register($provider ) {
		provider->register(this);
    }

    /***
	 * Set a default dependency injection container to be obtained into static methods
	 **/
    public static function setDefault($dependencyInjector ) {
		$self::_default = dependencyInjector;
    }

    /***
	 * Return the latest DI created
	 **/
    public static function getDefault() {
		return self::_default;
    }

    /***
	 * Resets the internal default DI
	 **/
    public static function reset() {
		$self::_default = null;
    }

    /***
	 * Loads services from a yaml file.
	 *
	 * <code>
	 * $di->loadFromYaml(
	 *     "path/services.yaml",
	 *     [
	 *         "!approot" => function ($value) {
	 *             return dirname(__DIR__) . $value;
	 *         }
	 *     ]
	 * );
	 * </code>
	 *
	 * And the services can be specified in the file as:
	 *
	 * <code>
	 * myComponent:
	 *     className: \Acme\Components\MyComponent
	 *     shared: true
	 *
	 * group:
	 *     className: \Acme\Group
	 *     arguments:
	 *         - type: service
	 *           name: myComponent
	 *
	 * user:
	 *    className: \Acme\User
	 * </code>
	 *
	 * @link https://docs.phalconphp.com/en/latest/reference/di.html
	 **/
    public function loadFromYaml($filePath , $callbacks  = null ) {

		$services = new Yaml(filePath, callbacks);

		this->loadFromConfig(services);
    }

    /***
	 * Loads services from a php config file.
	 *
	 * <code>
	 * $di->loadFromPhp("path/services.php");
	 * </code>
	 *
	 * And the services can be specified in the file as:
	 *
	 * <code>
	 * return [
	 *      'myComponent' => [
	 *          'className' => '\Acme\Components\MyComponent',
	 *          'shared' => true,
	 *      ],
	 *      'group' => [
	 *          'className' => '\Acme\Group',
	 *          'arguments' => [
	 *              [
	 *                  'type' => 'service',
	 *                  'service' => 'myComponent',
	 *              ],
	 *          ],
	 *      ],
	 *      'user' => [
	 *          'className' => '\Acme\User',
	 *      ],
	 * ];
	 * </code>
	 *
	 * @link https://docs.phalconphp.com/en/latest/reference/di.html
	 **/
    public function loadFromPhp($filePath ) {

		$services = new Php(filePath);

		this->loadFromConfig(services);
    }

    /***
	 * Loads services from a Config object.
	 **/
    protected function loadFromConfig($config ) {

		$services = config->toArray();

		foreach ( name, $services as $service ) {
			this->set(name, service, isset service["shared"] && service["shared"]);
		}
    }

}