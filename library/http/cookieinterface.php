<?php


namespace Phalcon\Http;



/***
 * Phalcon\Http\CookieInterface
 *
 * Interface for Phalcon\Http\Cookie
 **/

interface CookieInterface {

    /***
	 * Sets the cookie's value
	 *
	 * @param string value
	 * @return \Phalcon\Http\CookieInterface
	 **/
    public function setValue($value ); 

    /***
	 * Returns the cookie's value
	 *
	 * @param string|array filters
	 * @param string defaultValue
	 * @return mixed
	 **/
    public function getValue($filters  = null , $defaultValue  = null ); 

    /***
	 * Sends the cookie to the HTTP client
	 **/
    public function send(); 

    /***
	 * Deletes the cookie
	 **/
    public function delete(); 

    /***
	 * Sets if the cookie must be encrypted/decrypted automatically
	 **/
    public function useEncryption($useEncryption ); 

    /***
	 * Check if the cookie is using implicit encryption
	 **/
    public function isUsingEncryption(); 

    /***
	 * Sets the cookie's expiration time
	 **/
    public function setExpiration($expire ); 

    /***
	 * Returns the current expiration time
	 **/
    public function getExpiration(); 

    /***
	 * Sets the cookie's expiration time
	 **/
    public function setPath($path ); 

    /***
	 * Returns the current cookie's name
	 **/
    public function getName(); 

    /***
	 * Returns the current cookie's path
	 **/
    public function getPath(); 

    /***
	 * Sets the domain that the cookie is available to
	 **/
    public function setDomain($domain ); 

    /***
	 * Returns the domain that the cookie is available to
	 **/
    public function getDomain(); 

    /***
	 * Sets if the cookie must only be sent when the connection is secure (HTTPS)
	 **/
    public function setSecure($secure ); 

    /***
	 * Returns whether the cookie must only be sent when the connection is secure (HTTPS)
	 **/
    public function getSecure(); 

    /***
	 * Sets if the cookie is accessible only through the HTTP protocol
	 **/
    public function setHttpOnly($httpOnly ); 

    /***
	 * Returns if the cookie is accessible only through the HTTP protocol
	 **/
    public function getHttpOnly(); 

}