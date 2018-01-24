<?php


namespace Phalcon\Paginator\Adapter;

use Phalcon\Paginator\Exception;
use Phalcon\Paginator\Adapter;


/***
 * Phalcon\Paginator\Adapter\Model
 *
 * This adapter allows to paginate data using a Phalcon\Mvc\Model resultset as a base.
 *
 * <code>
 * use Phalcon\Paginator\Adapter\Model;
 *
 * $paginator = new Model(
 *     [
 *         "data"  => Robots::find(),
 *         "limit" => 25,
 *         "page"  => $currentPage,
 *     ]
 * );
 *
 * $paginate = $paginator->getPaginate();
 *</code>
 **/

class Model extends Adapter {

    /***
	 * Configuration of paginator by model
	 **/
    protected $_config;

    /***
	 * Phalcon\Paginator\Adapter\Model constructor
	 **/
    public function __construct($config ) {

    }

    /***
	 * Returns a slice of the resultset to show in the pagination
	 **/
    public function getPaginate() {

    }

}