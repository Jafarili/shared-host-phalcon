<?php


namespace Phalcon\Db;



/***
 * Phalcon\Db\Dialect
 *
 * This is the base class to each database dialect. This implements
 * common methods to transform intermediate code into its RDBMS related syntax
 **/

abstract class Dialect {

    protected $_escapeChar;

    protected $_customFunctions;

    /***
	 * Registers custom SQL functions
	 **/
    public function registerCustomFunction($name , $customFunction ) {
		$this->_customFunctions[name] = customFunction;
		return this;
    }

    /***
	 * Returns registered functions
	 **/
    public function getCustomFunctions() {
		return $this->_customFunctions;
    }

    /***
	 * Escape Schema
	 **/
    public final function escapeSchema($str , $escapeChar  = null ) {
		if ( !globals_get("db.escape_identif (iers") ) {
			return str;
		}

		if ( escapeChar == "" ) {
			$escapeChar = (string) $this->_escapeChar;
		}

		return escapeChar . trim(str, escapeChar) . escapeChar;
    }

    /***
	 * Escape identifiers
	 **/
    public final function escape($str , $escapeChar  = null ) {

		if ( !globals_get("db.escape_identif (iers") ) {
			return str;
		}

		if ( escapeChar == "" ) {
			$escapeChar = (string) $this->_escapeChar;
		}

		if ( !memstr(str, ".") ) {

			if ( escapeChar != "" && str != "*" ) {
				return escapeChar . str_replace(escapeChar, escapeChar . escapeChar, str) . escapeChar;
			}

			return str;
		}

		$parts = (array) explode(".", trim(str, escapeChar));

		$newParts = parts;
		foreach ( key, $parts as $part ) {

			if ( escapeChar == "" || part == "" || part == "*" ) {
				continue;
			}

			$newParts[key] = escapeChar . str_replace(escapeChar, escapeChar . escapeChar, part) . escapeChar;
		}

		return implode(".", newParts);
    }

    /***
	 * Generates the SQL for LIMIT clause
	 *
	 * <code>
	 * $sql = $dialect->limit("SELECT * FROM robots", 10);
	 * echo $sql; // SELECT * FROM robots LIMIT 10
	 *
	 * $sql = $dialect->limit("SELECT * FROM robots", [10, 50]);
	 * echo $sql; // SELECT * FROM robots LIMIT 10 OFFSET 50
	 * </code>
	 **/
    public function limit($sqlQuery , $number ) {
		if ( gettype($number) == "array" ) {

			$sqlQuery .= " LIMIT " . number[0];

			if ( isset($number[1]) && strlen(number[1]) ) {
				$sqlQuery .= " OFFSET " . number[1];
			}

			return sqlQuery;
		}

		return sqlQuery . " LIMIT " . number;
    }

    /***
	 * Returns a SQL modified with a FOR UPDATE clause
	 *
	 *<code>
	 * $sql = $dialect->forUpdate("SELECT * FROM robots");
	 * echo $sql; // SELECT * FROM robots FOR UPDATE
	 *</code>
	 **/
    public function forUpdate($sqlQuery ) {
		return sqlQuery . " FOR UPDATE";
    }

    /***
	 * Returns a SQL modified with a LOCK IN SHARE MODE clause
	 *
	 *<code>
	 * $sql = $dialect->sharedLock("SELECT * FROM robots");
	 * echo $sql; // SELECT * FROM robots LOCK IN SHARE MODE
	 *</code>
	 **/
    public function sharedLock($sqlQuery ) {
		return sqlQuery . " LOCK IN SHARE MODE";
    }

    /***
	 * Gets a list of columns with escaped identifiers
	 *
	 * <code>
	 * echo $dialect->getColumnList(
	 *     [
	 *         "column1",
	 *         "column",
	 *     ]
	 * );
	 * </code>
	 **/
    public final function getColumnList($columnList , $escapeChar  = null , $bindCounts  = null ) {
		$columns = [];

		foreach ( $columnList as $column ) {
			$columns[] = $this->getSqlColumn(column, escapeChar, bindCounts);
		}

		return join(", ", columns);
    }

    /***
	 * Resolve Column expressions
	 **/
    public final function getSqlColumn($column , $escapeChar  = null , $bindCounts  = null ) {

		if ( gettype($column) != "array" ) {
			return $this->prepareQualif (ied(column, null, escapeChar);
		}

		if ( !isset column["type"] ) {

			/**
			 * The index "0" is the column field
			 */
			$columnField = column[0];

			if ( gettype($columnField) == "array" ) {
				$columnExpression = [
					"type": "scalar",
					"value": columnField
				];

			} elseif ( columnField == "*" ) {
				$columnExpression = [
					"type": "all"
				];

			} else {
				$columnExpression = [
					"type": "qualif (ied",
					"name": columnField
				];
			}

			/**
			 * The index "1" is the domain column
			 */
			if ( fetch columnDomain, column[1] && columnDomain != "" ) {
				$columnExpression["domain"] = columnDomain;
			}

			/**
			 * The index "2" is the column alias
			 */
			if ( fetch columnAlias, column[2] && columnAlias ) {
				$columnExpression["sqlAlias"] = columnAlias;
			}
		} else {
			$columnExpression = column;
		}

		/**
		 * Resolve column expressions
		 */
		$column = $this->getSqlExpression(columnExpression, escapeChar, bindCounts);

		/**
		 * Escape alias and concatenate to value SQL
		 */
		if ( fetch columnAlias, columnExpression["sqlAlias"] || fetch columnAlias, columnExpression["alias"] ) {
			return $this->prepareColumnAlias(column, columnAlias, escapeChar);
		}

		return $this->prepareColumnAlias(column, null, escapeChar);
    }

    /***
	 * Transforms an intermediate representation for an expression into a database system valid expression
	 **/
    public function getSqlExpression($expression , $escapeChar  = null , $bindCounts  = null ) {
		int i;

		if ( !fetch type, expression["type"] ) {
			throw new Exception("Invalid SQL expression");
		}

		switch type {

			/**
			 * Resolve scalar column expressions
			 */
			case "scalar":
				return $this->getSqlExpressionScalar(expression, escapeChar, bindCounts);

			/**
			 * Resolve object expressions
			 */
			case "object":
				return $this->getSqlExpressionObject(expression, escapeChar, bindCounts);

			/**
			 * Resolve qualif (ied expressions
			 */
			case "qualif (ied":
				return $this->getSqlExpressionQualif (ied(expression, escapeChar);

			/**
			 * Resolve literal OR placeholder expressions
			 */
			case "literal":
				return expression["value"];

			case "placeholder":
				if ( fetch times, expression["times"] ) {

					$placeholders = [],
						rawValue = expression["rawValue"],
						value = expression["value"];

					if ( fetch postTimes, bindCounts[rawValue] ) {
						$times = postTimes;
					}

					for ( i in range(1, times) ) {
						$placeholders[] = value . (i - 1);
					}

					return join(", ", placeholders);
				}
				return expression["value"];

			/**
			 * Resolve binary operations expressions
			 */
			case "binary-op":
				return $this->getSqlExpressionBinaryOperations(expression, escapeChar, bindCounts);

			/**
			 * Resolve unary operations expressions
			 */
			case "unary-op":
				return $this->getSqlExpressionUnaryOperations(expression, escapeChar, bindCounts);

			/**
			 * Resolve parentheses
			 */
			case "parentheses":
				return "(" . $this->getSqlExpression(expression["left"], escapeChar, bindCounts) . ")";

			/**
			 * Resolve function calls
			 */
			case "functionCall":
				return $this->getSqlExpressionFunctionCall(expression, escapeChar, bindCounts);

			/**
			 * Resolve lists
			 */
			case "list":
				return $this->getSqlExpressionList(expression, escapeChar, bindCounts);

			/**
			 * Resolve *
			 */
			case "all":
				return $this->getSqlExpressionAll(expression, escapeChar);

			/**
			 * Resolve SELECT
			 */
			case "select":
				return "(" . $this->select(expression["value"]) . ")";

			/**
			 * Resolve CAST of values
			 */
			case "cast":
				return $this->getSqlExpressionCastValue(expression, escapeChar, bindCounts);

			/**
			 * Resolve CONVERT of values encodings
			 */
			case "convert":
				return $this->getSqlExpressionConvertValue(expression, escapeChar, bindCounts);

			case "case":
				return $this->getSqlExpressionCase(expression, escapeChar, bindCounts);
		}

		/**
		 * Expression type wasn't found
		 */
		throw new Exception("Invalid SQL expression type '" . type . "'");
    }

    /***
	 * Transform an intermediate representation of a schema/table into a database system valid expression
	 **/
    public final function getSqlTable($table , $escapeChar  = null ) {

		if ( gettype($table) == "array" ) {

			/**
			 * The index "0" is the table name
			 */
			$tableName = table[0];

			/**
			 * The index "1" is the schema name
			 */

			/**
			 * The index "2" is the table alias
			 */

			return $this->prepareTable(tableName, schemaName, aliasName, escapeChar);
		}

		return $this->escape(table, escapeChar);
    }

    /***
	 * Builds a SELECT statement
	 **/
    public function select($definition ) {
			groupBy, having, orderBy, limit, for (Update, bindCounts;

		if ( !fetch tables, definition["tables"] ) {
			throw new Exception("The index 'tables' is required in the definition array");
		}

		if ( !fetch columns, definition["columns"] ) {
			throw new Exception("The index 'columns' is required in the definition array");
		}

		if ( fetch distinct, definition["distinct"] ) {

			if ( distinct ) {
				$sql = "SELECT DISTINCT";
			} else {
				$sql = "SELECT ALL";
			}

		} else {
			$sql = "SELECT";
		}


		$escapeChar = $this->_escapeChar;

		/**
		 * Resolve COLUMNS
		 */
		$sql .= " " . $this->getColumnList(columns, escapeChar, bindCounts);

		/**
		 * Resolve FROM
		 */
		$sql .= " " . $this->getSqlExpressionFrom(tables, escapeChar);

		/**
		 * Resolve JOINs
		 */
		if ( fetch joins, definition["joins"] && joins ) {
			$sql .= " " . $this->getSqlExpressionJoins(definition["joins"], escapeChar, bindCounts);
		}

		/**
		 * Resolve WHERE
		 */
		if ( fetch where, definition["where"] && where ) {
			$sql .= " " . $this->getSqlExpressionWhere(where, escapeChar, bindCounts);
		}

		/**
		 * Resolve GROUP BY
		 */
		if ( fetch groupBy, definition["group"] && groupBy ) {
			$sql .= " " . $this->getSqlExpressionGroupBy(groupBy, escapeChar);
		}

		/**
		 * Resolve HAVING
		 */
		if ( fetch having, definition["having"] && having ) {
			$sql .= " " . $this->getSqlExpressionHaving(having, escapeChar, bindCounts);
		}

		/**
		 * Resolve ORDER BY
		 */
		if ( fetch orderBy, definition["order"] && orderBy ) {
			$sql .= " " . $this->getSqlExpressionOrderBy(orderBy, escapeChar, bindCounts);
		}

		/**
		 * Resolve LIMIT
		 */
		if ( fetch limit, definition["limit"] && limit ) {
			$sql = $this->getSqlExpressionLimit(["sql": sql, "value": limit], escapeChar, bindCounts);
		}

		/**
		 * Resolve FOR UPDATE
		 */
		if ( fetch for (Update, definition["for (Update"] && for (Update ) ) {
			$sql .= " FOR UPDATE";
		}

		return sql;
    }

    /***
	 * Checks whether the platform supports savepoints
	 **/
    public function supportsSavepoints() {
		return true;
    }

    /***
	 * Checks whether the platform supports releasing savepoints.
	 **/
    public function supportsReleaseSavepoints() {
		return $this->supportsSavePoints();
    }

    /***
	 * Generate SQL to create a new savepoint
	 **/
    public function createSavepoint($name ) {
		return "SAVEPOINT " . name;
    }

    /***
	 * Generate SQL to release a savepoint
	 **/
    public function releaseSavepoint($name ) {
		return "RELEASE SAVEPOINT " . name;
    }

    /***
	 * Generate SQL to rollback a savepoint
	 **/
    public function rollbackSavepoint($name ) {
		return "ROLLBACK TO SAVEPOINT " . name;
    }

    /***
	 * Resolve Column expressions
	 **/
    protected final function getSqlExpressionScalar($expression , $escapeChar  = null , $bindCounts  = null ) {

		if ( isset expression["column"] ) {
			return $this->getSqlColumn(expression["column"]);
		}

		if ( !fetch value, expression["value"] ) {
			throw new Exception("Invalid SQL expression");
		}

		if ( gettype($value) == "array" ) {
			return $this->getSqlExpression(value, escapeChar, bindCounts);
		}

		return value;
    }

    /***
	 * Resolve object expressions
	 **/
    protected final function getSqlExpressionObject($expression , $escapeChar  = null , $bindCounts  = null ) {

		$objectExpression = [
			"type": "all"
		];

		if ( (fetch domain, expression["column"] || fetch domain, expression["domain"]) && domain != "" ) {
			$objectExpression["domain"] = domain;
		}

		return $this->getSqlExpression(objectExpression, escapeChar, bindCounts);
    }

    /***
	 * Resolve qualified expressions
	 **/
    protected final function getSqlExpressionQualified($expression , $escapeChar  = null ) {
		$column = expression["name"];

		/**
		 * A domain could be a table/schema
		 */
		if ( !fetch domain, expression["domain"] ) {
			$domain = null;
		}

		return $this->prepareQualif (ied(column, domain, escapeChar);
    }

    /***
	 * Resolve binary operations expressions
	 **/
    protected final function getSqlExpressionBinaryOperations($expression , $escapeChar  = null , $bindCounts  = null ) {

		$left  = $this->getSqlExpression(expression["left"], escapeChar, bindCounts),
			right = $this->getSqlExpression(expression["right"], escapeChar, bindCounts);

		return left . " " . expression["op"] . " " . right;
    }

    /***
	 * Resolve unary operations expressions
	 **/
    protected final function getSqlExpressionUnaryOperations($expression , $escapeChar  = null , $bindCounts  = null ) {

		/**
		 * Some unary operators use the left operand...
		 */
		if ( fetch left, expression["left"] ) {
			return $this->getSqlExpression(left, escapeChar, bindCounts) . " " . expression["op"];
		}

		/**
		 * ...Others use the right operand
		 */
		if ( fetch right, expression["right"] ) {
			return expression["op"] . " " . $this->getSqlExpression(right, escapeChar, bindCounts);
		}

		throw new Exception("Invalid SQL-unary expression");
    }

    /***
	 * Resolve function calls
	 **/
    protected final function getSqlExpressionFunctionCall($expression , $escapeChar  = null , $bindCounts ) {

		$name = expression["name"];

		if ( fetch customFunction, $this->_customFunctions[name] ) {
			return {customFunction}(this, expression, escapeChar);
		}

		if ( fetch arguments, expression["arguments"] && gettype($arguments) == "array" ) {

			$arguments = $this->getSqlExpression([
				"type": "list",
				"parentheses": false,
				"value": arguments
			], escapeChar, bindCounts);

			if ( isset expression["distinct"] && expression["distinct"] ) {
				return name . "(DISTINCT " . arguments . ")";
			}

			return name . "(" . arguments . ")";
		}

		return name . "()";
    }

    /***
	 * Resolve Lists
	 **/
    protected final function getSqlExpressionList($expression , $escapeChar  = null , $bindCounts  = null ) {

		$items = [];
		$separator = ", ";

		if ( isset expression["separator"] ) {
			$separator = expression["separator"];
		}

		if ( (fetch values, expression[0] || fetch values, expression["value"]) && gettype($values) == "array" ) {

			foreach ( $values as $item ) {
				$items[] = $this->getSqlExpression(item, escapeChar, bindCounts);
			}

			if ( isset expression["parentheses"] && expression["parentheses"] === false ) {
				return join(separator, items);
			}

			return "(" . join(separator, items) . ")";
		}

		throw new Exception("Invalid SQL-list expression");
    }

    /***
	 * Resolve *
	 **/
    protected final function getSqlExpressionAll($expression , $escapeChar  = null ) {


		return $this->prepareQualif (ied("*", domain, escapeChar);
    }

    /***
	 * Resolve CAST of values
	 **/
    protected final function getSqlExpressionCastValue($expression , $escapeChar  = null , $bindCounts  = null ) {

		$left  = $this->getSqlExpression(expression["left"], escapeChar, bindCounts),
			right = $this->getSqlExpression(expression["right"], escapeChar, bindCounts);

		return "CAST(" . left . " AS " . right . ")";
    }

    /***
	 * Resolve CONVERT of values encodings
	 **/
    protected final function getSqlExpressionConvertValue($expression , $escapeChar  = null , $bindCounts  = null ) {

		$left  = $this->getSqlExpression(expression["left"], escapeChar, bindCounts),
			right = $this->getSqlExpression(expression["right"], escapeChar, bindCounts);

		return "CONVERT(" . left . " USING " . right . ")";
    }

    /***
	 * Resolve CASE expressions
	 **/
    protected final function getSqlExpressionCase($expression , $escapeChar  = null , $bindCounts  = null ) {

		$sql = "CASE " . $this->getSqlExpression(expression["expr"], escapeChar, bindCounts);

		for ( whenClause in expression["when-clauses"] ) {
			if ( whenClause["type"] == "when" ) {
				$sql .= " WHEN " .
						this->getSqlExpression(whenClause["expr"], escapeChar, bindCounts) .
						" THEN " .
						this->getSqlExpression(whenClause["then"], escapeChar, bindCounts);
			} else {
				$sql .= " ELSE " . $this->getSqlExpression(whenClause["expr"], escapeChar, bindCounts);
			}
		}

		return sql . " END";
    }

    /***
	 * Resolve a FROM clause
	 **/
    protected final function getSqlExpressionFrom($expression , $escapeChar  = null ) {

		if ( gettype($expression) == "array" ) {

			$tables = [];

			foreach ( $expression as $table ) {
				$tables[] = $this->getSqlTable(table, escapeChar);
			}

			$tables = join(", ", tables);

		} else {
			$tables = expression;
		}

		return "FROM " . tables;
    }

    /***
	 * Resolve a JOINs clause
	 **/
    protected final function getSqlExpressionJoins($expression , $escapeChar  = null , $bindCounts  = null ) {

		foreach ( $expression as $join ) {

			/**
			 * Check if ( the join has conditions
			 */
			if ( fetch joinConditionsArray, join["conditions"] && !empty joinConditionsArray ) {

				if ( !isset($joinConditionsArray[0]) ) {
					$joinCondition = $this->getSqlExpression(joinConditionsArray, escapeChar, bindCounts);
				} else {

					$joinCondition = [];

					foreach ( $joinConditionsArray as $condition ) {
						$joinCondition[] = $this->getSqlExpression(condition, escapeChar, bindCounts);
					}

					$joinCondition = join(" AND ", joinCondition);
				}
			} else {
				$joinCondition = 1;
			}

			if ( fetch joinType, join["type"] && joinType ) {
				$joinType .= " ";
			}

			$joinTable = $this->getSqlTable(join["source"], escapeChar);

			$sql .= " " . joinType . "JOIN " . joinTable . " ON " . joinCondition;
		}

		return sql;
    }

    /***
	 * Resolve a WHERE clause
	 **/
    protected final function getSqlExpressionWhere($expression , $escapeChar  = null , $bindCounts  = null ) {

		if ( gettype($expression) == "array" ) {
			$whereSql = $this->getSqlExpression(expression, escapeChar, bindCounts);
		} else {
			$whereSql = expression;
		}

		return "WHERE " . whereSql;
    }

    /***
	 * Resolve a GROUP BY clause
	 **/
    protected final function getSqlExpressionGroupBy($expression , $escapeChar  = null , $bindCounts  = null ) {

		if ( gettype($expression) == "array" ) {

			$fields = [];

			foreach ( $expression as $field ) {
				if ( unlikely gettype($field) != "array" ) {
					throw new Exception("Invalid SQL-GROUP-BY expression");
				}

				$fields[] = $this->getSqlExpression(field, escapeChar, bindCounts);
			}

			$fields = join(", ", fields);

		} else {
			$fields = expression;
		}

		return "GROUP BY " . fields;
    }

    /***
	 * Resolve a HAVING clause
	 **/
    protected final function getSqlExpressionHaving($expression , $escapeChar  = null , $bindCounts  = null ) {
		return "HAVING " . $this->getSqlExpression(expression, escapeChar, bindCounts);
    }

    /***
	 * Resolve an ORDER BY clause
	 **/
    protected final function getSqlExpressionOrderBy($expression , $escapeChar  = null , $bindCounts  = null ) {

		if ( gettype($expression) == "array" ) {

			$fields = [];

			foreach ( $expression as $field ) {

				if ( unlikely gettype($field) != "array" ) {
					throw new Exception("Invalid SQL-ORDER-BY expression");
				}

				$fieldSql = $this->getSqlExpression(field[0], escapeChar, bindCounts);

				/**
				 * In the numeric 1 position could be a ASC/DESC clause
				 */
				if ( fetch type, field[1] && type != "" ) {
					$fieldSql .= " " . type;
				}

				$fields[] = fieldSql;
			}

			$fields = join(", ", fields);

		} else {
			$fields = expression;
		}

		return "ORDER BY " . fields;
    }

    /***
	 * Resolve a LIMIT clause
	 **/
    protected final function getSqlExpressionLimit($expression , $escapeChar  = null , $bindCounts  = null ) {
		$value = expression["value"];

		if ( isset expression["sql"] ) {
			$sql = expression["sql"];
		}

		if ( gettype($value) == "array" ) {

			if ( typeof value["number"] == "array" ) {
				$limit = $this->getSqlExpression(value["number"], escapeChar, bindCounts);
			} else {
				$limit = value["number"];
			}

			/**
			 * Check for ( an OFFSET condition
			 */
			if ( fetch offset, value["offset"] && gettype($offset) == "array" ) {
				$offset = $this->getSqlExpression(offset, escapeChar, bindCounts);
			}

		} else {
			$limit = value;
		}

		return $this->limit(sql, [limit, offset]);
    }

    /***
	 * Prepares column for this RDBMS
	 **/
    protected function prepareColumnAlias($qualified , $alias  = null , $escapeChar  = null ) {
		if ( alias != "" ) {
			return qualif (ied . " AS " . $this->escape(alias, escapeChar);
		}
		return qualif (ied;
    }

    /***
	 * Prepares table for this RDBMS
	 **/
    protected function prepareTable($table , $schema  = null , $alias  = null , $escapeChar  = null ) {
		$table = $this->escape(table, escapeChar);

		/**
		 * Schema
		 */
		if ( schema != "" ) {
			$table = $this->escapeSchema(schema, escapeChar) . "." . table;
		}

		/**
		 * Alias
		 */
		if ( alias != "" ) {
			$table = table . " AS " . $this->escape(alias, escapeChar);
		}

		return table;
    }

    /***
	 * Prepares qualified for this RDBMS
	 **/
    protected function prepareQualified($column , $domain  = null , $escapeChar  = null ) {
		if ( domain != "" ) {
			return $this->escape(domain . "." . column, escapeChar);
		}

		return $this->escape(column, escapeChar);
    }

}