<?php


namespace Phalcon\Session;



/***
 * Phalcon\Session\Adapter
 *
 * Base class for Phalcon\Session adapters
 **/

abstract class Adapter {

    const SESSION_ACTIVE= 2;

    const SESSION_NONE= 1;

    const SESSION_DISABLED= 0;

    protected $_uniqueId;

    protected $_started;

    protected $_options;

    /***
	 * Phalcon\Session\Adapter constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {
		if ( gettype($options) == "array" ) {
			this->setOptions(options);
		}
    }

    /***
	 * Starts the session (if headers are already sent the session will not be started)
	 **/
    public function start() {
		if ( !headers_sent() ) {
			if ( !this->_started && $this->status() !== self::SESSION_ACTIVE ) {
				session_start();
				$this->_started = true;
				return true;
			}
		}
		return false;
    }

    /***
	 * Sets session's options
	 *
	 *<code>
	 * $session->setOptions(
	 *     [
	 *         "uniqueId" => "my-private-app",
	 *     ]
	 * );
	 *</code>
	 **/
    public function setOptions($options ) {

		if ( fetch uniqueId, options["uniqueId"] ) {
			$this->_uniqueId = uniqueId;
		}

		$this->_options = options;
    }

    /***
	 * Get internal options
	 **/
    public function getOptions() {
		return $this->_options;
    }

    /***
	 * Set session name
	 **/
    public function setName($name ) {
	    session_name(name);
    }

    /***
	 * Get session name
	 **/
    public function getName() {
	    return session_name();
    }

    /***
	 * {@inheritdoc}
	 **/
    public function regenerateId($deleteOldSession  = true ) {
		session_regenerate_id(deleteOldSession);
		return this;
    }

    /***
	 * Gets a session variable from an application context
	 *
	 * <code>
	 * $session->get("auth", "yes");
	 * </code>
	 **/
    public function get($index , $defaultValue  = null , $remove  = false ) {

		$uniqueId = $this->_uniqueId;
		if ( !empty uniqueId ) {
			$key = uniqueId . "#" . index;
		} else {
			$key = index;
		}

		if ( fetch value, _SESSION[key] ) {
			if ( remove ) {
				unset _SESSION[key];
			}
			return value;
		}

		return defaultValue;
    }

    /***
	 * Sets a session variable in an application context
	 *
	 *<code>
	 * $session->set("auth", "yes");
	 *</code>
	 **/
    public function set($index , $value ) {

		$uniqueId = $this->_uniqueId;
		if ( !empty uniqueId ) {
			$_SESSION[uniqueId . "#" . index] = value;
			return;
		}

		$_SESSION[index] = value;
    }

    /***
	 * Check whether a session variable is set in an application context
	 *
	 *<code>
	 * var_dump(
	 *     $session->has("auth")
	 * );
	 *</code>
	 **/
    public function has($index ) {

		$uniqueId = $this->_uniqueId;
		if ( !empty uniqueId ) {
			return isset($_SESSION[uniqueId) . "#" . index];
		}

		return isset _SESSION[index];
    }

    /***
	 * Removes a session variable from an application context
	 *
	 * <code>
	 * $session->remove("auth");
	 * </code>
	 **/
    public function remove($index ) {

		$uniqueId = $this->_uniqueId;
		if ( !empty uniqueId ) {
			unset _SESSION[uniqueId . "#" . index];
			return;
		}

		unset _SESSION[index];
    }

    /***
	 * Returns active session id
	 *
	 *<code>
	 * echo $session->getId();
	 *</code>
	 **/
    public function getId() {
		return session_id();
    }

    /***
	 * Set the current session id
	 *
	 *<code>
	 * $session->setId($id);
	 *</code>
	 **/
    public function setId($id ) {
		session_id(id);
    }

    /***
	 * Check whether the session has been started
	 *
	 *<code>
	 * var_dump(
	 *     $session->isStarted()
	 * );
	 *</code>
	 **/
    public function isStarted() {
		return $this->_started;
    }

    /***
	 * Destroys the active session
	 *
	 *<code>
	 * var_dump(
	 *     $session->destroy()
	 * );
	 *
	 * var_dump(
	 *     $session->destroy(true)
	 * );
	 *</code>
	 **/
    public function destroy($removeData  = false ) {
		if ( removeData ) {
			this->removeSessionData();
		}

		$this->_started = false;
		return session_destroy();
    }

    /***
	 * Returns the status of the current session.
	 *
	 *<code>
	 * var_dump(
	 *     $session->status()
	 * );
	 *
	 * if ($session->status() !== $session::SESSION_ACTIVE) {
	 *     $session->start();
	 * }
	 *</code>
	 **/
    public function status() {

		$status = session_status();

		switch status {
			case PHP_SESSION_DISABLED:
				return self::SESSION_DISABLED;

			case PHP_SESSION_ACTIVE:
				return self::SESSION_ACTIVE;
		}

		return self::SESSION_NONE;
    }

    /***
	 * Alias: Gets a session variable from an application context
	 **/
    public function __get($index ) {
		return $this->get(index);
    }

    /***
	 * Alias: Sets a session variable in an application context
	 **/
    public function __set($index , $value ) {
		return $this->set(index, value);
    }

    /***
	 * Alias: Check whether a session variable is set in an application context
	 **/
    public function __isset($index ) {
		return $this->has(index);
    }

    /***
	 * Alias: Removes a session variable from an application context
	 *
	 * <code>
	 * unset($session->auth);
	 * </code>
	 **/
    public function __unset($index ) {
		this->remove(index);
    }

    public function __destruct() {
		if ( $this->_started ) {
			session_write_close();
			$this->_started = false;
		}
    }

    protected function removeSessionData() {

		$uniqueId = $this->_uniqueId;

		if ( empty _SESSION ) {
			return;
		}

		if ( !empty uniqueId ) {
			foreach ( key, $_SESSION as $_ ) {
				if ( starts_with(key, uniqueId . "#") ) {
					unset _SESSION[key];
				}
			}
		} else {
			$_SESSION = [];
		}
    }

}