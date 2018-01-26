<?php


namespace Phalcon\Mvc\Model\Query;

use Phalcon\Di;
use Phalcon\Db\Column;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;


/***
 * Phalcon\Mvc\Model\Query\Builder
 *
 * Helps to create PHQL queries using an OO interface
 *
 *<code>
 * $params = [
 *     "models"     => ["Users"],
 *     "columns"    => ["id", "name", "status"],
 *     "conditions" => [
 *         [
 *             "created > :min: AND created < :max:",
 *             [
 *                 "min" => "2013-01-01",
 *                 "max" => "2014-01-01",
 *             ],
 *             [
 *                 "min" => PDO::PARAM_STR,
 *                 "max" => PDO::PARAM_STR,
 *             ],
 *         ],
 *     ],
 *     // or "conditions" => "created > '2013-01-01' AND created < '2014-01-01'",
 *     "group"      => ["id", "name"],
 *     "having"     => "name = 'Kamil'",
 *     "order"      => ["name", "id"],
 *     "limit"      => 20,
 *     "offset"     => 20,
 *     // or "limit" => [20, 20],
 * ];
 *
 * $queryBuilder = new \Phalcon\Mvc\Model\Query\Builder($params);
 *</code>
 **/

class Builder {

    protected $_dependencyInjector;

    protected $_columns;

    protected $_models;

    protected $_joins;

    /***
	 * @deprecated Will be removed in version 4.0.0
	 **/
    protected $_with;

    protected $_conditions;

    protected $_group;

    protected $_having;

    protected $_order;

    protected $_limit;

    protected $_offset;

    protected $_forUpdate;

    protected $_sharedLock;

    protected $_bindParams;

    protected $_bindTypes;

    protected $_distinct;

    protected $_hiddenParamNumber;

    /***
	 * Phalcon\Mvc\Model\Query\Builder constructor
	 **/
    public function __construct($params  = null , $dependencyInjector  = null ) {
			for (Update, sharedLock, orderClause, offsetClause, joinsClause,
			singleConditionArray, limit, offset, fromClause,
			mergedConditions, mergedParams, mergedTypes,
			singleCondition, singleParams, singleTypes,
			distinct, bind, bindTypes;

		if ( gettype($params) == "array" ) {

			/**
			 * Process conditions
			 */
			if ( fetch conditions, params[0] ) {
				$this->_conditions = conditions;
			} else {
				if ( fetch conditions, params["conditions"] ) {
					$this->_conditions = conditions;
				}
			}

			if ( gettype($conditions) == "array" ) {

				$mergedConditions = [];
				$mergedParams     = [];
				$mergedTypes      = [];
				foreach ( $conditions as $singleConditionArray ) {

					if ( gettype($singleConditionArray) == "array" ) {


						if ( gettype($singleCondition) == "string" ) {
							$mergedConditions[] = singleCondition;
						}

						if ( gettype($singleParams) == "array" ) {
							$mergedParams = mergedParams + singleParams;
						}

						if ( gettype($singleTypes) == "array" ) {
							$mergedTypes = mergedTypes + singleTypes;
						}
					}
				}

				$this->_conditions = implode(" AND ", mergedConditions);

				if ( gettype($mergedParams) == "array" ) {
					$this->_bindParams = mergedParams;
				}

				if ( gettype($mergedTypes) == "array" ) {
					$this->_bindTypes  = mergedTypes;
				}
			}

			/**
			 * Assign bind types
			 */
			if ( fetch bind, params["bind"] ) {
				$this->_bindParams = bind;
			}

			if ( fetch bindTypes, params["bindTypes"] ) {
				$this->_bindTypes = bindTypes;
			}

			/**
			 * Assign SELECT DISTINCT / SELECT ALL clause
			 */
			if ( fetch distinct, params["distinct"] ) {
				$this->_distinct = distinct;
			}

			/**
			 * Assign FROM clause
			 */
			if ( fetch fromClause, params["models"] ) {
				$this->_models = fromClause;
			}

			/**
			 * Assign COLUMNS clause
			 */
			if ( fetch columns, params["columns"] ) {
				$this->_columns = columns;
			}

			/**
			 * Assign JOIN clause
			 */
			if ( fetch joinsClause, params["joins"] ) {
				$this->_joins = joinsClause;
			}

			/**
			 * Assign GROUP clause
			 */
			if ( fetch groupClause, params["group"] ) {
				$this->_group = groupClause;
			}

			/**
			 * Assign HAVING clause
			 */
			if ( fetch havingClause, params["having"] ) {
				$this->_having = havingClause;
			}

			/**
			 * Assign ORDER clause
			 */
			if ( fetch orderClause, params["order"] ) {
				$this->_order = orderClause;
			}

			/**
			 * Assign LIMIT clause
			 */
			if ( fetch limitClause, params["limit"] ) {
				if ( gettype($limitClause) == "array" ) {
					if ( fetch limit, limitClause[0] ) {
						if ( is_int(limit) ) {
							$this->_limit = limit;
						}
						if ( fetch offset, limitClause[1] ) {
							if ( is_int(offset) ) {
								$this->_offset = offset;
							}
						}
					} else {
						$this->_limit = limitClause;
					}
				} else {
					$this->_limit = limitClause;
				}
			}

			/**
			 * Assign OFFSET clause
			 */
			if ( fetch offsetClause, params["offset"] ) {
				$this->_offset = offsetClause;
			}

			/**
			 * Assign FOR UPDATE clause
			 */
			if ( fetch for (Update, params["for (_update"] ) ) {
				$this->_for (Update = for (Update;
			}

			/**
			 * Assign SHARED LOCK clause
			 */
			if ( fetch sharedLock, params["shared_lock"] ) {
				$this->_sharedLock = sharedLock;
			}
		} else {
			if ( gettype($params) == "string" && params !== "" ) {
				$this->_conditions = params;
			}
		}

		/**
		 * Update the dependency injector if ( any
		 */
		if ( gettype($dependencyInjector) == "object" ) {
			$this->_dependencyInjector = dependencyInjector;
		}
    }

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
		return this;
    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets SELECT DISTINCT / SELECT ALL flag
	 *
	 *<code>
	 * $builder->distinct("status");
	 * $builder->distinct(null);
	 *</code>
	 **/
    public function distinct($distinct ) {
	 	$this->_distinct = distinct;
	 	return this;
    }

    /***
	 * Returns SELECT DISTINCT / SELECT ALL flag
	 **/
    public function getDistinct() {
		return $this->_distinct;
    }

    /***
	 * Sets the columns to be queried
	 *
	 *<code>
	 * $builder->columns("id, name");
	 *
	 * $builder->columns(
	 *     [
	 *         "id",
	 *         "name",
	 *     ]
	 * );
	 *
	 * $builder->columns(
	 *     [
	 *         "name",
	 *         "number" => "COUNT(*)",
	 *     ]
	 * );
	 *</code>
	 **/
    public function columns($columns ) {
		$this->_columns = columns;
		return this;
    }

    /***
	 * Return the columns to be queried
	 *
	 * @return string|array
	 **/
    public function getColumns() {
		return $this->_columns;
    }

    /***
	 * Sets the models who makes part of the query
	 *
	 *<code>
	 * $builder->from("Robots");
	 *
	 * $builder->from(
	 *     [
	 *         "Robots",
	 *         "RobotsParts",
	 *     ]
	 * );
	 *
	 * $builder->from(
	 *     [
	 *         "r"  => "Robots",
	 *         "rp" => "RobotsParts",
	 *     ]
	 * );
	 *</code>
	 **/
    public function from($models ) {
		$this->_models = models;
		return this;
    }

    /***
	 * Add a model to take part of the query
	 *
	 * NOTE: The third parameter $with is deprecated and will be removed in future releases.
	 *
	 *<code>
	 * // Load data from models Robots
	 * $builder->addFrom("Robots");
	 *
	 * // Load data from model 'Robots' using 'r' as alias in PHQL
	 * $builder->addFrom("Robots", "r");
	 *</code>
	 **/
    public function addFrom($model , $alias  = null , $with  = null ) {

		if ( gettype($with) != "null" ) {
			trigger_error(
				"The third parameter 'with' is deprecated and will be removed in future releases.",
				E_DEPRECATED
			);
		}

		$models = $this->_models;
		if ( gettype($models) != "array" ) {
			if ( gettype($models) != "null" ) {
				$currentModel = models,
					models = [currentModel];
			} else {
				$models = [];
			}
		}

		if ( gettype($alias) == "string" ) {
			$models[alias] = model;
		} else {
			$models[] = model;
		}

		$this->_models = models;
		return this;
    }

    /***
	 * Return the models who makes part of the query
	 *
	 * @return string|array
	 **/
    public function getFrom() {
		return $this->_models;
    }

    /***
	 * Adds an :type: join (by default type - INNER) to the query
	 *
	 *<code>
	 * // Inner Join model 'Robots' with automatic conditions and alias
	 * $builder->join("Robots");
	 *
	 * // Inner Join model 'Robots' specifying conditions
	 * $builder->join("Robots", "Robots.id = RobotsParts.robots_id");
	 *
	 * // Inner Join model 'Robots' specifying conditions and alias
	 * $builder->join("Robots", "r.id = RobotsParts.robots_id", "r");
	 *
	 * // Left Join model 'Robots' specifying conditions, alias and type of join
	 * $builder->join("Robots", "r.id = RobotsParts.robots_id", "r", "LEFT");
	 *</code>
	 *
	 * @param string model
	 * @param string conditions
	 * @param string alias
	 * @param string type
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function join($model , $conditions  = null , $alias  = null , $type  = null ) {
		$this->_joins[] = [model, conditions, alias, type];
		return this;
    }

    /***
	 * Adds an INNER join to the query
	 *
	 *<code>
	 * // Inner Join model 'Robots' with automatic conditions and alias
	 * $builder->innerJoin("Robots");
	 *
	 * // Inner Join model 'Robots' specifying conditions
	 * $builder->innerJoin("Robots", "Robots.id = RobotsParts.robots_id");
	 *
	 * // Inner Join model 'Robots' specifying conditions and alias
	 * $builder->innerJoin("Robots", "r.id = RobotsParts.robots_id", "r");
	 *</code>
	 *
	 * @param string model
	 * @param string conditions
	 * @param string alias
	 * @param string type
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function innerJoin($model , $conditions  = null , $alias  = null ) {
		$this->_joins[] = [model, conditions, alias, "INNER"];
		return this;
    }

    /***
	 * Adds a LEFT join to the query
	 *
	 *<code>
	 * $builder->leftJoin("Robots", "r.id = RobotsParts.robots_id", "r");
	 *</code>
	 *
	 * @param string model
	 * @param string conditions
	 * @param string alias
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function leftJoin($model , $conditions  = null , $alias  = null ) {
		$this->_joins[] = [model, conditions, alias, "LEFT"];
		return this;
    }

    /***
	 * Adds a RIGHT join to the query
	 *
	 *<code>
	 * $builder->rightJoin("Robots", "r.id = RobotsParts.robots_id", "r");
	 *</code>
	 *
	 * @param string model
	 * @param string conditions
	 * @param string alias
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function rightJoin($model , $conditions  = null , $alias  = null ) {
		$this->_joins[] = [model, conditions, alias, "RIGHT"];
		return this;
    }

    /***
	 * Return join parts of the query
	 *
	 * @return array
	 **/
    public function getJoins() {
		return $this->_joins;
    }

    /***
	 * Sets the query WHERE conditions
	 *
	 *<code>
	 * $builder->where(100);
	 *
	 * $builder->where("name = 'Peter'");
	 *
	 * $builder->where(
	 *     "name = :name: AND id > :id:",
	 *     [
	 *         "name" => "Peter",
	 *         "id"   => 100,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param mixed conditions
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function where($conditions , $bindParams  = null , $bindTypes  = null ) {

		$this->_conditions = conditions;

		/**
		 * Merge the bind params to the current ones
		 */
		if ( gettype($bindParams) == "array" ) {
			$currentBindParams = $this->_bindParams;
			if ( gettype($currentBindParams) == "array" ) {
				$this->_bindParams = currentBindParams + bindParams;
			} else {
				$this->_bindParams = bindParams;
			}
		}

		/**
		 * Merge the bind types to the current ones
		 */
		if ( gettype($bindTypes) == "array" ) {
			$currentBindTypes = $this->_bindTypes;
			if ( gettype($currentBindParams) == "array" ) {
				$this->_bindTypes = currentBindTypes + bindTypes;
			} else {
				$this->_bindTypes = bindTypes;
			}
		}

		return this;
    }

    /***
	 * Appends a condition to the current WHERE conditions using a AND operator
	 *
	 *<code>
	 * $builder->andWhere("name = 'Peter'");
	 *
	 * $builder->andWhere(
	 *     "name = :name: AND id > :id:",
	 *     [
	 *         "name" => "Peter",
	 *         "id"   => 100,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param string conditions
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function andWhere($conditions , $bindParams  = null , $bindTypes  = null ) {

		$currentConditions = $this->_conditions;

		/**
		 * Nest the condition to current ones or set as unique
		 */
		if ( currentConditions ) {
			$conditions = "(" . currentConditions . ") AND (" . conditions . ")";
		}

		return $this->where(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a condition to the current conditions using an OR operator
	 *
	 *<code>
	 * $builder->orWhere("name = 'Peter'");
	 *
	 * $builder->orWhere(
	 *     "name = :name: AND id > :id:",
	 *     [
	 *         "name" => "Peter",
	 *         "id"   => 100,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param string conditions
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function orWhere($conditions , $bindParams  = null , $bindTypes  = null ) {

		$currentConditions = $this->_conditions;

		/**
		 * Nest the condition to current ones or set as unique
		 */
		if ( currentConditions ) {
			$conditions = "(" . currentConditions . ") OR (" . conditions . ")";
		}

		return $this->where(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a BETWEEN condition to the current WHERE conditions
	 *
	 *<code>
	 * $builder->betweenWhere("price", 100.25, 200.50);
	 *</code>
	 **/
    public function betweenWhere($expr , $minimum , $maximum , $operator ) {
		return $this->_conditionBetween("Where", operator, expr, minimum, maximum);
    }

    /***
	 * Appends a NOT BETWEEN condition to the current WHERE conditions
	 *
	 *<code>
	 * $builder->notBetweenWhere("price", 100.25, 200.50);
	 *</code>
	 **/
    public function notBetweenWhere($expr , $minimum , $maximum , $operator ) {
		return $this->_conditionNotBetween("Where", operator, expr, minimum, maximum);
    }

    /***
	 * Appends an IN condition to the current WHERE conditions
	 *
	 *<code>
	 * $builder->inWhere("id", [1, 2, 3]);
	 *</code>
	 **/
    public function inWhere($expr , $values , $operator ) {
		return $this->_conditionIn("Where", operator, expr, values);
    }

    /***
	 * Appends a NOT IN condition to the current WHERE conditions
	 *
	 *<code>
	 * $builder->notInWhere("id", [1, 2, 3]);
	 *</code>
	 **/
    public function notInWhere($expr , $values , $operator ) {
		return $this->_conditionNotIn("Where", operator, expr, values);
    }

    /***
	 * Return the conditions for the query
	 *
	 * @return string|array
	 **/
    public function getWhere() {
		return $this->_conditions;
    }

    /***
	 * Sets an ORDER BY condition clause
	 *
	 *<code>
	 * $builder->orderBy("Robots.name");
	 * $builder->orderBy(["1", "Robots.name"]);
	 * $builder->orderBy(["Robots.name DESC"]);
	 *</code>
	 *
	 * @param string|array orderBy
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function orderBy($orderBy ) {
		$this->_order = orderBy;
		return this;
    }

    /***
	 * Returns the set ORDER BY clause
	 *
	 * @return string|array
	 **/
    public function getOrderBy() {
		return $this->_order;
    }

    /***
	 * Sets the HAVING condition clause
	 *
	 *<code>
	 * $builder->having("SUM(Robots.price) > 0");
	 *
	 * $builder->having(
	 * 		"SUM(Robots.price) > :sum:",
	 *   	[
	 *    		"sum" => 100,
	 *      ]
	 * );
	 *</code>
	 *
	 * @param mixed conditions
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function having($conditions , $bindParams  = null , $bindTypes  = null ) {

		$this->_having = conditions;

		/**
		 * Merge the bind params to the current ones
		 */
		if ( gettype($bindParams) == "array" ) {
			$currentBindParams = $this->_bindParams;
			if ( gettype($currentBindParams) == "array" ) {
				$this->_bindParams = currentBindParams + bindParams;
			} else {
				$this->_bindParams = bindParams;
			}
		}

		/**
		 * Merge the bind types to the current ones
		 */
		if ( gettype($bindTypes) == "array" ) {
			$currentBindTypes = $this->_bindTypes;
			if ( gettype($currentBindParams) == "array" ) {
				$this->_bindTypes = currentBindTypes + bindTypes;
			} else {
				$this->_bindTypes = bindTypes;
			}
		}

		return this;
    }

    /***
	 * Appends a condition to the current HAVING conditions clause using a AND operator
	 *
	 *<code>
	 * $builder->andHaving("SUM(Robots.price) > 0");
	 *
	 * $builder->andHaving(
	 * 		"SUM(Robots.price) > :sum:",
	 *   	[
	 *    		"sum" => 100,
	 *      ]
	 * );
	 *</code>
	 *
	 * @param string conditions
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function andHaving($conditions , $bindParams  = null , $bindTypes  = null ) {

		$currentConditions = $this->_having;

		/**
		 * Nest the condition to current ones or set as unique
		 */
		if ( currentConditions ) {
			$conditions = "(" . currentConditions . ") AND (" . conditions . ")";
		}

		return $this->having(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a condition to the current HAVING conditions clause using an OR operator
	 *
	 *<code>
	 * $builder->orHaving("SUM(Robots.price) > 0");
	 *
	 * $builder->orHaving(
	 * 		"SUM(Robots.price) > :sum:",
	 *   	[
	 *    		"sum" => 100,
	 *      ]
	 * );
	 *</code>
	 *
	 * @param string conditions
	 * @param array bindParams
	 * @param array bindTypes
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function orHaving($conditions , $bindParams  = null , $bindTypes  = null ) {

		$currentConditions = $this->_having;

		/**
		 * Nest the condition to current ones or set as unique
		 */
		if ( currentConditions ) {
			$conditions = "(" . currentConditions . ") OR (" . conditions . ")";
		}

		return $this->having(conditions, bindParams, bindTypes);
    }

    /***
	 * Appends a BETWEEN condition to the current HAVING conditions clause
	 *
	 *<code>
	 * $builder->betweenHaving("SUM(Robots.price)", 100.25, 200.50);
	 *</code>
	 **/
    public function betweenHaving($expr , $minimum , $maximum , $operator ) {
		return $this->_conditionBetween("Having", operator, expr, minimum, maximum);
    }

    /***
	 * Appends a NOT BETWEEN condition to the current HAVING conditions clause
	 *
	 *<code>
	 * $builder->notBetweenHaving("SUM(Robots.price)", 100.25, 200.50);
	 *</code>
	 **/
    public function notBetweenHaving($expr , $minimum , $maximum , $operator ) {
		return $this->_conditionNotBetween("Having", operator, expr, minimum, maximum);
    }

    /***
	 * Appends an IN condition to the current HAVING conditions clause
	 *
	 *<code>
	 * $builder->inHaving("SUM(Robots.price)", [100, 200]);
	 *</code>
	 **/
    public function inHaving($expr , $values , $operator ) {
		return $this->_conditionIn("Having", operator, expr, values);
    }

    /***
	 * Appends a NOT IN condition to the current HAVING conditions clause
	 *
	 *<code>
	 * $builder->notInHaving("SUM(Robots.price)", [100, 200]);
	 *</code>
	 **/
    public function notInHaving($expr , $values , $operator ) {
		return $this->_conditionNotIn("Having", operator, expr, values);
    }

    /***
	 * Return the current having clause
	 *
	 * @return string
	 **/
    public function getHaving() {
		return $this->_having;
    }

    /***
	 * Sets a FOR UPDATE clause
	 *
	 *<code>
	 * $builder->forUpdate(true);
	 *</code>
	 **/
    public function forUpdate($forUpdate ) {
		$this->_for (Update = for (Update;
		return this;
    }

    /***
	 * Sets a LIMIT clause, optionally an offset clause
	 *
	 * <code>
	 * $builder->limit(100);
	 * $builder->limit(100, 20);
	 * $builder->limit("100", "20");
	 * </code>
	 **/
    public function limit($limit , $offset  = null ) {
		$limit = abs(limit);

		if ( unlikely limit == 0 ) {
			return this;
		}

		$this->_limit = limit;

		if ( is_numeric(offset) ) {
			$this->_offset = abs((int) offset);
		}

		return this;
    }

    /***
	 * Returns the current LIMIT clause
	 *
	 * @return string|array
	 **/
    public function getLimit() {
		return $this->_limit;
    }

    /***
	 * Sets an OFFSET clause
	 *
	 *<code>
	 * $builder->offset(30);
	 *</code>
	 **/
    public function offset($offset ) {
		$this->_offset = offset;
		return this;
    }

    /***
	 * Returns the current OFFSET clause
	 *
	 * @return string|array
	 **/
    public function getOffset() {
		return $this->_offset;
    }

    /***
	 * Sets a GROUP BY clause
	 *
	 *<code>
	 * $builder->groupBy(
	 *     [
	 *         "Robots.name",
	 *     ]
	 * );
	 *</code>
	 *
	 * @param string|array group
	 * @return \Phalcon\Mvc\Model\Query\Builder
	 **/
    public function groupBy($group ) {
		$this->_group = group;
		return this;
    }

    /***
	 * Returns the GROUP BY clause
	 *
	 * @return string
	 **/
    public function getGroupBy() {
		return $this->_group;
    }

    /***
	 * Returns a PHQL statement built based on the builder parameters
	 *
	 * @return string
	 **/
    public final function getPhql() {
			modelInstance, primaryKeys, firstPrimaryKey, columnMap, modelAlias,
			attributeField, phql, column, columns, selectedColumns, selectedColumn,
			selectedModel, selectedModels, columnAlias, modelColumnAlias,
			joins, join, joinModel, joinConditions, joinAlias, joinType, group,
			groupItems, groupItem, having, order, orderItems, orderItem,
			limit, number, offset, for (Update, distinct;
		boolean noPrimary;

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = Di::getDefault(),
				this->_dependencyInjector = dependencyInjector;
		}

		$models = $this->_models;
		if ( gettype($models) == "array" ) {
			if ( !count(models) ) {
				throw new Exception("At least one model is required to build the query");
			}
		} else {
			if ( !models ) {
				throw new Exception("At least one model is required to build the query");
			}
		}

		$conditions = $this->_conditions;

		if ( is_numeric(conditions) ) {

			/**
			 * If the conditions is a single numeric field. We internally create a condition using the related primary key
			 */
			if ( gettype($models) == "array" ) {
				if ( count(models) > 1 ) {
					throw new Exception("Cannot build the query. Invalid condition");
				}
				$model = models[0];
			} else {
				$model = models;
			}

			/**
			 * Get the models metadata service to obtain the column names, column map and primary key
			 */
			$metaData = dependencyInjector->getShared("modelsMetadata"),
				modelInstance = new {model}(null, dependencyInjector);

			$noPrimary = true,
				primaryKeys = metaData->getPrimaryKeyAttributes(modelInstance);
			if ( count(primaryKeys) ) {

				if ( fetch firstPrimaryKey, primaryKeys[0] ) {

					/**
					 * The PHQL contains the renamed columns if ( available
					 */
					if ( globals_get("orm.column_renaming") ) {
						$columnMap = metaData->getColumnMap(modelInstance);
					} else {
						$columnMap = null;
					}

					if ( gettype($columnMap) == "array" ) {
						if ( !fetch attributeField, columnMap[firstPrimaryKey] ) {
							throw new Exception("Column '" . firstPrimaryKey . "' isn't part of the column map");
						}
					} else {
						$attributeField = firstPrimaryKey;
					}

					$conditions = $this->autoescape(model) . "." . $this->autoescape(attributeField) . " = " . conditions,
						noPrimary = false;
				}
			}

			/**
			 * A primary key is mandatory in these cases
			 */
			if ( noPrimary === true ) {
				throw new Exception("Source related to this model does not have a primary key defined");
			}
		}

		$distinct = $this->_distinct;
		if ( gettype($distinct) != "null" && gettype($distinct) == "bool" ) {
			if ( distinct ) {
				$phql = "SELECT DISTINCT ";
			} else {
				$phql = "SELECT ALL ";
			}
		} else {
			$phql = "SELECT ";
		}

		$columns = $this->_columns;
		if ( gettype($columns) !== "null" ) {

			/**
			 * Generate PHQL for ( columns
			 */
			if ( gettype($columns) == "array" ) {

				$selectedColumns = [];
				foreach ( columnAlias, $columns as $column ) {
					if ( gettype($columnAlias) == "integer" ) {
						$selectedColumns[] = column;
					} else {
						$selectedColumns[] = column . " AS " . $this->autoescape(columnAlias);
					}
				}

				$phql .= join(", ", selectedColumns);

			} else {
				$phql .= columns;
			}

		} else {

			/**
			 * Automatically generate an array of models
			 */
			if ( gettype($models) == "array" ) {

				$selectedColumns = [];
				foreach ( modelColumnAlias, $models as $model ) {
					if ( gettype($modelColumnAlias) == "integer" ) {
						$selectedColumn = $this->autoescape(model) . ".*";
					} else {
						$selectedColumn = $this->autoescape(modelColumnAlias) . ".*";
					}
					$selectedColumns[] = selectedColumn;
				}

				$phql .= join(", ", selectedColumns);
			} else {
				$phql .= $this->autoescape(models) . ".*";
			}
		}

		/**
		 * Join multiple models or use a single one if ( it is a string
		 */
		if ( gettype($models) == "array" ) {

			$selectedModels = [];
			foreach ( modelAlias, $models as $model ) {

				if ( gettype($modelAlias) == "string" ) {
					$selectedModel = $this->autoescape(model) . " AS " . $this->autoescape(modelAlias);
				} else {
					$selectedModel = $this->autoescape(model);
				}

				$selectedModels[] = selectedModel;
			}

			$phql .= " FROM " . join(", ", selectedModels);

		} else {
			$phql .= " FROM " . $this->autoescape(models);
		}

		/**
		 * Check if ( joins were passed to the builders
		 */
		$joins = $this->_joins;
		if ( gettype($joins) == "array" ) {

			foreach ( $joins as $join ) {

				/**
				 * The joined table is in the first place of the array
				 */
				$joinModel = join[0];

				/**
				 * The join conditions are in the second place of the array
				 */
				$joinConditions = join[1];

				/**
				 * The join alias is in the second place of the array
				 */
				$joinAlias = join[2];

				/**
				 * Join type
				 */
				$joinType = join[3];

				/**
				 * Create the join according to the type
				 */
				if ( joinType ) {
					$phql .= " " . joinType . " JOIN " . $this->autoescape(joinModel);
				} else {
					$phql .= " JOIN " . $this->autoescape(joinModel);
				}

				/**
				 * Alias comes first
				 */
				if ( joinAlias ) {
					$phql .= " AS " . $this->autoescape(joinAlias);
				}

				/**
				 * Conditions then
				 */
				if ( joinConditions ) {
					$phql .= " ON " . joinConditions;
				}
			}
		}

		// Only append where conditions if ( it's string
		if ( gettype($conditions) == "string" ) {
			if ( !empty conditions ) {
				$phql .= " WHERE " . conditions;
			}
		}

		/**
		 * Process group parameters
		 */
		$group = $this->_group;
		if ( group !== null ) {
			if ( gettype($group) == "string" ) {
				if ( memstr(group, ",") ) {
					$group = str_replace(" ", "", group);
				}

				$group = explode(",", group);
			}

			$groupItems = [];
			foreach ( $group as $groupItem ) {
				$groupItems[] = $this->autoescape(groupItem);
			}

			$phql .= " GROUP BY " . join(", ", groupItems);
		}

		/**
		 * Process having clause
		 */
		$having = $this->_having;
		if ( having !== null ) {
			if ( !empty having ) {
				$phql .= " HAVING " . having;
			}
		}

		/**
		 * Process order clause
		 */
		$order = $this->_order;
		if ( order !== null ) {
			if ( gettype($order) == "array" ) {
				$orderItems = [];
				foreach ( $order as $orderItem ) {
					/**
					 * For case 'ORDER BY 1'
					 */
					if ( gettype($orderItem) == "integer" ) {
						$orderItems[] = orderItem;

						continue;
					}

					if ( memstr(orderItem, " ") !== 0 ) {
						$itemExplode = explode(" ", orderItem);
						$orderItems[] = $this->autoescape(itemExplode[0]) . " " . itemExplode[1];

						continue;
					}

					$orderItems[] = $this->autoescape(orderItem);
				}

				$phql .= " ORDER BY " . join(", ", orderItems);
			} else {
				$phql .= " ORDER BY " . order;
			}
		}

		/**
		 * Process limit parameters
		 */
		$limit = $this->_limit;
		if ( limit !== null ) {

			$number = null;
			if ( gettype($limit) == "array" ) {

				$number = limit["number"];
				if ( fetch offset, limit["offset"] ) {
					if ( !is_numeric(offset) ) {
						$offset = 0;
					}
				}

			} else {
				if ( is_numeric(limit) ) {
					$number = limit,
						offset = $this->_offset;
					if ( offset !== null ) {
						if ( !is_numeric(offset) ) {
							$offset = 0;
						}
					}
				}
			}

			if ( is_numeric(number) ) {

				$phql .= " LIMIT :APL0:",
					this->_bindParams["APL0"] = intval(number, 10),
					this->_bindTypes["APL0"] = Column::BIND_PARAM_INT;

				if ( is_numeric(offset) ) {
					$phql .= " OFFSET :APL1:",
						this->_bindParams["APL1"] = intval(offset, 10),
						this->_bindTypes["APL1"] = Column::BIND_PARAM_INT;
				}
			}
		}

		$for (Update = $this->_for (Update;
		if ( gettype($for (Update) === "boolean" ) ) {
			if ( for (Update ) ) {
				$phql .= " FOR UPDATE";
			}
		}

		return phql;
    }

    /***
	 * Returns the query built
	 **/
    public function getQuery() {

		$phql = $this->getPhql();

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection object is required to access ORM services");
		}

		/**
		 * Gets Query instance from DI container
		 */
		$query = <QueryInterface> dependencyInjector->get(
			"Phalcon\\Mvc\\Model\\Query",
			[phql, dependencyInjector]
		);

		// Set default bind params
		$bindParams = $this->_bindParams;
		if ( gettype($bindParams) == "array" ) {
			query->setBindParams(bindParams);
		}

		// Set default bind params
		$bindTypes = $this->_bindTypes;
		if ( gettype($bindTypes) == "array" ) {
			query->setBindTypes(bindTypes);
		}

		if ( gettype($this->_sharedLock) === "boolean" ) {
			query->setSharedLock(this->_sharedLock);
		}

		return query;
    }

    /***
	 * Automatically escapes identifiers but only if they need to be escaped.
	 **/
    final public function autoescape($identifier ) {
		if ( memstr(identif (ier, "[") || memstr(identif (ier, ".") || is_numeric(identif (ier) ) {
			return identif (ier;
		}

		return "[" . identif (ier . "]";
    }

    /***
	 * Appends a BETWEEN condition
	 **/
    protected function _conditionBetween($clause , $operator , $expr , $minimum , $maximum ) {

		if ( (operator !== Builder::OPERATOR_AND && operator !== Builder::OPERATOR_OR) ) {
			throw new Exception(sprintf("Operator % is not available.", operator));
		}

		$operatorMethod = operator . clause;

		$hiddenParam = $this->_hiddenParamNumber,
			nextHiddenParam = hiddenParam + 1;

		/**
		 * Minimum key with auto bind-params and
		 * Maximum key with auto bind-params
		 */
		$minimumKey = "AP" . hiddenParam,
			maximumKey = "AP" . nextHiddenParam;

		/**
		 * Create a standard BETWEEN condition with bind params
		 * Append the BETWEEN to the current conditions using and "and"
		 */

		this->{operatorMethod}(
			expr . " BETWEEN :" . minimumKey . ": AND :" . maximumKey . ":",
			[minimumKey: minimum, maximumKey: maximum]
		);

		$nextHiddenParam++,
			this->_hiddenParamNumber = nextHiddenParam;

		return this;
    }

    /***
	 * Appends a NOT BETWEEN condition
	 **/
    protected function _conditionNotBetween($clause , $operator , $expr , $minimum , $maximum ) {

		if ( (operator !== Builder::OPERATOR_AND && operator !== Builder::OPERATOR_OR) ) {
			throw new Exception(sprintf("Operator % is not available.", operator));
		}

		$operatorMethod = operator . clause;

		$hiddenParam = $this->_hiddenParamNumber,
			nextHiddenParam = hiddenParam + 1;

		/**
		 * Minimum key with auto bind-params and
		 * Maximum key with auto bind-params
		 */
		$minimumKey = "AP" . hiddenParam,
			maximumKey = "AP" . nextHiddenParam;

		/**
		 * Create a standard BETWEEN condition with bind params
		 * Append the NOT BETWEEN to the current conditions using and "and"
		 */
		this->{operatorMethod}(
			expr . " NOT BETWEEN :" . minimumKey . ": AND :" . maximumKey . ":",
			[minimumKey: minimum, maximumKey: maximum]
		);

		$nextHiddenParam++,
			this->_hiddenParamNumber = nextHiddenParam;

		return this;
    }

    /***
	 * Appends an IN condition
	 **/
    protected function _conditionIn($clause , $operator , $expr , $values ) {
		int hiddenParam;

		if ( (operator !== Builder::OPERATOR_AND && operator !== Builder::OPERATOR_OR) ) {
			throw new Exception(sprintf("Operator % is not available.", operator));
		}

		$operatorMethod = operator . clause;

		if ( !count(values) ) {
			this->{operatorMethod}(expr . " != " . expr);
			return this;
		}

		$hiddenParam = (int) $this->_hiddenParamNumber;

		$bindParams = [], bindKeys = [];
		foreach ( $values as $value ) {

			/**
			 * Key with auto bind-params
			 */
			$key = "AP" . hiddenParam,
				queryKey = ":" . key . ":",
				bindKeys[] = queryKey,
				bindParams[key] = value,
				hiddenParam++;
		}

		/**
		 * Create a standard IN condition with bind params
		 * Append the IN to the current conditions using and "and"
		 */
		this->{operatorMethod}(expr . " IN (" . join(", ", bindKeys) . ")", bindParams);

		$this->_hiddenParamNumber = hiddenParam;

		return this;
    }

    /***
	 * Appends a NOT IN condition
	 **/
    protected function _conditionNotIn($clause , $operator , $expr , $values ) {
		int hiddenParam;

		if ( (operator !== Builder::OPERATOR_AND && operator !== Builder::OPERATOR_OR) ) {
			throw new Exception(sprintf("Operator % is not available.", operator));
		}

		$operatorMethod = operator . clause;

		if ( !count(values) ) {
			this->{operatorMethod}(expr . " != " . expr);
			return this;
		}

		$hiddenParam = (int) $this->_hiddenParamNumber;

		$bindParams = [], bindKeys = [];
		foreach ( $values as $value ) {

			/**
			 * Key with auto bind-params
			 */
			$key = "AP" . hiddenParam,
				queryKey = ":" . key . ":",
				bindKeys[] = queryKey,
				bindParams[key] = value,
				hiddenParam++;
		}

		/**
		 * Create a standard NOT IN condition with bind params
		 * Append the NOT IN to the current conditions using and "and"
		 */
		this->{operatorMethod}(expr . " NOT IN (" . join(", ", bindKeys) . ")", bindParams);

		$this->_hiddenParamNumber = hiddenParam;

		return this;
    }

}