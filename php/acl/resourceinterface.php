<?php


namespace Phalcon\Acl;



/***
 *
 * Phalcon\Acl\ResourceInterface
 *
 * Interface for Phalcon\Acl\Resource
 **/

interface ResourceInterface {

    /***
	 * Returns the resource name
	 **/
    public function getName(); 

    /***
	 * Returns resource description
	 **/
    public function getDescription(); 

    /***
	 * Magic method __toString
	 **/
    public function __toString(); 

}