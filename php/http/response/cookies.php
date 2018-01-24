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

    }

    /***
	 * Sets the dependency injector
	 **/
    public function setDI($dependencyInjector ) {

    }

    /***
	 * Returns the internal dependency injector
	 **/
    public function getDI() {

    }

    /***
	 * Set if cookies in the bag must be automatically encrypted/decrypted
	 **/
    public function useEncryption($useEncryption ) {

    }

    /***
	 * Returns if the bag is automatically encrypting/decrypting cookies
	 **/
    public function isUsingEncryption() {

    }

    /***
	 * Sets a cookie to be sent at the end of the request
	 * This method overrides any cookie set before with the same name
	 **/
    public function set($name , $value  = null , $expire  = 0 , $path  = / , $secure  = null , $domain  = null , $httpOnly  = null ) {

    }

    /***
	 * Gets a cookie from the bag
	 **/
    public function get($name ) {

    }

    /***
	 * Check if a cookie is defined in the bag or exists in the _COOKIE superglobal
	 **/
    public function has($name ) {

    }

    /***
	 * Deletes a cookie by its name
	 * This method does not removes cookies from the _COOKIE superglobal
	 **/
    public function delete($name ) {

    }

    /***
	 * Sends the cookies to the client
	 * Cookies aren't sent if headers are sent in the current request
	 **/
    public function send() {

    }

    /***
	 * Reset set cookies
	 **/
    public function reset() {

    }

}