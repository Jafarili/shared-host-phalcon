<?php


namespace Phalcon\Paginator\Adapter;

use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Adapter;
use Phalcon\Paginator\Exception;
use Phalcon\Db;


/***
 * Phalcon\Paginator\Adapter\QueryBuilder
 *
 * Pagination using a PHQL query builder as source of data
 *
 * <code>
 * use Phalcon\Paginator\Adapter\QueryBuilder;
 *
 * $builder = $this->modelsManager->createBuilder()
 *                 ->columns("id, name")
 *                 ->from("Robots")
 *                 ->orderBy("name");
 *
 * $paginator = new QueryBuilder(
 *     [
 *         "builder" => $builder,
 *         "limit"   => 20,
 *         "page"    => 1,
 *     ]
 * );
 *</code>
 **/

class QueryBuilder extends Adapter {

    /***
	 * Configuration of paginator by model
	 **/
    protected $_config;

    /***
	 * Paginator's data
	 **/
    protected $_builder;

    /***
	 * Columns for count query if builder has having
	 **/
    protected $_columns;

    /***
	 * Phalcon\Paginator\Adapter\QueryBuilder
	 **/
    public function __construct($config ) {

    }

    /***
	 * Get the current page number
	 **/
    public function getCurrentPage() {

    }

    /***
	 * Set query builder object
	 **/
    public function setQueryBuilder($builder ) {

    }

    /***
	 * Get query builder object
	 **/
    public function getQueryBuilder() {

    }

    /***
	 * Returns a slice of the resultset to show in the pagination
	 **/
    public function getPaginate() {

    }

}