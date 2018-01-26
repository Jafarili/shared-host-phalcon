<?php


namespace Phalcon\Mvc;

use Phalcon\Di;
use Phalcon\Db\Column;
use Phalcon\Db\RawValue;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\ManagerInterface;
use Phalcon\Mvc\Model\MetaDataInterface;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Db\AdapterInterface;
use Phalcon\Db\DialectInterface;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\TransactionInterface;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\RelationInterface;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\MessageInterface;
use Phalcon\Mvc\Model\Message;
use Phalcon\ValidationInterface;
use Phalcon\Mvc\Model\ValidationFailed;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;


/***
 * Phalcon\Mvc\Model
 *
 * Phalcon\Mvc\Model connects business objects and database tables to create
 * a persistable domain model where logic and data are presented in one wrapping.
 * It‘s an implementation of the object-relational mapping (ORM).
 *
 * A model represents the information (data) of the application and the rules to manipulate that data.
 * Models are primarily used for managing the rules of interaction with a corresponding database table.
 * In most cases, each table in your database will correspond to one model in your application.
 * The bulk of your application's business logic will be concentrated in the models.
 *
 * Phalcon\Mvc\Model is the first ORM written in Zephir/C languages for PHP, giving to developers high performance
 * when interacting with databases while is also easy to use.
 *
 * <code>
 * $robot = new Robots();
 *
 * $robot->type = "mechanical";
 * $robot->name = "Astro Boy";
 * $robot->year = 1952;
 *
 * if ($robot->save() === false) {
 *     echo "Umh, We can store robots: ";
 *
 *     $messages = $robot->getMessages();
 *
 *     foreach ($messages as $message) {
 *         echo $message;
 *     }
 * } else {
 *     echo "Great, a new robot was saved successfully!";
 * }
 * </code>
 **/

abstract class Model {

    const TRANSACTION_INDEX= transaction;

    const OP_NONE= 0;

    const OP_CREATE= 1;

    const OP_UPDATE= 2;

    const OP_DELETE= 3;

    const DIRTY_STATE_PERSISTENT= 0;

    const DIRTY_STATE_TRANSIENT= 1;

    const DIRTY_STATE_DETACHED= 2;

    protected $_dependencyInjector;

    protected $_modelsManager;

    protected $_modelsMetaData;

    protected $_errorMessages;

    protected $_operationMade;

    protected $_dirtyState;

    protected $_transaction;

    protected $_uniqueKey;

    protected $_uniqueParams;

    protected $_uniqueTypes;

    protected $_skipped;

    protected $_related;

    protected $_snapshot;

    protected $_oldSnapshot;

    /***
	 * Phalcon\Mvc\Model constructor
	 **/
    public final function __construct($data  = null , $dependencyInjector  = null , $modelsManager  = null ) {
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = Di::getDefault();
		}

		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ORM");
		}

		$this->_dependencyInjector = dependencyInjector;

		/**
		 * Inject the manager service from the DI
		 */
		if ( gettype($modelsManager) != "object" ) {
			$modelsManager = <ManagerInterface> dependencyInjector->getShared("modelsManager");
			if ( gettype($modelsManager) != "object" ) {
				throw new Exception("The injected service 'modelsManager' is not valid");
			}
		}

		/**
		 * Update the models-manager
		 */
		$this->_modelsManager = modelsManager;

		/**
		 * The manager always initializes the object
		 */
		modelsManager->initialize(this);

		/**
		 * This allows the developer to execute initialization stuff every time an instance is created
		 */
		if ( method_exists(this, "onConstruct") ) {
			this->{"onConstruct"}(data);
		}

		if ( gettype($data) == "array" ) {
			this->assign(data);
		}
    }

    /***
	 * Sets the dependency injection container
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the dependency injection container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets a custom events manager
	 **/
    protected function setEventsManager($eventsManager ) {
		this->_modelsManager->setCustomEventsManager(this, eventsManager);
    }

    /***
	 * Returns the custom events manager
	 **/
    protected function getEventsManager() {
		return $this->_modelsManager->getCustomEventsManager(this);
    }

    /***
	 * Returns the models meta-data service related to the entity instance
	 **/
    public function getModelsMetaData() {

		$metaData = $this->_modelsMetaData;
		if ( gettype($metaData) != "object" ) {

			$dependencyInjector = <DiInterface> $this->_dependencyInjector;

			/**
			 * Obtain the models-metadata service from the DI
			 */
			$metaData = <MetaDataInterface> dependencyInjector->getShared("modelsMetadata");
			if ( gettype($metaData) != "object" ) {
				throw new Exception("The injected service 'modelsMetadata' is not valid");
			}

			/**
			 * Update the models-metadata property
			 */
			$this->_modelsMetaData = metaData;
		}
		return metaData;
    }

    /***
	 * Returns the models manager related to the entity instance
	 **/
    public function getModelsManager() {
		return $this->_modelsManager;
    }

    /***
	 * Sets a transaction related to the Model instance
	 *
	 *<code>
	 * use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
	 * use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
	 *
	 * try {
	 *     $txManager = new TxManager();
	 *
	 *     $transaction = $txManager->get();
	 *
	 *     $robot = new Robots();
	 *
	 *     $robot->setTransaction($transaction);
	 *
	 *     $robot->name       = "WALL·E";
	 *     $robot->created_at = date("Y-m-d");
	 *
	 *     if ($robot->save() === false) {
	 *         $transaction->rollback("Can't save robot");
	 *     }
	 *
	 *     $robotPart = new RobotParts();
	 *
	 *     $robotPart->setTransaction($transaction);
	 *
	 *     $robotPart->type = "head";
	 *
	 *     if ($robotPart->save() === false) {
	 *         $transaction->rollback("Robot part cannot be saved");
	 *     }
	 *
	 *     $transaction->commit();
	 * } catch (TxFailed $e) {
	 *     echo "Failed, reason: ", $e->getMessage();
	 * }
	 *</code>
	 **/
    public function setTransaction($transaction ) {
		$this->_transaction = transaction;
		return this;
    }

    /***
	 * Sets the table name to which model should be mapped
	 **/
    protected function setSource($source ) {
		(<ManagerInterface> $this->_modelsManager)->setModelSource(this, source);
		return this;
    }

    /***
	 * Returns the table name mapped in the model
	 **/
    public function getSource() {
		return (<ManagerInterface> $this->_modelsManager)->getModelSource(this);
    }

    /***
	 * Sets schema name where the mapped table is located
	 **/
    protected function setSchema($schema ) {
		return (<ManagerInterface> $this->_modelsManager)->setModelSchema(this, schema);
    }

    /***
	 * Returns schema name where the mapped table is located
	 **/
    public function getSchema() {
		return (<ManagerInterface> $this->_modelsManager)->getModelSchema(this);
    }

    /***
	 * Sets the DependencyInjection connection service name
	 **/
    public function setConnectionService($connectionService ) {
		(<ManagerInterface> $this->_modelsManager)->setConnectionService(this, connectionService);
		return this;
    }

    /***
	 * Sets the DependencyInjection connection service name used to read data
	 **/
    public function setReadConnectionService($connectionService ) {
		(<ManagerInterface> $this->_modelsManager)->setReadConnectionService(this, connectionService);
		return this;
    }

    /***
	 * Sets the DependencyInjection connection service name used to write data
	 **/
    public function setWriteConnectionService($connectionService ) {
		return (<ManagerInterface> $this->_modelsManager)->setWriteConnectionService(this, connectionService);
    }

    /***
	 * Returns the DependencyInjection connection service name used to read data related the model
	 **/
    public function getReadConnectionService() {
		return (<ManagerInterface> $this->_modelsManager)->getReadConnectionService(this);
    }

    /***
	 * Returns the DependencyInjection connection service name used to write data related to the model
	 **/
    public function getWriteConnectionService() {
		return (<ManagerInterface> $this->_modelsManager)->getWriteConnectionService(this);
    }

    /***
	 * Sets the dirty state of the object using one of the DIRTY_STATE_* constants
	 **/
    public function setDirtyState($dirtyState ) {
		$this->_dirtyState = dirtyState;
		return this;
    }

    /***
	 * Returns one of the DIRTY_STATE_* constants telling if the record exists in the database or not
	 **/
    public function getDirtyState() {
		return $this->_dirtyState;
    }

    /***
	 * Gets the connection used to read data for the model
	 **/
    public function getReadConnection() {

		$transaction = <TransactionInterface> $this->_transaction;
		if ( gettype($transaction) == "object" ) {
			return transaction->getConnection();
		}

		return (<ManagerInterface> $this->_modelsManager)->getReadConnection(this);
    }

    /***
	 * Gets the connection used to write data to the model
	 **/
    public function getWriteConnection() {

		$transaction = <TransactionInterface> $this->_transaction;
		if ( gettype($transaction) == "object" ) {
			return transaction->getConnection();
		}

		return (<ManagerInterface> $this->_modelsManager)->getWriteConnection(this);
    }

    /***
	 * Assigns values to a model from an array
	 *
	 * <code>
	 * $robot->assign(
	 *     [
	 *         "type" => "mechanical",
	 *         "name" => "Astro Boy",
	 *         "year" => 1952,
	 *     ]
	 * );
	 *
	 * // Assign by db row, column map needed
	 * $robot->assign(
	 *     $dbRow,
	 *     [
	 *         "db_type" => "type",
	 *         "db_name" => "name",
	 *         "db_year" => "year",
	 *     ]
	 * );
	 *
	 * // Allow assign only name and year
	 * $robot->assign(
	 *     $_POST,
	 *     null,
	 *     [
	 *         "name",
	 *         "year",
	 *     ]
	 * );
	 *
	 * // By default assign method will use setters if exist, you can disable it by using ini_set to directly use properties
	 *
	 * ini_set("phalcon.orm.disable_assign_setters", true);
	 *
	 * $robot->assign(
	 *     $_POST,
	 *     null,
	 *     [
	 *         "name",
	 *         "year",
	 *     ]
	 * );
	 * </code>
	 *
	 * @param array data
	 * @param array dataColumnMap array to transform keys of data to another
	 * @param array whiteList
	 * @return \Phalcon\Mvc\Model
	 **/
    public function assign($data , $dataColumnMap  = null , $whiteList  = null ) {

		$disableAssignSetters = globals_get("orm.disable_assign_setters");

		// apply column map for ( data, if ( exist
		if ( gettype($dataColumnMap) == "array" ) {
			$dataMapped = [];
			foreach ( key, $data as $value ) {
				if ( fetch keyMapped, dataColumnMap[key] ) {
					$dataMapped[keyMapped] = value;
				}
			}
		} else {
			$dataMapped = data;
		}

		if ( count(dataMapped) == 0 ) {
			return this;
		}

		$metaData = $this->getModelsMetaData();

		if ( globals_get("orm.column_renaming") ) {
			$columnMap = metaData->getColumnMap(this);
		} else {
			$columnMap = null;
		}

		foreach ( $metaData->getAttributes(this) as $attribute ) {

			// Check if ( we need to rename the field
			if ( gettype($columnMap) == "array" ) {
				if ( !fetch attributeField, columnMap[attribute] ) {
					if ( !globals_get("orm.ignore_unknown_columns") ) {
						throw new Exception("Column '" . attribute. "' doesn\'t make part of the column map");
					} else {
						continue;
					}
				}
			} else {
				$attributeField = attribute;
			}

			// The value in the array passed
			// Check if ( we there is data for ( the field
			if ( fetch value, dataMapped[attributeField] ) {

				// If white-list exists check if ( the attribute is on that list
				if ( gettype($whiteList) == "array" ) {
					if ( !in_array(attributeField, whiteList) ) {
						continue;
					}
				}

				// Try to find a possible getter
				if ( disableAssignSetters || !this->_possibleSetter(attributeField, value) ) {
					$this->{attributeField} = value;
				}
			}
		}

		return this;
    }

    /***
	 * Assigns values to a model from an array, returning a new model.
	 *
	 *<code>
	 * $robot = \Phalcon\Mvc\Model::cloneResultMap(
	 *     new Robots(),
	 *     [
	 *         "type" => "mechanical",
	 *         "name" => "Astro Boy",
	 *         "year" => 1952,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param \Phalcon\Mvc\ModelInterface|\Phalcon\Mvc\Model\Row base
	 * @param array data
	 * @param array columnMap
	 * @param int dirtyState
	 * @param boolean keepSnapshots
	 **/
    public static function cloneResultMap($base , $data , $columnMap , $dirtyState  = 0 , $keepSnapshots  = null ) {

		$instance = clone base;

		// Change the dirty state to persistent
		instance->setDirtyState(dirtyState);

		foreach ( key, $data as $value ) {

			if ( gettype($key) == "string" ) {

				// Only string keys in the data are valid
				if ( gettype($columnMap) != "array" ) {
					$instance->{key} = value;
					continue;
				}

				// Every field must be part of the column map
				if ( !fetch attribute, columnMap[key] ) {
					if ( !globals_get("orm.ignore_unknown_columns") ) {
						throw new Exception("Column '" . key . "' doesn't make part of the column map");
					} else {
						continue;
					}
				}

				if ( gettype($attribute) != "array" ) {
					$instance->{attribute} = value;
					continue;
				}

				if ( value != "" && value !== null ) {
					switch attribute[1] {

						case Column::TYPE_INTEGER:
							$castValue = intval(value, 10);
							break;

						case Column::TYPE_DOUBLE:
						case Column::TYPE_DECIMAL:
						case Column::TYPE_FLOAT:
							$castValue = doubleval(value);
							break;

						case Column::TYPE_BOOLEAN:
							$castValue = (boolean) value;
							break;

						default:
							$castValue = value;
							break;
					}
				} else {
					switch attribute[1] {

						case Column::TYPE_INTEGER:
						case Column::TYPE_DOUBLE:
						case Column::TYPE_DECIMAL:
						case Column::TYPE_FLOAT:
						case Column::TYPE_BOOLEAN:
							$castValue = null;
							break;

						default:
							$castValue = value;
							break;
					}
				}

				$attributeName = attribute[0],
					instance->{attributeName} = castValue;
			}
		}

		/**
		 * Models that keep snapshots store the original data in t
		 */
		if ( keepSnapshots ) {
			instance->setSnapshotData(data, columnMap);
			instance->setOldSnapshotData(data, columnMap);
		}

		/**
		 * Call afterFetch, this allows the developer to execute actions after a record is fetched from the database
		 */
		if ( method_exists(instance, "fireEvent") ) {
			instance->{"fireEvent"}("afterFetch");
		}

		return instance;
    }

    /***
	 * Returns an hydrated result based on the data and the column map
	 *
	 * @param array data
	 * @param array columnMap
	 * @param int hydrationMode
	 * @return mixed
	 **/
    public static function cloneResultMapHydrate($data , $columnMap , $hydrationMode ) {

		/**
		 * If there is no column map and the hydration mode is arrays return the data as it is
		 */
		if ( gettype($columnMap) != "array" ) {
			if ( hydrationMode == Resultset::HYDRATE_ARRAYS ) {
				return data;
			}
		}

		/**
		 * Create the destination object according to the hydration mode
		 */
		if ( hydrationMode == Resultset::HYDRATE_ARRAYS ) {
			$hydrateArray = [];
		} else {
			$hydrateObject = new \stdclass();
		}

		foreach ( key, $data as $value ) {
			if ( gettype($key) != "string" ) {
				continue;
			}

			if ( gettype($columnMap) == "array" ) {

				/**
				 * Every field must be part of the column map
				 */
				if ( !fetch attribute, columnMap[key] ) {
					if ( !globals_get("orm.ignore_unknown_columns") ) {
						throw new Exception("Column '" . key . "' doesn't make part of the column map");
					} else {
						continue;
					}
				}

				/**
				 * Attribute can store info about his type
				 */
				if ( (gettype($attribute) == "array") ) {
					$attributeName = attribute[0];
				} else {
					$attributeName = attribute;
				}

				if ( hydrationMode == Resultset::HYDRATE_ARRAYS ) {
					$hydrateArray[attributeName] = value;
				} else {
					$hydrateObject->{attributeName} = value;
				}
			} else {
				if ( hydrationMode == Resultset::HYDRATE_ARRAYS ) {
					$hydrateArray[key] = value;
				} else {
					$hydrateObject->{key} = value;
				}
			}
		}

		if ( hydrationMode == Resultset::HYDRATE_ARRAYS ) {
			return hydrateArray;
		}

		return hydrateObject;
    }

    /***
	 * Assigns values to a model from an array returning a new model
	 *
	 *<code>
	 * $robot = Phalcon\Mvc\Model::cloneResult(
	 *     new Robots(),
	 *     [
	 *         "type" => "mechanical",
	 *         "name" => "Astro Boy",
	 *         "year" => 1952,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param \Phalcon\Mvc\ModelInterface $base
	 * @param array data
	 * @param int dirtyState
	 * @return \Phalcon\Mvc\ModelInterface
	 **/
    public static function cloneResult($base , $data , $dirtyState  = 0 ) {

		/**
		 * Clone the base record
		 */
		$instance = clone base;

		/**
		 * Mark the object as persistent
		 */
		instance->setDirtyState(dirtyState);

		foreach ( key, $data as $value ) {
			if ( gettype($key) != "string" ) {
				throw new Exception("Invalid key in array data provided to dumpResult()");
			}
			$instance->{key} = value;
		}

		/**
		 * Call afterFetch, this allows the developer to execute actions after a record is fetched from the database
		 */
		(<ModelInterface> instance)->fireEvent("afterFetch");

		return instance;
    }

    /***
	 * Query for a set of records that match the specified conditions
	 *
	 * <code>
	 * // How many robots are there?
	 * $robots = Robots::find();
	 *
	 * echo "There are ", count($robots), "\n";
	 *
	 * // How many mechanical robots are there?
	 * $robots = Robots::find(
	 *     "type = 'mechanical'"
	 * );
	 *
	 * echo "There are ", count($robots), "\n";
	 *
	 * // Get and print virtual robots ordered by name
	 * $robots = Robots::find(
	 *     [
	 *         "type = 'virtual'",
	 *         "order" => "name",
	 *     ]
	 * );
	 *
	 * foreach ($robots as $robot) {
	 *	 echo $robot->name, "\n";
	 * }
	 *
	 * // Get first 100 virtual robots ordered by name
	 * $robots = Robots::find(
	 *     [
	 *         "type = 'virtual'",
	 *         "order" => "name",
	 *         "limit" => 100,
	 *     ]
	 * );
	 *
	 * foreach ($robots as $robot) {
	 *	 echo $robot->name, "\n";
	 * }
	 *
	 * // encapsulate find it into an running transaction esp. useful for application unit-tests
	 * // or complex business logic where we wanna control which transactions are used.
	 *
	 * $myTransaction = new Transaction(\Phalcon\Di::getDefault());
	 * $myTransaction->begin();
	 * $newRobot = new Robot();
	 * $newRobot->setTransaction($myTransaction);
	 * $newRobot->save(['name' => 'test', 'type' => 'mechanical', 'year' => 1944]);
	 *
	 * $resultInsideTransaction = Robot::find(['name' => 'test', Model::TRANSACTION_INDEX => $myTransaction]);
	 * $resultOutsideTransaction = Robot::find(['name' => 'test']);
	 *
	 * foreach ($setInsideTransaction as $robot) {
	 *     echo $robot->name, "\n";
	 * }
	 *
	 * foreach ($setOutsideTransaction as $robot) {
	 *     echo $robot->name, "\n";
	 * }
	 *
	 * // reverts all not commited changes
	 * $myTransaction->rollback();
	 *
	 * // creating two different transactions
	 * $myTransaction1 = new Transaction(\Phalcon\Di::getDefault());
	 * $myTransaction1->begin();
	 * $myTransaction2 = new Transaction(\Phalcon\Di::getDefault());
	 * $myTransaction2->begin();
	 *
	 *  // add a new robots
	 * $firstNewRobot = new Robot();
	 * $firstNewRobot->setTransaction($myTransaction1);
	 * $firstNewRobot->save(['name' => 'first-transaction-robot', 'type' => 'mechanical', 'year' => 1944]);
	 *
	 * $secondNewRobot = new Robot();
	 * $secondNewRobot->setTransaction($myTransaction2);
	 * $secondNewRobot->save(['name' => 'second-transaction-robot', 'type' => 'fictional', 'year' => 1984]);
	 *
	 * // this transaction will find the robot.
	 * $resultInFirstTransaction = Robot::find(['name' => 'first-transaction-robot', Model::TRANSACTION_INDEX => $myTransaction1]);
	 * // this transaction won't find the robot.
	 * $resultInSecondTransaction = Robot::find(['name' => 'first-transaction-robot', Model::TRANSACTION_INDEX => $myTransaction2]);
	 * // this transaction won't find the robot.
	 * $resultOutsideAnyExplicitTransaction = Robot::find(['name' => 'first-transaction-robot']);
	 *
	 * // this transaction won't find the robot.
	 * $resultInFirstTransaction = Robot::find(['name' => 'second-transaction-robot', Model::TRANSACTION_INDEX => $myTransaction2]);
	 * // this transaction will find the robot.
	 * $resultInSecondTransaction = Robot::find(['name' => 'second-transaction-robot', Model::TRANSACTION_INDEX => $myTransaction1]);
	 * // this transaction won't find the robot.
	 * $resultOutsideAnyExplicitTransaction = Robot::find(['name' => 'second-transaction-robot']);
	 *
	 * $transaction1->rollback();
	 * $transaction2->rollback();
	 * </code>
	 **/
    public static function find($parameters  = null ) {

		if ( gettype($parameters) != "array" ) {
			$params = [];
			if ( parameters !== null ) {
				$params[] = parameters;
			}
		} else {
			$params = parameters;
		}

		$query = static::getPreparedQuery(params);

		/**
		 * Execute the query passing the bind-params and casting-types
		 */
		$resultset = query->execute();

		/**
		 * Define an hydration mode
		 */
		if ( gettype($resultset) == "object" ) {
			if ( fetch hydration, params["hydration"] ) {
				resultset->setHydrateMode(hydration);
			}
		}

		return resultset;
    }

    /***
	 * Query the first record that matches the specified conditions
	 *
	 * <code>
	 * // What's the first robot in robots table?
	 * $robot = Robots::findFirst();
	 *
	 * echo "The robot name is ", $robot->name;
	 *
	 * // What's the first mechanical robot in robots table?
	 * $robot = Robots::findFirst(
	 *	 "type = 'mechanical'"
	 * );
	 *
	 * echo "The first mechanical robot name is ", $robot->name;
	 *
	 * // Get first virtual robot ordered by name
	 * $robot = Robots::findFirst(
	 *     [
	 *         "type = 'virtual'",
	 *         "order" => "name",
	 *     ]
	 * );
	 *
	 * echo "The first virtual robot name is ", $robot->name;
	 *
	 * // behaviour with transaction
	 * $myTransaction = new Transaction(\Phalcon\Di::getDefault());
	 * $myTransaction->begin();
	 * $newRobot = new Robot();
	 * $newRobot->setTransaction($myTransaction);
	 * $newRobot->save(['name' => 'test', 'type' => 'mechanical', 'year' => 1944]);
	 *
	 * $findsARobot = Robot::findFirst(['name' => 'test', Model::TRANSACTION_INDEX => $myTransaction]);
	 * $doesNotFindARobot = Robot::findFirst(['name' => 'test']);
	 *
	 * var_dump($findARobot);
	 * var_dump($doesNotFindARobot);
	 *
	 * $transaction->commit();
	 * $doesFindTheRobotNow = Robot::findFirst(['name' => 'test']);
	 * </code>
	 **/
    public static function findFirst($parameters  = null ) {

		if ( gettype($parameters) != "array" ) {
			$params = [];
			if ( parameters !== null ) {
				$params[] = parameters;
			}
		} else {
			$params = parameters;
		}

		$query = static::getPreparedQuery(params, 1);

		/**
		 * Return only the first row
		 */
		query->setUniqueRow(true);

		/**
		 * Execute the query passing the bind-params and casting-types
		 */
		return query->execute();
    }

    /***
	 * shared prepare query logic for find and findFirst method
	 **/
    private static function getPreparedQuery($params , $limit  = null ) {

		$dependencyInjector = Di::getDefault();
		$manager = <ManagerInterface> dependencyInjector->getShared("modelsManager");

		/**
		 * Builds a query with the passed parameters
		 */
		$builder = manager->createBuilder(params);
		builder->from(get_called_class());

		if ( limit != null ) {
			builder->limit(limit);
		}

		$query = builder->getQuery();

		/**
		 * Check for ( bind parameters
		 */
		if ( fetch bindParams, params["bind"] ) {
			if ( gettype($bindParams) == "array" ) {
				query->setBindParams(bindParams, true);
			}

			if ( fetch bindTypes, params["bindTypes"] ) {
				if ( gettype($bindTypes) == "array" ) {
					query->setBindTypes(bindTypes, true);
				}
			}
		}

		if ( fetch transaction, params[self::TRANSACTION_INDEX] ) {
			if ( transaction instanceof TransactionInterface ) {
				query->setTransaction(transaction);
			}
		}

		/**
		 * Pass the cache options to the query
		 */
		if ( fetch cache, params["cache"] ) {
			query->cache(cache);
		}

		return query;
    }

    /***
	 * Create a criteria for a specific model
	 **/
    public static function query($dependencyInjector  = null ) {

		/**
		 * Use the global dependency injector if ( there is no one defined
		 */
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = Di::getDefault();
		}

		/**
		 * Gets Criteria instance from DI container
		 */
		if ( dependencyInjector instanceof DiInterface ) {
			$criteria = <CriteriaInterface> dependencyInjector->get("Phalcon\\Mvc\\Model\\Criteria");
		} else {
			$criteria = new Criteria();
			criteria->setDI(dependencyInjector);
		}

		criteria->setModelName(get_called_class());

		return criteria;
    }

    /***
	 * Checks whether the current record already exists
	 *
	 * @param \Phalcon\Mvc\Model\MetaDataInterface metaData
	 * @param \Phalcon\Db\AdapterInterface connection
	 * @param string|array table
	 * @return boolean
	 **/
    protected function _exists($metaData , $connection , $table  = null ) {
		int numberEmpty, numberPrimary;
			wherePk, field, attributeField, value, bindDataTypes,
			joinWhere, num, type, schema, source;

		$uniqueParams = null,
			uniqueTypes = null;

		/**
		 * Builds a unique primary key condition
		 */
		$uniqueKey = $this->_uniqueKey;
		if ( uniqueKey === null ) {

			$primaryKeys = metaData->getPrimaryKeyAttributes(this),
				bindDataTypes = metaData->getBindTypes(this);

			$numberPrimary = count(primaryKeys);
			if ( !numberPrimary ) {
				return false;
			}

			/**
			 * Check if ( column renaming is globally activated
			 */
			if ( globals_get("orm.column_renaming") ) {
				$columnMap = metaData->getColumnMap(this);
			} else {
				$columnMap = null;
			}

			$numberEmpty = 0,
				wherePk = [],
				uniqueParams = [],
				uniqueTypes = [];

			/**
			 * We need to create a primary key based on the current data
			 */
			foreach ( $primaryKeys as $field ) {

				if ( gettype($columnMap) == "array" ) {
					if ( !fetch attributeField, columnMap[field] ) {
						throw new Exception("Column '" . field . "' isn't part of the column map");
					}
				} else {
					$attributeField = field;
				}

				/**
				 * If the primary key attribute is set append it to the conditions
				 */
				$value = null;
				if ( fetch value, $this->) {attributeField} ) {

					/**
					 * We count how many fields are empty, if ( all fields are empty we don't perfor (m an 'exist' check
					 */
					if ( value === null || value === "" ) {
						$numberEmpty++;
					}
					$uniqueParams[] = value;

				} else {
					$uniqueParams[] = null,
						numberEmpty++;
				}

				if ( !fetch type, bindDataTypes[field] ) {
					throw new Exception("Column '" . field . "' isn't part of the table columns");
				}

				$uniqueTypes[] = type,
					wherePk[] = connection->escapeIdentif (ier(field) . " = ?";
			}

			/**
			 * There are no primary key fields defined, assume the record does not exist
			 */
			if ( numberPrimary == numberEmpty ) {
				return false;
			}

			$joinWhere = join(" AND ", wherePk);

			/**
			 * The unique key is composed of 3 parts _uniqueKey, uniqueParams, uniqueTypes
			 */
			$this->_uniqueKey = joinWhere,
				this->_uniqueParams = uniqueParams,
				this->_uniqueTypes = uniqueTypes,
				uniqueKey = joinWhere;
		}

		/**
		 * If we already know if ( the record exists we don't check it
		 */
		if ( !this->_dirtyState ) {
			return true;
		}

		if ( uniqueKey === null ) {
			$uniqueKey = $this->_uniqueKey;
		}

		if ( uniqueParams === null ) {
			$uniqueParams = $this->_uniqueParams;
		}

		if ( uniqueTypes === null ) {
			$uniqueTypes = $this->_uniqueTypes;
		}

		$schema = $this->getSchema(), source = $this->getSource();
		if ( schema ) {
			$table = [schema, source];
		} else {
			$table = source;
		}

		/**
		 * Here we use a single COUNT(*) without PHQL to make the execution faster
		 */
		$num = connection->fetchOne(
			"SELECT COUNT(*) \"rowcount\" FROM " . connection->escapeIdentif (ier(table) . " WHERE " . uniqueKey,
			null,
			uniqueParams,
			uniqueTypes
		);
		if ( num["rowcount"] ) {
			$this->_dirtyState = self::DIRTY_STATE_PERSISTENT;
			return true;
		} else {
			$this->_dirtyState = self::DIRTY_STATE_TRANSIENT;
		}

		return false;
    }

    /***
	 * Generate a PHQL SELECT statement for an aggregate
	 *
	 * @param string function
	 * @param string alias
	 * @param array parameters
	 * @return \Phalcon\Mvc\Model\ResultsetInterface
	 **/
    protected static function _groupResult($functionName , $alias , $parameters ) {
			bindParams, bindTypes, resultset, cache, firstRow, groupColumns,
			builder, query, dependencyInjector, manager;

		$dependencyInjector = Di::getDefault();
		$manager = <ManagerInterface> dependencyInjector->getShared("modelsManager");

		if ( gettype($parameters) != "array" ) {
			$params = [];
			if ( parameters !== null ) {
				$params[] = parameters;
			}
		} else {
			$params = parameters;
		}

		if ( !fetch groupColumn, params["column"] ) {
			$groupColumn = "*";
		}

		/**
		 * Builds the columns to query according to the received parameters
		 */
		if ( fetch distinctColumn, params["distinct"] ) {
			$columns = functionName . "(DISTINCT " . distinctColumn . ") AS " . alias;
		} else {
			if ( fetch groupColumns, params["group"] ) {
				$columns = groupColumns . ", " . functionName . "(" . groupColumn . ") AS " . alias;
			} else {
				$columns = functionName . "(" . groupColumn . ") AS " . alias;
			}
		}

		/**
		 * Builds a query with the passed parameters
		 */
		$builder = manager->createBuilder(params);
		builder->columns(columns);
		builder->from(get_called_class());

		$query = builder->getQuery();

		/**
		 * Check for ( bind parameters
		 */
		$bindParams = null, bindTypes = null;
		if ( fetch bindParams, params["bind"] ) {
		}

		/**
		 * Pass the cache options to the query
		 */
		if ( fetch cache, params["cache"] ) {
			query->cache(cache);
		}

		/**
		 * Execute the query
		 */
		$resultset = query->execute(bindParams, bindTypes);

		/**
		 * Return the full resultset if ( the query is grouped
		 */
		if ( isset params["group"] ) {
			return resultset;
		}

		/**
		 * Return only the value in the first result
		 */
		$firstRow = resultset->getFirst();
		return firstRow->{alias};
    }

    /***
	 * Counts how many records match the specified conditions
	 *
	 * <code>
	 * // How many robots are there?
	 * $number = Robots::count();
	 *
	 * echo "There are ", $number, "\n";
	 *
	 * // How many mechanical robots are there?
	 * $number = Robots::count("type = 'mechanical'");
	 *
	 * echo "There are ", $number, " mechanical robots\n";
	 * </code>
	 *
	 * @param array parameters
	 * @return mixed
	 **/
    public static function count($parameters  = null ) {

		$result = self::_groupResult("COUNT", "rowcount", parameters);
		if ( gettype($result) == "string" ) {
			return (int) result;
		}
		return result;
    }

    /***
	 * Calculates the sum on a column for a result-set of rows that match the specified conditions
	 *
	 * <code>
	 * // How much are all robots?
	 * $sum = Robots::sum(
	 *     [
	 *         "column" => "price",
	 *     ]
	 * );
	 *
	 * echo "The total price of robots is ", $sum, "\n";
	 *
	 * // How much are mechanical robots?
	 * $sum = Robots::sum(
	 *     [
	 *         "type = 'mechanical'",
	 *         "column" => "price",
	 *     ]
	 * );
	 *
	 * echo "The total price of mechanical robots is  ", $sum, "\n";
	 * </code>
	 *
	 * @param array parameters
	 * @return mixed
	 **/
    public static function sum($parameters  = null ) {
		return self::_groupResult("SUM", "sumatory", parameters);
    }

    /***
	 * Returns the maximum value of a column for a result-set of rows that match the specified conditions
	 *
	 * <code>
	 * // What is the maximum robot id?
	 * $id = Robots::maximum(
	 *     [
	 *         "column" => "id",
	 *     ]
	 * );
	 *
	 * echo "The maximum robot id is: ", $id, "\n";
	 *
	 * // What is the maximum id of mechanical robots?
	 * $sum = Robots::maximum(
	 *     [
	 *         "type = 'mechanical'",
	 *         "column" => "id",
	 *     ]
	 * );
	 *
	 * echo "The maximum robot id of mechanical robots is ", $id, "\n";
	 * </code>
	 *
	 * @param array parameters
	 * @return mixed
	 **/
    public static function maximum($parameters  = null ) {
		return self::_groupResult("MAX", "maximum", parameters);
    }

    /***
	 * Returns the minimum value of a column for a result-set of rows that match the specified conditions
	 *
	 * <code>
	 * // What is the minimum robot id?
	 * $id = Robots::minimum(
	 *     [
	 *         "column" => "id",
	 *     ]
	 * );
	 *
	 * echo "The minimum robot id is: ", $id;
	 *
	 * // What is the minimum id of mechanical robots?
	 * $sum = Robots::minimum(
	 *     [
	 *         "type = 'mechanical'",
	 *         "column" => "id",
	 *     ]
	 * );
	 *
	 * echo "The minimum robot id of mechanical robots is ", $id;
	 * </code>
	 *
	 * @param array parameters
	 * @return mixed
	 **/
    public static function minimum($parameters  = null ) {
		return self::_groupResult("MIN", "minimum", parameters);
    }

    /***
	 * Returns the average value on a column for a result-set of rows matching the specified conditions
	 *
	 * <code>
	 * // What's the average price of robots?
	 * $average = Robots::average(
	 *     [
	 *         "column" => "price",
	 *     ]
	 * );
	 *
	 * echo "The average price is ", $average, "\n";
	 *
	 * // What's the average price of mechanical robots?
	 * $average = Robots::average(
	 *     [
	 *         "type = 'mechanical'",
	 *         "column" => "price",
	 *     ]
	 * );
	 *
	 * echo "The average price of mechanical robots is ", $average, "\n";
	 * </code>
	 *
	 * @param array parameters
	 * @return double
	 **/
    public static function average($parameters  = null ) {
		return self::_groupResult("AVG", "average", parameters);
    }

    /***
	 * Fires an event, implicitly calls behaviors and listeners in the events manager are notified
	 **/
    public function fireEvent($eventName ) {
		if ( method_exists(this, eventName) ) {
			this->{eventName}();
		}

		/**
		 * Send a notif (ication to the events manager
		 */
		return (<ManagerInterface> $this->_modelsManager)->notif (yEvent(eventName, this);
    }

    /***
	 * Fires an event, implicitly calls behaviors and listeners in the events manager are notified
	 * This method stops if one of the callbacks/listeners returns boolean false
	 **/
    public function fireEventCancel($eventName ) {
		if ( method_exists(this, eventName) ) {
			if ( $this->) {eventName}() === false ) {
				return false;
			}
		}

		/**
		 * Send a notif (ication to the events manager
		 */
		if ( (<ManagerInterface> $this->_modelsManager)->notif (yEvent(eventName, this) === false ) {
			return false;
		}

		return true;
    }

    /***
	 * Cancel the current operation
	 **/
    protected function _cancelOperation() {
		if ( $this->_operationMade == self::OP_DELETE ) {
			this->fireEvent("notDeleted");
		} else {
			this->fireEvent("notSaved");
		}
    }

    /***
	 * Appends a customized message on the validation process
	 *
	 * <code>
	 * use Phalcon\Mvc\Model;
	 * use Phalcon\Mvc\Model\Message as Message;
	 *
	 * class Robots extends Model
	 * {
	 *     public function beforeSave()
	 *     {
	 *         if ($this->name === "Peter") {
	 *             $message = new Message(
	 *                 "Sorry, but a robot cannot be named Peter"
	 *             );
	 *
	 *             $this->appendMessage($message);
	 *         }
	 *     }
	 * }
	 * </code>
	 **/
    public function appendMessage($message ) {
		$this->_errorMessages[] = message;
		return this;
    }

    /***
	 * Executes validators on every validation call
	 *
	 *<code>
	 * use Phalcon\Mvc\Model;
	 * use Phalcon\Validation;
	 * use Phalcon\Validation\Validator\ExclusionIn;
	 *
	 * class Subscriptors extends Model
	 * {
	 *     public function validation()
	 *     {
	 *         $validator = new Validation();
	 *
	 *         $validator->add(
	 *             "status",
	 *             new ExclusionIn(
	 *                 [
	 *                     "domain" => [
	 *                         "A",
	 *                         "I",
	 *                     ],
	 *                 ]
	 *             )
	 *         );
	 *
	 *         return $this->validate($validator);
	 *     }
	 * }
	 *</code>
	 **/
    protected function validate($validator ) {

		$messages = validator->validate(null, this);

		// Call the validation, if ( it returns not the boolean
		// we append the messages to the current object
		if ( gettype($messages) == "boolean" ) {
			return messages;
		}

		foreach ( $iterator(messages) as $message ) {
			this->appendMessage(
				new Message(
					message->getMessage(),
					message->getField(),
					message->getType(),
					null,
					message->getCode()
				)
			);
		}

		// If there is a message, it returns false otherwise true
		return !count(messages);
    }

    /***
	 * Check whether validation process has generated any messages
	 *
	 *<code>
	 * use Phalcon\Mvc\Model;
	 * use Phalcon\Validation;
	 * use Phalcon\Validation\Validator\ExclusionIn;
	 *
	 * class Subscriptors extends Model
	 * {
	 *     public function validation()
	 *     {
	 *         $validator = new Validation();
	 *
	 *         $validator->validate(
	 *             "status",
	 *             new ExclusionIn(
	 *                 [
	 *                     "domain" => [
	 *                         "A",
	 *                         "I",
	 *                     ],
	 *                 ]
	 *             )
	 *         );
	 *
	 *         return $this->validate($validator);
	 *     }
	 * }
	 *</code>
	 **/
    public function validationHasFailed() {
		$errorMessages = $this->_errorMessages;
		if ( gettype($errorMessages) == "array" ) {
			return count(errorMessages) > 0;
		}
		return false;
    }

    /***
	 * Returns array of validation messages
	 *
	 *<code>
	 * $robot = new Robots();
	 *
	 * $robot->type = "mechanical";
	 * $robot->name = "Astro Boy";
	 * $robot->year = 1952;
	 *
	 * if ($robot->save() === false) {
	 *     echo "Umh, We can't store robots right now ";
	 *
	 *     $messages = $robot->getMessages();
	 *
	 *     foreach ($messages as $message) {
	 *         echo $message;
	 *     }
	 * } else {
	 *     echo "Great, a new robot was saved successfully!";
	 * }
	 * </code>
	 **/
    public function getMessages($filter  = null ) {

		if ( gettype($filter) == "string" && !empty filter ) {
			$filtered = [];
			foreach ( $this->_errorMessages as $message ) {
				if ( message->getField() == filter ) {
					$filtered[] = message;
				}
			}
			return filtered;
		}

		return $this->_errorMessages;
    }

    /***
	 * Reads "belongs to" relations and check the virtual foreign keys when inserting or updating records
	 * to verify that inserted/updated values are present in the related entity
	 **/
    protected final function _checkForeignKeysRestrict() {
			position, bindParams, extraConditions, message, fields,
			referencedFields, field, referencedModel, value, allowNulls;
		int action, numberNull;
		boolean error, validateWithNulls;

		/**
		 * Get the models manager
		 */
		$manager = <ManagerInterface> $this->_modelsManager;

		/**
		 * We check if ( some of the belongsTo relations act as virtual for (eign key
		 */
		$belongsTo = manager->getBelongsTo(this);

		$error = false;
		foreach ( $belongsTo as $relation ) {

			$validateWithNulls = false;
			$for (eignKey = relation->getForeignKey();
			if ( for (eignKey === false ) ) {
				continue;
			}

			/**
			 * By default action is restrict
			 */
			$action = Relation::ACTION_RESTRICT;

			/**
			 * Try to find a dif (ferent $the as $action foreach (eign key's options
			 */
			if ( gettype($for (eignKey) == "array" ) ) {
				if ( isset($for) (eignKey["action"] ) ) {
					$action = (int) for (eignKey["action"];
				}
			}

			/**
			 * Check only if ( the operation is restrict
			 */
			if ( action != Relation::ACTION_RESTRICT ) {
				continue;
			}

			/**
			 * Load the referenced model if ( needed
			 */
			$referencedModel = manager->load(relation->getReferencedModel());

			/**
			 * Since relations can have multiple columns or a single one, we need to build a condition for ( each of these cases
			 */
			$conditions = [], bindParams = [];

			$numberNull = 0,
				fields = relation->getFields(),
				referencedFields = relation->getReferencedFields();

			if ( gettype($fields) == "array" ) {
				/**
				 * Create a compound condition
				 */
				foreach ( position, $fields as $field ) {
					$conditions[] = "[" . referencedFields[position] . "] = ?" . position,
						bindParams[] = value;
					if ( gettype($value) == "null" ) {
						$numberNull++;
					}
				}

				$validateWithNulls = numberNull == count(fields);

			} else {

				$conditions[] = "[" . referencedFields . "] = ?0",
					bindParams[] = value;

				if ( gettype($value) == "null" ) {
					$validateWithNulls = true;
				}
			}

			/**
			 * Check if ( the virtual for (eign key has extra conditions
			 */
			if ( fetch extraConditions, for (eignKey["conditions"] ) ) {
				$conditions[] = extraConditions;
			}

			/**
			 * Check if ( the relation definition allows nulls
			 */
			if ( validateWithNulls ) {
				if ( fetch allowNulls, for (eignKey["allowNulls"] ) ) {
					$validateWithNulls = (boolean) allowNulls;
				} else {
					$validateWithNulls = false;
				}
			}

			/**
			 * We don't trust the actual values in the object and pass the values using bound parameters
			 * Let's make the checking
			 */
			if ( !validateWithNulls && !referencedModel->count([join(" AND ", conditions), "bind": bindParams]) ) {

				/**
				 * Get the user message or produce a new one
				 */
				if ( !fetch message, for (eignKey["message"] ) ) {
					if ( gettype($fields) == "array" ) {
						$message = "Value of fields \"" . join(", ", fields) . "\" does not exist on referenced table";
					} else {
						$message = "Value of field \"" . fields . "\" does not exist on referenced table";
					}
				}

				/**
				 * Create a message
				 */
				this->appendMessage(new Message(message, fields, "ConstraintViolation"));
				$error = true;
				break;
			}
		}

		/**
		 * Call 'onValidationFails' if ( the validation fails
		 */
		if ( error === true ) {
			if ( globals_get("orm.events") ) {
				this->fireEvent("onValidationFails");
				this->_cancelOperation();
			}
			return false;
		}

		return true;
    }

    /***
	 * Reads both "hasMany" and "hasOne" relations and checks the virtual foreign keys (cascade) when deleting records
	 **/
    protected final function _checkForeignKeysReverseCascade() {
			resultset, conditions, bindParams, referencedModel,
			referencedFields, fields, field, position, value,
			extraConditions;
		int action;

		/**
		 * Get the models manager
		 */
		$manager = <ManagerInterface> $this->_modelsManager;

		/**
		 * We check if ( some of the hasOne/hasMany relations is a for (eign key
		 */
		$relations = manager->getHasOneAndHasMany(this);

		foreach ( $relations as $relation ) {

			/**
			 * Check if ( the relation has a virtual for (eign key
			 */
			$for (eignKey = relation->getForeignKey();
			if ( for (eignKey === false ) ) {
				continue;
			}

			/**
			 * By default action is restrict
			 */
			$action = Relation::NO_ACTION;

			/**
			 * Try to find a dif (ferent $the as $action foreach (eign key's options
			 */
			if ( gettype($for (eignKey) == "array" ) ) {
				if ( isset($for) (eignKey["action"] ) ) {
					$action = (int) for (eignKey["action"];
				}
			}

			/**
			 * Check only if ( the operation is restrict
			 */
			if ( action != Relation::ACTION_CASCADE ) {
				continue;
			}

			/**
			 * Load a plain instance from the models manager
			 */
			$referencedModel = manager->load(relation->getReferencedModel());

			$fields = relation->getFields(),
				referencedFields = relation->getReferencedFields();

			/**
			 * Create the checking conditions. A relation can has many fields or a single one
			 */
			$conditions = [], bindParams = [];

			if ( gettype($fields) == "array" ) {
				foreach ( position, $fields as $field ) {
					$conditions[] = "[". referencedFields[position] . "] = ?" . position,
						bindParams[] = value;
				}
			} else {
				$conditions[] = "[" . referencedFields . "] = ?0",
					bindParams[] = value;
			}

			/**
			 * Check if ( the virtual for (eign key has extra conditions
			 */
			if ( fetch extraConditions, for (eignKey["conditions"] ) ) {
				$conditions[] = extraConditions;
			}

			/**
			 * We don't trust the actual values in the object and then we're passing the values using bound parameters
			 * Let's make the checking
			 */
			$resultset = referencedModel->find([
				join(" AND ", conditions),
				"bind": bindParams
			]);

			/**
			 * Delete the resultset
			 * Stop the operation if ( needed
			 */
			if ( resultset->delete() === false ) {
				return false;
			}
		}

		return true;
    }

    /***
	 * Reads both "hasMany" and "hasOne" relations and checks the virtual foreign keys (restrict) when deleting records
	 **/
    protected final function _checkForeignKeysReverseRestrict() {
		boolean error;
			relationClass, referencedModel, fields, referencedFields,
			conditions, bindParams,position, field,
			value, extraConditions, message;
		int action;

		/**
		 * Get the models manager
		 */
		$manager = <ManagerInterface> $this->_modelsManager;

		/**
		 * We check if ( some of the hasOne/hasMany relations is a for (eign key
		 */
		$relations = manager->getHasOneAndHasMany(this);

		$error = false;
		foreach ( $relations as $relation ) {

			/**
			 * Check if ( the relation has a virtual for (eign key
			 */
			$for (eignKey = relation->getForeignKey();
			if ( for (eignKey === false ) ) {
				continue;
			}

			/**
			 * By default action is restrict
			 */
			$action = Relation::ACTION_RESTRICT;

			/**
			 * Try to find a dif (ferent $the as $action foreach (eign key's options
			 */
			if ( gettype($for (eignKey) == "array" ) ) {
				if ( isset($for) (eignKey["action"] ) ) {
					$action = (int) for (eignKey["action"];
				}
			}

			/**
			 * Check only if ( the operation is restrict
			 */
			if ( action != Relation::ACTION_RESTRICT ) {
				continue;
			}

			$relationClass = relation->getReferencedModel();

			/**
			 * Load a plain instance from the models manager
			 */
			$referencedModel = manager->load(relationClass);

			$fields = relation->getFields(),
				referencedFields = relation->getReferencedFields();

			/**
			 * Create the checking conditions. A relation can has many fields or a single one
			 */
			$conditions = [], bindParams = [];

			if ( gettype($fields) == "array" ) {

				foreach ( position, $fields as $field ) {
					$conditions[] = "[" . referencedFields[position] . "] = ?" . position,
						bindParams[] = value;
				}

			} else {
				$conditions[] = "[" . referencedFields . "] = ?0",
					bindParams[] = value;
			}

			/**
			 * Check if ( the virtual for (eign key has extra conditions
			 */
			if ( fetch extraConditions, for (eignKey["conditions"] ) ) {
				$conditions[] = extraConditions;
			}

			/**
			 * We don't trust the actual values in the object and then we're passing the values using bound parameters
			 * Let's make the checking
			 */
			if ( referencedModel->count([join(" AND ", conditions), "bind": bindParams]) ) {

				/**
				 * Create a new message
				 */
				if ( !fetch message, for (eignKey["message"] ) ) {
					$message = "Record is referenced by model " . relationClass;
				}

				/**
				 * Create a message
				 */
				this->appendMessage(new Message(message, fields, "ConstraintViolation"));
				$error = true;
				break;
			}
		}

		/**
		 * Call validation fails event
		 */
		if ( error === true ) {
			if ( globals_get("orm.events") ) {
				this->fireEvent("onValidationFails");
				this->_cancelOperation();
			}
			return false;
		}

		return true;
    }

    /***
	 * Executes internal hooks before save a record
	 **/
    protected function _preSave($metaData , $exists , $identityField ) {
			field, attributeField, value, emptyStringValues;
		boolean error, isNull;

		/**
		 * Run Validation Callbacks Befor (e
		 */
		if ( globals_get("orm.events") ) {

			/**
			 * Call the befor (eValidation
			 */
			if ( $this->fireEventCancel("befor (eValidation") === false ) ) {
				return false;
			}

			/**
			 * Call the specif (ic befor (eValidation event for ( the current action
			 */
			if ( !exists ) {
				if ( $this->fireEventCancel("befor (eValidationOnCreate") === false ) ) {
					return false;
				}
			} else {
				if ( $this->fireEventCancel("befor (eValidationOnUpdate") === false ) ) {
					return false;
				}
			}
		}

		/**
		 * Check for ( Virtual for (eign keys
		 */
		if ( globals_get("orm.virtual_for (eign_keys") ) ) {
			if ( $this->_checkForeignKeysRestrict() === false ) {
				return false;
			}
		}

		/**
		 * Columns marked as not null are automatically validated by the ORM
		 */
		if ( globals_get("orm.not_null_validations") ) {

			$notNull = metaData->getNotNullAttributes(this);
			if ( gettype($notNull) == "array" ) {

				/**
				 * Gets the fields that are numeric, these are validated in a dif (ferent way
				 */
				$dataTypeNumeric = metaData->getDataTypesNumeric(this);

				if ( globals_get("orm.column_renaming") ) {
					$columnMap = metaData->getColumnMap(this);
				} else {
					$columnMap = null;
				}

				/**
				 * Get fields that must be omitted from the SQL generation
				 */
				if ( exists ) {
					$automaticAttributes = metaData->getAutomaticUpdateAttributes(this);
				} else {
					$automaticAttributes = metaData->getAutomaticCreateAttributes(this);
				}

				$defaultValues = metaData->getDefaultValues(this);

				/**
				 * Get string attributes that allow empty strings as defaults
				 */
				$emptyStringValues = metaData->getEmptyStringAttributes(this);

				$error = false;
				foreach ( $notNull as $field ) {

					/**
					 * We don't check fields that must be omitted
					 */
					if ( !isset($automaticAttributes[field]) ) {

						$isNull = false;

						if ( gettype($columnMap) == "array" ) {
							if ( !fetch attributeField, columnMap[field] ) {
								throw new Exception("Column '" . field . "' isn't part of the column map");
							}
						} else {
							$attributeField = field;
						}

						/**
						 * Field is null when: 1) is not set, 2) is numeric but
						 * its value is not numeric, 3) is null or 4) is empty string
						 * Read the attribute from the this_ptr using the real or renamed name
						 */
						if ( fetch value, $this->) {attributeField} ) {

							/**
							 * Objects are never treated as null, numeric fields must be numeric to be accepted as not null
							 */
							if ( gettype($value) != "object" ) {
								if ( !isset($dataTypeNumeric[field]) ) {
									if ( isset($emptyStringValues[field]) ) {
										if ( value === null ) {
											$isNull = true;
										}
									} else {
										if ( value === null || (value === "" && (!isset($defaultValues[field]) || value !== defaultValues[field])) ) {
											$isNull = true;
										}
									}
								} else {
									if ( !is_numeric(value) ) {
										$isNull = true;
									}
								}
							}

						} else {
							$isNull = true;
						}

						if ( isNull === true ) {

							if ( !exists ) {
								/**
								 * The identity field can be null
								 */
								if ( field == identityField ) {
									continue;
								}

								/**
								 * The field have default value can be null
								 */
								if ( isset($defaultValues[field]) ) {
									continue;
								}
							}

							/**
							 * An implicit PresenceOf message is created
							 */
							$this->_errorMessages[] = new Message(attributeField . " is required", attributeField, "PresenceOf"),
								error = true;
						}
					}
				}

				if ( error === true ) {
					if ( globals_get("orm.events") ) {
						this->fireEvent("onValidationFails");
						this->_cancelOperation();
					}
					return false;
				}
			}
		}

		/**
		 * Call the main validation event
		 */
		if ( $this->fireEventCancel("validation") === false ) {
			if ( globals_get("orm.events") ) {
				this->fireEvent("onValidationFails");
			}
			return false;
		}

		/**
		 * Run Validation
		 */
		if ( globals_get("orm.events") ) {

			/**
			 * Run Validation Callbacks After
			 */
			if ( !exists ) {
				if ( $this->fireEventCancel("afterValidationOnCreate") === false ) {
					return false;
				}
			} else {
				if ( $this->fireEventCancel("afterValidationOnUpdate") === false ) {
					return false;
				}
			}

			if ( $this->fireEventCancel("afterValidation") === false ) {
				return false;
			}

			/**
			 * Run Befor (e Callbacks
			 */
			if ( $this->fireEventCancel("befor (eSave") === false ) ) {
				return false;
			}

			$this->_skipped = false;

			/**
			 * The operation can be skipped here
			 */
			if ( exists ) {
				if ( $this->fireEventCancel("befor (eUpdate") === false ) ) {
					return false;
				}
			} else {
				if ( $this->fireEventCancel("befor (eCreate") === false ) ) {
					return false;
				}
			}

			/**
			 * Always return true if ( the operation is skipped
			 */
			if ( $this->_skipped === true ) {
				return true;
			}

		}

		return true;
    }

    /***
	 * Executes internal events after save a record
	 **/
    protected function _postSave($success , $exists ) {
		if ( success === true ) {
			if ( exists ) {
				this->fireEvent("afterUpdate");
			} else {
				this->fireEvent("afterCreate");
			}
		}

		return success;
    }

    /***
	 * Sends a pre-build INSERT SQL statement to the relational database system
	 *
	 * @param \Phalcon\Mvc\Model\MetaDataInterface metaData
	 * @param \Phalcon\Db\AdapterInterface connection
	 * @param string|array table
	 * @param boolean|string identityField
	 * @return boolean
	 **/
    protected function _doLowInsert($metaData , $connection , $table , $identityField ) {
			field, columnMap, value, attributeField, success, bindType,
			defaultValue, sequenceName, defaultValues, source, schema, snapshot, lastInsertedId, manager;
		boolean useExplicitIdentity;

		$bindSkip = Column::BIND_SKIP;
		$manager = <ManagerInterface> $this->_modelsManager;

		$fields = [],
			values = [],
			snapshot = [],
			bindTypes = [];

		$attributes = metaData->getAttributes(this),
			bindDataTypes = metaData->getBindTypes(this),
			automaticAttributes = metaData->getAutomaticCreateAttributes(this),
			defaultValues = metaData->getDefaultValues(this);

		if ( globals_get("orm.column_renaming") ) {
			$columnMap = metaData->getColumnMap(this);
		} else {
			$columnMap = null;
		}

		/**
		 * All fields in the model makes part or the INSERT
		 */
		foreach ( $attributes as $field ) {

			if ( !isset($automaticAttributes[field]) ) {

				/**
				 * Check if ( the model has a column map
				 */
				if ( gettype($columnMap) == "array" ) {
					if ( !fetch attributeField, columnMap[field] ) {
						throw new Exception("Column '" . field . "' isn't part of the column map");
					}
				} else {
					$attributeField = field;
				}

				/**
				 * Check every attribute in the model except identity field
				 */
				if ( field != identityField ) {

					/**
					 * This isset($checks) that the property be defined in the model
					 */
					if ( fetch value, $this->) {attributeField} ) {

						if ( value === null && isset($defaultValues[field]) ) {
							$snapshot[attributeField] = null;
							$value = connection->getDefaultValue();
						} else {
							$snapshot[attributeField] = value;
						}

						/**
						 * Every column must have a bind data type defined
						 */
						if ( !fetch bindType, bindDataTypes[field] ) {
							throw new Exception("Column '" . field . "' have not defined a bind data type");
						}

						$fields[] = field, values[] = value, bindTypes[] = bindType;
					} else {

						if ( isset($defaultValues[field]) ) {
							$values[] = connection->getDefaultValue();
							/**
							 * This is default value so we set null, keep in mind it's value in database!
							 */
							$snapshot[attributeField] = null;
						} else {
							$values[] = value;
							$snapshot[attributeField] = value;
						}

						$fields[] = field, bindTypes[] = bindSkip;
					}
				}
			}
		}

		/**
		 * If there is an identity field we add it using "null" or "default"
		 */
		if ( identityField !== false ) {

			$defaultValue = connection->getDefaultIdValue();

			/**
			 * Not all the database systems require an explicit value for ( identity columns
			 */
			$useExplicitIdentity = (boolean) connection->useExplicitIdValue();
			if ( useExplicitIdentity ) {
				$fields[] = identityField;
			}

			/**
			 * Check if ( the model has a column map
			 */
			if ( gettype($columnMap) == "array" ) {
				if ( !fetch attributeField, columnMap[identityField] ) {
					throw new Exception("Identity column '" . identityField . "' isn't part of the column map");
				}
			} else {
				$attributeField = identityField;
			}

			/**
			 * Check if ( the developer set an explicit value for ( the column
			 */
			if ( fetch value, $this->) {attributeField} ) {

				if ( value === null || value === "" ) {
					if ( useExplicitIdentity ) {
						$values[] = defaultValue, bindTypes[] = bindSkip;
					}
				} else {

					/**
					 * Add the explicit value to the field list if ( the user has defined a value for ( it
					 */
					if ( !useExplicitIdentity ) {
						$fields[] = identityField;
					}

					/**
					 * The field is valid we look for ( a bind value (normally int)
					 */
					if ( !fetch bindType, bindDataTypes[identityField] ) {
						throw new Exception("Identity column '" . identityField . "' isn\'t part of the table columns");
					}

					$values[] = value, bindTypes[] = bindType;
				}
			} else {
				if ( useExplicitIdentity ) {
					$values[] = defaultValue, bindTypes[] = bindSkip;
				}
			}
		}

		/**
		 * The low level insert is perfor (med
		 */
		$success = connection->insert(table, values, fields, bindTypes);
		if ( success && identityField !== false ) {

			/**
			 * We check if ( the model have sequences
			 */
			$sequenceName = null;
			if ( connection->supportSequences() === true ) {
				if ( method_exists(this, "getSequenceName") ) {
					$sequenceName = $this->{"getSequenceName"}();
				} else {

					$source = $this->getSource(),
						schema = $this->getSchema();

					if ( empty schema ) {
						$sequenceName = source . "_" . identityField . "_seq";
					} else {
						$sequenceName = schema . "." . source . "_" . identityField . "_seq";
					}
				}
			}

			/**
			 * Recover the last "insert id" and assign it to the object
			 */
			$lastInsertedId = connection->lastInsertId(sequenceName);

			$this->{attributeField} = lastInsertedId;
			$snapshot[attributeField] = lastInsertedId;

			/**
			 * Since the primary key was modif (ied, we delete the _uniqueParams
			 * to for (ce any future update to re-build the primary key
			 */
			$this->_uniqueParams = null;
		}

		if ( success && manager->isKeepingSnapshots(this) && globals_get("orm.update_snapshot_on_save") ) {
			$this->_snapshot = snapshot;
		}


		return success;
    }

    /***
	 * Sends a pre-build UPDATE SQL statement to the relational database system
	 *
	 * @param \Phalcon\Mvc\Model\MetaDataInterface metaData
	 * @param \Phalcon\Db\AdapterInterface connection
	 * @param string|array table
	 * @return boolean
	 **/
    protected function _doLowUpdate($metaData , $connection , $table ) {
 			automaticAttributes, snapshotValue, uniqueKey, uniqueParams, uniqueTypes,
 			snapshot, nonPrimary, columnMap, attributeField, value, primaryKeys, bindType, newSnapshot, success;
 		boolean useDynamicUpdate, changed;

 		$bindSkip = Column::BIND_SKIP,
 			fields = [],
 			values = [],
 			bindTypes = [],
 			newSnapshot = [],
 			manager = <ManagerInterface> $this->_modelsManager;

 		/**
 		 * Check if ( the model must use dynamic update
 		 */
 		$useDynamicUpdate = (boolean) manager->isUsingDynamicUpdate(this);

		$snapshot = $this->_snapshot;

 		if ( useDynamicUpdate ) {
 			if ( gettype($snapshot) != "array" ) {
 				$useDynamicUpdate = false;
 			}
 		}

 		$dataTypes = metaData->getDataTypes(this),
			 bindDataTypes = metaData->getBindTypes(this),
 			nonPrimary = metaData->getNonPrimaryKeyAttributes(this),
 			automaticAttributes = metaData->getAutomaticUpdateAttributes(this);

 		if ( globals_get("orm.column_renaming") ) {
 			$columnMap = metaData->getColumnMap(this);
 		} else {
 			$columnMap = null;
 		}

 		/**
 		 * We only make the update based on the non-primary attributes, values in primary key attributes are ignored
 		 */
 		foreach ( $nonPrimary as $field ) {

 			if ( !isset($automaticAttributes[field]) ) {

 				/**
 				 * Check a bind type for ( field to update
 				 */
 				if ( !fetch bindType, bindDataTypes[field] ) {
 					throw new Exception("Column '" . field . "' have not defined a bind data type");
 				}

 				/**
 				 * Check if ( the model has a column map
 				 */
 				if ( gettype($columnMap) == "array" ) {
 					if ( !fetch attributeField, columnMap[field] ) {
 						throw new Exception("Column '" . field . "' isn't part of the column map");
 					}
 				} else {
 					$attributeField = field;
 				}

 				/**
 				 * Get the field's value
 				 * If a field isn't set we pass a null value
 				 */
 				if ( fetch value, $this->) {attributeField} ) {

 					/**
 					 * When dynamic update is not used we pass every field to the update
 					 */
 					if ( !useDynamicUpdate ) {
 						$fields[] = field, values[] = value;
 						$bindTypes[] = bindType;
 					} else {

 						/**
 						 * If the field is not part of the snapshot we add them as changed
 						 */
 						if ( !fetch snapshotValue, snapshot[attributeField] ) {
 							$changed = true;
 						} else {
 							/**
 							 * See https://github.com/phalcon/cphalcon/issues/3247
 							 * Take a TEXT column with value '4' and replace it by
 							 * the value '4.0'. For PHP '4' and '4.0' are the same.
 							 * We can't use simple comparison...
 							 *
 							 * We must use the type of snapshotValue.
 							 */
 							if ( value === null ) {
 								$changed = snapshotValue !== null;
 							} else {
 								if ( snapshotValue === null ) {
 									$changed = true;
 								} else {

									 if ( !fetch dataType, dataTypes[field] ) {
										 throw new Exception("Column '" . field . "' have not defined a data type");
									 }

 									switch dataType {

 										case Column::TYPE_BOOLEAN:
 											$changed = (boolean) snapshotValue !== (boolean) value;
 											break;

 										case Column::TYPE_DECIMAL:
 										case Column::TYPE_FLOAT:
 											$changed = floatval(snapshotValue) !== floatval(value);
 											break;

 										case Column::TYPE_INTEGER:
 										case Column::TYPE_DATE:
 										case Column::TYPE_VARCHAR:
 										case Column::TYPE_DATETIME:
 										case Column::TYPE_CHAR:
 										case Column::TYPE_TEXT:
 										case Column::TYPE_VARCHAR:
 										case Column::TYPE_BIGINTEGER:
 											$changed = (string) snapshotValue !== (string) value;
 											break;

 										/**
 										 * Any other type is not really supported...
 										 */
 										default:
 											$changed = value != snapshotValue;
 									}
 								}
 							}
 						}

 						/**
 						 * Only changed values are added to the SQL Update
 						 */
 						if ( changed ) {
 							$fields[] = field, values[] = value;
 							$bindTypes[] = bindType;
 						}
 					}
					$newSnapshot[attributeField] = value;

 				} else {
 				    $newSnapshot[attributeField] = null;
 					$fields[] = field, values[] = null, bindTypes[] = bindSkip;
 				}
 			}
 		}

 		/**
 		 * If there is no fields to update we return true
 		 */
 		if ( !count(fields) ) {
 			if ( useDynamicUpdate ) {
 				$this->_oldSnapshot = snapshot;
 			}
 			return true;
 		}

 		$uniqueKey = $this->_uniqueKey,
 			uniqueParams = $this->_uniqueParams,
 			uniqueTypes = $this->_uniqueTypes;

 		/**
 		 * When unique params is null we need to rebuild the bind params
 		 */
 		if ( gettype($uniqueParams) != "array" ) {

 			$primaryKeys = metaData->getPrimaryKeyAttributes(this);

 			/**
 			 * We can't create dynamic SQL without a primary key
 			 */
 			if ( !count(primaryKeys) ) {
 				throw new Exception("A primary key must be $the as $defined model in order to perforeach (m the operation");
 			}

 			$uniqueParams = [];
 			foreach ( $primaryKeys as $field ) {

 				/**
 				 * Check if ( the model has a column map
 				 */
 				if ( gettype($columnMap) == "array" ) {
 					if ( !fetch attributeField, columnMap[field] ) {
 						throw new Exception("Column '" . field . "' isn't part of the column map");
 					}
 				} else {
 					$attributeField = field;
 				}

 				if ( fetch value, $this->) {attributeField} ) {
 				    $newSnapshot[attributeField] = value;
 					$uniqueParams[] = value;
 				} else {
 				    $newSnapshot[attributeField] = null;
 					$uniqueParams[] = null;
 				}
 			}
 		}

 		/**
 		 * We build the conditions as an array
 		 * Perfor (m the low level update
 		 */
 		$success = connection->update(table, fields, values, [
 			"conditions" : uniqueKey,
 			"bind"	     : uniqueParams,
 			"bindTypes"  : uniqueTypes
 		], bindTypes);

 		if ( success && manager->isKeepingSnapshots(this) && globals_get("orm.update_snapshot_on_save") ) {
			if ( gettype($snapshot) == "array" ) {
				$this->_oldSnapshot = snapshot;
				$this->_snapshot = array_merge(snapshot, newSnapshot);
			} else {
				$this->_oldSnapshot = [];
				$this->_snapshot = newSnapshot;
			}
		}

 		return success;
    }

    /***
	 * Saves related records that must be stored prior to save the master record
	 *
	 * @param \Phalcon\Db\AdapterInterface connection
	 * @param \Phalcon\Mvc\ModelInterface[] related
	 * @return boolean
	 **/
    protected function _preSaveRelatedRecords($connection , $related ) {
			referencedModel, message, nesting, name, record;

		$nesting = false;

		/**
		 * Start an implicit transaction
		 */
		connection->begin(nesting);

		$className = get_class(this),
			manager = <ManagerInterface> $this->getModelsManager();

		foreach ( name, $related as $record ) {

			/**
			 * Try to get a relation with the same name
			 */
			$relation = <RelationInterface> manager->getRelationByAlias(className, name);
			if ( gettype($relation) == "object" ) {

				/**
				 * Get the relation type
				 */
				$type = relation->getType();

				/**
				 * Only belongsTo are stored befor (e save the master record
				 */
				if ( type == Relation::BELONGS_TO ) {

					if ( gettype($record) != "object" ) {
						connection->rollback(nesting);
						throw new Exception("Only objects can be stored as part of belongs-to relations");
					}

					$columns = relation->getFields(),
						referencedModel = relation->getReferencedModel(),
						referencedFields = relation->getReferencedFields();

					if ( gettype($columns) == "array" ) {
						connection->rollback(nesting);
						throw new Exception("Not implemented");
					}

					/**
					 * If dynamic update is enabled, saving the record must not take any action
					 */
					if ( !record->save() ) {

						/**
						 * Get the validation messages generated by the referenced model
						 */
						foreach ( $record->getMessages() as $message ) {

							/**
							 * Set the related model
							 */
							if ( gettype($message) == "object" ) {
								message->setModel(record);
							}

							/**
							 * Appends the messages to the current model
							 */
							this->appendMessage(message);
						}

						/**
						 * Rollback the implicit transaction
						 */
						connection->rollback(nesting);
						return false;
					}

					/**
					 * Read the attribute from the referenced model and assigns it to the current model
					 * Assign it to the model
					 */
					$this->{columns} = record->readAttribute(referencedFields);
				}
			}
		}

		return true;
    }

    /***
	 * Save the related records assigned in the has-one/has-many relations
	 *
	 * @param  Phalcon\Db\AdapterInterface connection
	 * @param  Phalcon\Mvc\ModelInterface[] related
	 * @return boolean
	 **/
    protected function _postSaveRelatedRecords($connection , $related ) {
			columns, referencedModel, referencedFields, relatedRecords, value,
			recordAfter, intermediateModel, intermediateFields, intermediateValue,
			intermediateModelName, intermediateReferencedFields;
		boolean isThrough;

		$nesting = false,
			className = get_class(this),
			manager = <ManagerInterface> $this->getModelsManager();

		foreach ( name, $related as $record ) {

			/**
			 * Try to get a relation with the same name
			 */
			$relation = <RelationInterface> manager->getRelationByAlias(className, name);
			if ( gettype($relation) == "object" ) {

				/**
				 * Discard belongsTo relations
				 */
				if ( relation->getType() == Relation::BELONGS_TO ) {
					continue;
				}

				if ( gettype($record) != "object" && gettype($record) != "array" ) {
					connection->rollback(nesting);
					throw new Exception("Only objects/arrays can be stored as part of has-many/has-one/has-many-to-many relations");
				}

				$columns = relation->getFields(),
					referencedModel = relation->getReferencedModel(),
					referencedFields = relation->getReferencedFields();

				if ( gettype($columns) == "array" ) {
					connection->rollback(nesting);
					throw new Exception("Not implemented");
				}

				/**
				 * Create an implicit array for ( has-many/has-one records
				 */
				if ( gettype($record) == "object" ) {
					$relatedRecords = [record];
				} else {
					$relatedRecords = record;
				}

				if ( !fetch value, $this->) {columns} ) {
					connection->rollback(nesting);
					throw new Exception("The column '" . columns . "' needs to be present in the model");
				}

				/**
				 * Get the value of the field from the current model
				 * Check if ( the relation is a has-many-to-many
				 */
				$isThrough = (boolean) relation->isThrough();

				/**
				 * Get the rest of intermediate model info
				 */
				if ( isThrough ) {
					$intermediateModelName = relation->getIntermediateModel(),
						intermediateFields = relation->getIntermediateFields(),
						intermediateReferencedFields = relation->getIntermediateReferencedFields();
				}

				foreach ( $relatedRecords as $recordAfter ) {

					/**
					 * For non has-many-to-many relations just assign the local value in the referenced model
					 */
					if ( !isThrough ) {

						/**
						 * Assign the value to the
						 */
						recordAfter->writeAttribute(referencedFields, value);
					}

					/**
					 * Save the record and get messages
					 */
					if ( !recordAfter->save() ) {

						/**
						 * Get the validation messages generated by the referenced model
						 */
						foreach ( $recordAfter->getMessages() as $message ) {

							/**
							 * Set the related model
							 */
							if ( gettype($message) == "object" ) {
								message->setModel(record);
							}

							/**
							 * Appends the messages to the current model
							 */
							this->appendMessage(message);
						}

						/**
						 * Rollback the implicit transaction
						 */
						connection->rollback(nesting);
						return false;
					}

					if ( isThrough ) {

						/**
						 * Create a new instance of the intermediate model
						 */
						$intermediateModel = manager->load(intermediateModelName, true);

						/**
						 * Write value in the intermediate model
						 */
						intermediateModel->writeAttribute(intermediateFields, value);

						/**
						 * Get the value from the referenced model
						 */
						$intermediateValue = recordAfter->readAttribute(referencedFields);

						/**
						 * Write the intermediate value in the intermediate model
						 */
						intermediateModel->writeAttribute(intermediateReferencedFields, intermediateValue);

						/**
						 * Save the record and get messages
						 */
						if ( !intermediateModel->save() ) {

							/**
							 * Get the validation messages generated by the referenced model
							 */
							foreach ( $intermediateModel->getMessages() as $message ) {

								/**
								 * Set the related model
								 */
								if ( gettype($message) == "object" ) {
									message->setModel(record);
								}

								/**
								 * Appends the messages to the current model
								 */
								this->appendMessage(message);
							}

							/**
							 * Rollback the implicit transaction
							 */
							connection->rollback(nesting);
							return false;
						}
					}

				}
			} else {
				if ( gettype($record) != "array" ) {
					connection->rollback(nesting);

					throw new Exception(
						"There are no defined relations for ( the model '" . className . "' using alias '" . name . "'"
					);
				}
			}
		}

		/**
		 * Commit the implicit transaction
		 */
		connection->commit(nesting);
		return true;
    }

    /***
	 * Inserts or updates a model instance. Returning true on success or false otherwise.
	 *
	 *<code>
	 * // Creating a new robot
	 * $robot = new Robots();
	 *
	 * $robot->type = "mechanical";
	 * $robot->name = "Astro Boy";
	 * $robot->year = 1952;
	 *
	 * $robot->save();
	 *
	 * // Updating a robot name
	 * $robot = Robots::findFirst("id = 100");
	 *
	 * $robot->name = "Biomass";
	 *
	 * $robot->save();
	 *</code>
	 *
	 * @param array data
	 * @param array whiteList
	 * @return boolean
	 **/
    public function save($data  = null , $whiteList  = null ) {
			source, table, identityField, exists, success;

		$metaData = $this->getModelsMetaData();

		if ( gettype($data) == "array" && count(data) > 0 ) {
			this->assign(data, null, whiteList);
		}

		/**
		 * Create/Get the current database connection
		 */
		$writeConnection = $this->getWriteConnection();

		/**
		 * Fire the start event
		 */
		this->fireEvent("prepareSave");

		/**
		 * Save related records in belongsTo relationships
		 */
		$related = $this->_related;
		if ( gettype($related) == "array" ) {
			if ( $this->_preSaveRelatedRecords(writeConnection, related) === false ) {
				return false;
			}
		}

		$schema = $this->getSchema(),
			source = $this->getSource();

		if ( schema ) {
			$table = [schema, source];
		} else {
			$table = source;
		}

		/**
		 * Create/Get the current database connection
		 */
		$readConnection = $this->getReadConnection();

		/**
		 * We need to check if ( the record exists
		 */
		$exists = $this->_exists(metaData, readConnection, table);

		if ( exists ) {
			$this->_operationMade = self::OP_UPDATE;
		} else {
			$this->_operationMade = self::OP_CREATE;
		}

		/**
		 * Clean the messages
		 */
		$this->_errorMessages = [];

		/**
		 * Query the identity field
		 */
		$identityField = metaData->getIdentityField(this);

		/**
		 * _preSave() makes all the validations
		 */
		if ( $this->_preSave(metaData, exists, identityField) === false ) {

			/**
			 * Rollback the current transaction if ( there was validation errors
			 */
			if ( gettype($related) == "array" ) {
				writeConnection->rollback(false);
			}

			/**
			 * Throw exceptions on failed saves?
			 */
			if ( globals_get("orm.exception_on_failed_save") ) {
				/**
				 * Launch a Phalcon\Mvc\Model\ValidationFailed to notif (y that the save failed
				 */
				throw new ValidationFailed(this, $this->getMessages());
			}

			return false;
		}

		/**
		 * Depending if ( the record exists we do an update or an insert operation
		 */
		if ( exists ) {
			$success = $this->_doLowUpdate(metaData, writeConnection, table);
		} else {
			$success = $this->_doLowInsert(metaData, writeConnection, table, identityField);
		}

		/**
		 * Change the dirty state to persistent
		 */
		if ( success ) {
			$this->_dirtyState = self::DIRTY_STATE_PERSISTENT;
		}

		if ( gettype($related) == "array" ) {

			/**
			 * Rollbacks the implicit transaction if ( the master save has failed
			 */
			if ( success === false ) {
				writeConnection->rollback(false);
			} else {
				/**
				 * Save the post-related records
				 */
				$success = $this->_postSaveRelatedRecords(writeConnection, related);
			}
		}

		/**
		 * _postSave() invokes after* events if ( the operation was successful
		 */
		if ( globals_get("orm.events") ) {
			$success = $this->_postSave(success, exists);
		}

		if ( success === false ) {
			this->_cancelOperation();
		} else {
			this->fireEvent("afterSave");
		}

		return success;
    }

    /***
	 * Inserts a model instance. If the instance already exists in the persistence it will throw an exception
	 * Returning true on success or false otherwise.
	 *
	 *<code>
	 * // Creating a new robot
	 * $robot = new Robots();
	 *
	 * $robot->type = "mechanical";
	 * $robot->name = "Astro Boy";
	 * $robot->year = 1952;
	 *
	 * $robot->create();
	 *
	 * // Passing an array to create
	 * $robot = new Robots();
	 *
	 * $robot->create(
	 *     [
	 *         "type" => "mechanical",
	 *         "name" => "Astro Boy",
	 *         "year" => 1952,
	 *     ]
	 * );
	 *</code>
	 **/
    public function create($data  = null , $whiteList  = null ) {

		$metaData = $this->getModelsMetaData();

		/**
		 * Get the current connection
		 * If the record already exists we must throw an exception
		 */
		if ( $this->_exists(metaData, $this->getReadConnection()) ) {
			$this->_errorMessages = [
				new Message("Record cannot be created because it already exists", null, "InvalidCreateAttempt")
			];
			return false;
		}

		/**
		 * Using save() anyways
		 */
		return $this->save(data, whiteList);
    }

    /***
	 * Updates a model instance. If the instance doesn't exist in the persistence it will throw an exception
	 * Returning true on success or false otherwise.
	 *
	 *<code>
	 * // Updating a robot name
	 * $robot = Robots::findFirst("id = 100");
	 *
	 * $robot->name = "Biomass";
	 *
	 * $robot->update();
	 *</code>
	 **/
    public function update($data  = null , $whiteList  = null ) {

		/**
		 * We don't check if ( the record exists if ( the record is already checked
		 */
		if ( $this->_dirtyState ) {

			$metaData = $this->getModelsMetaData();

			if ( !this->_exists(metaData, $this->getReadConnection()) ) {
				$this->_errorMessages = [
					new Message(
						"Record cannot be updated because it does not exist",
						null,
						"InvalidUpdateAttempt"
					)
				];

				return false;
			}
		}

		/**
		 * Call save() anyways
		 */
		return $this->save(data, whiteList);
    }

    /***
	 * Deletes a model instance. Returning true on success or false otherwise.
	 *
	 * <code>
	 * $robot = Robots::findFirst("id=100");
	 *
	 * $robot->delete();
	 *
	 * $robots = Robots::find("type = 'mechanical'");
	 *
	 * foreach ($robots as $robot) {
	 *     $robot->delete();
	 * }
	 * </code>
	 **/
    public function delete() {
			bindDataTypes, columnMap, attributeField, conditions, primaryKey,
			bindType, value, schema, source, table, success;

		$metaData = $this->getModelsMetaData(),
			writeConnection = $this->getWriteConnection();

		/**
		 * Operation made is OP_DELETE
		 */
		$this->_operationMade = self::OP_DELETE,
			this->_errorMessages = [];

		/**
		 * Check if ( deleting the record violates a virtual for (eign key
		 */
		if ( globals_get("orm.virtual_for (eign_keys") ) ) {
			if ( $this->_checkForeignKeysReverseRestrict() === false ) {
				return false;
			}
		}

		$values = [],
			bindTypes = [],
			conditions = [];

		$primaryKeys = metaData->getPrimaryKeyAttributes(this),
			bindDataTypes = metaData->getBindTypes(this);

		if ( globals_get("orm.column_renaming") ) {
			$columnMap = metaData->getColumnMap(this);
		} else {
			$columnMap = null;
		}

		/**
		 * We can't create dynamic SQL without a primary key
		 */
		if ( !count(primaryKeys) ) {
			throw new Exception("A primary key must be $the as $defined model in order to perforeach (m the operation");
		}

		/**
		 * Create a condition from the primary keys
		 */
		foreach ( $primaryKeys as $primaryKey ) {

			/**
			 * Every column part of the primary key must be in the bind data types
			 */
			if ( !fetch bindType, bindDataTypes[primaryKey] ) {
				throw new Exception("Column '" . primaryKey . "' have not defined a bind data type");
			}

			/**
			 * Take the column values based on the column map if ( any
			 */
			if ( gettype($columnMap) == "array" ) {
				if ( !fetch attributeField, columnMap[primaryKey] ) {
					throw new Exception("Column '" . primaryKey . "' isn't part of the column map");
				}
			} else {
				$attributeField = primaryKey;
			}

			/**
			 * If the attribute is currently set in the object add it to the conditions
			 */
			if ( !fetch value, $this->) {attributeField} ) {
				throw new Exception(
					"Cannot delete the record because the primary key attribute: '" . attributeField . "' wasn't set"
				);
			}

			/**
			 * Escape the column identif (ier
			 */
			$values[] = value,
				conditions[] = writeConnection->escapeIdentif (ier(primaryKey) . " = ?",
				bindTypes[] = bindType;
		}

		if ( globals_get("orm.events") ) {

			$this->_skipped = false;

			/**
			 * Fire the befor (eDelete event
			 */
			if ( $this->fireEventCancel("befor (eDelete") === false ) ) {
				return false;
			} else {
				/**
				 * The operation can be skipped
				 */
				if ( $this->_skipped === true ) {
					return true;
				}
			}
		}

		$schema = $this->getSchema(),
			source = $this->getSource();

		if ( schema ) {
			$table = [schema, source];
		} else {
			$table = source;
		}

		/**
		 * Join the conditions in the array using an AND operator
		 * Do the deletion
		 */
		$success = writeConnection->delete(table, join(" AND ", conditions), values, bindTypes);

		/**
		 * Check if ( there is virtual for (eign keys with cascade action
		 */
		if ( globals_get("orm.virtual_for (eign_keys") ) ) {
			if ( $this->_checkForeignKeysReverseCascade() === false ) {
				return false;
			}
		}

		if ( globals_get("orm.events") ) {
			if ( success ) {
				this->fireEvent("afterDelete");
			}
		}

		/**
		 * Force perfor (m the record existence checking again
		 */
		$this->_dirtyState = self::DIRTY_STATE_DETACHED;

		return success;
    }

    /***
	 * Returns the type of the latest operation performed by the ORM
	 * Returns one of the OP_* class constants
	 **/
    public function getOperationMade() {
		return $this->_operationMade;
    }

    /***
	 * Refreshes the model attributes re-querying the record from the database
	 **/
    public function refresh() {
			uniqueKey, tables, uniqueParams, dialect, row, fields, attribute, manager, columnMap;

		if ( $this->_dirtyState != self::DIRTY_STATE_PERSISTENT ) {
			throw new Exception("The record cannot be refreshed because it does not exist or is deleted");
		}

		$metaData = $this->getModelsMetaData(),
			readConnection = $this->getReadConnection(),
			manager = <ManagerInterface> $this->_modelsManager;

		$schema = $this->getSchema(),
			source = $this->getSource();

		if ( schema ) {
			$table = [schema, source];
		} else {
			$table = source;
		}

		$uniqueKey = $this->_uniqueKey;
		if ( !uniqueKey ) {

			/**
			 * We need to check if ( the record exists
			 */
			if ( !this->_exists(metaData, readConnection, table) ) {
				throw new Exception("The record cannot be refreshed because it does not exist or is deleted");
			}

			$uniqueKey = $this->_uniqueKey;
		}

		$uniqueParams = $this->_uniqueParams;
		if ( gettype($uniqueParams) != "array" ) {
			throw new Exception("The record cannot be refreshed because it does not exist or is deleted");
		}

		/**
		 * We only refresh the attributes in the model's metadata
		 */
		$fields = [];
		foreach ( $metaData->getAttributes(this) as $attribute ) {
			$fields[] = [attribute];
		}

		/**
		 * We directly build the SELECT to save resources
		 */
		$dialect = readConnection->getDialect(),
			tables = dialect->select([
				"columns": fields,
				"tables":  readConnection->escapeIdentif (ier(table),
				"where":   uniqueKey
			]),
			row = readConnection->fetchOne(tables, \Phalcon\Db::FETCH_ASSOC, uniqueParams, $this->_uniqueTypes);

		/**
		 * Get a column map if ( any
		 * Assign the resulting array to the this object
		 */
		if ( gettype($row) == "array" ) {
			$columnMap = metaData->getColumnMap(this);
			this->assign(row, columnMap);
			if ( manager->isKeepingSnapshots(this) ) {
				this->setSnapshotData(row, columnMap);
				this->setOldSnapshotData(row, columnMap);
			}
		}

		return this;
    }

    /***
	 * Skips the current operation forcing a success state
	 **/
    public function skipOperation($skip ) {
		$this->_skipped = skip;
    }

    /***
	 * Reads an attribute value by its name
	 *
	 * <code>
	 * echo $robot->readAttribute("name");
	 * </code>
	 **/
    public function readAttribute($attribute ) {
		if ( !isset $this->) {attribute} ) {
			return null;
		}

		return $this->{attribute};
    }

    /***
	 * Writes an attribute value by its name
	 *
	 *<code>
	 * $robot->writeAttribute("name", "Rosey");
	 *</code>
	 **/
    public function writeAttribute($attribute , $value ) {
		$this->{attribute} = value;
    }

    /***
	 * Sets a list of attributes that must be skipped from the
	 * generated INSERT/UPDATE statement
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->skipAttributes(
	 *             [
	 *                 "price",
	 *             ]
	 *         );
	 *     }
	 * }
	 *</code>
	 **/
    protected function skipAttributes($attributes ) {
		this->skipAttributesOnCreate(attributes);
		this->skipAttributesOnUpdate(attributes);
    }

    /***
	 * Sets a list of attributes that must be skipped from the
	 * generated INSERT statement
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->skipAttributesOnCreate(
	 *             [
	 *                 "created_at",
	 *             ]
	 *         );
	 *     }
	 * }
	 *</code>
	 **/
    protected function skipAttributesOnCreate($attributes ) {

		$keysAttributes = [];
		foreach ( $attributes as $attribute ) {
			$keysAttributes[attribute] = null;
		}

		this->getModelsMetaData()->setAutomaticCreateAttributes(this, keysAttributes);
    }

    /***
	 * Sets a list of attributes that must be skipped from the
	 * generated UPDATE statement
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->skipAttributesOnUpdate(
	 *             [
	 *                 "modified_in",
	 *             ]
	 *         );
	 *     }
	 * }
	 *</code>
	 **/
    protected function skipAttributesOnUpdate($attributes ) {

		$keysAttributes = [];
		foreach ( $attributes as $attribute ) {
			$keysAttributes[attribute] = null;
		}

		this->getModelsMetaData()->setAutomaticUpdateAttributes(this, keysAttributes);
    }

    /***
	 * Sets a list of attributes that must be skipped from the
	 * generated UPDATE statement
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->allowEmptyStringValues(
	 *             [
	 *                 "name",
	 *             ]
	 *         );
	 *     }
	 * }
	 *</code>
	 **/
    protected function allowEmptyStringValues($attributes ) {

		$keysAttributes = [];
		foreach ( $attributes as $attribute ) {
			$keysAttributes[attribute] = true;
		}

		this->getModelsMetaData()->setEmptyStringAttributes(this, keysAttributes);
    }

    /***
	 * Setup a 1-1 relation between two models
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->hasOne("id", "RobotsDescription", "robots_id");
	 *     }
	 * }
	 *</code>
	 **/
    protected function hasOne($fields , $referenceModel , $referencedFields , $options  = null ) {
		return (<ManagerInterface> $this->_modelsManager)->addHasOne(this, fields, referenceModel, referencedFields, options);
    }

    /***
	 * Setup a reverse 1-1 or n-1 relation between two models
	 *
	 *<code>
	 *
	 * class RobotsParts extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->belongsTo("robots_id", "Robots", "id");
	 *     }
	 * }
	 *</code>
	 **/
    protected function belongsTo($fields , $referenceModel , $referencedFields , $options  = null ) {
		return (<ManagerInterface> $this->_modelsManager)->addBelongsTo(
			this,
			fields,
			referenceModel,
			referencedFields,
			options
		);
    }

    /***
	 * Setup a 1-n relation between two models
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->hasMany("id", "RobotsParts", "robots_id");
	 *     }
	 * }
	 *</code>
	 **/
    protected function hasMany($fields , $referenceModel , $referencedFields , $options  = null ) {
		return (<ManagerInterface> $this->_modelsManager)->addHasMany(
			this,
			fields,
			referenceModel,
			referencedFields,
			options
		);
    }

    /***
	 * Setup an n-n relation between two models, through an intermediate relation
	 *
	 *<code>
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function initialize()
	 *     {
	 *         // Setup a many-to-many relation to Parts through RobotsParts
	 *         $this->hasManyToMany(
	 *             "id",
	 *             "RobotsParts",
	 *             "robots_id",
	 *             "parts_id",
	 *             "Parts",
	 *             "id",
	 *         );
	 *     }
	 * }
	 *</code>
	 *
	 * @param	string|array fields
	 * @param	string intermediateModel
	 * @param	string|array intermediateFields
	 * @param	string|array intermediateReferencedFields
	 * @param	string referencedModel
	 * @param   string|array referencedFields
	 * @param   array options
	 * @return  Phalcon\Mvc\Model\Relation
	 **/
    protected function hasManyToMany($fields , $intermediateModel , $intermediateFields , $intermediateReferencedFields , $referenceModel , $referencedFields , $options  = null ) {
		return (<ManagerInterface> $this->_modelsManager)->addHasManyToMany(
			this,
			fields,
			intermediateModel,
			intermediateFields,
			intermediateReferencedFields,
			referenceModel,
			referencedFields,
			options
		);
    }

    /***
	 * Setups a behavior in a model
	 *
	 *<code>
	 *
	 * use Phalcon\Mvc\Model;
	 * use Phalcon\Mvc\Model\Behavior\Timestampable;
	 *
	 * class Robots extends Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->addBehavior(
	 *             new Timestampable(
	 *                [
	 *                    "onCreate" => [
	 *                         "field"  => "created_at",
	 *                         "format" => "Y-m-d",
	 * 	                   ],
	 *                 ]
	 *             )
	 *         );
	 *     }
	 * }
	 *</code>
	 **/
    public function addBehavior($behavior ) {
		(<ManagerInterface> $this->_modelsManager)->addBehavior(this, behavior);
    }

    /***
	 * Sets if the model must keep the original record snapshot in memory
	 *
	 *<code>
	 *
	 * use Phalcon\Mvc\Model;
	 *
	 * class Robots extends Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->keepSnapshots(true);
	 *     }
	 * }
	 *</code>
	 **/
    protected function keepSnapshots($keepSnapshot ) {
		(<ManagerInterface> $this->_modelsManager)->keepSnapshots(this, keepSnapshot);
    }

    /***
	 * Sets the record's snapshot data.
	 * This method is used internally to set snapshot data when the model was set up to keep snapshot data
	 *
	 * @param array data
	 * @param array columnMap
	 **/
    public function setSnapshotData($data , $columnMap  = null ) {

		/**
		 * Build the snapshot based on a column map
		 */
		if ( gettype($columnMap) == "array" ) {

			$snapshot = [];
			foreach ( key, $data as $value ) {

				/**
				 * Use only strings
				 */
				if ( gettype($key) != "string" ) {
					continue;
				}

				/**
				 * Every field must be part of the column map
				 */
				if ( !fetch attribute, columnMap[key] ) {
					if ( !globals_get("orm.ignore_unknown_columns") ) {
						throw new Exception("Column '" . key . "' doesn't make part of the column map");
					} else {
						continue;
					}
				}

				if ( gettype($attribute) == "array" ) {
					if ( !fetch attribute, attribute[0] ) {
						if ( !globals_get("orm.ignore_unknown_columns") ) {
							throw new Exception("Column '" . key . "' doesn't make part of the column map");
						} else {
							continue;
						}
					}
				}

				$snapshot[attribute] = value;
			}
		} else {
			$snapshot = data;
		}

		$this->_snapshot = snapshot;
    }

    /***
	 * Sets the record's old snapshot data.
	 * This method is used internally to set old snapshot data when the model was set up to keep snapshot data
	 *
	 * @param array data
	 * @param array columnMap
	 **/
    public function setOldSnapshotData($data , $columnMap  = null ) {
		/**
		 * Build the snapshot based on a column map
		 */
		if ( gettype($columnMap) == "array" ) {
			$snapshot = [];
			foreach ( key, $data as $value ) {
				/**
				 * Use only strings
				 */
				if ( gettype($key) != "string" ) {
					continue;
				}
				/**
				 * Every field must be part of the column map
				 */
				if ( !fetch attribute, columnMap[key] ) {
					if ( !globals_get("orm.ignore_unknown_columns") ) {
						throw new Exception("Column '" . key . "' doesn't make part of the column map");
					} else {
						continue;
					}
				}
				if ( gettype($attribute) == "array" ) {
					if ( !fetch attribute, attribute[0] ) {
						if ( !globals_get("orm.ignore_unknown_columns") ) {
							throw new Exception("Column '" . key . "' doesn't make part of the column map");
						} else {
							continue;
						}
					}
				}
				$snapshot[attribute] = value;
			}
		} else {
			$snapshot = data;
		}

		$this->_oldSnapshot = snapshot;
    }

    /***
	 * Checks if the object has internal snapshot data
	 **/
    public function hasSnapshotData() {
		$snapshot = $this->_snapshot;

		return gettype($snapshot) == "array";
    }

    /***
	 * Returns the internal snapshot data
	 **/
    public function getSnapshotData() {
		return $this->_snapshot;
    }

    /***
	 * Returns the internal old snapshot data
	 **/
    public function getOldSnapshotData() {
		return $this->_oldSnapshot;
    }

    /***
	 * Check if a specific attribute has changed
	 * This only works if the model is keeping data snapshots
	 *
	 *<code>
	 * $robot = new Robots();
	 *
	 * $robot->type = "mechanical";
	 * $robot->name = "Astro Boy";
	 * $robot->year = 1952;
	 *
	 * $robot->create();
	 * $robot->type = "hydraulic";
	 * $hasChanged = $robot->hasChanged("type"); // returns true
	 * $hasChanged = $robot->hasChanged(["type", "name"]); // returns true
	 * $hasChanged = $robot->hasChanged(["type", "name", true]); // returns false
	 *</code>
	 *
	 * @param string|array fieldName
	 * @param boolean allFields
	 **/
    public function hasChanged($fieldName  = null , $allFields  = false ) {

		$changedFields = $this->getChangedFields();

		/**
		 * If a field was specif (ied we only check it
		 */
		if ( gettype($fieldName) == "string" ) {
			return in_array(fieldName, changedFields);
		} elseif ( gettype($fieldName) == "array" ) {
		    if ( allFields ) {
		        return array_intersect(fieldName, changedFields) == fieldName;
		    }

		    return count(array_intersect(fieldName, changedFields)) > 0;
		}

		return count(changedFields) > 0;
    }

    /***
	 * Check if a specific attribute was updated
	 * This only works if the model is keeping data snapshots
	 *
	 * @param string|array fieldName
	 **/
    public function hasUpdated($fieldName  = null , $allFields  = false ) {

		$updatedFields = $this->getUpdatedFields();

		/**
		 * If a field was specif (ied we only check it
		 */
		if ( gettype($fieldName) == "string" ) {
			return in_array(fieldName, updatedFields);
		} elseif ( gettype($fieldName) == "array" ) {
			if ( allFields ) {
				return array_intersect(fieldName, updatedFields) == fieldName;
			}

			return count(array_intersect(fieldName, updatedFields)) > 0;
		}

		return count(updatedFields) > 0;
    }

    /***
	 * Returns a list of changed values.
	 *
	 * <code>
	 * $robots = Robots::findFirst();
	 * print_r($robots->getChangedFields()); // []
	 *
	 * $robots->deleted = 'Y';
	 *
	 * $robots->getChangedFields();
	 * print_r($robots->getChangedFields()); // ["deleted"]
	 * </code>
	 **/
    public function getChangedFields() {
			columnMap, allAttributes, value;

		$snapshot = $this->_snapshot;
		if ( gettype($snapshot) != "array" ) {
			throw new Exception("The record doesn't have a valid data snapshot");
		}

		/**
		 * Return the models meta-data
		 */
		$metaData = $this->getModelsMetaData();

		/**
		 * The reversed column map is an array if ( the model has a column map
		 */
		$columnMap = metaData->getReverseColumnMap(this);

		/**
		 * Data types are field indexed
		 */
		if ( gettype($columnMap) != "array" ) {
			$allAttributes = metaData->getDataTypes(this);
		} else {
			$allAttributes = columnMap;
		}

		/**
		 * Check every attribute in the model
		 */
		$changed = [];

		foreach ( name, $allAttributes as $_ ) {
			/**
			 * If some attribute is not present in the snapshot, we assume the record as changed
			 */
			if ( !isset($snapshot[name]) ) {
				$changed[] = name;
				continue;
			}

			/**
			 * If some attribute is not present in the model, we assume the record as changed
			 */
			if ( !fetch value, $this->) {name} ) {
				$changed[] = name;
				continue;
			}

			/**
			 * Check if ( the field has changed
			 */
			if ( value !== snapshot[name] ) {
				$changed[] = name;
				continue;
			}
		}

		return changed;
    }

    /***
	 * Returns a list of updated values.
	 *
	 * <code>
	 * $robots = Robots::findFirst();
	 * print_r($robots->getChangedFields()); // []
	 *
	 * $robots->deleted = 'Y';
	 *
	 * $robots->getChangedFields();
	 * print_r($robots->getChangedFields()); // ["deleted"]
	 * $robots->save();
	 * print_r($robots->getChangedFields()); // []
	 * print_r($robots->getUpdatedFields()); // ["deleted"]
	 * </code>
	 **/
    public function getUpdatedFields() {
			oldSnapshot, value;

		$snapshot = $this->_snapshot;
		$oldSnapshot = $this->_oldSnapshot;

		if ( !globals_get("orm.update_snapshot_on_save") ) {
			throw new Exception("Update snapshot on save must be enabled for ( this method to work properly");
		}

		if ( gettype($snapshot) != "array" ) {
			throw new Exception("The record doesn't have a valid data snapshot");
		}

		/**
		 * Dirty state must be DIRTY_PERSISTENT to make the checking
		 */
		if ( $this->_dirtyState != self::DIRTY_STATE_PERSISTENT ) {
			throw new Exception("Change checking cannot be perfor (med because the object has not been persisted or is deleted");
		}

		$updated = [];

		foreach ( name, $snapshot as $value ) {
			/**
			 * If some attribute is not present in the oldSnapshot, we assume the record as changed
			 */
			if ( !isset($oldSnapshot[name]) ) {
				$updated[] = name;
				continue;
			}

			if ( value !== oldSnapshot[name] ) {
				$updated[] = name;
				continue;
			}
		}

		return updated;
    }

    /***
	 * Sets if a model must use dynamic update instead of the all-field update
	 *
	 *<code>
	 *
	 * use Phalcon\Mvc\Model;
	 *
	 * class Robots extends Model
	 * {
	 *     public function initialize()
	 *     {
	 *         $this->useDynamicUpdate(true);
	 *     }
	 * }
	 *</code>
	 **/
    protected function useDynamicUpdate($dynamicUpdate ) {
		(<ManagerInterface> $this->_modelsManager)->useDynamicUpdate(this, dynamicUpdate);
    }

    /***
	 * Returns related records based on defined relations
	 *
	 * @param string alias
	 * @param array arguments
	 * @return \Phalcon\Mvc\Model\ResultsetInterface
	 **/
    public function getRelated($alias , $arguments  = null ) {

		/**
		 * Query the relation by alias
		 */
		$className = get_class(this),
			manager = <ManagerInterface> $this->_modelsManager,
			relation = <RelationInterface> manager->getRelationByAlias(className, alias);
		if ( gettype($relation) != "object" ) {
			throw new Exception("There is no defined relations for ( the model '" . className . "' using alias '" . alias . "'");
		}

		/**
		 * Call the 'getRelationRecords' in the models manager
		 */
		return manager->getRelationRecords(relation, null, this, arguments);
    }

    /***
	 * Returns related records defined relations depending on the method name
	 *
	 * @param string modelName
	 * @param string method
	 * @param array arguments
	 * @return mixed
	 **/
    protected function _getRelatedRecords($modelName , $method , $arguments ) {

		$manager = <ManagerInterface> $this->_modelsManager;

		$relation = false,
			queryMethod = null;

		/**
		 * Calling find/findFirst if ( the method starts with "get"
		 */
		if ( starts_with(method, "get") ) {
			$relation = <RelationInterface> manager->getRelationByAlias(modelName, substr(method, 3));
		}

		/**
		 * Calling count if ( the method starts with "count"
		 */
		elseif ( starts_with(method, "count") ) {
			$queryMethod = "count",
				relation = <RelationInterface> manager->getRelationByAlias(modelName, substr(method, 5));
		}

		/**
		 * If the relation was found perfor (m the query via the models manager
		 */
		if ( gettype($relation) != "object" ) {
			return null;
		}


		return manager->getRelationRecords(
			relation,
			queryMethod,
			this,
			extraArgs
		);
    }

    /***
	 * Try to check if the query must invoke a finder
	 *
	 * @param  string method
	 * @param  array arguments
	 * @return \Phalcon\Mvc\ModelInterface[]|\Phalcon\Mvc\ModelInterface|boolean
	 **/
    protected final static function _invokeFinder($method , $arguments ) {
			attributes, field, extraMethodFirst, metaData;

		$extraMethod = null;

		/**
		 * Check if ( the method starts with "findFirst"
		 */
		if ( starts_with(method, "findFirstBy") ) {
			$type = "findFirst",
				extraMethod = substr(method, 11);
		}

		/**
		 * Check if ( the method starts with "find"
		 */
		elseif ( starts_with(method, "findBy") ) {
			$type = "find",
				extraMethod = substr(method, 6);
		}

		/**
		 * Check if ( the method starts with "count"
		 */
		elseif ( starts_with(method, "countBy") ) {
			$type = "count",
				extraMethod = substr(method, 7);
		}

		/**
		 * The called class is the model
		 */
		$modelName = get_called_class();

		if ( !extraMethod ) {
			return null;
		}

		if ( !fetch value, arguments[0] ) {
			throw new Exception("The static method '" . method . "' requires one argument");
		}

		$model = new {modelName}(),
			metaData = model->getModelsMetaData();

		/**
		 * Get the attributes
		 */
		$attributes = metaData->getReverseColumnMap(model);
		if ( gettype($attributes) != "array" ) {
			$attributes = metaData->getDataTypes(model);
		}

		/**
		 * Check if ( the extra-method is an attribute
		 */
		if ( isset($attributes[extraMethod]) ) {
			$field = extraMethod;
		} else {

			/**
			 * Lowercase the first letter of the extra-method
			 */
			$extraMethodFirst = lcfirst(extraMethod);
			if ( isset($attributes[extraMethodFirst]) ) {
				$field = extraMethodFirst;
			} else {

				/**
				 * Get the possible real method name
				 */
				$field = uncamelize(extraMethod);
				if ( !isset($attributes[field]) ) {
					throw new Exception("Cannot resolve attribute '" . extraMethod . "' in the model");
				}
			}
		}

		/**
		 * Execute the query
		 */
		return {modelName}::{type}([
			"conditions": "[" . field . "] = ?0",
			"bind"		: [value]
		]);
    }

    /***
	 * Handles method calls when a method is not implemented
	 *
	 * @param	string method
	 * @param	array arguments
	 * @return	mixed
	 **/
    public function __call($method , $arguments ) {

		$records = self::_invokeFinder(method, arguments);
		if ( records !== null ) {
			return records;
		}

		$modelName = get_class(this);

		/**
		 * Check if ( there is a default action using the magic getter
		 */
		$records = $this->_getRelatedRecords(modelName, method, arguments);
		if ( records !== null ) {
			return records;
		}

		/**
		 * Try to find a replacement foreach ( the missing $a as $method behavior/listener
		 */
		$status = (<ManagerInterface> $this->_modelsManager)->missingMethod(this, method, arguments);
		if ( status !== null ) {
			return status;
		}

		/**
		 * The method doesn't exist throw an exception
		 */
		throw new Exception("The method '" . method . "' doesn't exist on model '" . modelName . "'");
    }

    /***
	 * Handles method calls when a static method is not implemented
	 *
	 * @param	string method
	 * @param	array arguments
	 * @return	mixed
	 **/
    public static function __callStatic($method , $arguments ) {

		$records = self::_invokeFinder(method, arguments);
		if ( records === null ) {
			throw new Exception("The static method '" . method . "' doesn't exist");
		}

		return records;
    }

    /***
	 * Magic method to assign values to the the model
	 *
	 * @param string property
	 * @param mixed value
	 **/
    public function __set($property , $value ) {
			relation, referencedModel, key, item, dirtyState;

		/**
		 * Values are probably relationships if ( they are objects
		 */
		if ( gettype($value) == "object" ) {
			if ( value instanceof ModelInterface ) {
				$dirtyState = $this->_dirtyState;
				if ( (value->getDirtyState() != dirtyState) ) {
					$dirtyState = self::DIRTY_STATE_TRANSIENT;
				}
				$lowerProperty = strtolower(property),
					this->{lowerProperty} = value,
					this->_related[lowerProperty] = value,
					this->_dirtyState = dirtyState;
				return value;
			}
		}

		/**
		 * Check if ( the value is an array
		 */
		if ( gettype($value) == "array" ) {

			$lowerProperty = strtolower(property),
				modelName = get_class(this),
				manager = $this->getModelsManager();

			$related = [];
			foreach ( key, $value as $item ) {
				if ( gettype($item) == "object" ) {
					if ( item instanceof ModelInterface ) {
						$related[] = item;
					}
				} else {
					$lowerKey = strtolower(key),
						this->{lowerKey} = item,
						relation = <RelationInterface> manager->getRelationByAlias(modelName, lowerProperty);
					if ( gettype($relation) == "object" ) {
						$referencedModel = manager->load(relation->getReferencedModel());
						referencedModel->writeAttribute(lowerKey, item);
					}
				}
			}

			if ( count(related) > 0 ) {
				$this->_related[lowerProperty] = related,
					this->_dirtyState = self::DIRTY_STATE_TRANSIENT;
			}

			return value;
		}

		// Use possible setter.
		if ( $this->_possibleSetter(property, value) ) {
			return value;
		}

		// Throw an exception if ( there is an attempt to set a non-public property.
		if ( property_exists(this, property) ) {
			$manager = $this->getModelsManager();
			if ( !manager->isVisibleModelProperty(this, property) ) {
				throw new Exception("Property '" . property . "' does not have a setter.");
			}
		}

		$this->{property} = value;

		return value;
    }

    /***
	 * Check for, and attempt to use, possible setter.
	 *
	 * @param string property
	 * @param mixed value
	 * @return string
	 **/
    protected final function _possibleSetter($property , $value ) {

		$possibleSetter = "set" . camelize(property);
		if ( method_exists(this, possibleSetter) ) {
			this->{possibleSetter}(value);
			return true;
		}
		return false;
    }

    /***
	 * Magic method to get related records using the relation alias as a property
	 *
	 * @param string property
	 * @return \Phalcon\Mvc\Model\Resultset|Phalcon\Mvc\Model
	 **/
    public function __get($property ) {

		$modelName = get_class(this),
			manager = $this->getModelsManager(),
			lowerProperty = strtolower(property);

		/**
		 * Check if ( the property is a relationship
		 */
		$relation = <RelationInterface> manager->getRelationByAlias(modelName, lowerProperty);
		if ( gettype($relation) == "object" ) {

			/*
			 Not fetch a relation if ( it is on CamelCase
			 */
			if ( isset $this->) {lowerProperty} && typeof $this->) {lowerProperty} == "object" ) {
				return $this->{lowerProperty};
			}
			/**
			 * Get the related records
			 */
			$result = manager->getRelationRecords(relation, null, this, null);

			/**
			 * Assign the result to the object
			 */
			if ( gettype($result) == "object" ) {

				/**
				 * We assign the result to the instance avoiding future queries
				 */
				$this->{lowerProperty} = result;

				/**
				 * For belongs-to relations we store the object in the related bag
				 */
				if ( result instanceof ModelInterface ) {
					$this->_related[lowerProperty] = result;
				}
			}

			return result;
		}

		/**
		 * Check if ( the property has getters
		 */
		$method = "get" . camelize(property);

		if ( method_exists(this, method) ) {
			return $this->{method}();
		}

		/**
		 * A notice is shown if ( the property is not defined and it isn't a relationship
		 */
		trigger_error("Access to undefined property " . modelName . "::" . property);
		return null;
    }

    /***
	 * Magic method to check if a property is a valid relation
	 **/
    public function __isset($property ) {

		$modelName = get_class(this),
			manager = <ManagerInterface> $this->getModelsManager();

		/**
		 * Check if ( the property is a relationship
		 */
		$relation = <RelationInterface> manager->getRelationByAlias(modelName, property);
		return gettype($relation) == "object";
    }

    /***
	 * Serializes the object ignoring connections, services, related objects or static properties
	 **/
    public function serialize() {

		$attributes = $this->toArray(),
		    manager = <ManagerInterface> $this->getModelsManager();

		if ( manager->isKeepingSnapshots(this) ) {
			$snapshot = $this->_snapshot;
			/**
			 * If attributes is not the same as snapshot then save snapshot too
			 */
			if ( snapshot != null && attributes != snapshot ) {
				return serialize(["_attributes": attributes, "_snapshot": snapshot]);
			}
		}

		return serialize(attributes);
    }

    /***
	 * Unserializes the object from a serialized string
	 **/
    public function unserialize($data ) {

		$attributes = unserialize(data);
		if ( gettype($attributes) == "array" ) {

			/**
			 * Obtain the default DI
			 */
			$dependencyInjector = Di::getDefault();
			if ( gettype($dependencyInjector) != "object" ) {
				throw new Exception("A dependency injector container is required to obtain the services related to the ORM");
			}

			/**
			 * Update the dependency injector
			 */
			$this->_dependencyInjector = dependencyInjector;

			/**
			 * Gets the default modelsManager service
			 */
			$manager = <ManagerInterface> dependencyInjector->getShared("modelsManager");
			if ( gettype($manager) != "object" ) {
				throw new Exception("The injected service 'modelsManager' is not valid");
			}

			/**
			 * Update the models manager
			 */
			$this->_modelsManager = manager;

			/**
			 * Try to initialize the model
			 */
			manager->initialize(this);
			if ( manager->isKeepingSnapshots(this) ) {
				if ( fetch snapshot, attributes["_snapshot"] ) {
					$this->_snapshot = snapshot;
					$attributes = attributes["_attributes"];
				}
				else {
					$this->_snapshot = attributes;
				}
			}

			/**
			 * Update the objects attributes
			 */
			foreach ( key, $attributes as $value ) {
				$this->{key} = value;
			}
		}
    }

    /***
	 * Returns a simple representation of the object that can be used with var_dump
	 *
	 *<code>
	 * var_dump(
	 *     $robot->dump()
	 * );
	 *</code>
	 **/
    public function dump() {
		return get_object_vars(this);
    }

    /***
	 * Returns the instance as an array representation
	 *
	 *<code>
	 * print_r(
	 *     $robot->toArray()
	 * );
	 *</code>
	 *
	 * @param array $columns
	 * @return array
	 **/
    public function toArray($columns  = null ) {
			attributeField, value;

		$data = [],
			metaData = $this->getModelsMetaData(),
			columnMap = metaData->getColumnMap(this);

		foreach ( $metaData->getAttributes(this) as $attribute ) {

			/**
			 * Check if ( the columns must be renamed
			 */
			if ( gettype($columnMap) == "array" ) {
				if ( !fetch attributeField, columnMap[attribute] ) {
					if ( !globals_get("orm.ignore_unknown_columns") ) {
						throw new Exception("Column '" . attribute . "' doesn't make part of the column map");
					} else {
						continue;
					}
				}
			} else {
				$attributeField = attribute;
			}

			if ( gettype($columns) == "array" ) {
				if ( !in_array(attributeField, columns) ) {
					continue;
				}
			}

			if ( fetch value, $this->) {attributeField} ) {
				$data[attributeField] = value;
			} else {
				$data[attributeField] = null;
			}
		}

		return data;
    }

    /***
	* Serializes the object for json_encode
	*
	*<code>
	* echo json_encode($robot);
	*</code>
	*
	* @return array
	**/
    public function jsonSerialize() {
		return $this->toArray();
    }

    /***
	 * Enables/disables options in the ORM
	 **/
    public static function setup($options ) {
			exceptionOnFailedSave, phqlLiterals, virtualForeignKeys,
			lateStateBinding, castOnHydrate, ignoreUnknownColumns,
			updateSnapshotOnSave, disableAssignSetters;

		/**
		 * Enables/Disables globally the internal events
		 */
		if ( fetch disableEvents, options["events"] ) {
			globals_set("orm.events", disableEvents);
		}

		/**
		 * Enables/Disables virtual for (eign keys
		 */
		if ( fetch virtualForeignKeys, options["virtualForeignKeys"] ) {
			globals_set("orm.virtual_for (eign_keys", virtualForeignKeys);
		}

		/**
		 * Enables/Disables column renaming
		 */
		if ( fetch columnRenaming, options["columnRenaming"] ) {
			globals_set("orm.column_renaming", columnRenaming);
		}

		/**
		 * Enables/Disables automatic not null validation
		 */
		if ( fetch notNullValidations, options["notNullValidations"] ) {
			globals_set("orm.not_null_validations", notNullValidations);
		}

		/**
		 * Enables/Disables throws an exception if ( the saving process fails
		 */
		if ( fetch exceptionOnFailedSave, options["exceptionOnFailedSave"] ) {
			globals_set("orm.exception_on_failed_save", exceptionOnFailedSave);
		}

		/**
		 * Enables/Disables literals in PHQL this improves the security of applications
		 */
		if ( fetch phqlLiterals, options["phqlLiterals"] ) {
			globals_set("orm.enable_literals", phqlLiterals);
		}

		/**
		 * Enables/Disables late state binding on model hydration
		 */
		if ( fetch lateStateBinding, options["lateStateBinding"] ) {
			globals_set("orm.late_state_binding", lateStateBinding);
		}

		/**
		 * Enables/Disables automatic cast to original types on hydration
		 */
		if ( fetch castOnHydrate, options["castOnHydrate"] ) {
			globals_set("orm.cast_on_hydrate", castOnHydrate);
		}

		/**
		 * Allows to ignore unknown columns when hydrating objects
		 */
		if ( fetch ignoreUnknownColumns, options["ignoreUnknownColumns"] ) {
			globals_set("orm.ignore_unknown_columns", ignoreUnknownColumns);
		}

		if ( fetch updateSnapshotOnSave, options["updateSnapshotOnSave"] ) {
			globals_set("orm.update_snapshot_on_save", updateSnapshotOnSave);
		}

		if ( fetch disableAssignSetters, options["disableAssignSetters"] ) {
		    globals_set("orm.disable_assign_setters", disableAssignSetters);
		}
    }

    /***
	 * Reset a model instance data
	 **/
    public function reset() {
		$this->_uniqueParams = null;
		$this->_snapshot = null;
    }

}