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
 * Phalcon\Db\Dialect\Postgresql
 *
 * Generates database specific SQL for the PostgreSQL RDBMS
 **/

class Postgresql extends Dialect {

    protected $_escapeChar;

    /***
	 * Gets the column name in PostgreSQL
	 **/
    public function getColumnDefinition($column ) {

		$size = column->getSize();
		$columnType = column->getType();
		$columnSql = "";

		if ( gettype($columnType) == "string" ) {
			$columnSql .= columnType;
			$columnType = column->getTypeReference();
		}

		switch columnType {

			case Column::TYPE_INTEGER:
				if ( empty columnSql ) {
					if ( column->isAutoIncrement() ) {
						$columnSql .= "SERIAL";
					} else {
						$columnSql .= "INT";
					}
				}
				break;

			case Column::TYPE_DATE:
				if ( empty columnSql ) {
					$columnSql .= "DATE";
				}
				break;

			case Column::TYPE_VARCHAR:
				if ( empty columnSql ) {
					$columnSql .= "CHARACTER VARYING";
				}
				$columnSql .= "(" . size . ")";
				break;

			case Column::TYPE_DECIMAL:
				if ( empty columnSql ) {
					$columnSql .= "NUMERIC";
				}
				$columnSql .= "(" . size . "," . column->getScale() . ")";
				break;

			case Column::TYPE_DATETIME:
				if ( empty columnSql ) {
					$columnSql .= "TIMESTAMP";
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
				$columnSql .= "(" . size . ")";
				break;

			case Column::TYPE_TEXT:
				if ( empty columnSql ) {
					$columnSql .= "TEXT";
				}
				break;

			case Column::TYPE_FLOAT:
				if ( empty columnSql ) {
					$columnSql .= "FLOAT";
				}
				break;

			case Column::TYPE_BIGINTEGER:
				if ( empty columnSql ) {
					if ( column->isAutoIncrement() ) {
						$columnSql .= "BIGSERIAL";
					} else {
						$columnSql .= "BIGINT";
					}
				}
				break;

			case Column::TYPE_JSON:
				if ( empty columnSql ) {
					$columnSql .= "JSON";
				}
				break;

			case Column::TYPE_JSONB:
				if ( empty columnSql ) {
					$columnSql .= "JSONB";
				}
				break;

			case Column::TYPE_BOOLEAN:
				if ( empty columnSql ) {
					$columnSql .= "BOOLEAN";
				}
				break;

			default:
				if ( empty columnSql ) {
					throw new Exception("Unrecognized PostgreSQL data type at column " . column->getName());
				}

				$typeValues = column->getTypeValues();
				if ( !empty typeValues ) {
					if ( gettype($typeValues) == "array" ) {
						$valueSql = "";
						foreach ( $typeValues as $value ) {
							$valueSql .= "'" . addcslashes(value, "\'") . "', ";
						}
						$columnSql .= "(" . substr(valueSql, 0, -2) . ")";
					} else {
						$columnSql .= "('" . addcslashes(typeValues, "\'") . "')";
					}
				}
		}

		return columnSql;
    }

    /***
	 * Generates SQL to add a column to a table
	 **/
    public function addColumn($tableName , $schemaName , $column ) {

		$columnDefinition = $this->getColumnDefinition(column);

		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD COLUMN ";
		$sql .= "\"" . column->getName() . "\" " . columnDefinition;

		if ( column->hasDefault() ) {
			$sql .= " DEFAULT " . $this->_castDefault(column);
		}

		if ( column->isNotNull() ) {
			$sql .= " NOT NULL";
		}

		return sql;
    }

    /***
	 * Generates SQL to modify a column in a table
	 **/
    public function modifyColumn($tableName , $schemaName , $column , $currentColumn  = null ) {

		$columnDefinition = $this->getColumnDefinition(column),
			sqlAlterTable = "ALTER TABLE " . $this->prepareTable(tableName, schemaName);

		if ( gettype($currentColumn) != "object" ) {
			$currentColumn = column;
		}

		// Rename
		if ( column->getName() !== currentColumn->getName() ) {
			$sql .= sqlAlterTable . " RENAME COLUMN \"" . currentColumn->getName() . "\" TO \"" . column->getName() . "\";";
		}

		// Change type
		if ( column->getType() !== currentColumn->getType() ) {
			$sql .= sqlAlterTable . " ALTER COLUMN \"" . column->getName() . "\" TYPE " . columnDefinition . ";";
		}

		// NULL
		if ( column->isNotNull() !== currentColumn->isNotNull() ) {
			if ( column->isNotNull() ) {
				$sql .= sqlAlterTable . " ALTER COLUMN \"" . column->getName() . "\" SET NOT NULL;";
			} else {
				$sql .= sqlAlterTable . " ALTER COLUMN \"" . column->getName() . "\" DROP NOT NULL;";
			}
		}

		// DEFAULT
		if ( column->getDefault() !== currentColumn->getDefault() ) {
			if ( empty column->getDefault() && !empty currentColumn->getDefault() ) {
				$sql .= sqlAlterTable . " ALTER COLUMN \"" . column->getName() . "\" DROP DEFAULT;";
			}

			if ( column->hasDefault() ) {
				$defaultValue = $this->_castDefault(column);
				if ( memstr(strtoupper(columnDefinition), "BOOLEAN") ) {
					$sql .= " ALTER COLUMN \"" . column->getName() . "\" SET DEFAULT " . defaultValue;
				} else {
					$sql .= sqlAlterTable . " ALTER COLUMN \"" . column->getName() . "\" SET DEFAULT " . defaultValue;
				}
			}
		}

		return sql;
    }

    /***
	 * Generates SQL to delete a column from a table
	 **/
    public function dropColumn($tableName , $schemaName , $columnName ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP COLUMN \"" . columnName . "\"";
    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addIndex($tableName , $schemaName , $index ) {

		if ( index->getName() === "PRIMARY" ) {
			return $this->addPrimaryKey(tableName, schemaName, index);
		}

		$sql = "CREATE";

		$indexType = index->getType();
		if ( !empty indexType ) {
			$sql .= " " . indexType;
		}
		$sql .= " INDEX \"" . index->getName() . "\" ON " . $this->prepareTable(tableName, schemaName);

		$sql .= " (" . $this->getColumnList(index->getColumns()) . ")";
		return sql;
    }

    /***
	 * Generates SQL to delete an index from a table
	 **/
    public function dropIndex($tableName , $schemaName , $indexName ) {
		return "DROP INDEX \"" . indexName . "\"";
    }

    /***
	 * Generates SQL to add the primary key to a table
	 **/
    public function addPrimaryKey($tableName , $schemaName , $index ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD CONSTRAINT \"PRIMARY\" PRIMARY KEY (" . $this->getColumnList(index->getColumns()) . ")";
    }

    /***
	 * Generates SQL to delete primary key from a table
	 **/
    public function dropPrimaryKey($tableName , $schemaName ) {
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP CONSTRAINT \"PRIMARY\"";
    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ) {

    		$sql = "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " ADD";
    		if ( reference->getName() ) {
    			$sql .= " CONSTRAINT \"" . reference->getName() . "\"";
    		}
    		$sql .= " FOREIGN KEY (" . $this->getColumnList(reference->getColumns()) . ")"
    				 . " REFERENCES \"" . reference->getReferencedTable() . "\" (" . $this->getColumnList(reference->getReferencedColumns()) . ")";

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
		return "ALTER TABLE " . $this->prepareTable(tableName, schemaName) . " DROP CONSTRAINT \"" . referenceName . "\"";
    }

    /***
	 * Generates SQL to create a table
	 **/
    public function createTable($tableName , $schemaName , $definition ) {
			column, indexes, index, reference, references, indexName,
			indexSql, indexSqlAfterCreate, sql, columnLine, indexType,
			referenceSql, onDelete, onUpdate, primaryColumns,
			columnDefinition;

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
		$primaryColumns = [];
		foreach ( $columns as $column ) {

			$columnDefinition = $this->getColumnDefinition(column);
			$columnLine = "\"" . column->getName() . "\" " . columnDefinition;

			/**
			 * Add a Default clause
			 */
			if ( column->hasDefault() ) {
				$columnLine .= " DEFAULT " . $this->_castDefault(column);
			}

			/**
			 * Add a NOT NULL clause
			 */
			if ( column->isNotNull() ) {
				$columnLine .= " NOT NULL";
			}

			/**
			 * Mark the column as primary key
			 */
			if ( column->isPrimary() ) {
				$primaryColumns[] = column->getName() ;
			}

			$createLines[] = columnLine;
		}
		if ( !empty primaryColumns ) {
			$createLines[] = "PRIMARY KEY (" . $this->getColumnList(primaryColumns) . ")";
		}

		/**
		 * Create related indexes
		 */
		$indexSqlAfterCreate = "";
		if ( fetch indexes, definition["indexes"] ) {

			foreach ( $indexes as $index ) {

				$indexName = index->getName();
				$indexType = index->getType();
				$indexSql = "";

				/**
				 * If the index name is primary we add a primary key
				 */
				if ( indexName == "PRIMARY" ) {
					$indexSql = "CONSTRAINT \"PRIMARY\" PRIMARY KEY (" . $this->getColumnList(index->getColumns()) . ")";
				} else {
					if ( !empty indexType ) {
						$indexSql = "CONSTRAINT \"" . indexName . "\" " . indexType . " (" . $this->getColumnList(index->getColumns()) . ")";
					} else {

						$indexSqlAfterCreate .= "CREATE INDEX \"" . index->getName() . "\" ON " . $this->prepareTable(tableName, schemaName);

						$indexSqlAfterCreate .= " (" . $this->getColumnList(index->getColumns()) . ");";
					}
				}
				if ( !empty indexSql ) {
					$createLines[] = indexSql;
				}
			}
		}
		/**
		 * Create related references
		 */
		if ( fetch references, definition["references"] ) {
			foreach ( $references as $reference ) {

				$referenceSql = "CONSTRAINT \"" . reference->getName() . "\" FOREIGN KEY (" . $this->getColumnList(reference->getColumns()) . ") REFERENCES ";

				$referenceSql .= $this->prepareTable(reference->getReferencedTable(), schemaName);

				$referenceSql .= " (" . $this->getColumnList(reference->getReferencedColumns()) . ")";

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
		$sql .= ";" . indexSqlAfterCreate;

		return sql;
    }

    /***
	 * Generates SQL to truncate a table
	 **/
    public function truncateTable($tableName , $schemaName ) {

		if ( schemaName ) {
			$table = schemaName . "." . tableName;
		} else {
			$table = tableName;
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
			return "SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM infor (mation_schema.tables WHERE table_schema = '" . schemaName . "' AND table_name='" . tableName . "'";
		}
		return "SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM infor (mation_schema.tables WHERE table_schema = 'public' AND table_name='" . tableName . "'";
    }

    /***
	 * Generates SQL checking for the existence of a schema.view
	 **/
    public function viewExists($viewName , $schemaName  = null ) {
		if ( schemaName ) {
			return "SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM pg_views WHERE viewname='" . viewName . "' AND schemaname='" . schemaName . "'";
		}
		return "SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM pg_views WHERE viewname='" . viewName . "' AND schemaname='public'";
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
		if ( schema ) {
			return "SELECT DISTINCT c.column_name AS Field, c.data_type AS Type, c.character_maximum_length AS Size, c.numeric_precision AS NumericSize, c.numeric_scale AS NumericScale, c.is_nullable AS Null, CASE WHEN pkc.column_name NOTNULL THEN 'PRI' ELSE '' END AS Key, CASE WHEN c.data_type LIKE '%int%' AND c.column_default LIKE '%nextval%' THEN 'auto_increment' ELSE '' END AS Extra, c.ordinal_position AS Position, c.column_default FROM infor (mation_schema.columns c LEFT JOIN ( SELECT kcu.column_name, kcu.table_name, kcu.table_schema FROM infor (mation_schema.table_constraints tc INNER JOIN infor (mation_schema.key_column_usage kcu on (kcu.constraint_name = tc.constraint_name and kcu.table_name=tc.table_name and kcu.table_schema=tc.table_schema) WHERE tc.constraint_type='PRIMARY KEY') pkc ON (c.column_name=pkc.column_name AND c.table_schema = pkc.table_schema AND c.table_name=pkc.table_name) WHERE c.table_schema='" . schema . "' AND c.table_name='" . table . "' ORDER BY c.ordinal_position";
		}
		return "SELECT DISTINCT c.column_name AS Field, c.data_type AS Type, c.character_maximum_length AS Size, c.numeric_precision AS NumericSize, c.numeric_scale AS NumericScale, c.is_nullable AS Null, CASE WHEN pkc.column_name NOTNULL THEN 'PRI' ELSE '' END AS Key, CASE WHEN c.data_type LIKE '%int%' AND c.column_default LIKE '%nextval%' THEN 'auto_increment' ELSE '' END AS Extra, c.ordinal_position AS Position, c.column_default FROM infor (mation_schema.columns c LEFT JOIN ( SELECT kcu.column_name, kcu.table_name, kcu.table_schema FROM infor (mation_schema.table_constraints tc INNER JOIN infor (mation_schema.key_column_usage kcu on (kcu.constraint_name = tc.constraint_name and kcu.table_name=tc.table_name and kcu.table_schema=tc.table_schema) WHERE tc.constraint_type='PRIMARY KEY') pkc ON (c.column_name=pkc.column_name AND c.table_schema = pkc.table_schema AND c.table_name=pkc.table_name) WHERE c.table_schema='public' AND c.table_name='" . table . "' ORDER BY c.ordinal_position";
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
			return "SELECT table_name FROM infor (mation_schema.tables WHERE table_schema = '" . schemaName . "' ORDER BY table_name";
		}
		return "SELECT table_name FROM infor (mation_schema.tables WHERE table_schema = 'public' ORDER BY table_name";
    }

    /***
	 * Generates the SQL to list all views of a schema or user
	 *
	 * @param string schemaName
	 * @return string
	 **/
    public function listViews($schemaName  = null ) {
		if ( schemaName ) {
			return "SELECT viewname AS view_name FROM pg_views WHERE schemaname = '" . schemaName . "' ORDER BY view_name";
		}
		return "SELECT viewname AS view_name FROM pg_views WHERE schemaname = 'public' ORDER BY view_name";
    }

    /***
	 * Generates SQL to query indexes on a table
	 **/
    public function describeIndexes($table , $schema  = null ) {
		return "SELECT 0 as c0, t.relname as table_name, i.relname as key_name, 3 as c3, a.attname as column_name FROM pg_class t, pg_class i, pg_index ix, pg_attribute a WHERE t.oid = ix.indrelid AND i.oid = ix.indexrelid AND a.attrelid = t.oid AND a.attnum = ANY(ix.indkey) AND t.relkind = 'r' AND t.relname = '" . table . "' ORDER BY t.relname, i.relname;";
    }

    /***
	 * Generates SQL to query foreign keys on a table
	 **/
    public function describeReferences($table , $schema  = null ) {

		if ( schema ) {
			$sql .= "tc.table_schema = '" . schema . "' AND tc.table_name='" . table . "'";
		} else {
			$sql .= "tc.table_schema = 'public' AND tc.table_name='" . table . "'";
		}

		return sql;
    }

    /***
	 * Generates the SQL to describe the table creation options
	 **/
    public function tableOptions($table , $schema  = null ) {
		return "";
    }

    protected function _castDefault($column ) {

		$defaultValue = column->getDefault(),
			columnDefinition = $this->getColumnDefinition(column),
			columnType = column->getType();

		if ( memstr(strtoupper(columnDefinition), "BOOLEAN") ) {
			return defaultValue;
		}

		if ( memstr(strtoupper(defaultValue), "CURRENT_TIMESTAMP") ) {
			return "CURRENT_TIMESTAMP";
		}

		if ( columnType === Column::TYPE_INTEGER ||
			columnType === Column::TYPE_BIGINTEGER ||
			columnType === Column::TYPE_DECIMAL ||
			columnType === Column::TYPE_FLOAT ||
			columnType === Column::TYPE_DOUBLE {
			$preparedValue = (string) defaultValue;
		} else {
			$preparedValue = "'" . addcslashes(defaultValue, "\'") . "'";
		}

		return preparedValue;
    }

    protected function _getTableOptions($definition ) {
		return "";
    }

}