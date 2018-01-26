<?php


namespace Phalcon\Http\Response;

use Phalcon\DiInterface;
use Phalcon\Http\CookieInterface;
use Phalcon\Http\Response\CookiesInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Http\Cookie\Exception;


/***
 * Phalcon\Http\Response\Cookies
 *
 * This class is a bag to manage the cookies
 * A cookies bag is automatically registered as part of the 'response' service in the DI
 **/

class Cookies {

    protected $_dependencyInjector;

    protected $_registered;

    protected $_useEncryption;

    protected $_cookies;

    public function __construct() {
		$this->_cookies = [];
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
	 * Set if cookies in the bag must be automatically encrypted/decrypted
	 **/
    public function useEncryption($useEncryption ) {
		$this->_useEncryption = useEncryption;
		return this;
    }

    /***
	 * Returns if the bag is automatically encrypting/decrypting cookies
	 **/
    public function isUsingEncryption() {
		return $this->_useEncryption;
    }

    /***
	 * Sets a cookie to be sent at the end of the request
	 * This method overrides any cookie set before with the same name
	 **/
    public function set($name , $value  = null , $expire  = 0 , $path  = / , $secure  = null , $domain  = null , $httpOnly  = null ) {

		$encryption = $this->_useEncryption;

		/**
		 * Check if ( the cookie needs to be updated or
		 */
		if ( !fetch cookie, $this->_cookies[name] ) {
			$cookie =
				<CookieInterface> $this->_dependencyInjector->get("Phalcon\\Http\\Cookie",
				[name, value, expire, path, secure, domain, httpOnly]);

			/**
			 * Pass the DI to created cookies
			 */
			cookie->setDi(this->_dependencyInjector);

			/**
			 * Enable encryption in the cookie
			 */
			if ( encryption ) {
				cookie->useEncryption(encryption);
			}

			$this->_cookies[name] = cookie;

		} else {

			/**
			 * Override any settings in the cookie
			 */
			cookie->setValue(value);
			cookie->setExpiration(expire);
			cookie->setPath(path);
			cookie->setSecure(secure);
			cookie->setDomain(domain);
			cookie->setHttpOnly(httpOnly);
		}

		/**
		 * Register the cookies bag in the response
		 */
		if ( $this->_registered === false ) {

			$dependencyInjector = $this->_dependencyInjector;
			if ( gettype($dependencyInjector) != "object" ) {
				throw new Exception("A dependency injection object is required to access the 'response' service");
			}

			$response = dependencyInjector->getShared("response");

			/**
			 * Pass the cookies bag to the response so it can send the headers at the of the request
			 */
			response->setCookies(this);

			$this->_registered = true;
		}

		return this;
    }

    /***
	 * Gets a cookie from the bag
	 **/
    public function get($name ) {

		/**
		 * Gets cookie from the cookies service. They will be sent with response.
		 */
		if ( fetch cookie, $this->_cookies[name] ) {
			return cookie;
		}

		/**
		 * Create the cookie if ( the it does not exist.
		 * It's value come from $_COOKIE with request, so it shouldn't be saved
		 * to _cookies property, otherwise it will always be resent after get.
		 */
		$cookie = <CookieInterface> $this->_dependencyInjector->get("Phalcon\\Http\\Cookie", [name]),
			dependencyInjector = $this->_dependencyInjector;

		if ( gettype($dependencyInjector) == "object" ) {

			/**
			 * Pass the DI to created cookies
			 */
			cookie->setDi(dependencyInjector);

			$encryption = $this->_useEncryption;

			/**
			 * Enable encryption in the cookie
			 */
			if ( encryption ) {
				cookie->useEncryption(encryption);
			}
		}

		return cookie;
    }

    /***
	 * Check if a cookie is defined in the bag or exists in the _COOKIE superglobal
	 **/
    public function has($name ) {
		if ( isset($this->_cookies[name]) ) {
			return true;
		}

		/**
		 * Check the superglobal
		 */
		if ( isset($_COOKIE[name]) ) {
			return true;
		}

		return false;
    }

    /***
	 * Deletes a cookie by its name
	 * This method does not removes cookies from the _COOKIE superglobal
	 **/
    public function delete($name ) {

		/**
		 * Check the internal bag
		 */
		if ( fetch cookie, $this->_cookies[name] ) {
			cookie->delete();
			return true;
		}

		return false;
    }

    /***
	 * Sends the cookies to the client
	 * Cookies aren't sent if headers are sent in the current request
	 **/
    public function send() {

		if ( !headers_sent() ) {
			foreach ( $this->_cookies as $cookie ) {
				cookie->send();
			}

			return true;
		}

		return false;
    }

    /***
	 * Reset set cookies
	 **/
    public function reset() {
		$this->_cookies = [];
		return this;
    }

}