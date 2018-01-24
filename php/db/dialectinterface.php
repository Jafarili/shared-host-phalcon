<?php


namespace Phalcon\Db;

use Phalcon\Db\ColumnInterface;
use Phalcon\Db\ReferenceInterface;
use Phalcon\Db\IndexInterface;


/***
 * Phalcon\Db\DialectInterface
 *
 * Interface for Phalcon\Db dialects
 **/

interface DialectInterface {

    /***
	 * Generates the SQL for LIMIT clause
	 **/
    public function limit($sqlQuery , $number ); 

    /***
	 * Returns a SQL modified with a FOR UPDATE clause
	 **/
    public function forUpdate($sqlQuery ); 

    /***
	 * Returns a SQL modified with a LOCK IN SHARE MODE clause
	 **/
    public function sharedLock($sqlQuery ); 

    /***
	 * Builds a SELECT statement
	 **/
    public function select($definition ); 

    /***
	 * Gets a list of columns
	 **/
    public function getColumnList($columnList ); 

    /***
	 * Gets the column name in RDBMS
	 **/
    public function getColumnDefinition($column ); 

    /***
	 * Generates SQL to add a column to a table
	 **/
    public function addColumn($tableName , $schemaName , $column ); 

    /***
	 * Generates SQL to modify a column in a table
	 **/
    public function modifyColumn($tableName , $schemaName , $column , $currentColumn  = null ); 

    /***
	 * Generates SQL to delete a column from a table
	 **/
    public function dropColumn($tableName , $schemaName , $columnName ); 

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addIndex($tableName , $schemaName , $index ); 

    /***
 	 * Generates SQL to delete an index from a table
	 **/
    public function dropIndex($tableName , $schemaName , $indexName ); 

    /***
	 * Generates SQL to add the primary key to a table
	 **/
    public function addPrimaryKey($tableName , $schemaName , $index ); 

    /***
	 * Generates SQL to delete primary key from a table
	 **/
    public function dropPrimaryKey($tableName , $schemaName ); 

    /***
	 * Generates SQL to add an index to a table
	 **/
    public function addForeignKey($tableName , $schemaName , $reference ); 

    /***
	 * Generates SQL to delete a foreign key from a table
	 **/
    public function dropForeignKey($tableName , $schemaName , $referenceName ); 

    /***
	 * Generates SQL to create a table
	 **/
    public function createTable($tableName , $schemaName , $definition ); 

    /***
	 * Generates SQL to create a view
	 **/
    public function createView($viewName , $definition , $schemaName  = null ); 

    /***
	 * Generates SQL to drop a table
	 **/
    public function dropTable($tableName , $schemaName ); 

    /***
	 * Generates SQL to drop a view
	 **/
    public function dropView($viewName , $schemaName  = null , $ifExists  = true ); 

    /***
	 * Generates SQL checking for the existence of a schema.table
	 **/
    public function tableExists($tableName , $schemaName  = null ); 

    /***
	 * Generates SQL checking for the existence of a schema.view
	 **/
    public function viewExists($viewName , $schemaName  = null ); 

    /***
	 * Generates SQL to describe a table
	 **/
    public function describeColumns($table , $schema  = null ); 

    /***
	 * List all tables in database
	 **/
    public function listTables($schemaName  = null ); 

    /***
	 * Generates SQL to query indexes on a table
	 **/
    public function describeIndexes($table , $schema  = null ); 

    /***
	 * Generates SQL to query foreign keys on a table
	 **/
    public function describeReferences($table , $schema  = null ); 

    /***
	 * Generates the SQL to describe the table creation options
	 **/
    public function tableOptions($table , $schema  = null ); 

    /***
	 * Checks whether the platform supports savepoints
	 **/
    public function supportsSavepoints(); 

    /***
	 * Checks whether the platform supports releasing savepoints.
	 **/
    public function supportsReleaseSavepoints(); 

    /***
	 * Generate SQL to create a new savepoint
	 **/
    public function createSavepoint($name ); 

    /***
	 * Generate SQL to release a savepoint
	 **/
    public function releaseSavepoint($name ); 

    /***
	 * Generate SQL to rollback a savepoint
	 **/
    public function rollbackSavepoint($name ); 

}