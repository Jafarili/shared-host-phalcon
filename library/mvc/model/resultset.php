<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Db;
use Phalcon\Mvc\Model;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\MessageInterface;
use Phalcon\Mvc\Model\ResultsetInterface;


/***
 * Phalcon\Mvc\Model\Resultset
 *
 * This component allows to Phalcon\Mvc\Model returns large resultsets with the minimum memory consumption
 * Resultsets can be traversed using a standard foreach or a while statement. If a resultset is serialized
 * it will dump all the rows into a big array. Then unserialize will retrieve the rows as they were before
 * serializing.
 *
 * <code>
 *
 * // Using a standard foreach
 * $robots = Robots::find(
 *     [
 *         "type = 'virtual'",
 *         "order" => "name",
 *     ]
 * );
 *
 * foreach ($robots as robot) {
 *     echo robot->name, "\n";
 * }
 *
 * // Using a while
 * $robots = Robots::find(
 *     [
 *         "type = 'virtual'",
 *         "order" => "name",
 *     ]
 * );
 *
 * $robots->rewind();
 *
 * while ($robots->valid()) {
 *     $robot = $robots->current();
 *
 *     echo $robot->name, "\n";
 *
 *     $robots->next();
 * }
 * </code>
 **/

abstract class Resultset {

    const TYPE_RESULT_FULL= 0;

    const TYPE_RESULT_PARTIAL= 1;

    const HYDRATE_RECORDS= 0;

    const HYDRATE_OBJECTS= 2;

    const HYDRATE_ARRAYS= 1;

    /***
	 * Phalcon\Db\ResultInterface or false for empty resultset
	 **/
    protected $_result;

    protected $_cache;

    protected $_isFresh;

    protected $_pointer;

    protected $_count;

    protected $_activeRow;

    protected $_rows;

    protected $_row;

    protected $_errorMessages;

    protected $_hydrateMode;

    /***
	 * Phalcon\Mvc\Model\Resultset constructor
	 *
	 * @param \Phalcon\Db\ResultInterface|false result
	 * @param \Phalcon\Cache\BackendInterface cache
	 **/
    public function __construct($result , $cache  = null ) {

		/**
		 * 'false' is given as result for ( empty result-sets
		 */
		if ( gettype($result) != "object" ) {
			$this->_count = 0;
			$this->_rows = [];
			return;
		}

		/**
		 * Valid resultsets are Phalcon\Db\ResultInterface instances
		 */
		$this->_result = result;

		/**
		 * Update the related cache if ( any
		 */
		if ( cache !== null ) {
			$this->_cache = cache;
		}

		/**
		 * Do the fetch using only associative indexes
		 */
		result->setFetchMode(Db::FETCH_ASSOC);

		/**
		 * Update the row-count
		 */
		$rowCount = result->numRows();
		$this->_count = rowCount;

		/**
		 * Empty result-set
		 */
		if ( rowCount == 0 ) {
			$this->_rows = [];
			return;
		}

		/**
		 * Small result-sets with less equals 32 rows are fetched at once
		 */
		if ( rowCount <= 32 ) {
			/**
			 * Fetch ALL rows from database
			 */
			$rows = result->fetchAll();
			if ( gettype($rows) == "array" ) {
				$this->_rows = rows;
			} else {
				$this->_rows = [];
			}
		}
    }

    /***
	 * Moves cursor to next row in the resultset
	 **/
    public function next() {
		this->seek(this->_pointer + 1);
    }

    /***
	 * Check whether internal resource has rows to fetch
	 **/
    public function valid() {
		return $this->_pointer < $this->_count;
    }

    /***
	 * Gets pointer number of active row in the resultset
	 **/
    public function key() {
		if ( $this->_pointer >= $this->_count ) {
			return null;
		}

		return $this->_pointer;
    }

    /***
	 * Rewinds resultset to its beginning
	 **/
    public final function rewind() {
		this->seek(0);
    }

    /***
	 * Changes the internal pointer to a specific position in the resultset.
	 * Set the new position if required, and then set this->_row
	 **/
    public final function seek($position ) {

		if ( $this->_pointer != position || $this->_row === null ) {
			if ( gettype($this->_rows) == "array" ) {
				/**
				* All rows are in memory
				*/
				if ( fetch row, $this->_rows[position] ) {
					$this->_row = row;
				}

				$this->_pointer = position;
				$this->_activeRow = null;
				return;
			}

			/**
			* Fetch from PDO one-by-one.
			*/
			$result = $this->_result;
			if ( $this->_row === null && $this->_pointer === 0 ) {
				/**
				 * Fresh result-set: Query was already executed in model\query::_executeSelect()
				 * The first row is available with fetch
				 */
				$this->_row = result->$fetch();
			}

			if ( $this->_pointer > position ) {
				/**
				* Current pointer is ahead requested position: e.g. request a previous row
				* It is not possible to rewind. Re-execute query with dataSeek
				*/
				result->dataSeek(position);
				$this->_row = result->$fetch();
				$this->_pointer = position;
			}

			while $this->_pointer < position {
				/**
				* Requested position is greater than current pointer,
				* seek for (ward until the requested position is reached.
				* We do not need to re-execute the query!
				*/
				$this->_row = result->$fetch();
				$this->_pointer++;
			}

			$this->_pointer = position;
			$this->_activeRow = null;
		}
    }

    /***
	 * Counts how many rows are in the resultset
	 **/
    public final function count() {
		return $this->_count;
    }

    /***
	 * Checks whether offset exists in the resultset
	 **/
    public function offsetExists($index ) {
		return index < $this->_count;
    }

    /***
	 * Gets row in a specific position of the resultset
	 **/
    public function offsetGet($index ) {
		if ( index < $this->_count ) {
	   		/**
	   		 * Move the cursor to the specif (ic position
	   		 */
			this->seek(index);

			return $this->{"current"}();

		}
		throw new Exception("The index does not exist in the cursor");
    }

    /***
	 * Resultsets cannot be changed. It has only been implemented to meet the definition of the ArrayAccess interface
	 *
	 * @param int index
	 * @param \Phalcon\Mvc\ModelInterface value
	 **/
    public function offsetSet($index , $value ) {
		throw new Exception("Cursor is an immutable ArrayAccess object");
    }

    /***
	 * Resultsets cannot be changed. It has only been implemented to meet the definition of the ArrayAccess interface
	 **/
    public function offsetUnset($offset ) {
		throw new Exception("Cursor is an immutable ArrayAccess object");
    }

    /***
	 * Returns the internal type of data retrieval that the resultset is using
	 **/
    public function getType() {
		return gettype($this->_rows) == "array" ? self::TYPE_RESULT_FULL : self::TYPE_RESULT_PARTIAL;
    }

    /***
	 * Get first row in the resultset
	 **/
    public function getFirst() {
		if ( $this->_count == 0 ) {
			return false;
		}

		this->seek(0);
		return $this->{"current"}();
    }

    /***
	 * Get last row in the resultset
	 **/
    public function getLast() {
		$count = $this->_count;
		if ( count == 0 ) {
			return false;
		}

		this->seek(count - 1);
		return $this->{"current"}();
    }

    /***
	 * Set if the resultset is fresh or an old one cached
	 **/
    public function setIsFresh($isFresh ) {
		$this->_isFresh = isFresh;
		return this;
    }

    /***
	 * Tell if the resultset if fresh or an old one cached
	 **/
    public function isFresh() {
		return $this->_isFresh;
    }

    /***
	 * Sets the hydration mode in the resultset
	 **/
    public function setHydrateMode($hydrateMode ) {
		$this->_hydrateMode = hydrateMode;
		return this;
    }

    /***
	 * Returns the current hydration mode
	 **/
    public function getHydrateMode() {
		return $this->_hydrateMode;
    }

    /***
	 * Returns the associated cache for the resultset
	 **/
    public function getCache() {
		return $this->_cache;
    }

    /***
	 * Returns the error messages produced by a batch operation
	 **/
    public function getMessages() {
		return $this->_errorMessages;
    }

    /***
	 * Updates every record in the resultset
	 *
	 * @param array data
	 * @param \Closure conditionCallback
	 * @return boolean
	 **/
    public function update($data , $conditionCallback  = null ) {
		boolean transaction;

		$transaction = false;

		this->rewind();

		while $this->valid() {

			$record = $this->current();

			if ( transaction === false ) {

				/**
				 * We only can update resultsets if ( every element is a complete object
				 */
				if ( !method_exists(record, "getWriteConnection") ) {
					throw new Exception("The returned record is not valid");
				}

				$connection = record->getWriteConnection(),
					transaction = true;

				connection->begin();
			}

			/**
			 * Perfor (m additional validations
			 */
			if ( gettype($conditionCallback) == "object" ) {
				if ( call_user_func_array(conditionCallback, [record]) === false ) {
					this->next();
					continue;
				}
			}

			/**
			 * Try to update the record
			 */
			if ( !record->save(data) ) {

				/**
				 * Get the messages from the record that produce the error
				 */
				$this->_errorMessages = record->getMessages();

				/**
				 * Rollback the transaction
				 */
				connection->rollback();
				$transaction = false;
				break;
			}

			this->next();
		}

		/**
		 * Commit the transaction
		 */
		if ( transaction === true ) {
			connection->commit();
		}

		return true;
    }

    /***
	 * Deletes every record in the resultset
	 **/
    public function delete($conditionCallback  = null ) {
		boolean result, transaction;

		$result = true;
		$transaction = false;

		this->rewind();

		while $this->valid() {

			$record = $this->current();

			if ( transaction === false ) {

				/**
				 * We only can delete resultsets if ( every element is a complete object
				 */
				if ( !method_exists(record, "getWriteConnection") ) {
					throw new Exception("The returned record is not valid");
				}

				$connection = record->getWriteConnection(),
					transaction = true;

				connection->begin();
			}

			/**
			 * Perfor (m additional validations
			 */
			if ( gettype($conditionCallback) == "object" ) {
				if ( call_user_func_array(conditionCallback, [record]) === false ) {
					this->next();
					continue;
				}
			}

			/**
			 * Try to delete the record
			 */
			if ( !record->delete() ) {

				/**
				 * Get the messages from the record that produce the error
				 */
				$this->_errorMessages = record->getMessages();

				/**
				 * Rollback the transaction
				 */
				connection->rollback();
				$result = false;
				$transaction = false;
				break;
			}

			this->next();
		}

		/**
		 * Commit the transaction
		 */
		if ( transaction === true ) {
			connection->commit();
		}

		return result;
    }

    /***
	 * Filters a resultset returning only those the developer requires
	 *
	 *<code>
	 * $filtered = $robots->filter(
	 *     function ($robot) {
	 *         if ($robot->id < 3) {
	 *             return $robot;
	 *         }
	 *     }
	 * );
	 *</code>
	 *
	 * @param callback filter
	 * @return \Phalcon\Mvc\Model[]
	 **/
    public function filter($filter ) {

		$records = [],
			parameters = [];

		this->rewind();

		while $this->valid() {

			$record = $this->current();

			$parameters[0] = record,
				processedRecord = call_user_func_array(filter, parameters);

			/**
			 * Only add processed records to 'records' if ( the returned value is an array/object
			 */
			if ( gettype($processedRecord) != "object" && gettype($processedRecord) != "array" ) {
				this->next();
				continue;
			}

			$records[] = processedRecord;
			this->next();
		}

		return records;
    }

    /***
     * Returns serialised model objects as array for json_encode.
	 * Calls jsonSerialize on each object if present
     *
     *<code>
     * $robots = Robots::find();
     * echo json_encode($robots);
     *</code>
     *
     * @return array
     **/
    public function jsonSerialize() {
        var records, current;
        $records = [];

		this->rewind();

		while $this->valid() {
			$current = $this->current();

        	if ( gettype($current) == "object" && method_exists(current, "jsonSerialize") ) {
        		$records[] = current->{"jsonSerialize"}();
        	} else {
        	    $records[] = current;
        	}

			this->next();
        }

        return records;
    }

}