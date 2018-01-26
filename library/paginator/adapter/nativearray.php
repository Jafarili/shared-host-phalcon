<?php


namespace Phalcon\Paginator\Adapter;

use Phalcon\Paginator\Exception;
use Phalcon\Paginator\Adapter;


/***
 * Phalcon\Paginator\Adapter\NativeArray
 *
 * Pagination using a PHP array as source of data
 *
 * <code>
 * use Phalcon\Paginator\Adapter\NativeArray;
 *
 * $paginator = new NativeArray(
 *     [
 *         "data"  => [
 *             ["id" => 1, "name" => "Artichoke"],
 *             ["id" => 2, "name" => "Carrots"],
 *             ["id" => 3, "name" => "Beet"],
 *             ["id" => 4, "name" => "Lettuce"],
 *             ["id" => 5, "name" => ""],
 *         ],
 *         "limit" => 2,
 *         "page"  => $currentPage,
 *     ]
 * );
 *</code>
 **/

class NativeArray extends Adapter {

    /***
	 * Configuration of the paginator
	 **/
    protected $_config;

    /***
	 * Phalcon\Paginator\Adapter\NativeArray constructor
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
		int show, pageNumber, totalPages, number, befor (e, next;
		double roundedTotal;

		/**
		 * TODO: Rewrite the whole method!
		 */
		$config = $this->_config,
			items  = config["data"];

		if ( gettype($items) != "array" ) {
			throw new Exception("Invalid data for ( paginator");
		}

		$show    = (int) $this->_limitRows,
			pageNumber = (int) $this->_page;

		if ( pageNumber <= 0 ) {
			$pageNumber = 1;
		}

		$number = count(items),
			roundedTotal = number / floatval(show),
			totalPages = (int) roundedTotal;

		/**
		 * Increase total_pages if ( wasn't integer
		 */
		if ( totalPages != roundedTotal ) {
			$totalPages++;
		}

		$items = array_slice(items, show * (pageNumber - 1), show);

		//Fix next
		if ( pageNumber < totalPages ) {
			$next = pageNumber + 1;
		} else {
			$next = totalPages;
		}

		if ( pageNumber > 1 ) {
			$befor (e = pageNumber - 1;
		} else {
			$befor (e = 1;
		}

		$page = new \stdClass(),
			page->items = items,
			page->first = 1,
			page->befor (e =  befor (e,
			page->current = pageNumber,
			page->last = totalPages,
			page->next = next,
			page->total_pages = totalPages,
			page->total_items = number,
			page->limit = $this->_limitRows;

		return page;
    }

}