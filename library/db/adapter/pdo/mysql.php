<?php


namespace Phalcon\Db\Adapter\Pdo;

use Phalcon\Db;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Db\IndexInterface;
use Phalcon\Db\Adapter\Pdo as PdoAdapter;
use Phalcon\Application\Exception;
use Phalcon\Db\ReferenceInterface;


/***
 * Phalcon\Db\Adapter\Pdo\Mysql
 *
 * Specific functions for the Mysql database system
 *
 *<code>
 * use Phalcon\Db\Adapter\Pdo\Mysql;
 *
 * $config = [
 *     "host"     => "localhost",
 *     "dbname"   => "blog",
 *     "port"     => 3306,
 *     "username" => "sigma",
 *     "password" => "secret",
 * ];
 *
 * $connection = new Mysql($config);
 *</code>
 **/

class Mysql extends PdoAdapter {

    protected $_type;

    protected $_dialectType;

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
		 * Get the SQL to describe a table
		 * We're using FETCH_NUM to fetch the columns
		 * Get the describe
		 * Field Indexes: 0:name, 1:type, 2:not null, 3:key, 4:default, 5:extra
		 */
		for ( field in $this->fetchAll(this->_dialect->describeColumns(table, schema), Db::FETCH_NUM) ) {

			/**
			 * By default the bind types is two
			 */
			$definition = ["bindType": Column::BIND_PARAM_STR];

			/**
			 * By checking every column type we convert it to a Phalcon\Db\Column
			 */
			$columnType = field[1];

			if ( memstr(columnType, "enum") ) {
				/**
				 * Enum are treated as char
				 */
				$definition["type"] = Column::TYPE_CHAR;
			} elseif ( memstr(columnType, "bigint") ) {
				/**
				 * Smallint/Bigint/Integers/Int are int
				 */
				$definition["type"] = Column::TYPE_BIGINTEGER,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_INT;
			} elseif ( memstr(columnType, "int") ) {
				/**
				 * Smallint/Bigint/Integers/Int are int
				 */
				$definition["type"] = Column::TYPE_INTEGER,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_INT;
			} elseif ( memstr(columnType, "varchar") ) {
				/**
				 * Varchar are varchars
				 */
				$definition["type"] = Column::TYPE_VARCHAR;
			} elseif ( memstr(columnType, "datetime") ) {
				/**
				 * Special type for ( datetime
				 */
				$definition["type"] = Column::TYPE_DATETIME;
			} elseif ( memstr(columnType, "char") ) {
				/**
				 * Chars are chars
				 */
				$definition["type"] = Column::TYPE_CHAR;
			} elseif ( memstr(columnType, "date") ) {
				/**
				 * Date are dates
				 */
				$definition["type"] = Column::TYPE_DATE;
			} elseif ( memstr(columnType, "timestamp") ) {
				/**
				 * Timestamp are dates
				 */
				$definition["type"] = Column::TYPE_TIMESTAMP;
			} elseif ( memstr(columnType, "text") ) {
				/**
				 * Text are varchars
				 */
				$definition["type"] = Column::TYPE_TEXT;
			} elseif ( memstr(columnType, "decimal") ) {
				/**
				 * Decimals are floats
				 */
				$definition["type"] = Column::TYPE_DECIMAL,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_DECIMAL;
			} elseif ( memstr(columnType, "double") ) {
				/**
				 * Doubles
				 */
				$definition["type"] = Column::TYPE_DOUBLE,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_DECIMAL;
			} elseif ( memstr(columnType, "float") ) {
				/**
				 * Float/Smallfloats/Decimals are float
				 */
				$definition["type"] = Column::TYPE_FLOAT,
					definition["isNumeric"] = true,
					definition["bindType"] = Column::BIND_PARAM_DECIMAL;
			} elseif ( memstr(columnType, "bit") ) {
				/**
				 * Boolean
				 */
				$definition["type"] = Column::TYPE_BOOLEAN,
					definition["bindType"] = Column::BIND_PARAM_BOOL;
			} elseif ( memstr(columnType, "tinyblob") ) {
				/**
				 * Tinyblob
				 */
				$definition["type"] = Column::TYPE_TINYBLOB,
					definition["bindType"] = Column::BIND_PARAM_BOOL;
			} elseif ( memstr(columnType, "mediumblob") ) {
				/**
				 * Mediumblob
				 */
				$definition["type"] = Column::TYPE_MEDIUMBLOB;
			} elseif ( memstr(columnType, "longblob") ) {
				/**
				 * Longblob
				 */
				$definition["type"] = Column::TYPE_LONGBLOB;
			} elseif ( memstr(columnType, "blob") ) {
				/**
				 * Blob
				 */
				$definition["type"] = Column::TYPE_BLOB;
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
			if ( field[3] == "PRI" ) {
				$definition["primary"] = true;
			}

			/**
			 * Check if ( the column allows null values
			 */
			if ( field[2] == "NO" ) {
				$definition["notNull"] = true;
			}

			/**
			 * Check if ( the column is auto increment
			 */
			if ( field[5] == "auto_increment" ) {
				$definition["autoIncrement"] = true;
			}

			/**
			 * Check if ( the column is default values
			 */
			if ( gettype($field[4]) != "null" ) {
				$definition["default"] = field[4];
			}

			/**
			 * Every route is stored as a Phalcon\Db\Column
			 */
			$columnName = field[0],
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
			$keyName = index["Key_name"];
			$indexType = index["Index_type"];

			if ( !isset($indexes[keyName]) ) {
				$indexes[keyName] = [];
			}

			if ( !isset indexes[keyName]["columns"] ) {
				$columns = [];
			} else {
				$columns = indexes[keyName]["columns"];
			}

			$columns[] = index["Column_name"];
			$indexes[keyName]["columns"] = columns;

			if ( keyName == "PRIMARY" ) {
				$indexes[keyName]["type"] = "PRIMARY";
			} elseif ( indexType == "FULLTEXT" ) {
				$indexes[keyName]["type"] = "FULLTEXT";
			} elseif ( index["Non_unique"] == 0 ) {
				$indexes[keyName]["type"] = "UNIQUE";
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
	 *<code>
	 * print_r(
	 *     $connection->describeReferences("robots_parts")
	 * );
	 *</code>
	 **/
    public function describeReferences($table , $schema  = null ) {
			arrayReference, constraintName, referenceObjects, name,
			referencedSchema, referencedTable, columns, referencedColumns,
			referenceUpdate, referenceDelete;

		$references = [];

		for ( reference in $this->fetchAll(this->_dialect->describeReferences(table, schema),Db::FETCH_NUM) ) {

			$constraintName = reference[2];
			if ( !isset($references[constraintName]) ) {
				$referencedSchema  = reference[3];
				$referencedTable   = reference[4];
				$referenceUpdate   = reference[6];
				$referenceDelete   = reference[7];
				$columns           = [];
				$referencedColumns = [];

			} else {
				$referencedSchema  = references[constraintName]["referencedSchema"];
				$referencedTable   = references[constraintName]["referencedTable"];
				$columns           = references[constraintName]["columns"];
				$referencedColumns = references[constraintName]["referencedColumns"];
				$referenceUpdate   = references[constraintName]["onUpdate"];
				$referenceDelete   = references[constraintName]["onDelete"];
			}

			$columns[] = reference[1],
				referencedColumns[] = reference[5];

			$references[constraintName] = [
				"referencedSchema"  : referencedSchema,
				"referencedTable"   : referencedTable,
				"columns"           : columns,
				"referencedColumns" : referencedColumns,
				"onUpdate"          : referenceUpdate,
				"onDelete"          : referenceDelete
			];
		}

		$referenceObjects = [];
		foreach ( name, $references as $arrayReference ) {
			$referenceObjects[name] = new Reference(name, [
				"referencedSchema"  : arrayReference["referencedSchema"],
				"referencedTable"   : arrayReference["referencedTable"],
				"columns"           : arrayReference["columns"],
				"referencedColumns" : arrayReference["referencedColumns"],
				"onUpdate"          : arrayReference["onUpdate"],
				"onDelete"          : arrayReference["onDelete"]
			]);
		}

		return referenceObjects;
    }

    /***
	 * Adds a foreign key to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ) {

		$for (eignKeyCheck = $this->) {"prepare"}(this->_dialect->getForeignKeyChecks());
		if ( !for (eignKeyCheck->execute() ) ) {
			throw new Exception("DATABASE PARAMETER 'FOREIGN_KEY_CHECKS' HAS TO BE 1");
		}

		return $this->{"execute"}(this->_dialect->addForeignKey(tableName, schemaName, reference));
    }

}