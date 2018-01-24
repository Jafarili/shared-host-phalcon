<?php


namespace Phalcon\Filter;



/***
 * Phalcon\Filter\UserFilterInterface
 *
 * Interface for Phalcon\Filter user-filters
 **/

interface UserFilterInterface {

    /***
	 * Filters a value
	 **/
    public function filter($value ); 

}