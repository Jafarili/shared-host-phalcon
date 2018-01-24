<?php


namespace Phalcon;

use Phalcon\DiInterface;
use Phalcon\Security\Random;
use Phalcon\Security\Exception;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Session\AdapterInterface as SessionInterface;


/***
 * Phalcon\Security
 *
 * This component provides a set of functions to improve the security in Phalcon applications
 *
 *<code>
 * $login    = $this->request->getPost("login");
 * $password = $this->request->getPost("password");
 *
 * $user = Users::findFirstByLogin($login);
 *
 * if ($user) {
 *     if ($this->security->checkHash($password, $user->password)) {
 *         // The password is valid
 *     }
 * }
 *</code>
 **/

class Security {

    const CRYPT_DEFAULT= 0;;

    const CRYPT_STD_DES= 1;;

    const CRYPT_EXT_DES= 2;;

    const CRYPT_MD5= 3;;

    const CRYPT_BLOWFISH= 4;;

    const CRYPT_BLOWFISH_A= 5;;

    const CRYPT_BLOWFISH_X= 6;;

    const CRYPT_BLOWFISH_Y= 7;;

    const CRYPT_SHA256= 8;;

    const CRYPT_SHA512= 9;;

    protected $_dependencyInjector;

    protected $_workFactor;

    protected $_numberBytes;

    protected $_tokenKeySessionID;

    protected $_tokenValueSessionID;

    protected $_token;

    protected $_tokenKey;

    protected $_random;

    protected $_defaultHash;

    /***
	 * Phalcon\Security constructor
	 **/
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
	 * Sets a number of bytes to be generated by the openssl pseudo random generator
	 **/
    public function setRandomBytes($randomBytes ) {

    }

    /***
	 * Returns a number of bytes to be generated by the openssl pseudo random generator
	 **/
    public function getRandomBytes() {

    }

    /***
	 * Returns a secure random number generator instance
	 **/
    public function getRandom() {

    }

    /***
	 * Generate a >22-length pseudo random string to be used as salt for passwords
	 **/
    public function getSaltBytes($numberBytes  = 0 ) {

    }

    /***
	 * Creates a password hash using bcrypt with a pseudo random salt
	 **/
    public function hash($password , $workFactor  = 0 ) {

    }

    /***
	 * Checks a plain text password and its hash version to check if the password matches
	 **/
    public function checkHash($password , $passwordHash , $maxPassLength  = 0 ) {

    }

    /***
	 * Checks if a password hash is a valid bcrypt's hash
	 **/
    public function isLegacyHash($passwordHash ) {

    }

    /***
	 * Generates a pseudo random token key to be used as input's name in a CSRF check
	 **/
    public function getTokenKey() {

    }

    /***
	 * Generates a pseudo random token value to be used as input's value in a CSRF check
	 **/
    public function getToken() {

    }

    /***
	 * Check if the CSRF token sent in the request is the same that the current in session
	 **/
    public function checkToken($tokenKey  = null , $tokenValue  = null , $destroyIfValid  = true ) {

    }

    /***
	 * Returns the value of the CSRF token in session
	 **/
    public function getSessionToken() {

    }

    /***
	 * Removes the value of the CSRF token and key from session
	 **/
    public function destroyToken() {

    }

    /***
	 * Computes a HMAC
	 **/
    public function computeHmac($data , $key , $algo , $raw  = false ) {

    }

    /***
 	 * Sets the default hash
 	 **/
    public function setDefaultHash($defaultHash ) {

    }

    /***
 	 * Returns the default hash
 	 **/
    public function getDefaultHash() {

    }

    /***
	 * Testing for LibreSSL
	 *
	 * @deprecated Will be removed in 4.0.0
	 **/
    public function hasLibreSsl() {

    }

    /***
	 * Getting OpenSSL or LibreSSL version.
	 *
	 * Parse OPENSSL_VERSION_TEXT because OPENSSL_VERSION_NUMBER is no use for LibreSSL.
	 * This constant show not the current system openssl library version but version PHP was compiled with.
	 *
	 * @deprecated Will be removed in 4.0.0
	 * @link https://bugs.php.net/bug.php?id=71143
	 *
	 * <code>
	 * if ($security->getSslVersionNumber() >= 20105) {
	 *     // ...
	 * }
	 * </code>
	 **/
    public function getSslVersionNumber() {

    }

}