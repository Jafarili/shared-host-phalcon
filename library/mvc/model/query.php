<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Db\Column;
use Phalcon\Db\RawValue;
use Phalcon\Db\ResultInterface;
use Phalcon\Db\AdapterInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Row;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\ManagerInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\Model\Query\Status;
use Phalcon\Mvc\Model\Resultset\Complex;
use Phalcon\Mvc\Model\Query\StatusInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\RelationInterface;
use Phalcon\Mvc\Model\TransactionInterface;
use Phalcon\Db\DialectInterface;


/***
 * Phalcon\Mvc\Model\Query
 *
 * This class takes a PHQL intermediate representation and executes it.
 *
 *<code>
 * $phql = "SELECT c.price*0.16 AS taxes, c.* FROM Cars AS c JOIN Brands AS b
 *          WHERE b.name = :name: ORDER BY c.name";
 *
 * $result = $manager->executeQuery(
 *     $phql,
 *     [
 *         "name" => "Lamborghini",
 *     ]
 * );
 *
 * foreach ($result as $row) {
 *     echo "Name: ",  $row->cars->name, "\n";
 *     echo "Price: ", $row->cars->price, "\n";
 *     echo "Taxes: ", $row->taxes, "\n";
 * }
 *
 * // with transaction
 * use Phalcon\Mvc\Model\Query;
 * use Phalcon\Mvc\Model\Transaction;
 *
 * // $di needs to have the service "db" registered for this to work
 * $di = Phalcon\Di\FactoryDefault::getDefault();
 *
 * $phql = 'SELECT * FROM robot';
 *
 * $myTransaction = new Transaction($di);
 * $myTransaction->begin();
 *
 * $newRobot = new Robot();
 * $newRobot->setTransaction($myTransaction);
 * $newRobot->type = "mechanical";
 * $newRobot->name = "Astro Boy";
 * $newRobot->year = 1952;
 * $newRobot->save();
 *
 * $queryWithTransaction = new Query($phql, $di);
 * $queryWithTransaction->setTransaction($myTransaction);
 *
 * $resultWithEntries = $queryWithTransaction->execute();
 *
 * $queryWithOutTransaction = new Query($phql, $di);
 * $resultWithOutEntries = $queryWithTransaction->execute()
 *
 *</code>
 **/

class Query {

    const TYPE_SELECT= 309;

    const TYPE_INSERT= 306;

    const TYPE_UPDATE= 300;

    const TYPE_DELETE= 303;

    protected $_dependencyInjector;

    protected $_manager;

    protected $_metaData;

    protected $_type;

    protected $_phql;

    protected $_ast;

    protected $_intermediate;

    protected $_models;

    protected $_sqlAliases;

    protected $_sqlAliasesModels;

    protected $_sqlModelsAliases;

    protected $_sqlAliasesModelsInstances;

    protected $_sqlColumnAliases;

    protected $_modelsInstances;

    protected $_cache;

    protected $_cacheOptions;

    protected $_uniqueRow;

    protected $_bindParams;

    protected $_bindTypes;

    protected $_enableImplicitJoins;

    protected $_sharedLock;

    /***
	 * TransactionInterface so that the query can wrap a transaction
	 * around batch updates and intermediate selects within the transaction.
	 * however if a model got a transaction set inside it will use the local transaction instead of this one
	 **/
    protected $_transaction;

    static protected $_irPhqlCache;

    /***
	 * Phalcon\Mvc\Model\Query constructor
	 *
	 * @param string phql
	 * @param \Phalcon\DiInterface dependencyInjector
	 **/
    public function __construct($phql  = null , $dependencyInjector  = null , $options  = null ) {

    }

    /***
	 * Sets the dependency injection container
	 **/
    public function setDI($dependencyInjector ) {

    }

    /***
	 * Returns the dependency injection container
	 **/
    public function getDI() {

    }

    /***
	 * Tells to the query if only the first row in the resultset must be returned
	 **/
    public function setUniqueRow($uniqueRow ) {

    }

    /***
	 * Check if the query is programmed to get only the first row in the resultset
	 **/
    public function getUniqueRow() {

    }

    /***
	 * Replaces the model's name to its source name in a qualified-name expression
	 **/
    protected final function _getQualified($expr ) {

    }

    /***
	 * Resolves an expression in a single call argument
	 **/
    protected final function _getCallArgument($argument ) {

    }

    /***
	 * Resolves an expression in a single call argument
	 **/
    protected final function _getCaseExpression($expr ) {

    }

    /***
	 * Resolves an expression in a single call argument
	 **/
    protected final function _getFunctionCall($expr ) {

    }

    /***
	 * Resolves an expression from its intermediate code into a string
	 *
	 * @param array expr
	 * @param boolean quoting
	 * @return string
	 **/
    protected final function _getExpression($expr , $quoting  = true ) {

    }

    /***
	 * Resolves a column from its intermediate representation into an array used to determine
	 * if the resultset produced is simple or complex
	 **/
    protected final function _getSelectColumn($column ) {

    }

    /***
	 * Resolves a table in a SELECT statement checking if the model exists
	 *
	 * @param \Phalcon\Mvc\Model\ManagerInterface manager
	 * @param array qualifiedName
	 * @return string
	 **/
    protected final function _getTable($manager , $qualifiedName ) {

    }

    /***
	 * Resolves a JOIN clause checking if the associated models exist
	 **/
    protected final function _getJoin($manager , $join ) {

    }

    /***
	 * Resolves a JOIN type
	 *
	 * @param array join
	 * @return string
	 **/
    protected final function _getJoinType($join ) {

    }

    /***
	 * Resolves joins involving has-one/belongs-to/has-many relations
	 *
	 * @param string joinType
	 * @param string joinSource
	 * @param string modelAlias
	 * @param string joinAlias
	 * @param \Phalcon\Mvc\Model\RelationInterface relation
	 * @return array
	 **/
    protected final function _getSingleJoin($joinType , $joinSource , $modelAlias , $joinAlias , $relation ) {

    }

    /***
	 * Resolves joins involving many-to-many relations
	 *
	 * @param string joinType
	 * @param string joinSource
	 * @param string modelAlias
	 * @param string joinAlias
	 * @param \Phalcon\Mvc\Model\RelationInterface relation
	 * @return array
	 **/
    protected final function _getMultiJoin($joinType , $joinSource , $modelAlias , $joinAlias , $relation ) {

    }

    /***
	 * Processes the JOINs in the query returning an internal representation for the database dialect
	 *
	 * @param array select
	 * @return array
	 **/
    protected final function _getJoins($select ) {

    }

    /***
	 * Returns a processed order clause for a SELECT statement
	 *
	 * @param array|string $order
	 * @return array
	 **/
    protected final function _getOrderClause($order ) {

    }

    /***
	 * Returns a processed group clause for a SELECT statement
	 **/
    protected final function _getGroupClause($group ) {

    }

    /***
	 * Returns a processed limit clause for a SELECT statement
	 **/
    protected final function _getLimitClause($limitClause ) {

    }

    /***
	 * Analyzes a SELECT intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareSelect($ast  = null , $merge  = null ) {

    }

    /***
	 * Analyzes an INSERT intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareInsert() {

    }

    /***
	 * Analyzes an UPDATE intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareUpdate() {

    }

    /***
	 * Analyzes a DELETE intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareDelete() {

    }

    /***
	 * Parses the intermediate code produced by Phalcon\Mvc\Model\Query\Lang generating another
	 * intermediate representation that could be executed by Phalcon\Mvc\Model\Query
	 **/
    public function parse() {

    }

    /***
	 * Returns the current cache backend instance
	 **/
    public function getCache() {

    }

    /***
	 * Executes the SELECT intermediate representation producing a Phalcon\Mvc\Model\Resultset
	 **/
    protected final function _executeSelect($intermediate , $bindParams , $bindTypes , $simulate  = false ) {

    }

    /***
	 * Executes the INSERT intermediate representation producing a Phalcon\Mvc\Model\Query\Status
	 *
	 * @param array intermediate
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\StatusInterface
	 **/
    protected final function _executeInsert($intermediate , $bindParams , $bindTypes ) {

    }

    /***
	 * Executes the UPDATE intermediate representation producing a Phalcon\Mvc\Model\Query\Status
	 *
	 * @param array intermediate
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\StatusInterface
	 **/
    protected final function _executeUpdate($intermediate , $bindParams , $bindTypes ) {

    }

    /***
	 * Executes the DELETE intermediate representation producing a Phalcon\Mvc\Model\Query\Status
	 *
	 * @param array intermediate
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\StatusInterface
	 **/
    protected final function _executeDelete($intermediate , $bindParams , $bindTypes ) {

    }

    /***
	 * Query the records on which the UPDATE/DELETE operation well be done
	 *
	 * @param \Phalcon\Mvc\ModelInterface model
	 * @param array intermediate
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\ResultsetInterface
	 **/
    protected final function _getRelatedRecords($model , $intermediate , $bindParams , $bindTypes ) {

    }

    /***
	 * Executes a parsed PHQL statement
	 *
	 * @param array bindParams
	 * @param array bindTypes
	 * @return mixed
	 **/
    public function execute($bindParams  = null , $bindTypes  = null ) {

    }

    /***
	 * Executes the query returning the first result
	 *
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\ModelInterface
	 **/
    public function getSingleResult($bindParams  = null , $bindTypes  = null ) {

    }

    /***
	 * Sets the type of PHQL statement to be executed
	 **/
    public function setType($type ) {

    }

    /***
	 * Gets the type of PHQL statement executed
	 **/
    public function getType() {

    }

    /***
	 * Set default bind parameters
	 **/
    public function setBindParams($bindParams , $merge  = false ) {

    }

    /***
	 * Returns default bind params
	 *
	 * @return array
	 **/
    public function getBindParams() {

    }

    /***
	 * Set default bind parameters
	 **/
    public function setBindTypes($bindTypes , $merge  = false ) {

    }

    /***
	 * Set SHARED LOCK clause
	 **/
    public function setSharedLock($sharedLock  = false ) {

    }

    /***
	 * Returns default bind types
	 *
	 * @return array
	 **/
    public function getBindTypes() {

    }

    /***
	 * Allows to set the IR to be executed
	 **/
    public function setIntermediate($intermediate ) {

    }

    /***
	 * Returns the intermediate representation of the PHQL statement
	 *
	 * @return array
	 **/
    public function getIntermediate() {

    }

    /***
	 * Sets the cache parameters of the query
	 **/
    public function cache($cacheOptions ) {

    }

    /***
	 * Returns the current cache options
	 *
	 * @param array
	 **/
    public function getCacheOptions() {

    }

    /***
	 * Returns the SQL to be generated by the internal PHQL (only works in SELECT statements)
	 **/
    public function getSql() {

    }

    /***
	 * Destroys the internal PHQL cache
	 **/
    public static function clean() {

    }

    /***
	 * Gets the read connection from the model if there is no transaction set inside the query object
	 **/
    protected function getReadConnection($model , $intermediate  = null , $bindParams  = null , $bindTypes  = null ) {

    }

    /***
	 * Gets the write connection from the model if there is no transaction inside the query object
	 **/
    protected function getWriteConnection($model , $intermediate  = null , $bindParams  = null , $bindTypes  = null ) {

    }

    /***
	 * allows to wrap a transaction around all queries
	 **/
    public function setTransaction($transaction ) {

    }

}