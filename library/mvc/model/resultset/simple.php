<?php


namespace Phalcon\Mvc\Model\Resultset;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Cache\BackendInterface;


/***
 * Phalcon\Mvc\Model\Resultset\Simple
 *
 * Simple resultsets only contains a complete objects
 * This class builds every complete object as it is required
 **/

class Simple extends Resultset {

    protected $_model;

    protected $_columnMap;

    protected $_keepSnapshots;

    /***
	 * Phalcon\Mvc\Model\Resultset\Simple constructor
	 *
	 * @param array columnMap
	 * @param \Phalcon\Mvc\ModelInterface|Phalcon\Mvc\Model\Row model
	 * @param \Phalcon\Db\Result\Pdo|null result
	 * @param \Phalcon\Cache\BackendInterface cache
	 * @param boolean keepSnapshots
	 **/
    public function __construct($columnMap , $model , $result , $cache  = null , $keepSnapshots  = null ) {
		$this->_model = model,
			this->_columnMap = columnMap;

		/**
		 * Set if ( the returned resultset must keep the record snapshots
		 */
		$this->_keepSnapshots = keepSnapshots;

		parent::__construct(result, cache);
    }

    /***
	 * Returns current row in the resultset
	 **/
    public final function current() {

		$activeRow = $this->_activeRow;
		if ( activeRow !== null ) {
			return activeRow;
		}

		/**
		 * Current row is set by seek() operations
		 */
		$row = $this->_row;

		/**
		 * Valid records are arrays
		 */
		if ( gettype($row) != "array" ) {
			$this->_activeRow = false;
			return false;
		}

		/**
		 * Get current hydration mode
		 */
		$hydrateMode = $this->_hydrateMode;

		/**
		 * Get the resultset column map
		 */
		$columnMap = $this->_columnMap;

		/**
		 * Hydrate based on the current hydration
		 */
		switch hydrateMode {

			case Resultset::HYDRATE_RECORDS:

				/**
				 * Set records as dirty state PERSISTENT by default
				 * Perfor (ms the standard hydration based on objects
				 */
				if ( globals_get("orm.late_state_binding") ) {

					if ( $this->_model instanceof \Phalcon\Mvc\Model ) {
						$modelName = get_class(this->_model);
					} else {
						$modelName = "Phalcon\\Mvc\\Model";
					}

					$activeRow = {modelName}::cloneResultMap(
						this->_model,
						row,
						columnMap,
						Model::DIRTY_STATE_PERSISTENT,
						this->_keepSnapshots
					);
				} else {
					$activeRow = Model::cloneResultMap(
						this->_model,
						row,
						columnMap,
						Model::DIRTY_STATE_PERSISTENT,
						this->_keepSnapshots
					);
				}
				break;

			default:
				/**
				 * Other kinds of hydrations
				 */
				$activeRow = Model::cloneResultMapHydrate(row, columnMap, hydrateMode);
				break;
		}

		$this->_activeRow = activeRow;
		return activeRow;
    }

    /***
	 * Returns a complete resultset as an array, if the resultset has a big number of rows
	 * it could consume more memory than currently it does. Export the resultset to an array
	 * couldn't be faster with a large number of records
	 **/
    public function toArray($renameColumns  = true ) {
			key, value, renamedRecords, columnMap;

		/**
		 * If _rows is not present, fetchAll from database
		 * and keep $memory as $them foreach ( further operations
		 */
		$records = $this->_rows;
		if ( gettype($records) != "array" ) {
			$result = $this->_result;
			if ( $this->_row !== null ) {
				// re-execute query if ( required and fetchAll rows
				result->execute();
			}
			$records = result->fetchAll();
			$this->_row = null;
			$this->_rows = records; // keep result-set in memory
		}

		/**
		 * We need to rename the whole set here, this could be slow
		 */
		if ( renameColumns ) {

			/**
			 * Get the resultset column map
			 */
			$columnMap = $this->_columnMap;
			if ( gettype($columnMap) != "array" ) {
				return records;
			}

			$renamedRecords = [];
			if ( gettype($records) == "array" ) {

				foreach ( $records as $record ) {

					$renamed = [];
					foreach ( key, $record as $value ) {

						/**
						 * Check if ( the key is part of the column map
						 */
						if ( !fetch renamedKey, columnMap[key] ) {
							throw new Exception("Column '" . key . "' is not part of the column map");
						}

						if ( gettype($renamedKey) == "array" ) {

							if ( !fetch renamedKey, renamedKey[0] ) {
								throw new Exception("Column '" . key . "' is not part of the column map");
							}
						}

						$renamed[renamedKey] = value;
					}

					/**
					 * Append the renamed records to the main array
					 */
					$renamedRecords[] = renamed;
				}
			}

			return renamedRecords;
		}

		return records;
    }

    /***
	 * Serializing a resultset will dump all related rows into a big array
	 **/
    public function serialize() {
			"model"         : $this->_model,
			"cache"         : $this->_cache,
			"rows"          : $this->toArray(false),
			"columnMap"     : $this->_columnMap,
			"hydrateMode"   : $this->_hydrateMode,
			"keepSnapshots" : $this->_keepSnapshots
		]);
    }

    /***
	 * Unserializing a resultset will allow to only works on the rows present in the saved state
	 **/
    public function unserialize($data ) {

		$resultset = unserialize(data);
		if ( gettype($resultset) != "array" ) {
			throw new Exception("Invalid serialization data");
		}

		$this->_model = resultset["model"],
			this->_rows = resultset["rows"],
			this->_count = count(resultset["rows"]),
			this->_cache = resultset["cache"],
			this->_columnMap = resultset["columnMap"],
			this->_hydrateMode = resultset["hydrateMode"];

		if ( fetch keepSnapshots, resultset["keepSnapshots"] ) {
		    $this->_keepSnapshots = keepSnapshots;
		}
    }

}