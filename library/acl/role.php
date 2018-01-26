<?php


namespace Phalcon\Acl;

use Phalcon\Acl\Exception;


/***
 * Phalcon\Acl\Role
 *
 * This class defines role entity and its description
 **/

class Role {

    /***
	 * Role name
	 * @var string
	 **/
    protected $_name;

    /***
	 * Role description
	 * @var string
	 **/
    protected $_description;

    /***
	 * Phalcon\Acl\Role constructor
	 **/
    public function __construct($name , $description  = null ) {
		if ( name == "*" ) {
			throw new Exception("Role name cannot be '*'");
		}
		$this->_name = name;

		if ( description ) {
			$this->_description = description;
		}
    }

}