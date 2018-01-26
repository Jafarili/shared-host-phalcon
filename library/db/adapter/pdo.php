<?php


namespace Phalcon\Db\Adapter;

use Phalcon\Db\Adapter;
use Phalcon\Db\Exception;
use Phalcon\Db\Column;
use Phalcon\Db\ResultInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Db\Result\Pdo as ResultPdo;


/***
 * Phalcon\Db\Adapter\Pdo
 *
 * Phalcon\Db\Adapter\Pdo is the Phalcon\Db that internally uses PDO to connect to a database
 *
 * <code>
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

abstract class Pdo extends Adapter {

    /***
	 * PDO Handler
	 *
	 * @var \Pdo
	 **/
    protected $_pdo;

    /***
	 * Last affected rows
	 **/
    protected $_affectedRows;

    /***
	 * Constructor for Phalcon\Db\Adapter\Pdo
	 **/
    public function __construct($descriptor ) {
		this->connect(descriptor);
		parent::__construct(descriptor);
    }

    /***
	 * This method is automatically called in \Phalcon\Db\Adapter\Pdo constructor.
	 *
	 * Call it when you need to restore a database connection.
	 *
	 *<code>
	 * use Phalcon\Db\Adapter\Pdo\Mysql;
	 *
	 * // Make a connection
	 * $connection = new Mysql(
	 *     [
	 *         "host"     => "localhost",
	 *         "username" => "sigma",
	 *         "password" => "secret",
	 *         "dbname"   => "blog",
	 *         "port"     => 3306,
	 *     ]
	 * );
	 *
	 * // Reconnect
	 * $connection->connect();
	 * </code>
	 **/
    public function connect($descriptor  = null ) {
			persistent, options, key, value;

		if ( empty descriptor ) {
			$descriptor = (array) $this->_descriptor;
		}

		/**
		 * Check for ( a username or use null as default
		 */
		if ( fetch username, descriptor["username"] ) {
			unset descriptor["username"];
		} else {
			$username = null;
		}

		/**
		 * Check for ( a password or use null as default
		 */
		if ( fetch password, descriptor["password"] ) {
			unset descriptor["password"];
		} else {
			$password = null;
		}

		/**
		 * Check if ( the developer has defined custom options or create one from scratch
		 */
		if ( fetch options, descriptor["options"] ) {
			unset descriptor["options"];
		} else {
			$options = [];
		}

		/**
		 * Check for ( \PDO::XXX class constant aliases
		 */
        foreach ( key, $options as $value ) {
            if ( gettype($key) == "string" && defined("\PDO::" . key->upper()) ) {
                $options[constant("\PDO::" . key->upper())] = value;
                unset options[key];
            }
        }

		/**
		 * Check if ( the connection must be persistent
		 */
		if ( fetch persistent, descriptor["persistent"] ) {
			if ( persistent ) {
				$options[\Pdo::ATTR_PERSISTENT] = true;
			}
			unset descriptor["persistent"];
		}

		/**
		 * Remove the dialectClass from the descriptor if ( any
		 */
		if ( isset descriptor["dialectClass"] ) {
			unset descriptor["dialectClass"];
		}

		/**
		 * Check if ( the user has defined a custom dsn
		 */
		 if ( !fetch dsnAttributes, descriptor["dsn"] ) {
			$dsnParts = [];
			foreach ( key, $descriptor as $value ) {
				$dsnParts[] = key . "=" . value;
			}
			$dsnAttributes = join(";", dsnParts);
		}

		$options[\Pdo::ATTR_ERRMODE] = \Pdo::ERRMODE_EXCEPTION;

		/**
		 * Create the connection using PDO
		 */
		$this->_pdo = new \Pdo(this->_type . ":" . dsnAttributes, username, password, options);

		return true;
    }

    /***
	 * Returns a PDO prepared statement to be executed with 'executePrepared'
	 *
	 *<code>
	 * use Phalcon\Db\Column;
	 *
	 * $statement = $db->prepare(
	 *     "SELECT * FROM robots WHERE name = :name"
	 * );
	 *
	 * $result = $connection->executePrepared(
	 *     $statement,
	 *     [
	 *         "name" => "Voltron",
	 *     ],
	 *     [
	 *         "name" => Column::BIND_PARAM_INT,
	 *     ]
	 * );
	 *</code>
	 **/
    public function prepare($sqlStatement ) {
		return $this->_pdo->prepare(sqlStatement);
    }

    /***
	 * Executes a prepared statement binding. This function uses integer indexes starting from zero
	 *
	 *<code>
	 * use Phalcon\Db\Column;
	 *
	 * $statement = $db->prepare(
	 *     "SELECT * FROM robots WHERE name = :name"
	 * );
	 *
	 * $result = $connection->executePrepared(
	 *     $statement,
	 *     [
	 *         "name" => "Voltron",
	 *     ],
	 *     [
	 *         "name" => Column::BIND_PARAM_INT,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param \PDOStatement statement
	 * @param array placeholders
	 * @param array dataTypes
	 * @return \PDOStatement
	 **/
    public function executePrepared($statement , $placeholders , $dataTypes ) {
			parameter, position, itemValue;

		foreach ( wildcard, $placeholders as $value ) {

			if ( gettype($wildcard) == "integer" ) {
				$parameter = wildcard + 1;
			} elseif ( gettype($wildcard) == "string" ) {
				$parameter = wildcard;
			} else {
				throw new Exception("Invalid bind parameter (1)");
			}

			if ( gettype($dataTypes) == "array" && fetch type, dataTypes[wildcard] ) {

				/**
				 * The bind type is double so we try to get the double value
				 */
				if ( type == Column::BIND_PARAM_DECIMAL ) {
					$castValue = doubleval(value),
						type = Column::BIND_SKIP;
				} else {
					if ( globals_get("db.for (ce_casting") ) ) {
						if ( gettype($value) != "array" ) {
							switch type {

								case Column::BIND_PARAM_INT:
									$castValue = intval(value, 10);
									break;

								case Column::BIND_PARAM_STR:
									$castValue = (string) value;
									break;

								case Column::BIND_PARAM_NULL:
									$castValue = null;
									break;

								case Column::BIND_PARAM_BOOL:
									$castValue = (boolean) value;
									break;

								default:
									$castValue = value;
									break;
							}
						} else {
							$castValue = value;
						}
					} else {
						$castValue = value;
					}
				}

				/**
				 * 1024 is ignore the bind type
				 */
				if ( gettype($castValue) != "array" ) {
					if ( type == Column::BIND_SKIP ) {
						statement->bindValue(parameter, castValue);
					} else {
						statement->bindValue(parameter, castValue, type);
					}
				} else {
					foreach ( position, $castValue as $itemValue ) {
						if ( type == Column::BIND_SKIP ) {
							statement->bindValue(parameter . position, itemValue);
						} else {
							statement->bindValue(parameter . position, itemValue, type);
						}
					}
				}
			} else {
				if ( gettype($value) != "array" ) {
					statement->bindValue(parameter, value);
				} else {
					foreach ( position, $value as $itemValue ) {
						statement->bindValue(parameter . position, itemValue);
					}
				}
			}
		}

		statement->execute();
		return statement;
    }

    /***
	 * Sends SQL statements to the database server returning the success state.
	 * Use this method only when the SQL statement sent to the server is returning rows
	 *
	 *<code>
	 * // Querying data
	 * $resultset = $connection->query(
	 *     "SELECT * FROM robots WHERE type = 'mechanical'"
	 * );
	 *
	 * $resultset = $connection->query(
	 *     "SELECT * FROM robots WHERE type = ?",
	 *     [
	 *         "mechanical",
	 *     ]
	 * );
	 *</code>
	 **/
    public function query($sqlStatement , $bindParams  = null , $bindTypes  = null ) {

		$eventsManager = <ManagerInterface> $this->_eventsManager;

		/**
		 * Execute the befor (eQuery event if ( an EventsManager is available
		 */
		if ( gettype($eventsManager) == "object" ) {
			$this->_sqlStatement = sqlStatement,
				this->_sqlVariables = bindParams,
				this->_sqlBindTypes = bindTypes;
			if ( eventsManager->fire("db:befor (eQuery", this) === false ) ) {
				return false;
			}
		}

		$pdo = <\Pdo> $this->_pdo;
		if ( gettype($bindParams) == "array" ) {
			$statement = pdo->prepare(sqlStatement);
			if ( gettype($statement) == "object" ) {
				$statement = $this->executePrepared(statement, bindParams, bindTypes);
			}
		} else {
			$statement = pdo->query(sqlStatement);
		}

		/**
		 * Execute the afterQuery event if ( an EventsManager is available
		 */
		if ( gettype($statement) == "object" ) {
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("db:afterQuery", this);
			}
			return new ResultPdo(this, statement, sqlStatement, bindParams, bindTypes);
		}

		return statement;
    }

    /***
	 * Sends SQL statements to the database server returning the success state.
	 * Use this method only when the SQL statement sent to the server doesn't return any rows
	 *
	 *<code>
	 * // Inserting data
	 * $success = $connection->execute(
	 *     "INSERT INTO robots VALUES (1, 'Astro Boy')"
	 * );
	 *
	 * $success = $connection->execute(
	 *     "INSERT INTO robots VALUES (?, ?)",
	 *     [
	 *         1,
	 *         "Astro Boy",
	 *     ]
	 * );
	 *</code>
	 **/
    public function execute($sqlStatement , $bindParams  = null , $bindTypes  = null ) {

		/**
		 * Execute the befor (eQuery event if ( an EventsManager is available
		 */
		$eventsManager = <ManagerInterface> $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			$this->_sqlStatement = sqlStatement,
				this->_sqlVariables = bindParams,
				this->_sqlBindTypes = bindTypes;
			if ( eventsManager->fire("db:befor (eQuery", this) === false ) ) {
				return false;
			}
		}

		/**
		 * Initialize affectedRows to 0
		 */
		$affectedRows = 0;

		$pdo = <\Pdo> $this->_pdo;
		if ( gettype($bindParams) == "array" ) {
			$statement = pdo->prepare(sqlStatement);
			if ( gettype($statement) == "object" ) {
				$newStatement = $this->executePrepared(statement, bindParams, bindTypes),
					affectedRows = newStatement->rowCount();
			}
		} else {
			$affectedRows = pdo->exec(sqlStatement);
		}

		/**
		 * Execute the afterQuery event if ( an EventsManager is available
		 */
		if ( gettype($affectedRows) == "integer" ) {
			$this->_affectedRows = affectedRows;
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("db:afterQuery", this);
			}
		}

		return true;
    }

    /***
	 * Returns the number of affected rows by the latest INSERT/UPDATE/DELETE executed in the database system
	 *
	 *<code>
	 * $connection->execute(
	 *     "DELETE FROM robots"
	 * );
	 *
	 * echo $connection->affectedRows(), " were deleted";
	 *</code>
	 **/
    public function affectedRows() {
		return $this->_affectedRows;
    }

    /***
	 * Closes the active connection returning success. Phalcon automatically closes and destroys
	 * active connections when the request ends
	 **/
    public function close() {
		$pdo = $this->_pdo;
		if ( gettype($pdo) == "object" ) {
			$this->_pdo = null;
		}
		return true;
    }

    /***
	 * Escapes a value to avoid SQL injections according to the active charset in the connection
	 *
	 *<code>
	 * $escapedStr = $connection->escapeString("some dangerous value");
	 *</code>
	 **/
    public function escapeString($str ) {
		return $this->_pdo->quote(str);
    }

    /***
	 * Converts bound parameters such as :name: or ?1 into PDO bind params ?
	 *
	 *<code>
	 * print_r(
	 *     $connection->convertBoundParams(
	 *         "SELECT * FROM robots WHERE name = :name:",
	 *         [
	 *             "Bender",
	 *         ]
	 *     )
	 * );
	 *</code>
	 **/
    public function convertBoundParams($sql , $params ) {
			setOrder, placeMatch, value;

		$placeHolders = [],
			bindPattern = "/\\?([0-9]+)|:([a-zA-Z0-9_]+):/",
			matches = null, setOrder = 2;

		if ( preg_match_all(bindPattern, sql, matches, setOrder) ) {
			foreach ( $matches as $placeMatch ) {

				if ( !fetch value, params[placeMatch[1]] ) {
					if ( isset($placeMatch[2]) ) {
						if ( !fetch value, params[placeMatch[2]] ) {
							throw new Exception("Matched parameter wasn't found in parameters list");
						}
					} else {
						throw new Exception("Matched parameter wasn't found in parameters list");
					}
				}

				$placeHolders[] = value;
			}

			$boundSql = preg_replace(bindPattern, "?", sql);
		} else {
			$boundSql = sql;
		}

		return [
			"sql"    : boundSql,
			"params" : placeHolders
		];
    }

    /***
	 * Returns the insert id for the auto_increment/serial column inserted in the latest executed SQL statement
	 *
	 *<code>
	 * // Inserting a new robot
	 * $success = $connection->insert(
	 *     "robots",
	 *     [
	 *         "Astro Boy",
	 *         1952,
	 *     ],
	 *     [
	 *         "name",
	 *         "year",
	 *     ]
	 * );
	 *
	 * // Getting the generated id
	 * $id = $connection->lastInsertId();
	 *</code>
	 *
	 * @param string sequenceName
	 * @return int|boolean
	 **/
    public function lastInsertId($sequenceName  = null ) {
		$pdo = $this->_pdo;
		if ( gettype($pdo) != "object" ) {
			return false;
		}
		return pdo->lastInsertId(sequenceName);
    }

    /***
	 * Starts a transaction in the connection
	 **/
    public function begin($nesting  = true ) {

		$pdo = $this->_pdo;
		if ( gettype($pdo) != "object" ) {
			return false;
		}

		/**
		 * Increase the transaction nesting level
		 */
		$this->_transactionLevel++;

		/**
		 * Check the transaction nesting level
		 */
		$transactionLevel = (int) $this->_transactionLevel;

		if ( transactionLevel == 1 ) {

			/**
			 * Notif (y the events manager about the started transaction
			 */
			$eventsManager = <ManagerInterface> $this->_eventsManager;
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("db:beginTransaction", this);
			}

			return pdo->beginTransaction();
		} else {

			/**
			 * Check if ( the current database system supports nested transactions
			 */
			if ( transactionLevel && nesting && $this->isNestedTransactionsWithSavepoints() ) {

				$eventsManager = <ManagerInterface> $this->_eventsManager,
					savepointName = $this->getNestedTransactionSavepointName();

				/**
				 * Notif (y the events manager about the created savepoint
				 */
				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("db:createSavepoint", this, savepointName);
				}

				return $this->createSavepoint(savepointName);
			}

		}

		return false;
    }

    /***
	 * Rollbacks the active transaction in the connection
	 **/
    public function rollback($nesting  = true ) {

		$pdo = $this->_pdo;
		if ( gettype($pdo) != "object" ) {
			return false;
		}

		/**
		 * Check the transaction nesting level
		 */
		$transactionLevel = (int) $this->_transactionLevel;
		if ( !transactionLevel ) {
			throw new Exception("There is no active transaction");
		}

		if ( transactionLevel == 1 ) {

			/**
			 * Notif (y the events manager about the rollbacked transaction
			 */
			$eventsManager = <ManagerInterface> $this->_eventsManager;
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("db:rollbackTransaction", this);
			}

			/**
			 * Reduce the transaction nesting level
			 */
			$this->_transactionLevel--;

			return pdo->rollback();

		} else {

			/**
			 * Check if ( the current database system supports nested transactions
			 */
			if ( transactionLevel && nesting && $this->isNestedTransactionsWithSavepoints() ) {

				$savepointName = $this->getNestedTransactionSavepointName();

				/**
				 * Notif (y the events manager about the rolled back savepoint
				 */
				$eventsManager = <ManagerInterface> $this->_eventsManager;
				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("db:rollbackSavepoint", this, savepointName);
				}

				/**
				 * Reduce the transaction nesting level
				 */
				$this->_transactionLevel--;

				return $this->rollbackSavepoint(savepointName);
			}
		}

		/**
		 * Reduce the transaction nesting level
		 */
		if ( transactionLevel > 0 ) {
			$this->_transactionLevel--;
		}

		return false;
    }

    /***
	 * Commits the active transaction in the connection
	 **/
    public function commit($nesting  = true ) {

		$pdo = $this->_pdo;
		if ( gettype($pdo) != "object" ) {
			return false;
		}

		/**
		 * Check the transaction nesting level
		 */
		$transactionLevel = (int) $this->_transactionLevel;
		if ( !transactionLevel ) {
			throw new Exception("There is no active transaction");
		}

		if ( transactionLevel == 1 ) {

			/**
			 * Notif (y the events manager about the committed transaction
			 */
			$eventsManager = <ManagerInterface> $this->_eventsManager;
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("db:commitTransaction", this);
			}

			/**
			 * Reduce the transaction nesting level
			 */
			$this->_transactionLevel--;

			return pdo->commit();
		} else {

			/**
			 * Check if ( the current database system supports nested transactions
			 */
			if ( transactionLevel && nesting && $this->isNestedTransactionsWithSavepoints() ) {

				/**
				 * Notif (y the events manager about the committed savepoint
				 */
				$eventsManager = <ManagerInterface> $this->_eventsManager,
					savepointName = $this->getNestedTransactionSavepointName();
				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("db:releaseSavepoint", this, savepointName);
				}

				/**
				 * Reduce the transaction nesting level
				 */
				$this->_transactionLevel--;

				return $this->releaseSavepoint(savepointName);
			}

		}

		/**
		 * Reduce the transaction nesting level
		 */
		if ( transactionLevel > 0 ) {
			$this->_transactionLevel--;
		}

		return false;
    }

    /***
	 * Returns the current transaction nesting level
	 **/
    public function getTransactionLevel() {
		return $this->_transactionLevel;
    }

    /***
	 * Checks whether the connection is under a transaction
	 *
	 *<code>
	 * $connection->begin();
	 *
	 * // true
	 * var_dump(
	 *     $connection->isUnderTransaction()
	 * );
	 *</code>
	 **/
    public function isUnderTransaction() {
		$pdo = $this->_pdo;
		if ( gettype($pdo) == "object" ) {
			return pdo->inTransaction();
		}
		return false;
    }

    /***
	 * Return internal PDO handler
	 **/
    public function getInternalHandler() {
		return $this->_pdo;
    }

    /***
	 * Return the error info, if any
	 *
	 * @return array
	 **/
    public function getErrorInfo() {
		return $this->_pdo->errorInfo();
    }

}