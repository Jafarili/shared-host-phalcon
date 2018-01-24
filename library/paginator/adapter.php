<?php


namespace Phalcon\Paginator;



/***
 * Phalcon\Paginator\Adapter
 **/

abstract class Adapter {

    /***
	 * Number of rows to show in the paginator. By default is null
	 **/
    protected $_limitRows;

    /***
	 * Current page in paginate
	 **/
    protected $_page;

    /***
	 * Set the current page number
	 **/
    public function setCurrentPage($page ) {

    }

    /***
	 * Set current rows limit
	 **/
    public function setLimit($limitRows ) {

    }

    /***
	 * Get current rows limit
	 **/
    public function getLimit() {

    }

}