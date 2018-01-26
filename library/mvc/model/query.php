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

		if ( gettype($phql) != "null" ) {
			$this->_phql = phql;
		}

		if ( gettype($dependencyInjector) == "object" ) {
			this->setDI(dependencyInjector);
		}

		if ( gettype($options) == "array" && fetch enableImplicitJoins, options["enable_implicit_joins"] ) {
			$this->_enableImplicitJoins = enableImplicitJoins == true;
		} else {
			$this->_enableImplicitJoins = globals_get("orm.enable_implicit_joins");
		}
    }

    /***
	 * Sets the dependency injection container
	 **/
    public function setDI($dependencyInjector ) {

		$manager = dependencyInjector->getShared("modelsManager");
		if ( gettype($manager) != "object" ) {
			throw new Exception("Injected service 'modelsManager' is invalid");
		}

		$metaData = dependencyInjector->getShared("modelsMetadata");
		if ( gettype($metaData) != "object" ) {
			throw new Exception("Injected service 'modelsMetaData' is invalid");
		}

		$this->_manager = manager,
			this->_metaData = metaData;

		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the dependency injection container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Tells to the query if only the first row in the resultset must be returned
	 **/
    public function setUniqueRow($uniqueRow ) {
		$this->_uniqueRow = uniqueRow;
		return this;
    }

    /***
	 * Check if the query is programmed to get only the first row in the resultset
	 **/
    public function getUniqueRow() {
		return $this->_uniqueRow;
    }

    /***
	 * Replaces the model's name to its source name in a qualified-name expression
	 **/
    protected final function _getQualified($expr ) {
			source, sqlAliasesModelsInstances, realColumnName, columnDomain,
			model, models, columnMap, hasModel, className;
		int number;

		$columnName = expr["name"];

		/**
		 * Check if ( the qualif (ied name is a column alias
		 */
		$sqlColumnAliases = $this->_sqlColumnAliases;
		if ( isset($sqlColumnAliases[columnName]) && (!isset expr["domain"] || empty expr["domain"]) ) {
			return [
				"type": "qualif (ied",
				"name": columnName
			];
		}

		$metaData = $this->_metaData;

		/**
		 * Check if ( the qualif (ied name has a domain
		 */
		if ( fetch columnDomain, expr["domain"] ) {

			$sqlAliases = $this->_sqlAliases;

			/**
			 * The column has a domain, we need to check if ( it's an alias
			 */
			if ( !fetch source, sqlAliases[columnDomain] ) {
				throw new Exception("Unknown model or alias '" . columnDomain . "' (11), when preparing: " . $this->_phql);
			}

			/**
			 * Change the selected column by its real name on its mapped table
			 */
			if ( globals_get("orm.column_renaming") ) {

				/**
				 * Retrieve the corresponding model by its alias
				 */
				$sqlAliasesModelsInstances = $this->_sqlAliasesModelsInstances;

				/**
				 * We need the model instance to retrieve the reversed column map
				 */
				if ( !fetch model, sqlAliasesModelsInstances[columnDomain] ) {
					throw new Exception(
						"There is no model related to model or alias '" . columnDomain . "', when executing: " . $this->_phql
					);
				}

				$columnMap = metaData->getReverseColumnMap(model);
			} else {
				$columnMap = null;
			}

			if ( gettype($columnMap) == "array" ) {
				if ( !fetch realColumnName, columnMap[columnName] ) {
					throw new Exception(
						"Column '" . columnName . "' doesn't belong to the model or alias '" . columnDomain . "', when executing: ". $this->_phql
					);
				}
			} else {
				$realColumnName = columnName;
			}

		} else {

			/**
			 * If the column IR doesn't have a domain, we must check for ( ambiguities
			 */
			$number = 0, hasModel = false;

			foreach ( $this->_modelsInstances as $model ) {

				/**
				 * Check if ( the attribute belongs to the current model
				 */
				if ( metaData->hasAttribute(model, columnName) ) {
					$number++;
					if ( number > 1 ) {
						throw new Exception("The column '" . columnName . "' is ambiguous, when preparing: " . $this->_phql);
					}
					$hasModel = model;
				}
			}

			/**
			 * After check in every model, the column does not belong to any of the selected models
			 */
			if ( hasModel === false ) {
				throw new Exception(
					"Column '" . columnName . "' doesn't belong to any of the selected models (1), when preparing: " . $this->_phql
				);
			}

			/**
			 * Check if ( the _models property is correctly prepared
			 */
			$models = $this->_models;
			if ( gettype($models) != "array" ) {
				throw new Exception("The models list was not loaded correctly");
			}

			/**
			 * Obtain the model's source from the _models list
			 */
			$className = get_class(hasModel);
			if ( !fetch source, models[className] ) {
				throw new Exception(
					"Can't obtain model's source from models list: '" . className . "', when preparing: " . $this->_phql
				);
			}

			/**
			 * Rename the column
			 */
			if ( globals_get("orm.column_renaming") ) {
				$columnMap = metaData->getReverseColumnMap(hasModel);
			} else {
				$columnMap = null;
			}

			if ( gettype($columnMap) == "array" ) {
				/**
				 * The real column name is in the column map
				 */
				if ( !fetch realColumnName, columnMap[columnName] ) {
					throw new Exception(
						"Column '" . columnName . "' doesn't belong to any of the selected models (3), when preparing: " . $this->_phql
					);
				}
			} else {
				$realColumnName = columnName;
			}
		}

		/**
		 * Create an array with the qualif (ied info
		 */
		return [
			"type"  : "qualif (ied",
			"domain": source,
			"name"  : realColumnName,
			"balias": columnName
		];
    }

    /***
	 * Resolves an expression in a single call argument
	 **/
    protected final function _getCallArgument($argument ) {
		if ( argument["type"] == PHQL_T_STARALL ) {
			return ["type": "all"];
		}
		return $this->_getExpression(argument);
    }

    /***
	 * Resolves an expression in a single call argument
	 **/
    protected final function _getCaseExpression($expr ) {

		$whenClauses = [];
		for ( whenExpr in expr["right"] ) {
			if ( isset whenExpr["right"] ) {
				$whenClauses[] = [
					"type": "when",
					"expr": $this->_getExpression(whenExpr["left"]),
					"then": $this->_getExpression(whenExpr["right"])
				];
			} else {
				$whenClauses[] = [
					"type": "else",
					"expr": $this->_getExpression(whenExpr["left"])
				];
			}
		}

		return [
			"type"        : "case",
			"expr"        : $this->_getExpression(expr["left"]),
			"when-clauses": whenClauses
		];
    }

    /***
	 * Resolves an expression in a single call argument
	 **/
    protected final function _getFunctionCall($expr ) {

		if ( fetch arguments, expr["arguments"] ) {

			if ( isset expr["distinct"] ) {
				$distinct = 1;
			} else {
				$distinct = 0;
			}

			if ( isset($arguments[0]) ) {
				// There are more than one argument
				$functionArgs = [];
				foreach ( $arguments as $argument ) {
					$functionArgs[] = $this->_getCallArgument(argument);
				}
			} else {
				// There is only one argument
				$functionArgs = [this->_getCallArgument(arguments)];
			}

			if ( distinct ) {
				return [
					"type"     : "functionCall",
					"name"     : expr["name"],
					"arguments": functionArgs,
					"distinct" : distinct
				];
			} else {
				return [
					"type"     : "functionCall",
					"name"     : expr["name"],
					"arguments": functionArgs
				];
			}
		}

		return [
			"type": "functionCall",
			"name": expr["name"]
		];
    }

    /***
	 * Resolves an expression from its intermediate code into a string
	 *
	 * @param array expr
	 * @param boolean quoting
	 * @return string
	 **/
    protected final function _getExpression($expr , $quoting  = true ) {
			exprReturn, tempNotQuoting, value, escapedValue, exprValue,
			valueParts, name, bindType, bind;

		if ( fetch exprType, expr["type"] ) {

			$tempNotQuoting = true;

			if ( exprType != PHQL_T_CASE ) {

				/**
				 * Resolving the left part of the expression if ( any
				 */
				if ( fetch exprLeft, expr["left"] ) {
					$left = $this->_getExpression(exprLeft, tempNotQuoting);
				}

				/**
				 * Resolving the right part of the expression if ( any
				 */
				if ( fetch exprRight, expr["right"] ) {
					$right = $this->_getExpression(exprRight, tempNotQuoting);
				}
			}

			/**
			 * Every node in the AST has a unique integer type
			 */
			switch exprType {

				case PHQL_T_LESS:
					$exprReturn = ["type": "binary-op", "op": "<", "left": left, "right": right];
					break;

				case PHQL_T_EQUALS:
					$exprReturn = ["type": "binary-op", "op": "=", "left": left, "right": right];
					break;

				case PHQL_T_GREATER:
					$exprReturn = ["type": "binary-op", "op": ">", "left": left, "right": right];
					break;

				case PHQL_T_NOTEQUALS:
					$exprReturn = ["type": "binary-op", "op": "<>", "left": left, "right": right];
					break;

				case PHQL_T_LESSEQUAL:
					$exprReturn = ["type": "binary-op", "op": "<=", "left": left, "right": right];
					break;

				case PHQL_T_GREATEREQUAL:
					$exprReturn = ["type": "binary-op", "op": ">=", "left": left, "right": right];
					break;

				case PHQL_T_AND:
					$exprReturn = ["type": "binary-op", "op": "AND", "left": left, "right": right];
					break;

				case PHQL_T_OR:
					$exprReturn = ["type": "binary-op", "op": "OR", "left": left, "right": right];
					break;

				case PHQL_T_QUALIFIED:
					$exprReturn = $this->_getQualif (ied(expr);
					break;

				case PHQL_T_ADD:
					$exprReturn = ["type": "binary-op", "op": "+", "left": left, "right": right];
					break;

				case PHQL_T_SUB:
					$exprReturn = ["type": "binary-op", "op": "-", "left": left, "right": right];
					break;

				case PHQL_T_MUL:
					$exprReturn = ["type": "binary-op", "op": "*", "left": left, "right": right];
					break;

				case PHQL_T_DIV:
					$exprReturn = ["type": "binary-op", "op": "/", "left": left, "right": right];
					break;

				case PHQL_T_MOD:
					$exprReturn = ["type": "binary-op", "op": "%", "left": left, "right": right];
					break;

				case PHQL_T_BITWISE_AND:
					$exprReturn = ["type": "binary-op", "op": "&", "left": left, "right": right];
					break;

				case PHQL_T_BITWISE_OR:
					$exprReturn = ["type": "binary-op", "op": "|", "left": left, "right": right];
					break;

				case PHQL_T_ENCLOSED:
				case PHQL_T_SUBQUERY:
					$exprReturn = ["type": "parentheses", "left": left];
					break;

				case PHQL_T_MINUS:
					$exprReturn = ["type": "unary-op", "op": "-", "right": right];
					break;

				case PHQL_T_INTEGER:
				case PHQL_T_DOUBLE:
				case PHQL_T_HINTEGER:
					$exprReturn = ["type": "literal", "value": expr["value"]];
					break;

				case PHQL_T_TRUE:
					$exprReturn = ["type": "literal", "value": "TRUE"];
					break;

				case PHQL_T_FALSE:
					$exprReturn = ["type": "literal", "value": "FALSE"];
					break;

				case PHQL_T_STRING:
					$value = expr["value"];
					if ( quoting === true ) {
						/**
						 * Check if ( static literals have single quotes and escape them
						 */
						if ( memstr(value, "'") ) {
							$escapedValue = phalcon_orm_singlequotes(value);
						} else {
							$escapedValue = value;
						}
						$exprValue = "'" . escapedValue . "'";
					} else {
						$exprValue = value;
					}
					$exprReturn = ["type": "literal", "value": exprValue];
					break;

				case PHQL_T_NPLACEHOLDER:
					$exprReturn = ["type": "placeholder", "value": str_replace("?", ":", expr["value"])];
					break;

				case PHQL_T_SPLACEHOLDER:
					$exprReturn = ["type": "placeholder", "value": ":" . expr["value"]];
					break;

				case PHQL_T_BPLACEHOLDER:
					$value = expr["value"];
					if ( memstr(value, ":") ) {

						$valueParts = explode(":", value),
							name = valueParts[0],
							bindType = valueParts[1];

						switch bindType {

							case "str":
								$this->_bindTypes[name] = Column::BIND_PARAM_STR;
								$exprReturn = ["type": "placeholder", "value": ":" . name];
								break;

							case "int":
								$this->_bindTypes[name] = Column::BIND_PARAM_INT;
								$exprReturn = ["type": "placeholder", "value": ":" . name];
								break;

							case "double":
								$this->_bindTypes[name] = Column::BIND_PARAM_DECIMAL;
								$exprReturn = ["type": "placeholder", "value": ":" . name];
								break;

							case "bool":
								$this->_bindTypes[name] = Column::BIND_PARAM_BOOL;
								$exprReturn = ["type": "placeholder", "value": ":" . name];
								break;

							case "blob":
								$this->_bindTypes[name] = Column::BIND_PARAM_BLOB;
								$exprReturn = ["type": "placeholder", "value": ":" . name];
								break;

							case "null":
								$this->_bindTypes[name] = Column::BIND_PARAM_NULL;
								$exprReturn = ["type": "placeholder", "value": ":" . name];
								break;

							case "array":
							case "array-str":
							case "array-int":

								if ( !fetch bind, $this->_bindParams[name] ) {
									throw new Exception("Bind value is required for ( array type placeholder: " . name);
								}

								if ( gettype($bind) != "array" ) {
									throw new Exception("Bind type requires an array in placeholder: " . name);
								}

								if ( count(bind) < 1 ) {
									throw new Exception("At least one value must be bound in placeholder: " . name);
								}

								$exprReturn = [
									"type": "placeholder",
									"value": ":" . name,
									"rawValue": name,
									"times": count(bind)
								];
								break;

							default:
								throw new Exception("Unknown bind type: " . bindType);
						}

					} else {
						$exprReturn = ["type": "placeholder", "value": ":" . value];
					}
					break;

				case PHQL_T_NULL:
					$exprReturn = ["type": "literal", "value": "NULL"];
					break;

				case PHQL_T_LIKE:
					$exprReturn = ["type": "binary-op", "op": "LIKE", "left": left, "right": right];
					break;

				case PHQL_T_NLIKE:
					$exprReturn = ["type": "binary-op", "op": "NOT LIKE", "left": left, "right": right];
					break;

				case PHQL_T_ILIKE:
					$exprReturn = ["type": "binary-op", "op": "ILIKE", "left": left, "right": right];
					break;

				case PHQL_T_NILIKE:
					$exprReturn = ["type": "binary-op", "op": "NOT ILIKE", "left": left, "right": right];
					break;

				case PHQL_T_NOT:
					$exprReturn = ["type": "unary-op", "op": "NOT ", "right": right];
					break;

				case PHQL_T_ISNULL:
					$exprReturn = ["type": "unary-op", "op": " IS NULL", "left": left];
					break;

				case PHQL_T_ISNOTNULL:
					$exprReturn = ["type": "unary-op", "op": " IS NOT NULL", "left": left];
					break;

				case PHQL_T_IN:
					$exprReturn = ["type": "binary-op", "op": "IN", "left": left, "right": right];
					break;

				case PHQL_T_NOTIN:
					$exprReturn = ["type": "binary-op", "op": "NOT IN", "left": left, "right": right];
					break;

				case PHQL_T_EXISTS:
					$exprReturn = ["type": "unary-op", "op": "EXISTS", "right": right];
					break;

				case PHQL_T_DISTINCT:
					$exprReturn = ["type": "unary-op", "op": "DISTINCT ", "right": right];
					break;

				case PHQL_T_BETWEEN:
					$exprReturn = ["type": "binary-op", "op": "BETWEEN", "left": left, "right": right];
					break;

				case PHQL_T_AGAINST:
					$exprReturn = ["type": "binary-op", "op": "AGAINST", "left": left, "right": right];
					break;

				case PHQL_T_CAST:
					$exprReturn = ["type": "cast", "left": left, "right": right];
					break;

				case PHQL_T_CONVERT:
					$exprReturn = ["type": "convert", "left": left, "right": right];
					break;

				case PHQL_T_RAW_QUALIFIED:
					$exprReturn = ["type": "literal", "value": expr["name"]];
					break;

				case PHQL_T_FCALL:
					$exprReturn = $this->_getFunctionCall(expr);
					break;

				case PHQL_T_CASE:
					$exprReturn = $this->_getCaseExpression(expr);
					break;

				case PHQL_T_SELECT:
					$exprReturn = ["type": "select", "value": $this->_prepareSelect(expr, true)];
					break;

				default:
					throw new Exception("Unknown expression type " . exprType);
			}

			return exprReturn;
		}

		/**
		 * It's a qualif (ied column
		 */
		if ( isset expr["domain"] ) {
			return $this->_getQualif (ied(expr);
		}

		/**
		 * If the expression doesn't have a type it's a list of nodes
		 */
		if ( isset($expr[0]) ) {
			$listItems = [];
			foreach ( $expr as $exprListItem ) {
				$listItems[] = $this->_getExpression(exprListItem);
			}
			return ["type": "list", listItems];
		}

		throw new Exception("Unknown expression");
    }

    /***
	 * Resolves a column from its intermediate representation into an array used to determine
	 * if the resultset produced is simple or complex
	 **/
    protected final function _getSelectColumn($column ) {
			columnDomain, sqlColumnAlias, preparedAlias, sqlExprColumn,
			sqlAliasesModels, sqlColumn, columnData, balias, eager;

		if ( !fetch columnType, column["type"] ) {
			throw new Exception("Corrupted SELECT AST");
		}

		$sqlColumns = [];

		/**
		 * Check if ( column is eager loaded
		 */

		/**
		 * Check for ( select * (all)
		 */
		if ( columnType == PHQL_T_STARALL ) {
			foreach ( modelName, $this->_models as $source ) {

				$sqlColumn = [
					"type"  : "object",
					"model" : modelName,
					"column": source,
					"balias": lcfirst(modelName)
				];

				if ( eager !== null ) {
					$sqlColumn["eager"] = eager,
						sqlColumn["eagerType"] = column["eagerType"];
				}

				$sqlColumns[] = sqlColumn;
			}
			return sqlColumns;
		}

		if ( !isset column["column"] ) {
			throw new Exception("Corrupted SELECT AST");
		}

		/**
		 * Check if ( selected column is qualif (ied.*, ex: robots.*
		 */
		if ( columnType == PHQL_T_DOMAINALL ) {

			$sqlAliases = $this->_sqlAliases;

			/**
			 * We only allow the alias.*
			 */
			$columnDomain = column["column"];

			if ( !fetch source, sqlAliases[columnDomain] ) {
				throw new Exception("Unknown model or alias '" . columnDomain . "' (2), when preparing: " . $this->_phql);
			}

			/**
			 * Get the SQL alias if ( any
			 */
			$sqlColumnAlias = source;


			/**
			 * Get the real source name
			 */
			$sqlAliasesModels = $this->_sqlAliasesModels,
				modelName = sqlAliasesModels[columnDomain];

			if ( gettype($preparedAlias) != "string" ) {

				/**
				 * If the best alias is the model name, we lowercase the first letter
				 */
				if ( columnDomain == modelName ) {
					$preparedAlias = lcfirst(modelName);
				} else {
					$preparedAlias = columnDomain;
				}
			}

			/**
			 * Each item is a complex type returning a complete object
			 */
			$sqlColumn = [
				"type":  "object",
				"model":  modelName,
				"column": sqlColumnAlias,
				"balias": preparedAlias
			];

			if ( eager !== null ) {
				$sqlColumn["eager"] = eager,
					sqlColumn["eagerType"] = column["eagerType"];
			}

			$sqlColumns[] = sqlColumn;

			return sqlColumns;
		}

		/**
		 * Check for ( columns qualif (ied and not qualif (ied
		 */
		if ( columnType == PHQL_T_EXPR ) {

			/**
			 * The sql_column is a scalar type returning a simple string
			 */
			$sqlColumn = ["type": "scalar"],
				columnData = column["column"],
				sqlExprColumn = $this->_getExpression(columnData);

			/**
			 * Create balias and sqlAlias
			 */
			if ( fetch balias, sqlExprColumn["balias"] ) {
				$sqlColumn["balias"] = balias,
					sqlColumn["sqlAlias"] = balias;
			}

			if ( eager !== null ) {
				$sqlColumn["eager"] = eager,
					sqlColumn["eagerType"] = column["eagerType"];
			}

			$sqlColumn["column"] = sqlExprColumn,
				sqlColumns[] = sqlColumn;

			return sqlColumns;
		}

		throw new Exception("Unknown type of column " . columnType);
    }

    /***
	 * Resolves a table in a SELECT statement checking if the model exists
	 *
	 * @param \Phalcon\Mvc\Model\ManagerInterface manager
	 * @param array qualifiedName
	 * @return string
	 **/
    protected final function _getTable($manager , $qualifiedName ) {

		if ( !fetch modelName, qualif (iedName["name"] ) {
			throw new Exception("Corrupted SELECT AST");
		}

		$model = manager->load(modelName),
			source = model->getSource(),
			schema = model->getSchema();

		if ( schema ) {
			return [schema, source];
		}

		return source;
    }

    /***
	 * Resolves a JOIN clause checking if the associated models exist
	 **/
    protected final function _getJoin($manager , $join ) {
			source, model, schema;

		if ( fetch qualif (ied, join["qualif (ied"] ) {

			if ( qualif (ied["type"] == PHQL_T_QUALIFIED ) {

				$modelName = qualif (ied["name"];

				if ( memstr(modelName, ":") ) {
					$nsAlias = explode(":", modelName);
					$realModelName = manager->getNamespaceAlias(nsAlias[0]) . "\\" . nsAlias[1];
				} else {
					$realModelName = modelName;
				}

				$model = manager->load(realModelName, true),
					source = model->getSource(),
					schema = model->getSchema();

				return [
					"schema"   : schema,
					"source"   : source,
					"modelName": realModelName,
					"model"    : model
				];
			}
		}

		throw new Exception("Corrupted SELECT AST");
    }

    /***
	 * Resolves a JOIN type
	 *
	 * @param array join
	 * @return string
	 **/
    protected final function _getJoinType($join ) {

		if ( !fetch type, join["type"] ) {
			throw new Exception("Corrupted SELECT AST");
		}

		switch type {

			case PHQL_T_INNERJOIN:
				return "INNER";

			case PHQL_T_LEFTJOIN:
				return "LEFT";

			case PHQL_T_RIGHTJOIN:
				return "RIGHT";

			case PHQL_T_CROSSJOIN:
				return "CROSS";

			case PHQL_T_FULLJOIN:
				return "FULL OUTER";
		}

		throw new Exception("Unknown join type " . type . ", when preparing: " . $this->_phql);
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
			sqlJoinPartialConditions, position, field, referencedField;

		/**
		 * Local fields in the 'from' relation
		 */
		$fields = relation->getFields();

		/**
		 * Referenced fields in the joined relation
		 */
		$referencedFields = relation->getReferencedFields();

		if ( gettype($fields) != "array" ) {

			/**
			 * Create the left part of the expression
			 * Create a binary operation for ( the join conditions
			 * Create the right part of the expression
			 */
			$sqlJoinConditions = [
				[
					"type"     : "binary-op",
					"op"       : "=",
					"left"     : $this->_getQualif (ied([
						"type"   : PHQL_T_QUALIFIED,
						"domain" : modelAlias,
						"name"   : fields
					]),
					"right"    : $this->_getQualif (ied([
						"type"   : "qualif (ied",
						"domain" : joinAlias,
						"name"   : referencedFields
					])
				]
			];

		} else {

			/**
			 * Resolve the compound operation
			 */
			$sqlJoinPartialConditions = [];
			foreach ( position, $fields as $field ) {

				/**
				 * Get the referenced field in the same position
				 */
				if ( !fetch referencedField, referencedFields[position] ) {
					throw new Exception(
						"The number of fields must be equal to the number of referenced fields in join " . modelAlias . "-" . joinAlias . ", when preparing: " . $this->_phql
					);
				}

				/**
				 * Create the left part of the expression
				 * Create the right part of the expression
				 * Create a binary operation for ( the join conditions
				 */
				$sqlJoinPartialConditions[] = [
					"type" : "binary-op",
					"op"   : "=",
					"left" : $this->_getQualif (ied([
						"type"   : PHQL_T_QUALIFIED,
						"domain" : modelAlias,
						"name"   : field
					]),
					"right"      : $this->_getQualif (ied([
						"type"   : "qualif (ied",
						"domain" : joinAlias,
						"name"   : referencedField
					])
				];
			}

		}

		/**
		 * A single join
		 */
		return [
			"type"       : joinType,
			"source"     : joinSource,
			"conditions" : sqlJoinConditions
		];
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
			intermediateModelName, intermediateModel, intermediateSource,
			intermediateSchema, intermediateFields, intermediateReferencedFields,
			referencedModelName, manager, field, position, intermediateField,
			sqlEqualsJoinCondition;

		$sqlJoins = [];

		/**
		 * Local fields in the 'from' relation
		 */
		$fields = relation->getFields();

		/**
		 * Referenced fields in the joined relation
		 */
		$referencedFields = relation->getReferencedFields();

		/**
		 * Intermediate model
		 */
		$intermediateModelName = relation->getIntermediateModel();

		$manager = $this->_manager;

		/**
		 * Get the intermediate model instance
		 */
		$intermediateModel = manager->load(intermediateModelName);

		/**
		 * Source of the related model
		 */
		$intermediateSource = intermediateModel->getSource();

		/**
		 * Schema of the related model
		 */
		$intermediateSchema = intermediateModel->getSchema();

		//intermediateFullSource = array(intermediateSchema, intermediateSource);

		/**
		 * Update the internal sqlAliases to set up the intermediate model
		 */
		$this->_sqlAliases[intermediateModelName] = intermediateSource;

		/**
		 * Update the internal _sqlAliasesModelsInstances to rename columns if ( necessary
		 */
		$this->_sqlAliasesModelsInstances[intermediateModelName] = intermediateModel;

		/**
		 * Fields that join the 'from' model with the 'intermediate' model
		 */
		$intermediateFields = relation->getIntermediateFields();

		/**
		 * Fields that join the 'intermediate' model with the intermediate model
		 */
		$intermediateReferencedFields = relation->getIntermediateReferencedFields();

		/**
		 * Intermediate model
		 */
		$referencedModelName = relation->getReferencedModel();

		if ( gettype($fields) == "array" ) {

			foreach ( field, $fields as $position ) {

				if ( !isset($referencedFields[position]) ) {
					throw new Exception(
						"The number of fields must be equal to the number of referenced fields in join " . modelAlias . "-" . joinAlias . ", when preparing: " . $this->_phql
					);
				}

				/**
				 * Get the referenced field in the same position
				 */
				$intermediateField = intermediateFields[position];

				/**
				 * Create a binary operation for ( the join conditions
				 */
				$sqlEqualsJoinCondition = [
					"type" : "binary-op",
					"op" : "=",
					"left" : $this->_getQualif (ied([
						"type" : PHQL_T_QUALIFIED,
						"domain" : modelAlias,
						"name" : field
					]),
					"right" : $this->_getQualif (ied([
						"type" : "qualif (ied",
						"domain" : joinAlias,
						"name" : referencedFields
					])
				];

				//$sqlJoinPartialConditions[] = sqlEqualsJoinCondition;
			}

		} else {

			/**
			 * Create the left part of the expression
			 * Create the right part of the expression
			 * Create a binary operation for ( the join conditions
			 * A single join
			 */
			$sqlJoins = [

				[
					"type" : joinType,
					"source" : intermediateSource,
					"conditions" : [[
						"type" : "binary-op",
						"op" : "=",
						"left" : $this->_getQualif (ied([
							"type" : PHQL_T_QUALIFIED,
							"domain" : modelAlias,
							"name" : fields
						]),
						"right" : $this->_getQualif (ied([
							"type" : "qualif (ied",
							"domain" : intermediateModelName,
							"name" : intermediateFields
						])
					]]
				],

				/**
				 * Create the left part of the expression
				 * Create the right part of the expression
				 * Create a binary operation for ( the join conditions
				 * A single join
				 */
				[
					"type" : joinType,
					"source" : joinSource,
					"conditions" : [[
						"type" : "binary-op",
						"op" : "=",
						"left" : $this->_getQualif (ied([
							"type" : PHQL_T_QUALIFIED,
							"domain" : intermediateModelName,
							"name" : intermediateReferencedFields
						]),
						"right" : $this->_getQualif (ied([
							"type" : "qualif (ied",
							"domain" : referencedModelName,
							"name" : referencedFields
						])
					]]
				]
			];
		}

		return sqlJoins;
    }

    /***
	 * Processes the JOINs in the query returning an internal representation for the database dialect
	 *
	 * @param array select
	 * @return array
	 **/
    protected final function _getJoins($select ) {
			modelsInstances, fromModels, sqlJoins, joinModels, joinSources, joinTypes, joinPreCondition,
			joinPrepared, manager, selectJoins, joinItem, joins, joinData, schema, source, model,
			realModelName, completeSource, joinType, aliasExpr, alias, joinAliasName, joinExpr,
			fromModelName, joinAlias, joinModel, joinSource, preCondition, modelNameAlias,
			relation, relations, modelAlias, sqlJoin, sqlJoinItem, selectTables, tables, tableItem;

		$models = $this->_models,
			sqlAliases = $this->_sqlAliases,
			sqlAliasesModels = $this->_sqlAliasesModels,
			sqlModelsAliases = $this->_sqlModelsAliases,
			sqlAliasesModelsInstances = $this->_sqlAliasesModelsInstances,
			modelsInstances = $this->_modelsInstances,
			fromModels = models;

		$sqlJoins = [],
			joinModels = [],
			joinSources = [],
			joinTypes = [],
			joinPreCondition = [],
			joinPrepared = [];

		$manager = $this->_manager;

		$tables = select["tables"];
		if ( !isset($tables[0]) ) {
			$selectTables = [tables];
		} else {
			$selectTables = tables;
		}

		$joins = select["joins"];
		if ( !isset($joins[0]) ) {
			$selectJoins = [joins];
		} else {
			$selectJoins = joins;
		}

		foreach ( $selectJoins as $joinItem ) {

			/**
			 * Check join alias
			 */
			$joinData = $this->_getJoin(manager, joinItem),
				source = joinData["source"],
				schema = joinData["schema"],
				model = joinData["model"],
				realModelName = joinData["modelName"],
				completeSource = [source, schema];

			/**
			 * Check join alias
			 */
			$joinType = $this->_getJoinType(joinItem);

			/**
			 * Process join alias
			 */
			if ( fetch aliasExpr, joinItem["alias"] ) {

				$alias = aliasExpr["name"];

				/**
				 * Check if ( alias is unique
				 */
				if ( isset($joinModels[alias]) ) {
					throw new Exception(
						"Cannot use '" . alias . "' as join alias because it was already used, when preparing: " . $this->_phql
					);
				}

				/**
				 * Add the alias to the source
				 */
				$completeSource[] = alias;

				/**
				 * Set the join type
				 */
				$joinTypes[alias] = joinType;

				/**
				 * Update alias: alias
				 */
				$sqlAliases[alias] = alias;

				/**
				 * Update model: alias
				 */
				$joinModels[alias] = realModelName;

				/**
				 * Update model: alias
				 */
				$sqlModelsAliases[realModelName] = alias;

				/**
				 * Update model: model
				 */
				$sqlAliasesModels[alias] = realModelName;

				/**
				 * Update alias: model
				 */
				$sqlAliasesModelsInstances[alias] = model;

				/**
				 * Update model: alias
				 */
				$models[realModelName] = alias;

				/**
				 * Complete source related to a model
				 */
				$joinSources[alias] = completeSource;

				/**
				 * Complete source related to a model
				 */
				$joinPrepared[alias] = joinItem;

			} else {

				/**
				 * Check if ( alias is unique
				 */
				if ( isset($joinModels[realModelName]) ) {
					throw new Exception(
						"Cannot use '" . realModelName . "' as join alias because it was already used, when preparing: " . $this->_phql
					);
				}

				/**
				 * Set the join type
				 */
				$joinTypes[realModelName] = joinType;

				/**
				 * Update model: source
				 */
				$sqlAliases[realModelName] = source;

				/**
				 * Update model: source
				 */
				$joinModels[realModelName] = source;

				/**
				 * Update model: model
				 */
				$sqlModelsAliases[realModelName] = realModelName;

				/**
				 * Update model: model
				 */
				$sqlAliasesModels[realModelName] = realModelName;

				/**
				 * Update model: model instance
				 */
				$sqlAliasesModelsInstances[realModelName] = model;

				/**
				 * Update model: source
				 */
				$models[realModelName] = source;

				/**
				 * Complete source related to a model
				 */
				$joinSources[realModelName] = completeSource;

				/**
				 * Complete source related to a model
				 */
				$joinPrepared[realModelName] = joinItem;
			}

			$modelsInstances[realModelName] = model;
		}

		/**
		 * Update temporary properties
		 */
		$this->_models = models,
			this->_sqlAliases = sqlAliases,
			this->_sqlAliasesModels = sqlAliasesModels,
			this->_sqlModelsAliases = sqlModelsAliases,
			this->_sqlAliasesModelsInstances = sqlAliasesModelsInstances,
			this->_modelsInstances = modelsInstances;

		foreach ( joinAliasName, $joinPrepared as $joinItem ) {

			/**
			 * Check for ( predefined conditions
			 */
			if ( fetch joinExpr, joinItem["conditions"] ) {
				$joinPreCondition[joinAliasName] = $this->_getExpression(joinExpr);
			}
		}

		/**
		 * Skip all implicit joins if ( the option is not enabled
		 */
		if ( !this->_enableImplicitJoins ) {
			foreach ( joinAliasName, $joinPrepared as $_ ) {
				$joinType = joinTypes[joinAliasName],
					joinSource = joinSources[joinAliasName],
					preCondition = joinPreCondition[joinAliasName],
					sqlJoins[] = [
						"type": joinType,
						"source": joinSource,
						"conditions": [preCondition]
					];
			}
			return sqlJoins;
		}

		/**
		 * Build the list of tables used in the SELECT clause
		 */
		$fromModels = [];
		foreach ( $selectTables as $tableItem ) {
			$fromModels[tableItem["qualif (iedName"]["name"]]	= true;
		}

		/**
		 * Create join relationships dynamically
		 */
		foreach ( fromModelName, $fromModels as $_ ) {

			foreach ( joinAlias, $joinModels as $joinModel ) {

				/**
				 * Real source name for ( joined model
				 */
				$joinSource = joinSources[joinAlias];

				/**
				 * Join type is: LEFT, RIGHT, INNER, etc
				 */
				$joinType = joinTypes[joinAlias];

				/**
				 * Check if ( the model already have pre-defined conditions
				 */
				if ( !fetch preCondition, joinPreCondition[joinAlias] ) {

					/**
					 * Get the model name from its source
					 */
					$modelNameAlias = sqlAliasesModels[joinAlias];

					/**
					 * Check if ( the joined model is an alias
					 */
					$relation = manager->getRelationByAlias(fromModelName, modelNameAlias);
					if ( relation === false ) {

						/**
						 * Check for ( relations between models
						 */
						$relations = manager->getRelationsBetween(fromModelName, modelNameAlias);
						if ( gettype($relations) == "array" ) {

							/**
							 * More than one relation must throw an exception
							 */
							if ( count(relations) != 1 ) {
								throw new Exception(
									"There is more than one relation between models '" . fromModelName . "' and '" . joinModel . "', the join must be done using an alias, when preparing: " . $this->_phql
								);
							}

							/**
							 * Get the first relationship
							 */
							$relation = relations[0];
						}
					}

					/*
					 * Valid relations are objects
					 */
					if ( gettype($relation) == "object" ) {

						/**
						 * Get the related model alias of the left part
						 */
						$modelAlias = sqlModelsAliases[fromModelName];

						/**
						 * Generate the conditions based on the type of join
						 */
						if ( !relation->isThrough() ) {
							$sqlJoin = $this->_getSingleJoin(joinType, joinSource, modelAlias, joinAlias, relation);
						} else {
							$sqlJoin = $this->_getMultiJoin(joinType, joinSource, modelAlias, joinAlias, relation);
						}

						/**
						 * Append or merge joins
						 */
						if ( isset($sqlJoin[0]) ) {
							foreach ( $sqlJoin as $sqlJoinItem ) {
								$sqlJoins[] = sqlJoinItem;
							}
						} else {
							$sqlJoins[] = sqlJoin;
						}

					} else {

						/**
						 * Join without conditions because no relation has been found between the models
						 */
						$sqlJoins[] = [
							"type": joinType,
							"source": joinSource,
							"conditions": []
						];
					}
				} else {

					/**
					 * Get the conditions established by the developer
					 * Join with conditions established by the developer
					 */
					$sqlJoins[] = [
						"type": joinType,
						"source": joinSource,
						"conditions": [preCondition]
					];
				}
			}
		}

		return sqlJoins;
    }

    /***
	 * Returns a processed order clause for a SELECT statement
	 *
	 * @param array|string $order
	 * @return array
	 **/
    protected final function _getOrderClause($order ) {
			orderSort, orderPartSort;

		if ( !isset($order[0]) ) {
			$orderColumns = [order];
		} else {
			$orderColumns = order;
		}

		$orderParts = [];
		foreach ( $orderColumns as $orderItem ) {

			$orderPartExpr = $this->_getExpression(orderItem["column"]);

			/**
			 * Check if ( the order has a predefined ordering mode
			 */
			if ( fetch orderSort, orderItem["sort"] ) {
				if ( orderSort == PHQL_T_ASC ) {
					$orderPartSort = [orderPartExpr, "ASC"];
				} else {
					$orderPartSort = [orderPartExpr, "DESC"];
				}
			} else {
				$orderPartSort = [orderPartExpr];
			}

			$orderParts[] = orderPartSort;
		}

		return orderParts;
    }

    /***
	 * Returns a processed group clause for a SELECT statement
	 **/
    protected final function _getGroupClause($group ) {

		if ( isset($group[0]) ) {
			/**
			 * The select is grouped by several columns
			 */
			$groupParts = [];
			foreach ( $group as $groupItem ) {
				$groupParts[] = $this->_getExpression(groupItem);
			}
		} else {
			$groupParts = [this->_getExpression(group)];
		}
		return groupParts;
    }

    /***
	 * Returns a processed limit clause for a SELECT statement
	 **/
    protected final function _getLimitClause($limitClause ) {
		array limit = [];

		if ( fetch number, limitClause["number"] ) {
			$limit["number"] = $this->_getExpression(number);
		}

		if ( fetch offset, limitClause["offset"] ) {
			$limit["offset"] = $this->_getExpression(offset);
		}

		return limit;
    }

    /***
	 * Analyzes a SELECT intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareSelect($ast  = null , $merge  = null ) {
		int position;
			sqlAliasesModels, sqlModelsAliases, sqlAliasesModelsInstances,
			models, modelsInstances, selectedModels, manager, metaData,
			selectedModel, qualif (iedName, modelName, nsAlias, realModelName, model,
			schema, source, completeSource, alias, joins, sqlJoins, selectColumns,
			sqlColumnAliases, column, sqlColumn, sqlSelect, distinct, having, where,
			groupBy, order, limit, tempModels, tempModelsInstances, tempSqlAliases,
			tempSqlModelsAliases, tempSqlAliasesModelsInstances, tempSqlAliasesModels,
			with, withs, withItem, automaticJoins, number, relation, joinAlias,
			relationModel, bestAlias, eagerType;

		if ( empty ast ) {
			$ast = $this->_ast;
		}

		if ( gettype($merge) == "null" ) {
			$merge = false;
		}

		if ( !fetch select, ast["select"] ) {
			$select = ast;
		}

		if ( !fetch tables, select["tables"] ) {
			throw new Exception("Corrupted SELECT AST");
		}

		if ( !fetch columns, select["columns"] ) {
			throw new Exception("Corrupted SELECT AST");
		}

		/**
		 * sqlModels is an array of the models to be used in the query
		 */
		$sqlModels = [];

		/**
		 * sqlTables is an array of the mapped models sources to be used in the query
		 */
		$sqlTables = [];

		/**
		 * sqlColumns is an array of every column expression
		 */
		$sqlColumns = [];

		/**
		 * sqlAliases is a map from aliases to mapped sources
		 */
		$sqlAliases = [];

		/**
		 * sqlAliasesModels is a map from aliases to model names
		 */
		$sqlAliasesModels = [];

		/**
		 * sqlAliasesModels is a map from model names to aliases
		 */
		$sqlModelsAliases = [];

		/**
		 * sqlAliasesModelsInstances is a map from aliases to model instances
		 */
		$sqlAliasesModelsInstances = [];

		/**
		 * Models infor (mation
		 */
		$models = [],
			modelsInstances = [];

		// Convert selected models in an array
		if ( !isset($tables[0]) ) {
			$selectedModels = [tables];
		} else {
			$selectedModels = tables;
		}

		// Convert selected columns in an array
		if ( !isset($columns[0]) ) {
			$selectColumns = [columns];
		} else {
			$selectColumns = columns;
		}

		$manager = $this->_manager,
			metaData = $this->_metaData;

		if ( gettype($manager) != "object" ) {
			throw new Exception("A models-manager is required to execute the query");
		}

		if ( gettype($metaData) != "object" ) {
			throw new Exception("A meta-data is required to execute the query");
		}

		// Process selected models
		$number = 0,
			automaticJoins = [];

		foreach ( $selectedModels as $selectedModel ) {

			$qualif (iedName = selectedModel["qualif (iedName"],
				modelName = qualif (iedName["name"];

			// Check if ( the table has a namespace alias
			if ( memstr(modelName, ":") ) {
				$nsAlias = explode(":", modelName);
				$realModelName = manager->getNamespaceAlias(nsAlias[0]) . "\\" . nsAlias[1];
			} else {
				$realModelName = modelName;
			}

			// Load a model instance from the models manager
			$model = manager->load(realModelName, true);

			// Define a complete schema/source
			$schema = model->getSchema(),
				source = model->getSource();

			// Obtain the real source including the schema
			if ( schema ) {
				$completeSource = [source, schema];
			} else {
				$completeSource = source;
			}

			// If an alias is defined foreach ( a model then the model cannot be $the as $referenced column list
			if ( fetch alias, selectedModel["alias"] ) {

				// Check if ( the alias was used befor (e
				if ( isset($sqlAliases[alias]) ) {
					throw new Exception("Alias '" . alias . "' is used more than once, when preparing: " . $this->_phql);
				}

				$sqlAliases[alias] = alias,
					sqlAliasesModels[alias] = realModelName,
					sqlModelsAliases[realModelName] = alias,
					sqlAliasesModelsInstances[alias] = model;

				/**
				 * Append or convert complete source to an array
				 */
				if ( gettype($completeSource) == "array" ) {
					$completeSource[] = alias;
				} else {
					$completeSource = [source, null, alias];
				}
				$models[realModelName] = alias;

			} else {
				$alias = source,
					sqlAliases[realModelName] = source,
					sqlAliasesModels[realModelName] = realModelName,
					sqlModelsAliases[realModelName] = realModelName,
					sqlAliasesModelsInstances[realModelName] = model,
					models[realModelName] = source;
			}

			// Eager load any specif (ied relationship(s)
			if ( fetch with, selectedModel["with"] ) {

				if ( !isset($with[0]) ) {
					$withs = [with];
				} else {
					$withs = with;
				}

				// Simulate the definition of inner joins
				foreach ( $withs as $withItem ) {

					$joinAlias = "AA" . number,
						relationModel = withItem["name"],
						relation = manager->getRelationByAlias(realModelName, relationModel);

					if ( gettype($relation) == "object" ) {
						$bestAlias = relation->getOption("alias"),
							relationModel = relation->getReferencedModel(),
							eagerType = relation->getType();
					} else {
						$relation = manager->getRelationsBetween(realModelName, relationModel);
						if ( gettype($relation) == "object" ) {
							$bestAlias = relation->getOption("alias"),
								relationModel = relation->getReferencedModel(),
								eagerType = relation->getType();
						} else {
							throw new Exception(
								"Can't find a relationship between '" . realModelName . "' and '" . relationModel . "' when preparing: " . $this->_phql
							);
						}
					}

					$selectColumns[] = [
						"type":   PHQL_T_DOMAINALL,
						"column": joinAlias,
						"eager":  alias,
						"eagerType": eagerType,
						"balias": bestAlias
					];

					$automaticJoins[] = [
						"type": PHQL_T_INNERJOIN,
						"qualif (ied": [
							"type": PHQL_T_QUALIFIED,
							"name": relationModel
						],
						"alias": [
							"type": PHQL_T_QUALIFIED,
							"name": joinAlias
						]
					];

					$number++;
				}
			}

			$sqlModels[] = realModelName,
				sqlTables[] = completeSource,
				modelsInstances[realModelName] = model;
		}

		// Assign Models/Tables infor (mation
		if ( !merge ) {
			$this->_models = models,
				this->_modelsInstances = modelsInstances,
				this->_sqlAliases = sqlAliases,
				this->_sqlAliasesModels = sqlAliasesModels,
				this->_sqlModelsAliases = sqlModelsAliases,
				this->_sqlAliasesModelsInstances = sqlAliasesModelsInstances;
		} else {

			$tempModels = $this->_models,
				tempModelsInstances = $this->_modelsInstances,
				tempSqlAliases = $this->_sqlAliases,
				tempSqlAliasesModels = $this->_sqlAliasesModels,
				tempSqlModelsAliases = $this->_sqlModelsAliases,
				tempSqlAliasesModelsInstances = $this->_sqlAliasesModelsInstances;

			$this->_models = array_merge(this->_models, models),
				this->_modelsInstances = array_merge(this->_modelsInstances, modelsInstances),
				this->_sqlAliases = array_merge(this->_sqlAliases, sqlAliases),
				this->_sqlAliasesModels = array_merge(this->_sqlAliasesModels, sqlAliasesModels),
				this->_sqlModelsAliases = array_merge(this->_sqlModelsAliases, sqlModelsAliases),
				this->_sqlAliasesModelsInstances = array_merge(this->_sqlAliasesModelsInstances, sqlAliasesModelsInstances);
		}


		// Join existing JOINS with automatic Joins
		if ( count(joins) ) {
			if ( count(automaticJoins) ) {
				if ( isset($joins[0]) ) {
					$select["joins"] = array_merge(joins, automaticJoins);
				} else {
					$automaticJoins[] = joins,
						select["joins"] = automaticJoins;
				}
			}
			$sqlJoins = $this->_getJoins(select);
		} else {
			if ( count(automaticJoins) ) {
				$select["joins"] = automaticJoins,
					sqlJoins = $this->_getJoins(select);
			} else {
				$sqlJoins = [];
			}
		}

		// Resolve selected columns
		$position = 0,
			sqlColumnAliases = [];

		foreach ( $selectColumns as $column ) {

			foreach ( $this->_getSelectColumn(column) as $sqlColumn ) {

				/**
				 * If "alias" is set, the user defined an alias for ( the column
				 */
				if ( fetch alias, column["alias"] ) {

					/**
					 * The best alias is the one provided by the user
					 */
					$sqlColumn["balias"] = alias,
						sqlColumn["sqlAlias"] = alias,
						sqlColumns[alias] = sqlColumn,
						sqlColumnAliases[alias] = true;

				} else {
					/**
					 * "balias" is the best alias chosen for ( the column
					 */
					if ( fetch alias, sqlColumn["balias"] ) {
						$sqlColumns[alias] = sqlColumn;
					} else {
						if ( sqlColumn["type"] == "scalar" ) {
							$sqlColumns["_" . position] = sqlColumn;
						} else {
							$sqlColumns[] = sqlColumn;
						}
					}
				}

				$position++;
			}
		}
		$this->_sqlColumnAliases = sqlColumnAliases;

		// sqlSelect is the final prepared SELECT
		$sqlSelect = [
			"models" : sqlModels,
			"tables" : sqlTables,
			"columns": sqlColumns
		];

		if ( fetch distinct, select["distinct"] ) {
			$sqlSelect["distinct"] = distinct;
		}

		if ( count(sqlJoins) ) {
			$sqlSelect["joins"] = sqlJoins;
		}

		// Process "WHERE" clause if ( set
		if ( fetch where, ast["where"] ) {
			$sqlSelect["where"] = $this->_getExpression(where);
		}

		// Process "GROUP BY" clause if ( set
		if ( fetch groupBy, ast["groupBy"] ) {
			$sqlSelect["group"] = $this->_getGroupClause(groupBy);
		}

		// Process "HAVING" clause if ( set
		if ( fetch having , ast["having"] ) {
			$sqlSelect["having"] = $this->_getExpression(having);
		}

		// Process "ORDER BY" clause if ( set
		if ( fetch order, ast["orderBy"] ) {
			$sqlSelect["order"] = $this->_getOrderClause(order);
		}

		// Process "LIMIT" clause if ( set
		if ( fetch limit, ast["limit"] ) {
			$sqlSelect["limit"] = $this->_getLimitClause(limit);
		}

		// Process "FOR UPDATE" clause if ( set
		if ( isset ast["for (Update"] ) ) {
			$sqlSelect["for (Update"] = true;
		}

		if ( merge ) {
			$this->_models = tempModels,
				this->_modelsInstances = tempModelsInstances,
				this->_sqlAliases = tempSqlAliases,
				this->_sqlAliasesModels = tempSqlAliasesModels,
				this->_sqlModelsAliases = tempSqlModelsAliases,
				this->_sqlAliasesModelsInstances = tempSqlAliasesModelsInstances;
		}

		return sqlSelect;
    }

    /***
	 * Analyzes an INSERT intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareInsert() {
			exprValues, exprValue, sqlInsert, metaData, fields,
			sqlFields, field, name, realModelName;
		boolean notQuoting;

		$ast = $this->_ast;

		if ( !isset ast["qualif (iedName"] ) {
			throw new Exception("Corrupted INSERT AST");
		}

		if ( !isset ast["values"] ) {
			throw new Exception("Corrupted INSERT AST");
		}

		$qualif (iedName = ast["qualif (iedName"];

		// Check if ( the related model exists
		if ( !isset qualif (iedName["name"] ) {
			throw new Exception("Corrupted INSERT AST");
		}

		$manager = $this->_manager,
			modelName = qualif (iedName["name"];

		// Check if ( the table have a namespace alias
		if ( memstr(modelName, ":") ) {
			$nsAlias = explode(":", modelName);
			$realModelName = manager->getNamespaceAlias(nsAlias[0]) . "\\" . nsAlias[1];
		} else {
			$realModelName = modelName;
		}

		$model = manager->load(realModelName, true),
			source = model->getSource(),
			schema = model->getSchema();

		if ( schema ) {
			$source = [schema, source];
		}

		$notQuoting = false,
		    exprValues = [];

		for ( exprValue in ast["values"] ) {

			// Resolve every expression in the "values" clause
			$exprValues[] = [
				"type" : exprValue["type"],
				"value": $this->_getExpression(exprValue, notQuoting)
			];
		}

		$sqlInsert = [
			"model": modelName,
			"table": source
		];

		$metaData = $this->_metaData;

		if ( fetch fields, ast["fields"] ) {
			$sqlFields = [];
			foreach ( $fields as $field ) {

				$name = field["name"];

				// Check that inserted fields are part of the model
				if ( !metaData->hasAttribute(model, name) ) {
					throw new Exception(
						"The model '" . modelName . "' doesn't have the attribute '" . name . "', when preparing: " . $this->_phql
					);
				}

				// Add the file to the insert list
				$sqlFields[] = name;
			}

			$sqlInsert["fields"] = sqlFields;
		}

		$sqlInsert["values"] = exprValues;

		return sqlInsert;
    }

    /***
	 * Analyzes an UPDATE intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareUpdate() {
			sqlTables, sqlAliases, sqlAliasesModelsInstances, updateTables,
			nsAlias, realModelName, completeSource, sqlModels, manager,
			table, qualif (iedName, modelName, model, source, schema, alias,
			sqlFields, sqlValues, updateValues, updateValue, exprColumn, sqlUpdate,
			where, limit;
		boolean notQuoting;

		$ast = $this->_ast;

		if ( !fetch update, ast["update"] ) {
			throw new Exception("Corrupted UPDATE AST");
		}

		if ( !fetch tables, update["tables"] ) {
			throw new Exception("Corrupted UPDATE AST");
		}

		if ( !fetch values, update["values"] ) {
			throw new Exception("Corrupted UPDATE AST");
		}

		/**
		 * We use these arrays to store info related to models, alias and its sources. With them we can rename columns later
		 */
		$models = [],
			modelsInstances = [];

		$sqlTables = [],
			sqlModels = [],
			sqlAliases = [],
			sqlAliasesModelsInstances = [];

		if ( !isset($tables[0]) ) {
			$updateTables = [tables];
		} else {
			$updateTables = tables;
		}

		$manager = $this->_manager;
		foreach ( $updateTables as $table ) {

			$qualif (iedName = table["qualif (iedName"],
				modelName = qualif (iedName["name"];

			/**
			 * Check if ( the table have a namespace alias
			 */
			if ( memstr(modelName, ":") ) {
				$nsAlias = explode(":", modelName);
				$realModelName = manager->getNamespaceAlias(nsAlias[0]) . "\\" . nsAlias[1];
			} else {
				$realModelName = modelName;
			}

			/**
			 * Load a model instance from the models manager
			 */
			$model = manager->load(realModelName, true),
				source = model->getSource(),
				schema = model->getSchema();

			/**
			 * Create a full source representation including schema
			 */
			if ( schema ) {
				$completeSource = [source, schema];
			} else {
				$completeSource = [source, null];
			}

			/**
			 * Check if ( the table is aliased
			 */
			if ( fetch alias, table["alias"] ) {
				$sqlAliases[alias] = alias,
					completeSource[] = alias,
					sqlTables[] = completeSource,
					sqlAliasesModelsInstances[alias] = model,
					models[alias] = realModelName;
			} else {
				$sqlAliases[realModelName] = source,
					sqlAliasesModelsInstances[realModelName] = model,
					sqlTables[] = source,
					models[realModelName] = source;
			}

			$sqlModels[] = realModelName,
				modelsInstances[realModelName] = model;
		}

		/**
		 * Update the models/alias/sources in the object
		 */
		$this->_models = models,
			this->_modelsInstances = modelsInstances,
			this->_sqlAliases = sqlAliases,
			this->_sqlAliasesModelsInstances = sqlAliasesModelsInstances;

		$sqlFields = [], sqlValues = [];

		if ( !isset($values[0]) ) {
			$updateValues = [values];
		} else {
			$updateValues = values;
		}

		$notQuoting = false;
		foreach ( $updateValues as $updateValue ) {

			$sqlFields[] = $this->_getExpression(updateValue["column"], notQuoting),
				exprColumn = updateValue["expr"],
				sqlValues[] = [
					"type" : exprColumn["type"],
					"value": $this->_getExpression(exprColumn, notQuoting)
				];
		}

		$sqlUpdate = [
			"tables": sqlTables,
			"models": sqlModels,
			"fields": sqlFields,
			"values": sqlValues
		];

		if ( fetch where, ast["where"] ) {
			$sqlUpdate["where"] = $this->_getExpression(where, true);
		}

		if ( fetch limit, ast["limit"] ) {
			$sqlUpdate["limit"] = $this->_getLimitClause(limit);
		}

		return sqlUpdate;
    }

    /***
	 * Analyzes a DELETE intermediate code and produces an array to be executed later
	 **/
    protected final function _prepareDelete() {
			sqlTables, sqlModels, sqlAliases, sqlAliasesModelsInstances,
			deleteTables, manager, table, qualif (iedName, modelName, nsAlias,
			realModelName, model, source, schema, completeSource, alias,
			sqlDelete, where, limit;

		$ast = $this->_ast;

		if ( !fetch delete, ast["delete"] ) {
			throw new Exception("Corrupted DELETE AST");
		}

		if ( !fetch tables, delete["tables"] ) {
			throw new Exception("Corrupted DELETE AST");
		}

		/**
		 * We use these arrays to store info related to models, alias and its sources.
		 * Thanks to them we can rename columns later
		 */
		$models = [],
			modelsInstances = [];

		$sqlTables = [],
			sqlModels = [],
			sqlAliases = [],
			sqlAliasesModelsInstances = [];

		if ( !isset($tables[0]) ) {
			$deleteTables = [tables];
		} else {
			$deleteTables = tables;
		}

		$manager = $this->_manager;
		foreach ( $deleteTables as $table ) {

			$qualif (iedName = table["qualif (iedName"],
				modelName = qualif (iedName["name"];

			/**
			 * Check if ( the table have a namespace alias
			 */
			if ( memstr(modelName, ":") ) {
				$nsAlias = explode(":", modelName);
				$realModelName = manager->getNamespaceAlias(nsAlias[0]) . "\\" . nsAlias[1];
			} else {
				$realModelName = modelName;
			}

			/**
			 * Load a model instance from the models manager
			 */
			$model = manager->load(realModelName, true),
				source = model->getSource(),
				schema = model->getSchema();

			if ( schema ) {
				$completeSource = [source, schema];
			} else {
				$completeSource = [source, null];
			}

			if ( fetch alias, table["alias"] ) {
				$sqlAliases[alias] = alias,
					completeSource[] = alias,
					sqlTables[] = completeSource,
					sqlAliasesModelsInstances[alias] = model,
					models[alias] = realModelName;
			} else {
				$sqlAliases[realModelName] = source,
					sqlAliasesModelsInstances[realModelName] = model,
					sqlTables[] = source,
					models[realModelName] = source;
			}

			$sqlModels[] = realModelName,
				modelsInstances[realModelName] = model;
		}

		/**
		 * Update the models/alias/sources in the object
		 */
		$this->_models = models,
			this->_modelsInstances = modelsInstances,
			this->_sqlAliases = sqlAliases,
			this->_sqlAliasesModelsInstances = sqlAliasesModelsInstances;

		$sqlDelete = [],
			sqlDelete["tables"] = sqlTables,
			sqlDelete["models"] = sqlModels;

		if ( fetch where, ast["where"] ) {
			$sqlDelete["where"] = $this->_getExpression(where, true);
		}

		if ( fetch limit, ast["limit"] ) {
			$sqlDelete["limit"] = $this->_getLimitClause(limit);
		}

		return sqlDelete;
    }

    /***
	 * Parses the intermediate code produced by Phalcon\Mvc\Model\Query\Lang generating another
	 * intermediate representation that could be executed by Phalcon\Mvc\Model\Query
	 **/
    public function parse() {

		$intermediate = $this->_intermediate;
		if ( gettype($intermediate) == "array" ) {
			return intermediate;
		}

		/**
		 * This function parses the PHQL statement
		 */
		$phql = $this->_phql,
			ast = phql_parse_phql(phql);

		$irPhql = null, uniqueId = null;

		if ( gettype($ast) == "array" ) {

			/**
			 * Check if ( the prepared PHQL is already cached
			 * Parsed ASTs have a unique id
			 */
			if ( fetch uniqueId, ast["id"] ) {
				if ( fetch irPhql, self::_irPhqlCache[uniqueId] ) {
					if ( gettype($irPhql) == "array" ) {
						// Assign the type to the query
						$this->_type = ast["type"];
						return irPhql;
					}
				}
			}

			/**
			 * A valid AST must have a type
			 */
			if ( fetch type, ast["type"] ) {

				$this->_ast = ast,
					this->_type = type;

				switch type {

					case PHQL_T_SELECT:
						$irPhql = $this->_prepareSelect();
						break;

					case PHQL_T_INSERT:
						$irPhql = $this->_prepareInsert();
						break;

					case PHQL_T_UPDATE:
						$irPhql = $this->_prepareUpdate();
						break;

					case PHQL_T_DELETE:
						$irPhql = $this->_prepareDelete();
						break;

					default:
						throw new Exception("Unknown statement " . type . ", when preparing: " . phql);
				}
			}
		}

		if ( gettype($irPhql) != "array" ) {
			throw new Exception("Corrupted AST");
		}

		/**
		 * Store the prepared AST in the cache
		 */
		if ( gettype($uniqueId) == "int" ) {
			$self::_irPhqlCache[uniqueId] = irPhql;
		}

		$this->_intermediate = irPhql;
		return irPhql;
    }

    /***
	 * Returns the current cache backend instance
	 **/
    public function getCache() {
		return $this->_cache;
    }

    /***
	 * Executes the SELECT intermediate representation producing a Phalcon\Mvc\Model\Resultset
	 **/
    protected final function _executeSelect($intermediate , $bindParams , $bindTypes , $simulate  = false ) {
			columns, column, selectColumns, simpleColumnMap, metaData, aliasCopy,
			sqlColumn, attributes, instance, columnMap, attribute,
			columnAlias, sqlAlias, dialect, sqlSelect, bindCounts,
			processed, wildcard, value, processedTypes, typeWildcard, result,
			resultData, cache, resultObject, columns1, typesColumnMap, wildcardValue, resultsetClassName;
		boolean haveObjects, haveScalars, isComplex, isSimpleStd, isKeepingSnapshots;
		int numberObjects;

		$manager = $this->_manager;

		/**
		 * Get a database connection
		 */
		$connectionTypes = [];
		$models = intermediate["models"];

		foreach ( $models as $modelName ) {

			// Load model if ( it is not loaded
			if ( !fetch model, $this->_modelsInstances[modelName] ) {
				$model = manager->load(modelName, true),
					this->_modelsInstances[modelName] = model;
			}

			$connection = $this->getReadConnection(model, intermediate, bindParams, bindTypes);

			if ( gettype($connection) == "object" ) {
				// More than one type of connection is not allowed
				$connectionTypes[connection->getType()] = true;
				if ( count(connectionTypes) == 2 ) {
					throw new Exception("Cannot use models of dif (ferent database systems in the same query");
				}
			}
		}

		$columns = intermediate["columns"];

		$haveObjects = false,
			haveScalars = false,
			isComplex = false;

		// Check if ( the resultset have objects and how many of them have
		$numberObjects = 0;
		$columns1 = columns;

		foreach ( $columns as $column ) {

			if ( gettype($column) != "array" ) {
				throw new Exception("Invalid column definition");
			}

			if ( column["type"] == "scalar" ) {
				if ( !isset column["balias"] ) {
					$isComplex = true;
				}
				$haveScalars = true;
			} else {
				$haveObjects = true, numberObjects++;
			}
		}

		// Check if ( the resultset to return is complex or simple
		if ( isComplex === false ) {
			if ( haveObjects === true ) {
				if ( haveScalars === true ) {
					$isComplex = true;
				} else {
					if ( numberObjects == 1 ) {
						$isSimpleStd = false;
					} else {
						$isComplex = true;
					}
				}
			} else {
				$isSimpleStd = true;
			}
		}

		// Processing selected columns
		$instance = null,
			selectColumns = [],
			simpleColumnMap = [],
			metaData = $this->_metaData;

		foreach ( aliasCopy, $columns as $column ) {

			$sqlColumn = column["column"];

			// Complete objects are treated in a dif (ferent way
			if ( column["type"] == "object" ) {

				$modelName = column["model"];

				/**
				 * Base instance
				 */
				if ( !fetch instance, $this->_modelsInstances[modelName] ) {
					$instance = manager->load(modelName),
						this->_modelsInstances[modelName] = instance;
				}

				$attributes = metaData->getAttributes(instance);
				if ( isComplex === true ) {

					// If the resultset is complex we open every model into their columns
					if ( globals_get("orm.column_renaming") ) {
						$columnMap = metaData->getColumnMap(instance);
					} else {
						$columnMap = null;
					}

					// Add every attribute in the model to the generated select
					foreach ( $attributes as $attribute ) {
						$selectColumns[] = [attribute, sqlColumn, "_" . sqlColumn . "_" . attribute];
					}

					// We cache required meta-data to make its future access faster
					$columns1[aliasCopy]["instance"] = instance,
						columns1[aliasCopy]["attributes"] = attributes,
						columns1[aliasCopy]["columnMap"] = columnMap;

					// Check if ( the model keeps snapshots
					$isKeepingSnapshots = (boolean) manager->isKeepingSnapshots(instance);
					if ( isKeepingSnapshots ) {
						$columns1[aliasCopy]["keepSnapshots"] = isKeepingSnapshots;
					}

				} else {

					/**
					 * Query only the columns that are registered as attributes in the metaData
					 */
					foreach ( $attributes as $attribute ) {
						$selectColumns[] = [attribute, sqlColumn];
					}
				}
			} else {

				/**
				 * Create an alias if ( the column doesn't have one
				 */
				if ( gettype($aliasCopy) == "int" ) {
					$columnAlias = [sqlColumn, null];
				} else {
					$columnAlias = [sqlColumn, null, aliasCopy];
				}
				$selectColumns[] = columnAlias;
			}

			/**
			 * Simulate a column map
			 */
			if ( isComplex === false && isSimpleStd === true ) {
				if ( fetch sqlAlias, column["sqlAlias"] ) {
					$simpleColumnMap[sqlAlias] = aliasCopy;
				} else {
					$simpleColumnMap[aliasCopy] = aliasCopy;
				}
			}
		}

		$bindCounts = [],
			intermediate["columns"] = selectColumns;

		/**
		 * Replace the placeholders
		 */
		if ( gettype($bindParams) == "array" ) {
			$processed = [];
			foreach ( wildcard, $bindParams as $value ) {

				if ( gettype($wildcard) == "integer" ) {
					$wildcardValue = ":" . wildcard;
				} else {
					$wildcardValue = wildcard;
				}

				$processed[wildcardValue] = value;
				if ( gettype($value) == "array" ) {
					$bindCounts[wildcardValue] = count(value);
				}
			}
		} else {
			$processed = bindParams;
		}

		/**
		 * Replace the bind Types
		 */
		if ( gettype($bindTypes) == "array" ) {
			$processedTypes = [];
			foreach ( typeWildcard, $bindTypes as $value ) {
				if ( gettype($typeWildcard) == "integer" ) {
					$processedTypes[":" . typeWildcard] = value;
				} else {
					$processedTypes[typeWildcard] = value;
				}
			}
		} else {
			$processedTypes = bindTypes;
		}

		if ( count(bindCounts) ) {
			$intermediate["bindCounts"] = bindCounts;
		}

		/**
		 * The corresponding SQL dialect generates the SQL statement based accordingly with the database system
		 */
		$dialect = connection->getDialect(),
			sqlSelect = dialect->select(intermediate);
		if ( $this->_sharedLock ) {
			$sqlSelect = dialect->sharedLock(sqlSelect);
		}

		/**
		 * Return the SQL to be executed instead of execute it
		 */
		if ( simulate ) {
			return [
				"sql"       : sqlSelect,
				"bind"      : processed,
				"bindTypes" : processedTypes
			];
		}

		/**
		 * Execute the query
		 */
		$result = connection->query(sqlSelect, processed, processedTypes);

		/**
		 * Check if ( the query has data
		 */
		if ( result instanceof ResultInterface && result->numRows() ) {
			$resultData = result;
		} else {
			$resultData = false;
		}

		/**
		 * Choose a resultset type
		 */
		$cache = $this->_cache;
		if ( isComplex === false ) {

			/**
			 * Select the base object
			 */
			if ( isSimpleStd === true ) {

				/**
				 * If the result is a simple standard object use an Phalcon\Mvc\Model\Row as base
				 */
				$resultObject = new Row();

				/**
				 * Standard objects can't keep snapshots
				 */
				$isKeepingSnapshots = false;

			} else {

				if ( gettype($instance) == "object" ) {
					$resultObject = instance;
				} else {
					$resultObject = model;
				}

				/**
				 * Get the column map
				 */
				if ( !globals_get("orm.cast_on_hydrate") ) {
					$simpleColumnMap = metaData->getColumnMap(resultObject);
				} else {

					$columnMap = metaData->getColumnMap(resultObject),
						typesColumnMap = metaData->getDataTypes(resultObject);

					if ( gettype($columnMap) === "null" ) {
						$simpleColumnMap = [];
						foreach ( $metaData->getAttributes(resultObject) as $attribute ) {
							$simpleColumnMap[attribute] = [attribute, typesColumnMap[attribute]];
						}
					} else {
						$simpleColumnMap = [];
						foreach ( column, $columnMap as $attribute ) {
							$simpleColumnMap[column] = [attribute, typesColumnMap[column]];
						}
					}
				}

				/**
				 * Check if ( the model keeps snapshots
				 */
				$isKeepingSnapshots = (boolean) manager->isKeepingSnapshots(resultObject);
			}

			if ( resultObject instanceof ModelInterface && method_exists(resultObject, "getResultsetClass") ) {
				$resultsetClassName = (<ModelInterface> resultObject)->getResultsetClass();

				if ( resultsetClassName ) {
					if ( ! class_exists(resultsetClassName) ) {
						throw new Exception("Resultset class \"" . resultsetClassName . "\" not found");
					}

					if ( ! is_subclass_of(resultsetClassName, "Phalcon\\Mvc\\Model\\ResultsetInterface") ) {
						throw new Exception("Resultset class \"" . resultsetClassName . "\" must be an implementation of Phalcon\\Mvc\\Model\\ResultsetInterface");
					}

					return new {resultsetClassName}(simpleColumnMap, resultObject, resultData, cache, isKeepingSnapshots);
				}
			}

			/**
			 * Simple resultsets contains only complete objects
			 */
			return new Simple(simpleColumnMap, resultObject, resultData, cache, isKeepingSnapshots);
		}

		/**
		 * Complex resultsets may contain complete objects and scalars
		 */
		return new Complex(columns1, resultData, cache);
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
			fields, columnMap, dialect, insertValues, number, value, model,
			values, exprValue, insertValue, wildcard, fieldName, attributeName,
			insertModel;
		boolean automaticFields;

		$modelName = intermediate["model"];

		$manager = $this->_manager;
		if ( !fetch model, $this->_modelsInstances[modelName] ) {
			$model = manager->load(modelName, true);
		}

		$connection = $this->getWriteConnection(model, intermediate, bindParams, bindTypes);

		$metaData = $this->_metaData, attributes = metaData->getAttributes(model);

		$automaticFields = false;

		/**
		 * The "fields" index may already have the fields to be used in the query
		 */
		if ( !fetch fields, intermediate["fields"] ) {
			$automaticFields = true,
				fields = attributes;
			if ( globals_get("orm.column_renaming") ) {
				$columnMap = metaData->getColumnMap(model);
			} else {
				$columnMap = null;
			}
		}

		$values = intermediate["values"];

		/**
		 * The number of calculated values must be equal to the number of fields in the model
		 */
		if ( count(fields) != count(values) ) {
			throw new Exception("The column count does not match the values count");
		}

		/**
		 * Get the dialect to resolve the SQL expressions
		 */
		$dialect = connection->getDialect();

		$insertValues = [];
		foreach ( number, $values as $value ) {

			$exprValue = value["value"];
			switch value["type"] {

				case PHQL_T_STRING:
				case PHQL_T_INTEGER:
				case PHQL_T_DOUBLE:
					$insertValue = dialect->getSqlExpression(exprValue);
					break;

				case PHQL_T_NULL:
					$insertValue = null;
					break;

				case PHQL_T_NPLACEHOLDER:
				case PHQL_T_SPLACEHOLDER:
				case PHQL_T_BPLACEHOLDER:

					if ( gettype($bindParams) != "array" ) {
						throw new Exception("Bound parameter cannot be replaced because placeholders is not an array");
					}

					$wildcard = str_replace(":", "", dialect->getSqlExpression(exprValue));
					if ( !fetch insertValue, bindParams[wildcard] ) {
						throw new Exception(
							"Bound parameter '" . wildcard . "' cannot be replaced because it isn't in the placeholders list"
						);
					}

					break;

				default:
					$insertValue = new RawValue(dialect->getSqlExpression(exprValue));
					break;
			}

			$fieldName = fields[number];

			/**
			 * If the user didn't define a column list we assume all the model's attributes as columns
			 */
			if ( automaticFields === true ) {
				if ( gettype($columnMap) == "array" ) {
					if ( !fetch attributeName, columnMap[fieldName] ) {
						throw new Exception("Column '" . fieldName . "' isn't part of the column map");
					}
				} else {
					$attributeName = fieldName;
				}
			} else {
				$attributeName = fieldName;
			}

			$insertValues[attributeName] = insertValue;
		}

		/**
		 * Get a base model from the Models Manager
		 * Clone the base model
		 */
		$insertModel = clone manager->load(modelName);

		/**
		 * Call 'create' to ensure that an insert is perfor (med
		 * Return the insert status
		 */
		return new Status(insertModel->create(insertValues), insertModel);
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
			fields, values, updateValues, fieldName, value,
			selectBindParams, selectBindTypes, number, field,
			records, exprValue, updateValue, wildcard, record;

		$models = intermediate["models"];

		if ( isset($models[1]) ) {
			throw new Exception("Updating several models at the same time is still not supported");
		}

		$modelName = models[0];

		/**
		 * Load the model from the modelsManager or from the _modelsInstances property
		 */
		if ( !fetch model, $this->_modelsInstances[modelName] ) {
			$model = $this->_manager->load(modelName);
		}

		$connection = $this->getWriteConnection(model, intermediate, bindParams, bindTypes);

		$dialect = connection->getDialect();

		$fields = intermediate["fields"],
			values = intermediate["values"];

		/**
		 * updateValues is applied to every record
		 */
		$updateValues = [];

		/**
		 * If a placeholder is unused in the update values, we assume that it's used in the SELECT
		 */
		$selectBindParams = bindParams,
			selectBindTypes = bindTypes;

		foreach ( number, $fields as $field ) {

			$value = values[number],
				exprValue = value["value"];

			if ( isset field["balias"] ) {
				$fieldName = field["balias"];
			} else {
				$fieldName = field["name"];
			}

			switch value["type"] {

				case PHQL_T_STRING:
				case PHQL_T_INTEGER:
				case PHQL_T_DOUBLE:
					$updateValue = dialect->getSqlExpression(exprValue);
					break;

				case PHQL_T_NULL:
					$updateValue = null;
					break;

				case PHQL_T_NPLACEHOLDER:
				case PHQL_T_SPLACEHOLDER:
				case PHQL_T_BPLACEHOLDER:

					if ( gettype($bindParams) != "array" ) {
						throw new Exception("Bound parameter cannot be replaced because placeholders is not an array");
					}

					$wildcard = str_replace(":", "", dialect->getSqlExpression(exprValue));
					if ( fetch updateValue, bindParams[wildcard] ) {
						unset selectBindParams[wildcard];
						unset selectBindTypes[wildcard];
					} else {
						throw new Exception(
							"Bound parameter '" . wildcard . "' cannot be replaced because it's not in the placeholders list"
						);
					}
					break;

				case PHQL_T_BPLACEHOLDER:
					throw new Exception("Not supported");

				default:
					$updateValue = new RawValue(dialect->getSqlExpression(exprValue));
					break;
			}

			$updateValues[fieldName] = updateValue;
		}

		/**
		 * We need to query the records related to the update
		 */
		$records = $this->_getRelatedRecords(model, intermediate, selectBindParams, selectBindTypes);

		/**
		 * If there are no records to apply the update we return success
		 */
		if ( !count(records) ) {
			return new Status(true);
		}

		$connection = $this->getWriteConnection(model, intermediate, bindParams, bindTypes);

		/**
		 * Create a transaction in the write connection
		 */
		connection->begin();

		records->rewind();

		//foreach ( $iterator(records) as $record ) {
		while records->valid() {

			$record = records->current();

			/**
			 * We apply the executed values to every record found
			 */
			if ( !record->update(updateValues) ) {

				/**
				 * Rollback the transaction on failure
				 */
				connection->rollback();

				return new Status(false, record);
			}

			records->next();
		}

		/**
		 * Commit transaction on success
		 */
		connection->commit();

		return new Status(true);
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

		$models = intermediate["models"];

		if ( isset($models[1]) ) {
			throw new Exception("Delete from several models at the same time is still not supported");
		}

		$modelName = models[0];

		/**
		 * Load the model from the modelsManager or from the _modelsInstances property
		 */
		if ( !fetch model, $this->_modelsInstances[modelName] ) {
			$model = $this->_manager->load(modelName);
		}

		/**
		 * Get the records to be deleted
		 */
		$records = $this->_getRelatedRecords(model, intermediate, bindParams, bindTypes);

		/**
		 * If there are no records to delete we return success
		 */
		if ( !count(records) ) {
			return new Status(true);
		}

		$connection = $this->getWriteConnection(model, intermediate, bindParams, bindTypes);

		/**
		 * Create a transaction in the write connection
		 */
		connection->begin();
		records->rewind();

		while records->valid() {

			$record = records->current();

			/**
			 * We delete every record found
			 */
			if ( !record->delete() ) {

				/**
				 * Rollback the transaction
				 */
				connection->rollback();

				return new Status(false, record);
			}

			records->next();
		}

		/**
		 * Commit the transaction
		 */
		connection->commit();

		/**
		 * Create a status to report the deletion status
		 */
		return new Status(true);
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

		/**
		 * Instead of create a PHQL string statement we manually create the IR representation
		 */
		$selectIr = [
			"columns": [[
				"type"  : "object",
				"model" : get_class(model),
				"column": model->getSource()
			]],
			"models":  intermediate["models"],
			"tables":  intermediate["tables"]
		];

		/**
		 * Check if ( a WHERE clause was specif (ied
		 */
		if ( fetch whereConditions, intermediate["where"] ) {
			$selectIr["where"] = whereConditions;
		}

		/**
		 * Check if ( a LIMIT clause was specif (ied
		 */
		if ( fetch limitConditions, intermediate["limit"] ) {
			$selectIr["limit"] = limitConditions;
		}

		/**
		 * We create another Phalcon\Mvc\Model\Query to get the related records
		 */
		$query = new self();
		query->setDI(this->_dependencyInjector);
		query->setType(PHQL_T_SELECT);
		query->setIntermediate(selectIr);

		return query->execute(bindParams, bindTypes);
    }

    /***
	 * Executes a parsed PHQL statement
	 *
	 * @param array bindParams
	 * @param array bindTypes
	 * @return mixed
	 **/
    public function execute($bindParams  = null , $bindTypes  = null ) {
			cache, result, preparedResult, defaultBindParams, mergedParams,
			defaultBindTypes, mergedTypes, type, lif (etime, intermediate;

		$uniqueRow = $this->_uniqueRow;

		$cacheOptions = $this->_cacheOptions;
		if ( gettype($cacheOptions) != "null" ) {

			if ( gettype($cacheOptions) != "array" ) {
				throw new Exception("Invalid caching options");
			}

			/**
			 * The user must set a cache key
			 */
			if ( !fetch key, cacheOptions["key"] ) {
				throw new Exception("A cache key must be provided to identif (y the cached resultset in the cache backend");
			}

			/**
			 * By default use use 3600 seconds (1 hour) as cache lif (etime
			 */
			if ( !fetch lif (etime, cacheOptions["lif (etime"] ) {
				$lif (etime = 3600;
			}

			/**
			 * "modelsCache" is the default name for ( the models cache service
			 */
			if ( !fetch cacheService, cacheOptions["service"] ) {
				$cacheService = "modelsCache";
			}

			$cache = $this->_dependencyInjector->getShared(cacheService);
			if ( gettype($cache) != "object" ) {
				throw new Exception("Cache service must be an object");
			}

			$result = cache->get(key, lif (etime);
			if ( result !== null ) {

				if ( gettype($result) != "object" ) {
					throw new Exception("Cache didn't return a valid resultset");
				}

				result->setIsFresh(false);

				/**
				 * Check if ( only the first row must be returned
				 */
				if ( uniqueRow ) {
					$preparedResult = result->getFirst();
				} else {
					$preparedResult = result;
				}

				return preparedResult;
			}

			$this->_cache = cache;
		}

		/**
		 * The statement is parsed from its PHQL string or a previously processed IR
		 */
		$intermediate = $this->parse();

		/**
		 * Check for ( default bind parameters and merge them with the passed ones
		 */
		$defaultBindParams = $this->_bindParams;
		if ( gettype($defaultBindParams) == "array" ) {
			if ( gettype($bindParams) == "array" ) {
				$mergedParams = defaultBindParams + bindParams;
			} else {
				$mergedParams = defaultBindParams;
			}
		} else {
			$mergedParams = bindParams;
		}

		/**
		 * Check for ( default bind types and merge them with the passed ones
		 */
		$defaultBindTypes = $this->_bindTypes;
		if ( gettype($defaultBindTypes) == "array" ) {
			if ( gettype($bindTypes) == "array" ) {
				$mergedTypes = defaultBindTypes + bindTypes;
			} else {
				$mergedTypes = defaultBindTypes;
			}
		} else {
			$mergedTypes = bindTypes;
		}

		if ( gettype($mergedParams) != "null" && gettype($mergedParams) != "array" ) {
			throw new Exception("Bound parameters must be an array");
		}

		if ( gettype($mergedTypes) != "null" && gettype($mergedTypes) != "array" ) {
			throw new Exception("Bound parameter types must be an array");
		}

		$type = $this->_type;
		switch type {

			case PHQL_T_SELECT:
				$result = $this->_executeSelect(intermediate, mergedParams, mergedTypes);
				break;

			case PHQL_T_INSERT:
				$result = $this->_executeInsert(intermediate, mergedParams, mergedTypes);
				break;

			case PHQL_T_UPDATE:
				$result = $this->_executeUpdate(intermediate, mergedParams, mergedTypes);
				break;

			case PHQL_T_DELETE:
				$result = $this->_executeDelete(intermediate, mergedParams, mergedTypes);
				break;

			default:
				throw new Exception("Unknown statement " . type);
		}

		/**
		 * We store the resultset in the cache if ( any
		 */
		if ( cacheOptions !== null ) {

			/**
			 * Only PHQL SELECTs can be cached
			 */
			if ( type != PHQL_T_SELECT ) {
				throw new Exception("Only PHQL statements that return resultsets can be cached");
			}

			cache->save(key, result, lif (etime);
		}

		/**
		 * Check if ( only the first row must be returned
		 */
		if ( uniqueRow ) {
			$preparedResult = result->getFirst();
		} else {
			$preparedResult = result;
		}

		return preparedResult;
    }

    /***
	 * Executes the query returning the first result
	 *
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\ModelInterface
	 **/
    public function getSingleResult($bindParams  = null , $bindTypes  = null ) {
		if ( $this->_uniqueRow ) {
			return $this->execute(bindParams, bindTypes);
		}

		return $this->execute(bindParams, bindTypes)->getFirst();
    }

    /***
	 * Sets the type of PHQL statement to be executed
	 **/
    public function setType($type ) {
		$this->_type = type;
		return this;
    }

    /***
	 * Gets the type of PHQL statement executed
	 **/
    public function getType() {
		return $this->_type;
    }

    /***
	 * Set default bind parameters
	 **/
    public function setBindParams($bindParams , $merge  = false ) {

		if ( merge ) {
			$currentBindParams = $this->_bindParams;
			if ( gettype($currentBindParams) == "array" ) {
				$this->_bindParams = currentBindParams + bindParams;
			} else {
				$this->_bindParams = bindParams;
			}
		} else {
			$this->_bindParams = bindParams;
		}

		return this;
    }

    /***
	 * Returns default bind params
	 *
	 * @return array
	 **/
    public function getBindParams() {
		return $this->_bindParams;
    }

    /***
	 * Set default bind parameters
	 **/
    public function setBindTypes($bindTypes , $merge  = false ) {

		if ( merge ) {
			$currentBindTypes = $this->_bindTypes;
			if ( gettype($currentBindTypes) == "array" ) {
				$this->_bindTypes = currentBindTypes + bindTypes;
			} else {
				$this->_bindTypes = bindTypes;
			}
		} else {
			$this->_bindTypes = bindTypes;
		}

		return this;
    }

    /***
	 * Set SHARED LOCK clause
	 **/
    public function setSharedLock($sharedLock  = false ) {
		$this->_sharedLock = sharedLock;

		return this;
    }

    /***
	 * Returns default bind types
	 *
	 * @return array
	 **/
    public function getBindTypes() {
		return $this->_bindTypes;
    }

    /***
	 * Allows to set the IR to be executed
	 **/
    public function setIntermediate($intermediate ) {
		$this->_intermediate = intermediate;
		return this;
    }

    /***
	 * Returns the intermediate representation of the PHQL statement
	 *
	 * @return array
	 **/
    public function getIntermediate() {
		return $this->_intermediate;
    }

    /***
	 * Sets the cache parameters of the query
	 **/
    public function cache($cacheOptions ) {
		$this->_cacheOptions = cacheOptions;
		return this;
    }

    /***
	 * Returns the current cache options
	 *
	 * @param array
	 **/
    public function getCacheOptions() {
		return $this->_cacheOptions;
    }

    /***
	 * Returns the SQL to be generated by the internal PHQL (only works in SELECT statements)
	 **/
    public function getSql() {

		/**
		 * The statement is parsed from its PHQL string or a previously processed IR
		 */
		$intermediate = $this->parse();

		if ( $this->_type == PHQL_T_SELECT ) {
			return $this->_executeSelect(intermediate, $this->_bindParams, $this->_bindTypes, true);
		}

		throw new Exception("This type of statement generates multiple SQL statements");
    }

    /***
	 * Destroys the internal PHQL cache
	 **/
    public static function clean() {
		$self::_irPhqlCache = [];
    }

    /***
	 * Gets the read connection from the model if there is no transaction set inside the query object
	 **/
    protected function getReadConnection($model , $intermediate  = null , $bindParams  = null , $bindTypes  = null ) {
		$transaction = $this->_transaction;

		if ( gettype($transaction) == "object" && transaction instanceof TransactionInterface ) {
			return transaction->getConnection();
		}

		if ( method_exists(model, "selectReadConnection") ) {
			// use selectReadConnection() if ( implemented in extended Model class
			$connection = model->selectReadConnection(intermediate, bindParams, bindTypes);
			if ( gettype($connection) != "object" ) {
				throw new Exception("selectReadConnection did not return a connection");
			}
			return connection;
		}
		return model->getReadConnection();
    }

    /***
	 * Gets the write connection from the model if there is no transaction inside the query object
	 **/
    protected function getWriteConnection($model , $intermediate  = null , $bindParams  = null , $bindTypes  = null ) {
		$transaction = $this->_transaction;

		if ( gettype($transaction) == "object" && transaction instanceof TransactionInterface ) {
			return transaction->getConnection();
		}

		if ( method_exists(model, "selectWriteConnection") ) {
			$connection = model->selectWriteConnection(intermediate, bindParams, bindTypes);
			if ( gettype($connection) != "object" ) {
				throw new Exception("selectWriteConnection did not return a connection");
			}
			return connection;
		}
		return model->getWriteConnection();
    }

    /***
	 * allows to wrap a transaction around all queries
	 **/
    public function setTransaction($transaction ) {
		$this->_transaction = transaction;
		return this;
    }

}