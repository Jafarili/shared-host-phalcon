<?php


namespace Phalcon\Db\Dialect;

use Phalcon\Db\Dialect;
use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\IndexInterface;
use Phalcon\Db\ColumnInterface;
use Phalcon\Db\ReferenceInterface;
use Phalcon\Db\DialectInterface;


/***
 * Phalcon\Db\Dialect\Mysql
 *
 * Generates database specific SQL for the MySQL RDBMS
 **/

class Mysql extends Dialect {

    protected $_escapeChar;

    /***
	 * Gets the column name in MySQL
	 **/
    public function getColumnDefinition($column ) {

		$columnSql = "";

		$type = column->getType();
		if ( gettype($type) == "string" ) {
			$columnSql .= type;
			$type = column->getTypeReference();
		}

		switch type {

			case Column::TYPE_INTEGER:
				if ( empty columnSql ) {
					$columnSql .= "INT";
				}
				$columnSql .= "(" . column->getSize() . ")";
				if ( column->isUnsigned() ) {
					$columnSql .= " UNSIGNED";
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
					$columnSql .= "DECIMAL";
				}
				$columnSql .= "(" . column->getSize() . "," . column->getScale() . ")";
				if ( column->isUnsigned() ) {
					$columnSql .= " UNSIGNED";
				}
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
					$columnSql .= "CHAR";
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
					$columnSql .= "TINYINT(1)";
				}
				break;

			case Column::TYPE_FLOAT:
				if ( empty columnSql ) {
					$columnSql .= "FLOAT";
				}
				$size = column->getSize();
				if ( size ) {
					$scale = column->getScale();
					if ( scale ) {
						$columnSql .= "(" . size . "," . scale . ")";
					} else {
						$columnSql .= "(" . size . ")";
					}
				}
				if ( column->isUnsigned() ) {
					$columnSql .= " UNSIGNED";
				}
				break;

			case Column::TYPE_DOUBLE:
				if ( empty columnSql ) {
					$columnSql .= "DOUBLE";
				}
				$size = column->getSize();
				if ( size ) {
					$scale = column->getScale(),
						columnSql .= "(" . size;
					if ( scale ) {
						$columnSql .= "," . scale . ")";
					} else {
						$columnSql .= ")";
					}
				}
				if ( column->isUnsigned() ) {
					$columnSql .= " UNSIGNED";
				}
				break;

			case Column::TYPE_BIGINTEGER:
				if ( empty columnSql ) {
					$columnSql .= "BIGINT";
				}
				$scale = column->getSize();
				if ( scale ) {
					$columnSql .= "(" . column->getSize() . ")";
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
					throw new Exception("Unrecognized MySQL data type at column " . column->getName());
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

		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD `" . column->getName() . "` " . $this->getColumnDefinition(column);

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

		if ( column->isAutoIncrement() ) {
			$sql .= " AUTO_INCREMENT";
		}

		if ( column->isFirst() ) {
			$sql .= " FIRST";
		} else {
			$afterPosition = column->getAfterPosition();
			if ( afterPosition ) {
				$sql .=  " AFTER `" . afterPosition . "`";
			}
		}
		return sql;
    }

    /***
	 * Generates SQL to modify a column in a table
	 **/
    public function modifyColumn($tableName , $schemaName , $column , $currentColumn  = null ) {

		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " MODIFY `" . column->getName() . "` " . $this->getColumnDefinition(column);

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

		if ( column->isAutoIncrement() ) {
			$sql .= " AUTO_INCREMENT";
		}

		if ( column->isFirst() ) {
			$sql .= " FIRST";
		} else {
			$afterPosition = column->getAfterPosition();
			if ( afterPosition ) {
				$sql .=  " AFTER `" . afterPosition . "`";
			}
		}
		return sql;
    }

    /***
	 * Generates SQL to delete a column from a table
	 **/
    public function dropColumn($tableName , $schemaName , $columnName ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP COLUMN `" . columnName . "`";
    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addIndex($tableName , $schemaName , $index ) {

		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName);

		$indexType = index->getType();
		if ( !empty indexType ) {
			$sql .= " ADD " . indexType . " INDEX ";
		} else {
			$sql .= " ADD INDEX ";
		}

		$sql .= "`" . index->getName() . "` (" . $this->getColumnList(index->getColumns()) . ")";
		return sql;
    }

    /***
	 * Generates SQL to delete an index from a table
	 **/
    public function dropIndex($tableName , $schemaName , $indexName ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP INDEX `" . indexName . "`";
    }

    /***
	 * Generates SQL to add the primary key to a table
	 **/
    public function addPrimaryKey($tableName , $schemaName , $index ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD PRIMARY KEY (" . $this->getColumnList(index->getColumns()) . ")";
    }

    /***
	 * Generates SQL to delete primary key from a table
	 **/
    public function dropPrimaryKey($tableName , $schemaName ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP PRIMARY KEY";
    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ) {

		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD";
		if ( reference->getName() ) {
			$sql .= " CONSTRAINT `" . $reference->getName() . "`";
		}
		$sql .= " FOREIGN KEY (" . $this->getColumnList(reference->getColumns()) . ") REFERENCES " . $this->prepareTable(reference->getReferencedTable(), reference->getReferencedSchema()) . "(" . $this->getColumnList(reference->getReferencedColumns()) . ")";

		$onDelete = reference->getOnDelete();
		if ( !empty onDelete ) {
			$sql .= " ON DELETE " . onDelete;
		}

		$onUpdate = reference->getOnUpdate();
		if ( !empty onUpdate ) {
			$sql .= " ON UPDATE " . onUpdate;
		}

		return sql;
    }

    /***
	 * Generates SQL to delete a foreign key from a table
	 **/
    public function dropForeignKey($tableName , $schemaName , $referenceName ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP FOREIGN KEY `" . referenceName . "`";
    }

    /***
	 * Generates SQL to create a table
	 **/
    public function createTable($tableName , $schemaName , $definition ) {
			column, indexes, index, reference, references, indexName,
			indexSql, sql, columnLine, indexType,
			referenceSql, onDelete, onUpdate, defaultValue;

		if ( !fetch columns, definition["columns"] ) {
			throw new Exception("The index 'columns' is required in the definition array");
		}

		$table = $this->prepareTable(tableName, schemaName);

		$temporary = false;
		if ( fetch options, definition["options"] ) {
		}

		/**
		 * Create a temporary or normal table
		 */
		if ( temporary ) {
			$sql = "CREATE TEMPORARY TABLE " . table . " (\n\t";
		} else {
			$sql = "CREATE TABLE " . table . " (\n\t";
		}

		$createLines = [];
		foreach ( $columns as $column ) {

			$columnLine = "`" . column->getName() . "` " . $this->getColumnDefinition(column);

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

			/**
			 * Add an AUTO_INCREMENT clause
			 */
			if ( column->isAutoIncrement() ) {
				$columnLine .= " AUTO_INCREMENT";
			}

			/**
			 * Mark the column as primary key
			 */
			if ( column->isPrimary() ) {
				$columnLine .= " PRIMARY KEY";
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
				if ( indexName == "PRIMARY" ) {
					$indexSql = "PRIMARY KEY (" . $this->getColumnList(index->getColumns()) . ")";
				} else {
					if ( !empty indexType ) {
						$indexSql = indexType . " KEY `" . indexName . "` (" . $this->getColumnList(index->getColumns()) . ")";
					} else {
						$indexSql = "KEY `" . indexName . "` (" . $this->getColumnList(index->getColumns()) . ")";
					}
				}

				$createLines[] = indexSql;
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
		if ( isset definition["options"] ) {
			$sql .= " " . $this->_getTableOptions(definition);
		}

		return sql;
    }

    /***
	 * Generates SQL to truncate a table
	 **/
    public function truncateTable($tableName , $schemaName ) {

		if ( schemaName ) {
			$table = "`" . schemaName . "`.`" . tableName . "`";
		} else {
			$table = "`" . tableName . "`";
		}

		$sql = "TRUNCATE TABLE " . table;

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
			$sql = "DROP VIEW IF EXISTS " . view;
		} else {
			$sql = "DROP VIEW " . view;
		}

		return sql;
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
		if ( schemaName ) {
			return "SELECT IF(COUNT(*) > 0, 1, 0) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME`= '" . tableName . "' AND `TABLE_SCHEMA` = '" . schemaName . "'";
		}
		return "SELECT IF(COUNT(*) > 0, 1, 0) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_NAME` = '" . tableName . "' AND `TABLE_SCHEMA` = DATABASE()";
    }

    /***
	 * Generates SQL checking for the existence of a schema.view
	 **/
    public function viewExists($viewName , $schemaName  = null ) {
		if ( schemaName ) {
			return "SELECT IF(COUNT(*) > 0, 1, 0) FROM `INFORMATION_SCHEMA`.`VIEWS` WHERE `TABLE_NAME`= '" . viewName . "' AND `TABLE_SCHEMA`='" . schemaName . "'";
		}
		return "SELECT IF(COUNT(*) > 0, 1, 0) FROM `INFORMATION_SCHEMA`.`VIEWS` WHERE `TABLE_NAME`='" . viewName . "' AND `TABLE_SCHEMA` = DATABASE()";
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
		return "DESCRIBE " . $this->prepareTable(table, schema);
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
		if ( schemaName ) {
			return "SHOW TABLES FROM `" . schemaName . "`";
		}
		return "SHOW TABLES";
    }

    /***
	 * Generates the SQL to list all views of a schema or user
	 **/
    public function listViews($schemaName  = null ) {
		if ( schemaName ) {
			return "SELECT `TABLE_NAME` AS view_name FROM `INFORMATION_SCHEMA`.`VIEWS` WHERE `TABLE_SCHEMA` = '" . schemaName . "' ORDER BY view_name";
		}
		return "SELECT `TABLE_NAME` AS view_name FROM `INFORMATION_SCHEMA`.`VIEWS` WHERE `TABLE_SCHEMA` = DATABASE() ORDER BY view_name";
    }

    /***
	 * Generates SQL to query indexes on a table
	 **/
    public function describeIndexes($table , $schema  = null ) {
		return "SHOW INDEXES FROM " . $this->prepareTable(table, schema);
    }

    /***
	 * Generates SQL to query foreign keys on a table
	 **/
    public function describeReferences($table , $schema  = null ) {
		if ( schema ) {
			$sql .= "KCU.CONSTRAINT_SCHEMA = '" . schema . "' AND KCU.TABLE_NAME = '" . table . "'";
		} else {
			$sql .= "KCU.CONSTRAINT_SCHEMA = DATABASE() AND KCU.TABLE_NAME = '" . table . "'";
		}
		return sql;
    }

    /***
	 * Generates the SQL to describe the table creation options
	 **/
    public function tableOptions($table , $schema  = null ) {
		if ( schema ) {
			return sql . "TABLES.TABLE_SCHEMA = '" . schema . "' AND TABLES.TABLE_NAME = '" . table . "'";
		}
		return sql . "TABLES.TABLE_SCHEMA = DATABASE() AND TABLES.TABLE_NAME = '" . table . "'";
    }

    /***
	 * Generates SQL to add the table creation options
	 **/
    protected function _getTableOptions($definition ) {
			collationParts, tableOptions;

		if ( fetch options, definition["options"] ) {

			$tableOptions = [];

			/**
			 * Check if ( there is an ENGINE option
			 */
			if ( fetch engine, options["ENGINE"] ) {
				if ( engine ) {
					$tableOptions[] = "ENGINE=" . engine;
				}
			}

			/**
			 * Check if ( there is an AUTO_INCREMENT option
			 */
			if ( fetch autoIncrement, options["AUTO_INCREMENT"] ) {
				if ( autoIncrement ) {
					$tableOptions[] = "AUTO_INCREMENT=" . autoIncrement;
				}
			}

			/**
			 * Check if ( there is a TABLE_COLLATION option
			 */
			if ( fetch tableCollation, options["TABLE_COLLATION"] ) {
				if ( tableCollation ) {
					$collationParts = explode("_", tableCollation),
						tableOptions[] = "DEFAULT CHARSET=" . collationParts[0],
						tableOptions[] = "COLLATE=" . tableCollation;
				}
			}

			if ( count(tableOptions) ) {
				return join(" ", tableOptions);
			}
		}

		return "";
    }

    /***
	 * Generates SQL to check DB parameter FOREIGN_KEY_CHECKS.
	 **/
    public function getForeignKeyChecks() {

		$sql = "SELECT @@for (eign_key_checks";

		return sql;
    }

}