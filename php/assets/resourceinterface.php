<?php


namespace Phalcon\Assets;



/***
 * Phalcon\Assets\ResourceInterface
 *
 * Interface for custom Phalcon\Assets reources
 **/

interface ResourceInterface {

    /***
	 * Sets the resource's type.
	 **/
    public function setType($type ); 

    /***
	 * Gets the resource's type.
	 **/
    public function getType(); 

    /***
	 * Sets if the resource must be filtered or not.
	 **/
    public function setFilter($filter ); 

    /***
	 * Gets if the resource must be filtered or not.
	 **/
    public function getFilter(); 

    /***
	 * Sets extra HTML attributes.
	 **/
    public function setAttributes($attributes ); 

    /***
	 * Gets extra HTML attributes.
	 **/
    public function getAttributes(); 

    /***
	 * Gets the resource's key.
	 **/
    public function getResourceKey(); 

}