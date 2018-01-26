<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Di;
use Phalcon\Db\Column;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\CriteriaInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;


/***
 * Phalcon\Mvc\Model\Criteria
 *
 * This class is used to build the array parameter required by
 * Phalcon\Mvc\Model::find() and Phalcon\Mvc\Model::findFirst()
 * using an object-oriented interface.
 *
 * <code>
 * $robots = Robots::query()
 *     ->where("type = :type:")
 *     ->andWhere("year < 2000")
 *     ->bind(["type" => "mechanical"])
 *     ->limit(5, 10)
 *     ->orderBy("name")
 *     ->execute();
 * </code>
 **/

class Criteria {

    protected $_model;

    protected $_params;

    protected $_bindParams;

    protected $_bindTypes;

    protected $_hiddenParamNumber;

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {
		$this->_params["di"] = dependencyInjector;
    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {
		if ( fetch dependencyInjector, $this->_params["di"] ) {
			return dependencyInjector;
		}
		return null;
    }

    /***
	 * Set a model on which the query will be executed
	 **/
    public function setModelName($modelName ) {
		$this->_model = modelName;
		return this;
    }

    /***
	 * Returns an internal model name on which the criteria will be applied
	 **/
    public function getModelName() {
		return $this->_model;
    }

    /***
	 * Sets the bound parameters in the criteria
	 * This method replaces all previously set bound parameters
	 **/
    public function bind($bindParams , $merge  = false ) {

		if ( merge ) {
			if ( isset $this->_params["bind"] ) {
				$bind = $this->_params["bind"];
			} else {
				$bind = null;
			}
			if ( gettype($bind) == "array" ) {
				$this->_params["bind"] = bind + bindParams;
			} else {
				$this->_params["bind"] = bindParams;
			}
		} else {
			$this->_params["bind"] = bindParams;
		}

		return this;
    }

    /***
	 * Sets the bind types in the criteria
	 * This method replaces all previously set bound parameters
	 **/
    public function bindTypes($bindTypes ) {
		$this->_params["bindTypes"] = bindTypes;
		return this;
    }

    /***
	 * Sets SELECT DISTINCT / SELECT ALL flag
	 **/
    public function distinct($distinct ) {
	 	$this->_params["distinct"] = distinct;
	 	return this;
    }

    /***
	 * Sets the columns to be queried
	 *
	 *<code>
	 * $criteria->columns(
	 *     [
	 *         "id",
	 *         "name",
	 *     ]
	 * );
	 *</code>
	 *
	 * @param string|array columns
	 * @return \Phalcon\Mvc\Model\Criteria
	 **/
    public function columns($columns ) {
		$this->_params["columns"] = columns;
		return this;
    }

    /***
	 * Adds an INNER join to the query
	 *
	 *<code>
	 * $criteria->join("Robots");
	 * $criteria->join("Robots", "r.id = RobotsParts.robots_id");
	 * $criteria->join("Robots", "r.id = RobotsParts.robots_id", "r");
	 * $criteria->join("Robots", "r.id = RobotsParts.robots_id", "r", "LEFT");
	 *</code>
	 **/
    public function join($model , $conditions  = null , $alias  = null , $type  = null ) {

		$join = [model, conditions, alias, type];
		if ( fetch currentJoins, $this->_params["joins"] ) {
			if ( gettype($currentJoins) == "array" ) {
				$mergedJoins = array_merge(currentJoins, [join]);
			} else {
				$mergedJoins = [join];
			}
		} else {
			$mergedJoins = [join];
		}

		$this->_params["joins"] = mergedJoins;

		return this;
    }

    /***
	 * Adds an INNER join to the query
	 *
	 *<code>
	 * $criteria->innerJoin("Robots");
	 * $criteria->innerJoin("Robots", "r.id = RobotsParts.robots_id");
	 * $criteria->innerJoin("Robots", "r.id = RobotsParts.robots_id", "r");
	 *</code>
	 **/
    public function innerJoin($model , $conditions  = null , $alias  = null ) {
		return $this->join(model, conditions, alias, "INNER");
    }

    /***
	 * Adds a LEFT join to the query
	 *
	 *<code>
	 * $criteria->leftJoin("Robots", "r.id = RobotsParts.robots_id", "r");
	 *</code>
	 **/
    public function leftJoin($model , $conditions  = null , $alias  = null ) {
		return $this->join(model, conditions, alias, "LEFT");
    }

    /***
	 * Adds a RIGHT join to the query
	 *
	 *<code>
	 * $criteria->rightJoin("Robots", "r.id = RobotsParts.robots_id", "r");
	 *</code>
	 **/
    public function rightJoin($model , $conditions  = null , $alias  = null ) {
		return $this->join(model, conditions, alias, "RIGHT");
    }

    /***
	 * Sets the conditions parameter in the criteria
	 **/
    public function where($conditions , $bindParams  = null , $bindTypes  = null ) {

		$this->_params["conditions"] = conditions;

		/**
		 * Update or merge existing bound parameters
		 */
		if ( gettype($bindParams) == "array" ) {
			if ( fetch currentBindParams, $this->_params["bind"] ) {
				$this->_params["bind"] = array_merge(currentBindParams, bindParams);
			} else {
				$this->_params["bind"] = bindParams;
			}
		}

		/**
		 * Update or merge existing bind types parameters
		 */
		if ( gettype($bindTypes) == "array" ) {
			if ( fetch currentBindTypes, $this->_params["bindTypes"] ) {
				$this->_params["bindTypes"] = array_merge(currentBindTypes, bindTypes);
			} else {
				$this->_params["bindTypes"] = bindTypes;
			}
		}

		return this;
    }

    /***
	 * Appends a condition to the current conditions using an AND operator (deprecated)
	 *
	 * @deprecated 1.0.0
	 * @see \Phalcon\Mvc\Model\Criteria::andWhere()
	 **/
    public function addWhere($conditions , $bindParams  = null , $bindTypes  = null ) {
		return $this->andWhere(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a condition to the current conditions using an AND operator
	 **/
    public function andWhere($conditions , $bindParams  = null , $bindTypes  = null ) {

		if ( fetch currentConditions, $this->_params["conditions"] ) {
			$conditions = "(" . currentConditions . ") AND (" . conditions . ")";
		}

		return $this->where(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a condition to the current conditions using an OR operator
	 **/
    public function orWhere($conditions , $bindParams  = null , $bindTypes  = null ) {

		if ( fetch currentConditions, $this->_params["conditions"] ) {
			$conditions = "(" . currentConditions . ") OR (" . conditions . ")";
		}

		return $this->where(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a BETWEEN condition to the current conditions
	 *
	 *<code>
	 * $criteria->betweenWhere("price", 100.25, 200.50);
	 *</code>
	 **/
    public function betweenWhere($expr , $minimum , $maximum ) {

		$hiddenParam = $this->_hiddenParamNumber, nextHiddenParam = hiddenParam + 1;

		/**
		 * Minimum key with auto bind-params
		 */
		$minimumKey = "ACP" . hiddenParam;

		/**
		 * Maximum key with auto bind-params
		 */
		$maximumKey = "ACP" . nextHiddenParam;

		/**
		 * Create a standard BETWEEN condition with bind params
		 * Append the BETWEEN to the current conditions using and "and"
		 */
		this->andWhere(
			expr . " BETWEEN :" . minimumKey . ": AND :" . maximumKey . ":",
			[minimumKey: minimum, maximumKey: maximum]
		);

		$nextHiddenParam++, $this->_hiddenParamNumber = nextHiddenParam;

		return this;
    }

    /***
	 * Appends a NOT BETWEEN condition to the current conditions
	 *
	 *<code>
	 * $criteria->notBetweenWhere("price", 100.25, 200.50);
	 *</code>
	 **/
    public function notBetweenWhere($expr , $minimum , $maximum ) {

		$hiddenParam = $this->_hiddenParamNumber;

		$nextHiddenParam = hiddenParam + 1;

		/**
		 * Minimum key with auto bind-params
		 */
		$minimumKey = "ACP" . hiddenParam;

		/**
		 * Maximum key with auto bind-params
		 */
		$maximumKey = "ACP" . nextHiddenParam;

		/**
		 * Create a standard BETWEEN condition with bind params
		 * Append the BETWEEN to the current conditions using and "and"
		 */
		this->andWhere(
			expr . " NOT BETWEEN :" . minimumKey . ": AND :"  . maximumKey . ":",
			[minimumKey: minimum, maximumKey: maximum]
		);

		$nextHiddenParam++;

		$this->_hiddenParamNumber = nextHiddenParam;

		return this;
    }

    /***
	 * Appends an IN condition to the current conditions
	 *
	 * <code>
	 * $criteria->inWhere("id", [1, 2, 3]);
	 * </code>
	 **/
    public function inWhere($expr , $values ) {

		if ( !count(values) ) {
			this->andWhere(expr . " != " . expr);
			return this;
		}

		$hiddenParam = $this->_hiddenParamNumber;

		$bindParams = [], bindKeys = [];
		foreach ( $values as $value ) {

			/**
			 * Key with auto bind-params
			 */
			$key = "ACP" . hiddenParam;

			$queryKey = ":" . key . ":";

			$bindKeys[] = queryKey, bindParams[key] = value;

			$hiddenParam++;
		}

		/**
		 * Create a standard IN condition with bind params
		 * Append the IN to the current conditions using and "and"
		 */
		this->andWhere(expr . " IN (" . join(", ", bindKeys) . ")", bindParams);

		$this->_hiddenParamNumber = hiddenParam;

		return this;
    }

    /***
	 * Appends a NOT IN condition to the current conditions
	 *
	 *<code>
	 * $criteria->notInWhere("id", [1, 2, 3]);
	 *</code>
	 **/
    public function notInWhere($expr , $values ) {

		$hiddenParam = $this->_hiddenParamNumber;

		$bindParams = [], bindKeys = [];
		foreach ( $values as $value ) {

			/**
			 * Key with auto bind-params
			 */
			$key = "ACP" . hiddenParam,
				bindKeys[] = ":" . key . ":",
				bindParams[key] = value;

			$hiddenParam++;
		}

		/**
		 * Create a standard IN condition with bind params
		 * Append the IN to the current conditions using and "and"
		 */
		this->andWhere(expr . " NOT IN (" . join(", ", bindKeys) . ")", bindParams);
		$this->_hiddenParamNumber = hiddenParam;

		return this;
    }

    /***
	 * Adds the conditions parameter to the criteria
	 **/
    public function conditions($conditions ) {
		$this->_params["conditions"] = conditions;
		return this;
    }

    /***
	 * Adds the order-by parameter to the criteria (deprecated)
	 *
	 * @see \Phalcon\Mvc\Model\Criteria::orderBy()
	 **/
    public function order($orderColumns ) {
		$this->_params["order"] = orderColumns;
		return this;
    }

    /***
	 * Adds the order-by clause to the criteria
	 **/
    public function orderBy($orderColumns ) {
		$this->_params["order"] = orderColumns;
		return this;
    }

    /***
	 * Adds the group-by clause to the criteria
	 **/
    public function groupBy($group ) {
		$this->_params["group"] = group;
		return this;
    }

    /***
	 * Adds the having clause to the criteria
	 **/
    public function having($having ) {
		$this->_params["having"] = having;
		return this;
    }

    /***
	 * Adds the limit parameter to the criteria.
	 *
	 * <code>
	 * $criteria->limit(100);
	 * $criteria->limit(100, 200);
	 * $criteria->limit("100", "200");
	 * </code>
	 **/
    public function limit($limit , $offset  = null ) {
		$limit = abs(limit);

		if ( unlikely limit == 0 ) {
			return this;
		}

		if ( is_numeric(offset) ) {
			$offset = abs((int) offset);
			$this->_params["limit"] = ["number": limit, "offset": offset];
		} else {
			$this->_params["limit"] = limit;
		}

		return this;
    }

    /***
	 * Adds the "for_update" parameter to the criteria
	 **/
    public function forUpdate($forUpdate  = true ) {
		$this->_params["for (_update"] = for (Update;
		return this;
    }

    /***
	 * Adds the "shared_lock" parameter to the criteria
	 **/
    public function sharedLock($sharedLock  = true ) {
		$this->_params["shared_lock"] = sharedLock;
		return this;
    }

    /***
	 * Sets the cache options in the criteria
	 * This method replaces all previously set cache options
	 **/
    public function cache($cache ) {
		$this->_params["cache"] = cache;
		return this;
    }

    /***
	 * Returns the conditions parameter in the criteria
	 **/
    public function getWhere() {
		if ( fetch conditions, $this->_params["conditions"] ) {
			return conditions;
		}
		return null;
    }

    /***
	 * Returns the columns to be queried
	 *
	 * @return string|array|null
	 **/
    public function getColumns() {
		if ( fetch columns, $this->_params["columns"] ) {
			return columns;
		}
		return null;
    }

    /***
	 * Returns the conditions parameter in the criteria
	 **/
    public function getConditions() {
		if ( fetch conditions, $this->_params["conditions"] ) {
			return conditions;
		}
		return null;
    }

    /***
	 * Returns the limit parameter in the criteria, which will be
	 * an integer if limit was set without an offset,
	 * an array with 'number' and 'offset' keys if an offset was set with the limit,
	 * or null if limit has not been set.
	 *
	 * @return int|array|null
	 **/
    public function getLimit() {
		if ( fetch limit, $this->_params["limit"] ) {
			return limit;
		}
		return null;
    }

    /***
	 * Returns the order clause in the criteria
	 **/
    public function getOrderBy() {
		if ( fetch order, $this->_params["order"] ) {
			return order;
		}
		return null;
    }

    /***
	 * Returns the group clause in the criteria
	 **/
    public function getGroupBy() {
		if ( fetch group, $this->_params["group"] ) {
			return group;
		}
		return null;
    }

    /***
	 * Returns the having clause in the criteria
	 **/
    public function getHaving() {
		if ( fetch having, $this->_params["having"] ) {
			return having;
		}
		return null;
    }

    /***
	 * Returns all the parameters defined in the criteria
	 *
	 * @return array
	 **/
    public function getParams() {
		return $this->_params;
    }

    /***
	 * Builds a Phalcon\Mvc\Model\Criteria based on an input array like $_POST
	 **/
    public static function fromInput($dependencyInjector , $modelName , $data , $operator  = AND ) {
			model, dataTypes, bind, criteria, columnMap;

		$conditions = [];
		if ( count(data) ) {

			$metaData = dependencyInjector->getShared("modelsMetadata");

			$model = new {modelName}(null, dependencyInjector),
				dataTypes = metaData->getDataTypes(model),
				columnMap = metaData->getReverseColumnMap(model);

			/**
			 * We look foreach ( $the as $attributes array passed as data
			 */
			$bind = [];
			foreach ( field, $data as $value ) {

				if ( gettype($columnMap) == "array" && count(columnMap) ) {
					$attribute = columnMap[field];
				} else {
					$attribute = field;
				}

				if ( fetch type, dataTypes[attribute] ) {
					if ( value !== null && value !== "" ) {

						if ( type == Column::TYPE_VARCHAR ) {
							/**
							 * For varchar types we use LIKE operator
							 */
							$conditions[] = "[" . field . "] LIKE :" . field . ":", bind[field] = "%" . value . "%";
							continue;
						}

						/**
						 * For the rest of data types we use a plain = operator
						 */
						$conditions[] = "[" . field . "] = :" . field . ":", bind[field] = value;
					}
				}
			}
		}

		/**
		 * Create an object instance and pass the parameters to it
		 */
		$criteria = new self();
		if ( count(conditions) ) {
			criteria->where(join(" " . operator . " ", conditions));
			criteria->bind(bind);
		}

		criteria->setModelName(modelName);
		return criteria;
    }

    /***
	 * Creates a query builder from criteria.
	 *
	 * <code>
	 * $builder = Robots::query()
	 *     ->where("type = :type:")
	 *     ->bind(["type" => "mechanical"])
	 *     ->createBuilder();
	 * </code>
	 **/
    public function createBuilder() {

		$dependencyInjector = $this->getDI();
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = Di::getDefault();
			this->setDI(dependencyInjector);
		}

		$manager = <ManagerInterface> dependencyInjector->getShared("modelsManager");

		/**
		 * Builds a query with the passed parameters
		 */
		$builder = manager->createBuilder(this->_params);
		builder->from(this->_model);

		return builder;
    }

    /***
	 * Executes a find using the parameters built with the criteria
	 **/
    public function execute() {

		$model = $this->getModelName();
		if ( gettype($model) != "string" ) {
			throw new Exception("Model name must be string");
		}

		return {model}::find(this->getParams());
    }

}