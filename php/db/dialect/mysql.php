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

    }

    /***
	 * Generates SQL to add a column to a table
	 **/
    public function addColumn($tableName , $schemaName , $column ) {

    }

    /***
	 * Generates SQL to modify a column in a table
	 **/
    public function modifyColumn($tableName , $schemaName , $column , $currentColumn  = null ) {

    }

    /***
	 * Generates SQL to delete a column from a table
	 **/
    public function dropColumn($tableName , $schemaName , $columnName ) {

    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addIndex($tableName , $schemaName , $index ) {

    }

    /***
	 * Generates SQL to delete an index from a table
	 **/
    public function dropIndex($tableName , $schemaName , $indexName ) {

    }

    /***
	 * Generates SQL to add the primary key to a table
	 **/
    public function addPrimaryKey($tableName , $schemaName , $index ) {

    }

    /***
	 * Generates SQL to delete primary key from a table
	 **/
    public function dropPrimaryKey($tableName , $schemaName ) {

    }

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ) {

    }

    /***
	 * Generates SQL to delete a foreign key from a table
	 **/
    public function dropForeignKey($tableName , $schemaName , $referenceName ) {

    }

    /***
	 * Generates SQL to create a table
	 **/
    public function createTable($tableName , $schemaName , $definition ) {

    }

    /***
	 * Generates SQL to truncate a table
	 **/
    public function truncateTable($tableName , $schemaName ) {

    }

    /***
	 * Generates SQL to drop a table
	 **/
    public function dropTable($tableName , $schemaName  = null , $ifExists  = true ) {

    }

    /***
	 * Generates SQL to create a view
	 **/
    public function createView($viewName , $definition , $schemaName  = null ) {

    }

    /***
	 * Generates SQL to drop a view
	 **/
    public function dropView($viewName , $schemaName  = null , $ifExists  = true ) {

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

    }

    /***
	 * Generates SQL checking for the existence of a schema.view
	 **/
    public function viewExists($viewName , $schemaName  = null ) {

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

    }

    /***
	 * Generates the SQL to list all views of a schema or user
	 **/
    public function listViews($schemaName  = null ) {

    }

    /***
	 * Generates SQL to query indexes on a table
	 **/
    public function describeIndexes($table , $schema  = null ) {

    }

    /***
	 * Generates SQL to query foreign keys on a table
	 **/
    public function describeReferences($table , $schema  = null ) {

    }

    /***
	 * Generates the SQL to describe the table creation options
	 **/
    public function tableOptions($table , $schema  = null ) {

    }

    /***
	 * Generates SQL to add the table creation options
	 **/
    protected function _getTableOptions($definition ) {

    }

    /***
	 * Generates SQL to check DB parameter FOREIGN_KEY_CHECKS.
	 **/
    public function getForeignKeyChecks() {

    }

}