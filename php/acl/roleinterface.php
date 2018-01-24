<?php


namespace Phalcon\Acl;



/***
 * Phalcon\Acl\RoleInterface
 *
 * Interface for Phalcon\Acl\Role
 **/

interface RoleInterface {

    /***
	 * Returns the role name
	 **/
    public function getName(); 

    /***
	 * Returns role description
	 **/
    public function getDescription(); 

    /***
	 * Magic method __toString
	 **/
    public function __toString(); 

}