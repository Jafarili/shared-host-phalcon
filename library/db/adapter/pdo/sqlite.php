<?php


namespace Phalcon\Db\Adapter\Pdo;

use Phalcon\Db;
use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\RawValue;
use Phalcon\Db\Reference;
use Phalcon\Db\ReferenceInterface;
use Phalcon\Db\Index;
use Phalcon\Db\IndexInterface;
use Phalcon\Db\Adapter\Pdo as PdoAdapter;


/***
 * Phalcon\Db\Adapter\Pdo\Sqlite
 *
 * Specific functions for the Sqlite database system
 *
 * <code>
 * use Phalcon\Db\Adapter\Pdo\Sqlite;
 *
 * $connection = new Sqlite(
 *     [
 *         "dbname" => "/tmp/test.sqlite",
 *     ]
 * );
 * </code>
 **/

class Sqlite extends PdoAdapter {

    protected $_type;

    protected $_dialectType;

    /***
	 * This method is automatically called in Phalcon\Db\Adapter\Pdo constructor.
	 * Call it when you need to restore a database connection.
	 **/
    public function connect($descriptor  = null ) {

		if ( empty descriptor ) {
			$descriptor = (array) $this->_descriptor;
		}

		if ( !fetch dbname, descriptor["dbname"] ) {
			throw new Exception("dbname must be specif (ied");
		}

		$descriptor["dsn"] = dbname;

		return parent::connect(descriptor);
    }

    /***
	 * Returns an array of Phalcon\Db\Column objects describing a table
	 *
	 * <code>
	 * print_r(
	 *     $connection->describeColumns("posts")
	 * );
	 * </code>
	 **/
    public function describeColumns($table , $schema  = null ) {
			oldColumn, sizePattern, matches, matchOne, matchTwo, columnName;

		$oldColumn = null,
			sizePattern = "#\\(([0-9]+)(?:,\\s*([0-9]+))*\\)#";

		$columns = [];

		/**
		 * We're using FETCH_NUM to fetch the columns
		 */
		for ( field in $this->fetchAll(this->_dialect->describeColumns(table, schema), Db::FETCH_NUM) ) {

			/**
			 * By default the bind types is two
			 */
			$definition = ["bindType": Column::BIND_PARAM_STR];

			/**
			 * By checking every column type we convert it to a Phalcon\Db\Column
			 */
			$columnType = field[2];

			if ( memstr(columnType, "tinyint(1)") ) {
				/**
				 * Tinyint(1) is boolean
				 */
				$definition["type"] = Column::TYPE_BOOLEAN,
					definition["bindType"] = Column::BIND_PARAM_BOOL,
					columnType = "boolean"; // Change column type to skip size check
			} elseif ( memstr(columnType, "bigint") ) {
				/**
				 * Bigint are int
				 */
				$definition["type"] = Column::TYPE_BIGINTEGER,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_INT;
			} elseif ( memstr(columnType, "int") || memstr(columnType, "INT") ) {
				/**
				 * Smallint/Integers/Int are int
				 */
				$definition["type"] = Column::TYPE_INTEGER,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_INT;

				if ( field[5] ) {
					$definition["autoIncrement"] = true;
				}
			} elseif ( memstr(columnType, "varchar") ) {
				/**
				 * Varchar are varchars
				 */
				$definition["type"] = Column::TYPE_VARCHAR;
			} elseif ( memstr(columnType, "date") ) {
				/**
				 * Date/Datetime are varchars
				 */
				$definition["type"] = Column::TYPE_DATE;
			} elseif ( memstr(columnType, "timestamp") ) {
				/**
				 * Timestamp as date
				 */
				$definition["type"] = Column::TYPE_TIMESTAMP;
			} elseif ( memstr(columnType, "decimal") ) {
				/**
				 * Decimals are floats
				 */
				$definition["type"] = Column::TYPE_DECIMAL,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_DECIMAL;
			} elseif ( memstr(columnType, "char") ) {
				/**
				 * Chars are chars
				 */
				$definition["type"] = Column::TYPE_CHAR;
			} elseif ( memstr(columnType, "datetime") ) {
				/**
				 * Special type for ( datetime
				 */
				$definition["type"] = Column::TYPE_DATETIME;
			} elseif ( memstr(columnType, "text") ) {
				/**
				 * Text are varchars
				 */
				$definition["type"] = Column::TYPE_TEXT;
			} elseif ( memstr(columnType, "float") ) {
				/**
				 * Float/Smallfloats/Decimals are float
				 */
				$definition["type"] = Column::TYPE_FLOAT,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::TYPE_DECIMAL;
			} elseif ( memstr(columnType, "enum") ) {
				/**
				 * Enum are treated as char
				 */
				$definition["type"] = Column::TYPE_CHAR;
			} else {
				/**
				 * By default is string
				 */
				$definition["type"] = Column::TYPE_VARCHAR;
			}

			/**
			 * If the column type has a parentheses we try to get the column size from it
			 */
			if ( memstr(columnType, "(") ) {
				$matches = null;
				if ( preg_match(sizePattern, columnType, matches) ) {
					if ( fetch matchOne, matches[1] ) {
						$definition["size"] = (int) matchOne;
					}
					if ( fetch matchTwo, matches[2] ) {
						$definition["scale"] = (int) matchTwo;
					}
				}
			}

			/**
			 * Check if ( the column is unsigned, only MySQL support this
			 */
			if ( memstr(columnType, "unsigned") ) {
				$definition["unsigned"] = true;
			}

			/**
			 * Positions
			 */
			if ( oldColumn == null ) {
				$definition["first"] = true;
			} else {
				$definition["after"] = oldColumn;
			}

			/**
			 * Check if ( the field is primary key
			 */
			if ( field[5] ) {
				$definition["primary"] = true;
			}

			/**
			 * Check if ( the column allows null values
			 */
			if ( field[3] ) {
				$definition["notNull"] = true;
			}

			/**
			 * Check if ( the column is default values
			 * When field is empty default value is null
			 */
			if ( strcasecmp(field[4], "null") != 0 && field[4] != "" ) {
				$definition["default"] = preg_replace("/^'|'$/", "", field[4]);
			}

			/**
			 * Every route is stored as a Phalcon\Db\Column
			 */
			$columnName = field[1],
				columns[] = new Column(columnName, definition),
				oldColumn = columnName;
		}

		return columns;
    }

    /***
	 * Lists table indexes
	 *
	 * <code>
	 * print_r(
	 *     $connection->describeIndexes("robots_parts")
	 * );
	 * </code>
	 *
	 * @param  string table
	 * @param  string schema
	 * @return \Phalcon\Db\IndexInterface[]
	 **/
    public function describeIndexes($table , $schema  = null ) {

		$indexes = [];
		for ( index in $this->fetchAll(this->_dialect->describeIndexes(table, schema), Db::FETCH_ASSOC) ) {
			$keyName = index["name"];

			if ( !isset($indexes[keyName]) ) {
				$indexes[keyName] = [];
			}

			if ( !isset indexes[keyName]["columns"] ) {
				$columns = [];
			} else {
				$columns = indexes[keyName]["columns"];
			}

			for ( describeIndex in $this->fetchAll(this->_dialect->describeIndex(keyName), Db::FETCH_ASSOC) ) {
				$columns[] = describeIndex["name"];
			}

			$indexes[keyName]["columns"] = columns;
			$indexSql = $this->fetchColumn(this->_dialect->listIndexesSql(table, schema, keyName));

			if ( index["unique"] ) {
				if ( preg_match("# UNIQUE #i", indexSql) ) {
					$indexes[keyName]["type"] = "UNIQUE";
				} else {
					$indexes[keyName]["type"] = "PRIMARY";
				}
			} else {
				$indexes[keyName]["type"] = null;
			}
		}

		$indexObjects = [];
		foreach ( name, $indexes as $index ) {
			$indexObjects[name] = new Index(name, index["columns"], index["type"]);
		}

		return indexObjects;
    }

    /***
	 * Lists table references
	 *
	 * @param	string table
	 * @param	string schema
	 * @return	Phalcon\Db\ReferenceInterface[]
	 **/
    public function describeReferences($table , $schema  = null ) {
			arrayReference, constraintName, referenceObjects, name,
			referencedSchema, referencedTable, columns, referencedColumns,
			number;

		$references = [];

		for ( number, reference in $this->fetchAll(this->_dialect->describeReferences(table, schema), Db::FETCH_NUM) ) {

			$constraintName = "for (eign_key_" . number;
			if ( !isset($references[constraintName]) ) {
				$referencedSchema = null;
				$referencedTable = reference[2];
				$columns = [];
				$referencedColumns = [];
			} else {
				$referencedSchema = references[constraintName]["referencedSchema"];
				$referencedTable = references[constraintName]["referencedTable"];
				$columns = references[constraintName]["columns"];
				$referencedColumns = references[constraintName]["referencedColumns"];
			}

			$columns[] = reference[3],
				referencedColumns[] = reference[4];

			$references[constraintName] = [
				"referencedSchema"  : referencedSchema,
				"referencedTable"   : referencedTable,
				"columns"           : columns,
				"referencedColumns" : referencedColumns
			];
		}

		$referenceObjects = [];
		foreach ( name, $references as $arrayReference ) {
			$referenceObjects[name] = new Reference(name, [
				"referencedSchema"	: arrayReference["referencedSchema"],
				"referencedTable"	: arrayReference["referencedTable"],
				"columns"			: arrayReference["columns"],
				"referencedColumns" : arrayReference["referencedColumns"]
			]);
		}

		return referenceObjects;
    }

    /***
	 * Check whether the database system requires an explicit value for identity columns
	 **/
    public function useExplicitIdValue() {
		return true;
    }

    /***
	 * Returns the default value to make the RBDM use the default value declared in the table definition
	 *
	 *<code>
	 * // Inserting a new robot with a valid default value for the column 'year'
	 * $success = $connection->insert(
	 *     "robots",
	 *     [
	 *         "Astro Boy",
	 *         $connection->getDefaultValue(),
	 *     ],
	 *     [
	 *         "name",
	 *         "year",
	 *     ]
	 * );
	 *</code>
	 **/
    public function getDefaultValue() {
		return new RawValue("NULL");
    }

}