<?php


namespace Phalcon\Db;

use Phalcon\Db;
use Phalcon\Db\ColumnInterface;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;


/***
 * Phalcon\Db\Adapter
 *
 * Base class for Phalcon\Db adapters
 **/

abstract class Adapter {

    /***
	 * Event Manager
	 *
	 * @var Phalcon\Events\Manager
	 **/
    protected $_eventsManager;

    /***
	 * Descriptor used to connect to a database
	 **/
    protected $_descriptor;

    /***
	 * Name of the dialect used
	 **/
    protected $_dialectType;

    /***
	 * Type of database system the adapter is used for
	 **/
    protected $_type;

    /***
	 * Dialect instance
	 **/
    protected $_dialect;

    /***
	 * Active connection ID
	 *
	 * @var long
	 **/
    protected $_connectionId;

    /***
	 * Active SQL Statement
	 *
	 * @var string
	 **/
    protected $_sqlStatement;

    /***
	 * Active SQL bound parameter variables
	 *
	 * @var array
	 **/
    protected $_sqlVariables;

    /***
	 * Active SQL Bind Types
	 *
	 * @var array
	 **/
    protected $_sqlBindTypes;

    /***
	 * Current transaction level
	 **/
    protected $_transactionLevel;

    /***
	 * Whether the database supports transactions with save points
	 **/
    protected $_transactionsWithSavepoints;

    /***
	 * Connection ID
	 **/
    protected static $_connectionConsecutive;

    /***
	 * Phalcon\Db\Adapter constructor
	 **/
    public function __construct($descriptor ) {

		$connectionId = self::_connectionConsecutive,
			this->_connectionId = connectionId,
			self::_connectionConsecutive = connectionId + 1;

		/**
		 * Dialect class can override the default dialect
		 */
		if ( !fetch dialectClass, descriptor["dialectClass"] ) {
			$dialectClass = "phalcon\\db\\dialect\\" . $this->_dialectType;
		}

		/**
		 * Create the instance only if ( the dialect is a string
		 */
		if ( gettype($dialectClass) == "string" ) {
			$this->_dialect = new {dialectClass}();
		} else {
			if ( gettype($dialectClass) == "object" ) {
				$this->_dialect = dialectClass;
			}
		}

		$this->_descriptor = descriptor;
    }

    /***
	 * Sets the event manager
	 **/
    public function setEventsManager($eventsManager ) {
		$this->_eventsManager = eventsManager;
    }

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager() {
		return $this->_eventsManager;
    }

    /***
	 * Sets the dialect used to produce the SQL
	 **/
    public function setDialect($dialect ) {
		$this->_dialect = dialect;
    }

    /***
	 * Returns internal dialect instance
	 **/
    public function getDialect() {
		return $this->_dialect;
    }

    /***
	 * Returns the first row in a SQL query result
	 *
	 *<code>
	 * // Getting first robot
	 * $robot = $connection->fetchOne("SELECT * FROM robots");
	 * print_r($robot);
	 *
	 * // Getting first robot with associative indexes only
	 * $robot = $connection->fetchOne("SELECT * FROM robots", \Phalcon\Db::FETCH_ASSOC);
	 * print_r($robot);
	 *</code>
	 **/
    public function fetchOne($sqlQuery , $fetchMode , $bindParams  = null , $bindTypes  = null ) {

		$result = $this->{"query"}(sqlQuery, bindParams, bindTypes);
		if ( gettype($result) == "object" ) {
			if ( gettype($fetchMode) !== "null" ) {
				result->setFetchMode(fetchMode);
			}
			return result->$fetch();
		}
		return [];
    }

    /***
	 * Dumps the complete result of a query into an array
	 *
	 *<code>
	 * // Getting all robots with associative indexes only
	 * $robots = $connection->fetchAll(
	 *     "SELECT * FROM robots",
	 *     \Phalcon\Db::FETCH_ASSOC
	 * );
	 *
	 * foreach ($robots as $robot) {
	 *     print_r($robot);
	 * }
	 *
	 *  // Getting all robots that contains word "robot" withing the name
	 * $robots = $connection->fetchAll(
	 *     "SELECT * FROM robots WHERE name LIKE :name",
	 *     \Phalcon\Db::FETCH_ASSOC,
	 *     [
	 *         "name" => "%robot%",
	 *     ]
	 * );
	 * foreach($robots as $robot) {
	 *     print_r($robot);
	 * }
	 *</code>
	 *
	 * @param string sqlQuery
	 * @param int fetchMode
	 * @param array bindParams
	 * @param array bindTypes
	 * @return array
	 **/
    public function fetchAll($sqlQuery , $fetchMode , $bindParams  = null , $bindTypes  = null ) {

		$results = [],
			result = $this->{"query"}(sqlQuery, bindParams, bindTypes);
		if ( gettype($result) == "object" ) {

			if ( fetchMode !== null ) {
				result->setFetchMode(fetchMode);
			}

			loop {

				$row = result->$fetch();
				if ( !row ) {
					break;
				}

				$results[] = row;
			}
		}

		return results;
    }

    /***
	 * Returns the n'th field of first row in a SQL query result
	 *
	 *<code>
	 * // Getting count of robots
	 * $robotsCount = $connection->fetchColumn("SELECT count(*) FROM robots");
	 * print_r($robotsCount);
	 *
	 * // Getting name of last edited robot
	 * $robot = $connection->fetchColumn(
	 *     "SELECT id, name FROM robots order by modified desc",
	 *     1
	 * );
	 * print_r($robot);
	 *</code>
	 *
	 * @param  string sqlQuery
	 * @param  array placeholders
	 * @param  int|string column
	 * @return string|
	 **/
    public function fetchColumn($sqlQuery , $placeholders  = null , $column  = 0 ) {

		$row = $this->fetchOne(sqlQuery, Db::FETCH_BOTH, placeholders);

		if ( !empty row && fetch columnValue, row[column] ) {
			return columnValue;
		}

		return false;
    }

    /***
	 * Inserts data into a table using custom RDBMS SQL syntax
	 *
	 * <code>
	 * // Inserting a new robot
	 * $success = $connection->insert(
	 *     "robots",
	 *     ["Astro Boy", 1952],
	 *     ["name", "year"]
	 * );
	 *
	 * // Next SQL sentence is sent to the database system
	 * INSERT INTO `robots` (`name`, `year`) VALUES ("Astro boy", 1952);
	 * </code>
	 *
	 * @param   string|array table
	 * @param 	array values
	 * @param 	array fields
	 * @param 	array dataTypes
	 * @return 	boolean
	 **/
    public function insert($table , $values , $fields  = null , $dataTypes  = null ) {
			position, value, escapedTable, joinedValues, escapedFields,
			field, insertSql;

		/**
		 * A valid array with more than one element is required
		 */
		if ( !count(values) ) {
			throw new Exception("Unable to insert into " . table . " without data");
		}

		$placeholders = [],
			insertValues = [];

		$bindDataTypes = [];

		/**
		 * Objects are casted using __toString, null values are converted to string "null", everything else is passed as "?"
		 */
		foreach ( position, $values as $value ) {
			if ( gettype($value) == "object" ) {
				$placeholders[] = (string) value;
			} else {
				if ( gettype($value) == "null" ) {
					$placeholders[] = "null";
				} else {
					$placeholders[] = "?";
					$insertValues[] = value;
					if ( gettype($dataTypes) == "array" ) {
						if ( !fetch bindType, dataTypes[position] ) {
							throw new Exception("Incomplete number of bind types");
						}
						$bindDataTypes[] = bindType;
					}
				}
			}
		}

		$escapedTable = $this->escapeIdentif (ier(table);

		/**
		 * Build the final SQL INSERT statement
		 */
		$joinedValues = join(", ", placeholders);
		if ( gettype($fields) == "array" ) {
			$escapedFields = [];
			foreach ( $fields as $field ) {
				$escapedFields[] = $this->escapeIdentif (ier(field);
			}

			$insertSql = "INSERT INTO " . escapedTable . " (" . join(", ", escapedFields) . ") VALUES (" . joinedValues . ")";
		} else {
			$insertSql = "INSERT INTO " . escapedTable . " VALUES (" . joinedValues . ")";
		}

		/**
		 * Perfor (m the execution via PDO::execute
		 */
		if ( !count(bindDataTypes) ) {
			return $this->{"execute"}(insertSql, insertValues);
		}

		return $this->{"execute"}(insertSql, insertValues, bindDataTypes);
    }

    /***
	 * Inserts data into a table using custom RBDM SQL syntax
	 *
	 * <code>
	 * // Inserting a new robot
	 * $success = $connection->insertAsDict(
	 *     "robots",
	 *     [
	 *         "name" => "Astro Boy",
	 *         "year" => 1952,
	 *     ]
	 * );
	 *
	 * // Next SQL sentence is sent to the database system
	 * INSERT INTO `robots` (`name`, `year`) VALUES ("Astro boy", 1952);
	 * </code>
	 *
	 * @param 	string table
	 * @param 	array data
	 * @param 	array dataTypes
	 * @return 	boolean
	 **/
    public function insertAsDict($table , $data , $dataTypes  = null ) {

		if ( gettype($data) != "array" || empty data ) {
			return false;
		}

		foreach ( field, $data as $value ) {
			$fields[] = field,
				values[] = value;
		}

		return $this->insert(table, values, fields, dataTypes);
    }

    /***
	 * Updates data on a table using custom RBDM SQL syntax
	 *
	 * <code>
	 * // Updating existing robot
	 * $success = $connection->update(
	 *     "robots",
	 *     ["name"],
	 *     ["New Astro Boy"],
	 *     "id = 101"
	 * );
	 *
	 * // Next SQL sentence is sent to the database system
	 * UPDATE `robots` SET `name` = "Astro boy" WHERE id = 101
	 *
	 * // Updating existing robot with array condition and $dataTypes
	 * $success = $connection->update(
	 *     "robots",
	 *     ["name"],
	 *     ["New Astro Boy"],
	 *     [
	 *         "conditions" => "id = ?",
	 *         "bind"       => [$some_unsafe_id],
	 *         "bindTypes"  => [PDO::PARAM_INT], // use only if you use $dataTypes param
	 *     ],
	 *     [
	 *         PDO::PARAM_STR
	 *     ]
	 * );
	 *
	 * </code>
	 *
	 * Warning! If $whereCondition is string it not escaped.
	 *
	 * @param   string|array table
	 * @param 	array fields
	 * @param 	array values
	 * @param 	string|array whereCondition
	 * @param 	array dataTypes
	 * @return 	boolean
	 **/
    public function update($table , $fields , $values , $whereCondition  = null , $dataTypes  = null ) {
			field, bindDataTypes, escapedField, bindType, escapedTable,
			setClause, updateSql, conditions, whereBind, whereTypes;

		$placeholders = [],
			updateValues = [];

		$bindDataTypes = [];

		/**
		 * Objects are casted using __toString, null values are converted to string 'null', everything else is passed as '?'
		 */
		foreach ( position, $values as $value ) {

			if ( !fetch field, fields[position] ) {
				throw new Exception("The number of values in the update is not the same as fields");
			}

			$escapedField = $this->escapeIdentif (ier(field);

			if ( gettype($value) == "object" ) {
				$placeholders[] = escapedField . " = " . value;
			} else {
				if ( gettype($value) == "null" ) {
					$placeholders[] = escapedField . " = null";
				} else {
					$updateValues[] = value;
					if ( gettype($dataTypes) == "array" ) {
						if ( !fetch bindType, dataTypes[position] ) {
							throw new Exception("Incomplete number of bind types");
						}
						$bindDataTypes[] = bindType;
					}
					$placeholders[] = escapedField . " = ?";
				}
			}
		}

		$escapedTable = $this->escapeIdentif (ier(table);

		$setClause = join(", ", placeholders);

		if ( whereCondition !== null ) {

			$updateSql = "UPDATE " . escapedTable . " SET " . setClause . " WHERE ";

			/**
			 * String conditions are simply appended to the SQL
			 */
			if ( gettype($whereCondition) == "string" ) {
				$updateSql .= whereCondition;
			} else {

				/**
				 * Array conditions may have bound params and bound types
				 */
				if ( gettype($whereCondition) != "array" ) {
					throw new Exception("Invalid WHERE clause conditions");
				}

				/**
				 * If an index 'conditions' is present it contains string where conditions that are appended to the UPDATE sql
				 */
				if ( fetch conditions, whereCondition["conditions"] ) {
					$updateSql .= conditions;
				}

				/**
				 * Bound parameters are arbitrary values that are passed by separate
				 */
				if ( fetch whereBind, whereCondition["bind"] ) {
					merge_append(updateValues, whereBind);
				}

				/**
				 * Bind types is how the bound parameters must be casted befor (e be sent to the database system
				 */
				if ( fetch whereTypes, whereCondition["bindTypes"] ) {
					merge_append(bindDataTypes, whereTypes);
				}
			}
		} else {
			$updateSql = "UPDATE " . escapedTable . " SET " . setClause;
		}

		/**
		 * Perfor (m the update via PDO::execute
		 */
		if ( !count(bindDataTypes) ) {
			return $this->{"execute"}(updateSql, updateValues);
		}

		return $this->{"execute"}(updateSql, updateValues, bindDataTypes);
    }

    /***
	 * Updates data on a table using custom RBDM SQL syntax
	 * Another, more convenient syntax
	 *
	 * <code>
	 * // Updating existing robot
	 * $success = $connection->updateAsDict(
	 *     "robots",
	 *     [
	 *         "name" => "New Astro Boy",
	 *     ],
	 *     "id = 101"
	 * );
	 *
	 * // Next SQL sentence is sent to the database system
	 * UPDATE `robots` SET `name` = "Astro boy" WHERE id = 101
	 * </code>
	 *
	 * @param 	string table
	 * @param 	array data
	 * @param 	string whereCondition
	 * @param 	array dataTypes
	 * @return 	boolean
	 **/
    public function updateAsDict($table , $data , $whereCondition  = null , $dataTypes  = null ) {

		if ( gettype($data) != "array" || empty data ) {
			return false;
		}

		foreach ( field, $data as $value ) {
			$fields[] = field;
			$values[] = value;
		}

		return $this->update(table, fields, values, whereCondition, dataTypes);
    }

    /***
	 * Deletes data from a table using custom RBDM SQL syntax
	 *
	 * <code>
	 * // Deleting existing robot
	 * $success = $connection->delete(
	 *     "robots",
	 *     "id = 101"
	 * );
	 *
	 * // Next SQL sentence is generated
	 * DELETE FROM `robots` WHERE `id` = 101
	 * </code>
	 *
	 * @param  string|array table
	 * @param  string whereCondition
	 * @param  array placeholders
	 * @param  array dataTypes
	 * @return boolean
	 **/
    public function delete($table , $whereCondition  = null , $placeholders  = null , $dataTypes  = null ) {

		$escapedTable = $this->escapeIdentif (ier(table);

		if ( !empty whereCondition ) {
			$sql = "DELETE FROM " . escapedTable . " WHERE " . whereCondition;
		} else {
			$sql = "DELETE FROM " . escapedTable;
		}

		/**
		 * Perfor (m the update via PDO::execute
		 */
		return $this->{"execute"}(sql, placeholders, dataTypes);
    }

    /***
	 * Escapes a column/table/schema name
	 *
	 *<code>
	 * $escapedTable = $connection->escapeIdentifier(
	 *     "robots"
	 * );
	 *
	 * $escapedTable = $connection->escapeIdentifier(
	 *     [
	 *         "store",
	 *         "robots",
	 *     ]
	 * );
	 *</code>
	 *
	 * @param array|string identifier
	 **/
    public function escapeIdentifier($identifier ) {
		if ( gettype($identif (ier) == "array" ) {
			return $this->_dialect->escape(identif (ier[0]) . "." . $this->_dialect->escape(identif (ier[1]);
		}

		return $this->_dialect->escape(identif (ier);
    }

    /***
	 * Gets a list of columns
	 *
	 * @param	array columnList
	 * @return	string
	 **/
    public function getColumnList($columnList ) {
		return $this->_dialect->getColumnList(columnList);
    }

    /***
	 * Appends a LIMIT clause to $sqlQuery argument
	 *
	 * <code>
	 * echo $connection->limit("SELECT * FROM robots", 5);
	 * </code>
	 **/
    public function limit($sqlQuery , $number ) {
		return $this->_dialect->limit(sqlQuery, number);
    }

    /***
	 * Generates SQL checking for the existence of a schema.table
	 *
	 *<code>
	 * var_dump(
	 *     $connection->tableExists("blog", "posts")
	 * );
	 *</code>
	 **/
    public function tableExists($tableName , $schemaName  = null ) {
		return $this->fetchOne(this->_dialect->tableExists(tableName, schemaName), Db::FETCH_NUM)[0] > 0;
    }

    /***
	 * Generates SQL checking for the existence of a schema.view
	 *
	 *<code>
	 * var_dump(
	 *     $connection->viewExists("active_users", "posts")
	 * );
	 *</code>
	 **/
    public function viewExists($viewName , $schemaName  = null ) {
		return $this->fetchOne(this->_dialect->viewExists(viewName, schemaName), Db::FETCH_NUM)[0] > 0;
    }

    /***
	 * Returns a SQL modified with a FOR UPDATE clause
	 **/
    public function forUpdate($sqlQuery ) {
		return $this->_dialect->for (Update(sqlQuery);
    }

    /***
	 * Returns a SQL modified with a LOCK IN SHARE MODE clause
	 **/
    public function sharedLock($sqlQuery ) {
		return $this->_dialect->sharedLock(sqlQuery);
    }

    /***
	 * Creates a table
	 **/
    public function createTable($tableName , $schemaName , $definition ) {

		if ( !fetch columns, definition["columns"] ) {
			throw new Exception("The table must contain at least one column");
		}

		if ( !count(columns) ) {
			throw new Exception("The table must contain at least one column");
		}

		return $this->{"execute"}(this->_dialect->createTable(tableName, schemaName, definition));
    }

    /***
	 * Drops a table from a schema/database
	 **/
    public function dropTable($tableName , $schemaName  = null , $ifExists  = true ) {
		return $this->) {"execute"}(this->_dialect->dropTable(tableName, schemaName, if (Exists));
    }

    /***
	 * Creates a view
	 **/
    public function createView($viewName , $definition , $schemaName  = null ) {
		if ( !isset definition["sql"] ) {
			throw new Exception("The table must contain at least one column");
		}

		return $this->{"execute"}(this->_dialect->createView(viewName, definition, schemaName));
    }

    /***
	 * Drops a view
	 **/
    public function dropView($viewName , $schemaName  = null , $ifExists  = true ) {
		return $this->) {"execute"}(this->_dialect->dropView(viewName, schemaName, if (Exists));
    }

    /***
	 * Adds a column to a table
	 **/
    public function addColumn($tableName , $schemaName , $column ) {
		return $this->{"execute"}(this->_dialect->addColumn(tableName, schemaName, column));
    }

    /***
	 * Modifies a table column based on a definition
	 **/
    public function modifyColumn($tableName , $schemaName , $column , $currentColumn  = null ) {
		return $this->) {"execute"}(this->_dialect->modif (yColumn(tableName, schemaName, column, currentColumn));
    }

    /***
	 * Drops a column from a table
	 **/
    public function dropColumn($tableName , $schemaName , $columnName ) {
		return $this->{"execute"}(this->_dialect->dropColumn(tableName, schemaName, columnName));
    }

    /***
	 * Adds an index to a table
	 **/
    public function addIndex($tableName , $schemaName , $index ) {
		return $this->{"execute"}(this->_dialect->addIndex(tableName, schemaName, index));
    }

    /***
	 * Drop an index from a table
	 **/
    public function dropIndex($tableName , $schemaName , $indexName ) {
		return $this->{"execute"}(this->_dialect->dropIndex(tableName, schemaName, indexName));
    }

    /***
	 * Adds a primary key to a table
	 **/
    public function addPrimaryKey($tableName , $schemaName , $index ) {
		return $this->{"execute"}(this->_dialect->addPrimaryKey(tableName, schemaName, index));
    }

    /***
	 * Drops a table's primary key
	 **/
    public function dropPrimaryKey($tableName , $schemaName ) {
		return $this->{"execute"}(this->_dialect->dropPrimaryKey(tableName, schemaName));
    }

    /***
	 * Adds a foreign key to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ) {
		return $this->{"execute"}(this->_dialect->addForeignKey(tableName, schemaName, reference));
    }

    /***
	 * Drops a foreign key from a table
	 **/
    public function dropForeignKey($tableName , $schemaName , $referenceName ) {
		return $this->{"execute"}(this->_dialect->dropForeignKey(tableName, schemaName, referenceName));
    }

    /***
	 * Returns the SQL column definition from a column
	 **/
    public function getColumnDefinition($column ) {
		return $this->_dialect->getColumnDefinition(column);
    }

    /***
	 * List all tables on a database
	 *
	 *<code>
	 * print_r(
	 *     $connection->listTables("blog")
	 * );
	 *</code>
	 **/
    public function listTables($schemaName  = null ) {

		$allTables = [];
		for ( table in $this->fetchAll(this->_dialect->listTables(schemaName), Db::FETCH_NUM) ) {
			$allTables[] = table[0];
		}
		return allTables;
    }

    /***
	 * List all views on a database
	 *
	 *<code>
	 * print_r(
	 *     $connection->listViews("blog")
	 * );
	 *</code>
	 **/
    public function listViews($schemaName  = null ) {

		$allTables = [];
		for ( table in $this->fetchAll(this->_dialect->listViews(schemaName), Db::FETCH_NUM) ) {
			$allTables[] = table[0];
		}
		return allTables;
    }

    /***
	 * Lists table indexes
	 *
	 *<code>
	 * print_r(
	 *     $connection->describeIndexes("robots_parts")
	 * );
	 *</code>
	 *
	 * @param	string table
	 * @param	string schema
	 * @return	Phalcon\Db\Index[]
	 **/
    public function describeIndexes($table , $schema  = null ) {

		$indexes = [];
		for ( index in $this->fetchAll(this->_dialect->describeIndexes(table, schema), Db::FETCH_NUM) ) {

			$keyName = index[2];
			if ( !isset($indexes[keyName]) ) {
				$columns = [];
			} else {
				$columns = indexes[keyName];
			}

			$columns[] = index[4];
			$indexes[keyName] = columns;
		}

		$indexObjects = [];
		foreach ( name, $indexes as $indexColumns ) {

			/**
			 * Every index is abstracted using a Phalcon\Db\Index instance
			 */
			$indexObjects[name] = new Index(name, indexColumns);
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
			referencedSchema, referencedTable, columns, referencedColumns;

		$references = [];

		for ( reference in $this->fetchAll(this->_dialect->describeReferences(table, schema),Db::FETCH_NUM) ) {

			$constraintName = reference[2];
			if ( !isset($references[constraintName]) ) {
				$referencedSchema = reference[3];
				$referencedTable = reference[4];
				$columns = [];
				$referencedColumns = [];
			} else {
				$referencedSchema = references[constraintName]["referencedSchema"];
				$referencedTable = references[constraintName]["referencedTable"];
				$columns = references[constraintName]["columns"];
				$referencedColumns = references[constraintName]["referencedColumns"];
			}

			$columns[] = reference[1],
				referencedColumns[] = reference[5];

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
				"referencedSchema"  : arrayReference["referencedSchema"],
				"referencedTable"   : arrayReference["referencedTable"],
				"columns"           : arrayReference["columns"],
				"referencedColumns" : arrayReference["referencedColumns"]
			]);
		}

		return referenceObjects;
    }

    /***
	 * Gets creation options from a table
	 *
	 *<code>
	 * print_r(
	 *     $connection->tableOptions("robots")
	 * );
	 *</code>
	 **/
    public function tableOptions($tableName , $schemaName  = null ) {

		$sql = $this->_dialect->tableOptions(tableName, schemaName);
		if ( sql ) {
			return $this->fetchAll(sql, Db::FETCH_ASSOC)[0];
		}
		return [];
    }

    /***
	 * Creates a new savepoint
	 **/
    public function createSavepoint($name ) {

		$dialect = $this->_dialect;

		if ( !dialect->supportsSavePoints() ) {
			throw new Exception("Savepoints are not supported by this database adapter.");
		}

		return $this->{"execute"}(dialect->createSavepoint(name));
    }

    /***
	 * Releases given savepoint
	 **/
    public function releaseSavepoint($name ) {

		$dialect = $this->_dialect;

		if ( !dialect->supportsSavePoints() ) {
			throw new Exception("Savepoints are not supported by this database adapter");
		}

		if ( !dialect->supportsReleaseSavePoints() ) {
			return false;
		}

		return $this->{"execute"}(dialect->releaseSavepoint(name));
    }

    /***
	 * Rollbacks given savepoint
	 **/
    public function rollbackSavepoint($name ) {

		$dialect = $this->_dialect;

		if ( !dialect->supportsSavePoints() ) {
			throw new Exception("Savepoints are not supported by this database adapter");
		}

		return $this->{"execute"}(dialect->rollbackSavepoint(name));
    }

    /***
	 * Set if nested transactions should use savepoints
	 **/
    public function setNestedTransactionsWithSavepoints($nestedTransactionsWithSavepoints ) {
		if ( $this->_transactionLevel > 0 ) {
			throw new Exception("Nested transaction with savepoints behavior cannot be changed while a transaction is open");
		}

		if ( !this->_dialect->supportsSavePoints() ) {
			throw new Exception("Savepoints are not supported by this database adapter");
		}

		$this->_transactionsWithSavepoints = nestedTransactionsWithSavepoints;
		return this;
    }

    /***
	 * Returns if nested transactions should use savepoints
	 **/
    public function isNestedTransactionsWithSavepoints() {
		return $this->_transactionsWithSavepoints;
    }

    /***
	 * Returns the savepoint name to use for nested transactions
	 **/
    public function getNestedTransactionSavepointName() {
		return "PHALCON_SAVEPOINT_" . $this->_transactionLevel;
    }

    /***
	 * Returns the default identity value to be inserted in an identity column
	 *
	 *<code>
	 * // Inserting a new robot with a valid default value for the column 'id'
	 * $success = $connection->insert(
	 *     "robots",
	 *     [
	 *         $connection->getDefaultIdValue(),
	 *         "Astro Boy",
	 *         1952,
	 *     ],
	 *     [
	 *         "id",
	 *         "name",
	 *         "year",
	 *     ]
	 * );
	 *</code>
	 **/
    public function getDefaultIdValue() {
		return new RawValue("null");
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
	 *         $connection->getDefaultValue()
	 *     ],
	 *     [
	 *         "name",
	 *         "year",
	 *     ]
	 * );
	 *</code>
	 **/
    public function getDefaultValue() {
		return new RawValue("DEFAULT");
    }

    /***
	 * Check whether the database system requires a sequence to produce auto-numeric values
	 **/
    public function supportSequences() {
		return false;
    }

    /***
	 * Check whether the database system requires an explicit value for identity columns
	 **/
    public function useExplicitIdValue() {
		return false;
    }

    /***
	 * Return descriptor used to connect to the active database
	 **/
    public function getDescriptor() {
		return $this->_descriptor;
    }

    /***
	 * Gets the active connection unique identifier
	 *
	 * @return string
	 **/
    public function getConnectionId() {
		return $this->_connectionId;
    }

    /***
	 * Active SQL statement in the object
	 **/
    public function getSQLStatement() {
		return $this->_sqlStatement;
    }

    /***
	 * Active SQL statement in the object without replace bound parameters
	 **/
    public function getRealSQLStatement() {
		return $this->_sqlStatement;
    }

    /***
	 * Active SQL statement in the object
	 *
	 * @return array
	 **/
    public function getSQLBindTypes() {
		return $this->_sqlBindTypes;
    }

}