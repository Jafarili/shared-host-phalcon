<?php


namespace Phalcon\Paginator;



/***
 * Phalcon\Paginator\AdapterInterface
 *
 * Interface for Phalcon\Paginator adapters
 **/

interface AdapterInterface {

    /***
	 * Set the current page number
	 **/
    public function setCurrentPage($page ); 

    /***
	 * Returns a slice of the resultset to show in the pagination
	 **/
    public function getPaginate(); 

    /***
	 * Set current rows limit
	 **/
    public function setLimit($limit ); 

    /***
	 * Get current rows limit
	 **/
    public function getLimit(); 

}