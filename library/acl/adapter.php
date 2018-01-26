<?php


namespace Phalcon\Acl;

use Phalcon\Events\ManagerInterface;
use Phalcon\Events\EventsAwareInterface;


/***
 * Phalcon\Acl\Adapter
 *
 * Adapter for Phalcon\Acl adapters
 **/

abstract class Adapter {

    /***
	 * Events manager
	 * @var mixed
	 **/
    protected $_eventsManager;

    /***
	 * Default access
	 * @var bool
	 **/
    protected $_defaultAccess;

    /***
	 * Access Granted
	 * @var bool
	 **/
    protected $_accessGranted;

    /***
	 * Role which the list is checking if it's allowed to certain resource/access
	 * @var mixed
	 **/
    protected $_activeRole;

    /***
	 * Resource which the list is checking if some role can access it
	 * @var mixed
	 **/
    protected $_activeResource;

    /***
	 * Active access which the list is checking if some role can access it
	 * @var mixed
	 **/
    protected $_activeAccess;

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
	 * Sets the default access level (Phalcon\Acl::ALLOW or Phalcon\Acl::DENY)
	 **/
    public function setDefaultAction($defaultAccess ) {
		$this->_defaultAccess = defaultAccess;
    }

    /***
	 * Returns the default ACL access level
	 **/
    public function getDefaultAction() {
		return $this->_defaultAccess;
    }

}