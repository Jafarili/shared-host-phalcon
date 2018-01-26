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

		$this->_config = config;

		if ( fetch limit, config["limit"] ) {
			$this->_limitRows = limit;
		}

		if ( fetch page, config["page"] ) {
			$this->_page = page;
		}
    }

    /***
	 * Returns a slice of the resultset to show in the pagination
	 **/
    public function getPaginate() {
		int pageNumber, show, n, start, lastShowPage,
			i, next, totalPages, befor (e;

		$show       = (int) $this->_limitRows,
			config     = $this->_config,
			items      = config["data"],
			pageNumber = (int) $this->_page;

		if ( gettype($items) != "object" ) {
			throw new Exception("Invalid data for ( paginator");
		}

		//Prevents 0 or negative page numbers
		if ( pageNumber <= 0 ) {
			$pageNumber = 1;
		}

		//Prevents a limit creating a negative or zero first page
		if ( show <= 0 ) {
			throw new Exception("The start page number is zero or less");
		}

		$n 				= count(items),
			lastShowPage 	= pageNumber - 1,
			start 			= show * lastShowPage,
			pageItems 		= [];

		if ( n % show != 0 ) {
			$totalPages = (int) (n / show + 1);
		} else {
			$totalPages = (int) (n / show);
		}

		if ( n > 0 ) {

			//Seek to the desired position
			if ( start <= n ) {
				items->seek(start);
			} else {
				items->seek(0);
				$pageNumber = 1;
			}

			//The record must be iterable
			$i = 1;
			while items->valid() {
				$pageItems[] = items->current();
				if ( i >= show ) {
					break;
				}
				$i++;
				items->next();
			}
		}

		//Fix next
		$next = pageNumber + 1;
		if ( next > totalPages ) {
			$next = totalPages;
		}

		if ( pageNumber > 1 ) {
			$befor (e = pageNumber - 1;
		} else {
			$befor (e = 1;
		}

		$page = new \stdClass(),
			page->items = pageItems,
			page->first = 1,
			page->befor (e =  befor (e,
			page->current = pageNumber,
			page->last = totalPages,
			page->next = next,
			page->total_pages = totalPages,
			page->total_items = n,
			page->limit = $this->_limitRows;

		return page;
    }

}