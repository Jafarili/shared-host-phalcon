<?php


namespace Phalcon\Db\Dialect;

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\IndexInterface;
use Phalcon\Db\Dialect;
use Phalcon\Db\DialectInterface;
use Phalcon\Db\ColumnInterface;
use Phalcon\Db\ReferenceInterface;


/***
 * Phalcon\Db\Dialect\Sqlite
 *
 * Generates database specific SQL for the Sqlite RDBMS
 **/

class Sqlite extends Dialect {

    protected $_escapeChar;

    /***
	 * Gets the column name in SQLite
	 **/
    public function getColumnDefinition($column ) {

		$columnSql = "";

		$type = column->getType();
		if ( gettype($type) == "string" ) {
			$columnSql .= type;
			$type = column->getTypeReference();
		}

		// SQLite has dynamic column typing. The conversion below maximizes
		// compatibility with other DBMS's while following the type affinity
		// rules: http://www.sqlite.org/datatype3.html.
		switch type {

			case Column::TYPE_INTEGER:
				if ( empty columnSql ) {
					$columnSql .= "INTEGER";
				}
				break;

			case Column::TYPE_DATE:
				if ( empty columnSql ) {
					$columnSql .= "DATE";
				}
				break;

			case Column::TYPE_VARCHAR:
				if ( empty columnSql ) {
					$columnSql .= "VARCHAR";
				}
				$columnSql .= "(" . column->getSize() . ")";
				break;

			case Column::TYPE_DECIMAL:
				if ( empty columnSql ) {
					$columnSql .= "NUMERIC";
				}
				$columnSql .= "(" . column->getSize() . "," . column->getScale() . ")";
				break;

			case Column::TYPE_DATETIME:
				if ( empty columnSql ) {
					$columnSql .= "DATETIME";
				}
				break;

			case Column::TYPE_TIMESTAMP:
				if ( empty columnSql ) {
					$columnSql .= "TIMESTAMP";
				}
				break;

			case Column::TYPE_CHAR:
				if ( empty columnSql ) {
					$columnSql .= "CHARACTER";
				}
				$columnSql .= "(" . column->getSize() . ")";
				break;

			case Column::TYPE_TEXT:
				if ( empty columnSql ) {
					$columnSql .= "TEXT";
				}
				break;

			case Column::TYPE_BOOLEAN:
				if ( empty columnSql ) {
					$columnSql .= "TINYINT";
				}
				break;

			case Column::TYPE_FLOAT:
				if ( empty columnSql ) {
					$columnSql .= "FLOAT";
				}
				break;

			case Column::TYPE_DOUBLE:
				if ( empty columnSql ) {
					$columnSql .= "DOUBLE";
				}
				if ( column->isUnsigned() ) {
					$columnSql .= " UNSIGNED";
				}
				break;

			case Column::TYPE_BIGINTEGER:
				if ( empty columnSql ) {
					$columnSql .= "BIGINT";
				}
				if ( column->isUnsigned() ) {
					$columnSql .= " UNSIGNED";
				}
				break;

			case Column::TYPE_TINYBLOB:
				if ( empty columnSql ) {
					$columnSql .= "TINYBLOB";
				}
				break;

			case Column::TYPE_BLOB:
				if ( empty columnSql ) {
					$columnSql .= "BLOB";
				}
				break;

			case Column::TYPE_MEDIUMBLOB:
				if ( empty columnSql ) {
					$columnSql .= "MEDIUMBLOB";
				}
				break;

			case Column::TYPE_LONGBLOB:
				if ( empty columnSql ) {
					$columnSql .= "LONGBLOB";
				}
				break;

			default:
				if ( empty columnSql ) {
					throw new Exception("Unrecognized SQLite data type at column " . column->getName());
				}

				$typeValues = column->getTypeValues();
				if ( !empty typeValues ) {
					if ( gettype($typeValues) == "array" ) {
						$valueSql = "";
						foreach ( $typeValues as $value ) {
							$valueSql .= "\"" . addcslashes(value, "\"") . "\", ";
						}
						$columnSql .= "(" . substr(valueSql, 0, -2) . ")";
					} else {
						$columnSql .= "(\"" . addcslashes(typeValues, "\"") . "\")";
					}
				}
		}

		return columnSql;
    }

    /***
	 * Generates SQL to add a column to a table
	 **/
    public function addColumn($tableName , $schemaName , $column ) {

		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD COLUMN ";

		$sql .= "\"" . column->getName() . "\" " . $this->getColumnDefinition(column);

		if ( column->hasDefault() ) {
			$defaultValue = column->getDefault();
			if ( memstr(strtoupper(defaultValue), "CURRENT_TIMESTAMP") ) {
				$sql .= " DEFAULT CURRENT_TIMESTAMP";
			} else {
				$sql .= " DEFAULT \"" . addcslashes(defaultValue, "\"") . "\"";
			}
		}

		if ( column->isNotNull() ) {
			$sql .= " NOT NULL";
		}

		if ( column->isAutoincrement() ) {
			$sql .= " PRIMARY KEY AUTOINCREMENT";
		}

		return sql;
    }

    /***
	 * Generates SQL to modify a column in a table
	 **/
    public function modifyColumn($tableName , $schemaName , $column , $currentColumn  = null ) {
		throw new Exception("Altering a DB column is not supported by SQLite");
    }

    /***
	 * Generates SQL to delete a column from a table
	 **/
    public function dropColumn($tableName , $schemaName , $columnName ) {
		throw new Exception("Dropping DB column is not supported by SQLite");
    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addIndex($tableName , $schemaName , $index ) {

		$indexType = index->getType();

		if ( !empty indexType ) {
			$sql = "CREATE " . indexType . " INDEX \"";
		} else {
			$sql = "CREATE INDEX \"";
		}

		if ( schemaName ) {
			$sql .= schemaName . "\".\"" . index->getName() . "\" ON \"" . tableName . "\" (";
		} else {
			$sql .= index->getName() . "\" ON \"" . tableName . "\" (";
		}

		$sql .= $this->getColumnList(index->getColumns()) . ")";
		return sql;
    }

    /***
	 * Generates SQL to delete an index from a table
	 **/
    public function dropIndex($tableName , $schemaName , $indexName ) {
		if ( schemaName ) {
			return "DROP INDEX \"" . schemaName . "\".\"" . indexName . "\"";
		}
		return "DROP INDEX \"" . indexName . "\"";
    }

    /***
	 * Generates SQL to add the primary key to a table
	 **/
    public function addPrimaryKey($tableName , $schemaName , $index ) {
		throw new Exception("Adding a primary key after table has been created is not supported by SQLite");
    }

    /***
	 * Generates SQL to delete primary key from a table
	 **/
    public function dropPrimaryKey($tableName , $schemaName ) {
		throw new Exception("Removing a primary key after table has been created is not supported by SQLite");
    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ) {
		throw new Exception("Adding a for (eign key constraint to an existing table is not supported by SQLite");
    }

    /***
	 * Generates SQL to delete a foreign key from a table
	 **/
    public function dropForeignKey($tableName , $schemaName , $referenceName ) {
		throw new Exception("Dropping a for (eign key constraint is not supported by SQLite");
    }

    /***
	 * Generates SQL to create a table
	 **/
    public function createTable($tableName , $schemaName , $definition ) {
			indexes, index, indexName, indexType, references, reference, defaultValue,
			referenceSql, onDelete, onUpdate, sql, hasPrimary;

		$table = $this->prepareTable(tableName, schemaName);

		$temporary = false;
		if ( fetch options, definition["options"] ) {
		}

		if ( !fetch columns, definition["columns"] ) {
			throw new Exception("The index 'columns' is required in the definition array");
		}

		/**
		 * Create a temporary or normal table
		 */
		if ( temporary ) {
			$sql = "CREATE TEMPORARY TABLE " . table . " (\n\t";
		} else {
			$sql = "CREATE TABLE " . table . " (\n\t";
		}

		$hasPrimary = false;
		$createLines = [];

		foreach ( $columns as $column ) {
			$columnLine = "`" . column->getName() . "` " . $this->getColumnDefinition(column);

			/**
			 * Mark the column as primary key
			 */
			if ( column->isPrimary() && !hasPrimary ) {
				$columnLine .= " PRIMARY KEY";
				$hasPrimary = true;
			}

			/**
			 * Add an AUTOINCREMENT clause
			 */
			if ( column->isAutoIncrement() && hasPrimary ) {
				$columnLine .= " AUTOINCREMENT";
			}

			/**
			 * Add a Default clause
			 */
			if ( column->hasDefault() ) {
				$defaultValue = column->getDefault();
				if ( memstr(strtoupper(defaultValue), "CURRENT_TIMESTAMP") ) {
					$columnLine .= " DEFAULT CURRENT_TIMESTAMP";
				} else {
					$columnLine .= " DEFAULT \"" . addcslashes(defaultValue, "\"") . "\"";
				}
			}

			/**
			 * Add a NOT NULL clause
			 */
			if ( column->isNotNull() ) {
				$columnLine .= " NOT NULL";
			}

			$createLines[] = columnLine;
		}

		/**
		 * Create related indexes
		 */
		if ( fetch indexes, definition["indexes"] ) {

			foreach ( $indexes as $index ) {

				$indexName = index->getName();
				$indexType = index->getType();

				/**
				 * If the index name is primary we add a primary key
				 */
				if ( indexName == "PRIMARY" && !hasPrimary ) {
					$createLines[] = "PRIMARY KEY (" . $this->getColumnList(index->getColumns()) . ")";
				} elseif ( !empty indexType && memstr(strtoupper(indexType), "UNIQUE") ) {
					$createLines[] = "UNIQUE (" . $this->getColumnList(index->getColumns()) . ")";
				}
			}
		}

		/**
		 * Create related references
		 */
		if ( fetch references, definition["references"] ) {
			foreach ( $references as $reference ) {
				$referenceSql = "CONSTRAINT `" . reference->getName() . "` FOREIGN KEY (" . $this->getColumnList(reference->getColumns()) . ")"
					. " REFERENCES `" . reference->getReferencedTable() . "`(" . $this->getColumnList(reference->getReferencedColumns()) . ")";

				$onDelete = reference->getOnDelete();
				if ( !empty onDelete ) {
					$referenceSql .= " ON DELETE " . onDelete;
				}

				$onUpdate = reference->getOnUpdate();
				if ( !empty onUpdate ) {
					$referenceSql .= " ON UPDATE " . onUpdate;
				}

				$createLines[] = referenceSql;
			}
		}

		$sql .= join(",\n\t", createLines) . "\n)";

		return sql;
    }

    /***
	 * Generates SQL to truncate a table
	 **/
    public function truncateTable($tableName , $schemaName ) {

		if ( schemaName ) {
			$table = schemaName . "\".\"" . tableName;
		} else {
			$table = tableName;
		}

		$sql = "DELETE FROM \"" . table . "\"";

		return sql;
    }

    /***
	 * Generates SQL to drop a table
	 **/
    public function dropTable($tableName , $schemaName  = null , $ifExists  = true ) {

		$table = $this->prepareTable(tableName, schemaName);

		if ( if (Exists ) {
			$sql = "DROP TABLE IF EXISTS " . table;
		} else {
			$sql = "DROP TABLE " . table;
		}

		return sql;
    }

    /***
	 * Generates SQL to create a view
	 **/
    public function createView($viewName , $definition , $schemaName  = null ) {

		if ( !fetch viewSql, definition["sql"] ) {
			throw new Exception("The index 'sql' is required in the definition array");
		}

		return "CREATE VIEW " . $this->prepareTable(viewName, schemaName) . " AS " . viewSql;
    }

    /***
	 * Generates SQL to drop a view
	 **/
    public function dropView($viewName , $schemaName  = null , $ifExists  = true ) {

		$view = $this->prepareTable(viewName, schemaName);

		if ( if (Exists ) {
			return "DROP VIEW IF EXISTS " . view;
		}
		return "DROP VIEW " . view;
    }

    /***
	 * Generates SQL checking for the existence of a schema.table
	 *
	 * <code>
	 * echo $dialect->tableExists("posts", "blog");
	 *
	 * echo $dialect->tableExists("posts");
	 * </code>
	 **/
    public function tableExists($tableName , $schemaName  = null ) {
		return "SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM sqlite_master WHERE type='table' AND tbl_name='" . tableName . "'";
    }

    /***
	 * Generates SQL checking for the existence of a schema.view
	 **/
    public function viewExists($viewName , $schemaName  = null ) {
		return "SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM sqlite_master WHERE type='view' AND tbl_name='" . viewName . "'";
    }

    /***
	 * Generates SQL describing a table
	 *
	 * <code>
	 * print_r(
	 *     $dialect->describeColumns("posts")
	 * );
	 * </code>
	 **/
    public function describeColumns($table , $schema  = null ) {
		return "PRAGMA table_info('" . table . "')";
    }

    /***
	 * List all tables in database
	 *
	 * <code>
	 * print_r(
	 *     $dialect->listTables("blog")
	 * );
	 * </code>
	 **/
    public function listTables($schemaName  = null ) {
		return "SELECT tbl_name FROM sqlite_master WHERE type = 'table' ORDER BY tbl_name";
    }

    /***
	 * Generates the SQL to list all views of a schema or user
	 **/
    public function listViews($schemaName  = null ) {
		return "SELECT tbl_name FROM sqlite_master WHERE type = 'view' ORDER BY tbl_name";
    }

    /***
	 * Generates the SQL to get query list of indexes
	 *
	 * <code>
	 * print_r(
	 *     $dialect->listIndexesSql("blog")
	 * );
	 * </code>
	 **/
    public function listIndexesSql($table , $schema  = null , $keyName  = null ) {
		string sql;

		$sql = "SELECT sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ". $this->escape(table) ." COLLATE NOCASE";

		if ( keyName ) {
			$sql .= " AND name = ". $this->escape(keyName) ." COLLATE NOCASE";
		}

		return sql;
    }

    /***
	 * Generates SQL to query indexes on a table
	 **/
    public function describeIndexes($table , $schema  = null ) {
		return "PRAGMA index_list('" . table . "')";
    }

    /***
	 * Generates SQL to query indexes detail on a table
	 **/
    public function describeIndex($index ) {
		return "PRAGMA index_info('" . index . "')";
    }

    /***
	 * Generates SQL to query foreign keys on a table
	 **/
    public function describeReferences($table , $schema  = null ) {
		return "PRAGMA for (eign_key_list('" . table . "')";
    }

    /***
	 * Generates the SQL to describe the table creation options
	 **/
    public function tableOptions($table , $schema  = null ) {
		return "";
    }

}