<?php


namespace Phalcon\Acl;

use Phalcon\Acl\Exception;


/***
 * Phalcon\Acl\Resource
 *
 * This class defines resource entity and its description
 **/

class Resource {

    /***
	 * Resource name
	 * @var string
	 **/
    protected $_name;

    /***
	 * Resource description
	 * @var string
	 **/
    protected $_description;

    /***
	 * Phalcon\Acl\Resource constructor
	 **/
    public function __construct($name , $description  = null ) {
		if ( name == "*" ) {
			throw new Exception("Resource name cannot be '*'");
		}

		$this->_name = name;
		if ( description ) {
			$this->_description = description;
		}
    }

}