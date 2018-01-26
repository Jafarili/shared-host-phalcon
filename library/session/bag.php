<?php


namespace Phalcon\Session;

use Phalcon\Di;
use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;


/***
 * Phalcon\Session\Bag
 *
 * This component helps to separate session data into "namespaces". Working by this way
 * you can easily create groups of session variables into the application
 *
 * <code>
 * $user = new \Phalcon\Session\Bag("user");
 *
 * $user->name = "Kimbra Johnson";
 * $user->age  = 22;
 * </code>
 **/

class Bag {

    protected $_dependencyInjector;

    protected $_name;

    protected $_data;

    protected $_initialized;

    protected $_session;

    /***
	 * Phalcon\Session\Bag constructor
	 **/
    public function __construct($name ) {
		$this->_name = name;
    }

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Initializes the session bag. This method must not be called directly, the
	 * class calls it when its internal data is accessed
	 **/
    public function initialize() {

		$session = $this->_session;
		if ( gettype($session) != "object" ) {

			$dependencyInjector = $this->_dependencyInjector;
			if ( gettype($dependencyInjector) != "object" ) {
				$dependencyInjector = Di::getDefault();
				if ( gettype($dependencyInjector) != "object" ) {
					throw new Exception("A dependency injection object is required to access the 'session' service");
				}
			}

			$session = dependencyInjector->getShared("session"),
				this->_session = session;
		}

		$data = session->get(this->_name);
		if ( gettype($data) != "array" ) {
			$data = [];
		}

		$this->_data = data;
		$this->_initialized = true;
    }

    /***
	 * Destroys the session bag
	 *
	 *<code>
	 * $user->destroy();
	 *</code>
	 **/
    public function destroy() {
		if ( $this->_initialized === false ) {
			this->initialize();
		}
		$this->_data = [];
		this->_session->remove(this->_name);
    }

    /***
	 * Sets a value in the session bag
	 *
	 *<code>
	 * $user->set("name", "Kimbra");
	 *</code>
	 **/
    public function set($property , $value ) {
		if ( $this->_initialized === false ) {
			this->initialize();
		}

		$this->_data[property] = value;
		this->_session->set(this->_name, $this->_data);
    }

    /***
	 * Magic setter to assign values to the session bag
	 *
	 *<code>
	 * $user->name = "Kimbra";
	 *</code>
	 **/
    public function __set($property , $value ) {
		this->set(property, value);
    }

    /***
	 * Obtains a value from the session bag optionally setting a default value
	 *
	 *<code>
	 * echo $user->get("name", "Kimbra");
	 *</code>
	 **/
    public function get($property , $defaultValue  = null ) {

		/**
		 * Check first if ( the bag is initialized
		 */
		if ( $this->_initialized === false ) {
			this->initialize();
		}

		/**
		 * Retrieve the data
		 */
		if ( fetch value, $this->_data[property] ) {
			return value;
		}

		return defaultValue;
    }

    /***
	 * Magic getter to obtain values from the session bag
	 *
	 *<code>
	 * echo $user->name;
	 *</code>
	 **/
    public function __get($property ) {
		return $this->get(property);
    }

    /***
	 * Check whether a property is defined in the internal bag
	 *
	 *<code>
	 * var_dump(
	 *     $user->has("name")
	 * );
	 *</code>
	 **/
    public function has($property ) {
		if ( $this->_initialized === false ) {
			this->initialize();
		}

		return isset $this->_data[property];
    }

    /***
	 * Magic isset to check whether a property is defined in the bag
	 *
	 *<code>
	 * var_dump(
	 *     isset($user["name"])
	 * );
	 *</code>
	 **/
    public function __isset($property ) {
		return $this->has(property);
    }

    /***
	 * Removes a property from the internal bag
	 *
	 *<code>
	 * $user->remove("name");
	 *</code>
	 **/
    public function remove($property ) {
		if ( $this->_initialized === false ) {
			this->initialize();
		}


		$data = $this->_data;
		if ( isset($data[property]) ) {
			unset data[property];
			this->_session->set(this->_name, data);
			$this->_data = data;
			return true;
		}

		return false;
    }

    /***
	 * Magic unset to remove items using the array syntax
	 *
	 *<code>
	 * unset($user["name"]);
	 *</code>
	 **/
    public function __unset($property ) {
		return $this->remove(property);
    }

    /***
	 * Return length of bag
	 *
	 *<code>
	 * echo $user->count();
	 *</code>
	 **/
    public final function count() {
		if ( $this->_initialized === false ) {
			this->initialize();
		}
		return count(this->_data);
    }

    /***
	 * Returns the bag iterator
	 **/
    public final function getIterator() {
		if ( $this->_initialized === false ) {
			this->initialize();
		}

		return new \ArrayIterator(this->_data);
    }

    public final function offsetSet($property , $value ) {
		return $this->set(property, value);
    }

    public final function offsetExists($property ) {
		return $this->has(property);
    }

    public final function offsetUnset($property ) {
		return $this->remove(property);
    }

    public final function offsetGet($property ) {
		return $this->get(property);
    }

}