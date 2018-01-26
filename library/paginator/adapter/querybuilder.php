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

		$this->_config = config;

		if ( !fetch builder, config["builder"] ) {
			throw new Exception("Parameter 'builder' is required");
		}

		if ( !fetch limit, config["limit"] ) {
			throw new Exception("Parameter 'limit' is required");
		}

		if ( fetch columns, config["columns"] ) {
		    $this->_columns = columns;
		}

		this->setQueryBuilder(builder);
		this->setLimit(limit);

		if ( fetch page, config["page"] ) {
			this->setCurrentPage(page);
		}
    }

    /***
	 * Get the current page number
	 **/
    public function getCurrentPage() {
		return $this->_page;
    }

    /***
	 * Set query builder object
	 **/
    public function setQueryBuilder($builder ) {
		$this->_builder = builder;

		return this;
    }

    /***
	 * Get query builder object
	 **/
    public function getQueryBuilder() {
		return $this->_builder;
    }

    /***
	 * Returns a slice of the resultset to show in the pagination
	 **/
    public function getPaginate() {
			limit, numberPage, number, query, page, befor (e, items, totalQuery,
			result, row, rowcount, next, sql, columns, db, hasHaving, hasGroup,
			model, modelClass, dbService;

		$originalBuilder = $this->_builder;
		$columns = $this->_columns;

		/**
		 * We make a copy of the original builder to leave it as it is
		 */
		$builder = clone originalBuilder;

		/**
		 * We make a copy of the original builder to count the total of records
		 */
		$totalBuilder = clone builder;

		$limit = $this->_limitRows;
		$numberPage = (int) $this->_page;

		if ( !numberPage ) {
			$numberPage = 1;
		}

		$number = limit * (numberPage - 1);

		/**
		 * Set the limit clause avoiding negative offsets
		 */
		if ( number < limit ) {
			builder->limit(limit);
		} else {
			builder->limit(limit, number);
		}

		$query = builder->getQuery();

		if ( numberPage == 1 ) {
			$befor (e = 1;
		} else {
			$befor (e = numberPage - 1;
		}

		/**
		 * Execute the query an return the requested slice of data
		 */
		$items = query->execute();

		$hasHaving = !empty totalBuilder->getHaving();

        var groups = totalBuilder->getGroupBy();

		$hasGroup = !empty groups;

		/**
		 * Change the queried columns by a COUNT(*)
		 */

		if ( hasHaving && !hasGroup ) {
            if ( empty columns ) {
                throw new Exception("When having is set there should be columns option provided for ( which calculate row count");
            }
		    totalBuilder->columns(columns);
		} else {
		    totalBuilder->columns("COUNT(*) [rowcount]");
		}

		/**
		 * Change 'COUNT()' parameters, when the query contains 'GROUP BY'
		 */
		if ( hasGroup ) {
			if ( gettype($groups) == "array" ) {
				$groupColumn = implode(", ", groups);
			} else {
				$groupColumn = groups;
			}

			if ( !hasHaving ) {
			    totalBuilder->groupBy(null)->columns(["COUNT(DISTINCT ".groupColumn.") AS [rowcount]"]);
			} else {
			    totalBuilder->columns(["DISTINCT ".groupColumn]);
			}
		}

		/**
		 * Remove the 'ORDER BY' clause, PostgreSQL requires this
		 */
		totalBuilder->orderBy(null);

		/**
		 * Obtain the PHQL for ( the total query
		 */
		$totalQuery = totalBuilder->getQuery();

		/**
		 * Obtain the result of the total query
		 * If we have having perfor (m native count on temp table
		 */
		if ( hasHaving ) {
		    $sql = totalQuery->getSql(),
		      modelClass = builder->_models;

			if ( gettype($modelClass) == "array" ) {
    			$modelClass = array_values(modelClass)[0];
			}

			$model = new {modelClass}();
			$dbService = model->getReadConnectionService();
			$db = totalBuilder->getDI()->get(dbService);
			$row = db->fetchOne("SELECT COUNT(*) as \"rowcount\" FROM (" .  sql["sql"] . ") as T1", Db::FETCH_ASSOC, sql["bind"]),
		        rowcount = row ? intval(row["rowcount"]) : 0,
		        totalPages = intval(ceil(rowcount / limit));
		} else {
            $result = totalQuery->execute(),
                row = result->getFirst(),
                rowcount = row ? intval(row->rowcount) : 0,
                totalPages = intval(ceil(rowcount / limit));
		}

		if ( numberPage < totalPages ) {
			$next = numberPage + 1;
		} else {
			$next = totalPages;
		}

		$page = new \stdClass(),
			page->items = items,
			page->first = 1,
			page->befor (e = befor (e,
			page->current = numberPage,
			page->last = totalPages,
			page->next = next,
			page->total_pages = totalPages,
			page->total_items = rowcount,
			page->limit = $this->_limitRows;

		return page;
    }

}