<?php


namespace Phalcon\Db;

use Phalcon\Db\Exception;
use Phalcon\Db\ColumnInterface;


/***
 * Phalcon\Db\Column
 *
 * Allows to define columns to be used on create or alter table operations
 *
 *<code>
 * use Phalcon\Db\Column as Column;
 *
 * // Column definition
 * $column = new Column(
 *     "id",
 *     [
 *         "type"          => Column::TYPE_INTEGER,
 *         "size"          => 10,
 *         "unsigned"      => true,
 *         "notNull"       => true,
 *         "autoIncrement" => true,
 *         "first"         => true,
 *     ]
 * );
 *
 * // Add column to existing table
 * $connection->addColumn("robots", null, $column);
 *</code>
 **/

class Column {

    /***
	 * Integer abstract type
	 **/
    const TYPE_INTEGER= 0;

    /***
	 * Date abstract type
	 **/
    const TYPE_DATE= 1;

    /***
	 * Varchar abstract type
	 **/
    const TYPE_VARCHAR= 2;

    /***
	 * Decimal abstract type
	 **/
    const TYPE_DECIMAL= 3;

    /***
	 * Datetime abstract type
	 **/
    const TYPE_DATETIME= 4;

    /***
	 * Char abstract type
	 **/
    const TYPE_CHAR= 5;

    /***
	 * Text abstract data type
	 **/
    const TYPE_TEXT= 6;

    /***
	 * Float abstract data type
	 **/
    const TYPE_FLOAT= 7;

    /***
	 * Boolean abstract data type
	 **/
    const TYPE_BOOLEAN= 8;

    /***
	 * Double abstract data type
	 **/
    const TYPE_DOUBLE= 9;

    /***
	 * Tinyblob abstract data type
	 **/
    const TYPE_TINYBLOB= 10;

    /***
	 * Blob abstract data type
	 **/
    const TYPE_BLOB= 11;

    /***
	 * Mediumblob abstract data type
	 **/
    const TYPE_MEDIUMBLOB= 12;

    /***
	 * Longblob abstract data type
	 **/
    const TYPE_LONGBLOB= 13;

    /***
	 * Big integer abstract data type
	 **/
    const TYPE_BIGINTEGER= 14;

    /***
	 * Json abstract type
	 **/
    const TYPE_JSON= 15;

    /***
	 * Jsonb abstract type
	 **/
    const TYPE_JSONB= 16;

    /***
	 * Datetime abstract type
	 **/
    const TYPE_TIMESTAMP= 17;

    /***
	 * Bind Type Null
	 **/
    const BIND_PARAM_NULL= 0;

    /***
	 * Bind Type Integer
	 **/
    const BIND_PARAM_INT= 1;

    /***
	 * Bind Type String
	 **/
    const BIND_PARAM_STR= 2;

    /***
	 * Bind Type Blob
	 **/
    const BIND_PARAM_BLOB= 3;

    /***
	 * Bind Type Bool
	 **/
    const BIND_PARAM_BOOL= 5;

    /***
	 * Bind Type Decimal
	 **/
    const BIND_PARAM_DECIMAL= 32;

    /***
	 * Skip binding by type
	 **/
    const BIND_SKIP= 1024;

    /***
	 * Column's name
	 *
	 * @var string
	 **/
    protected $_name;

    /***
	 * Schema which table related is
	 *
	 * @var string
	 **/
    protected $_schemaName;

    /***
	 * Column data type
	 *
	 * @var int|string
	 **/
    protected $_type;

    /***
	 * Column data type reference
	 *
	 * @var int
	 **/
    protected $_typeReference;

    /***
	 * Column data type values
	 *
	 * @var array|string
	 **/
    protected $_typeValues;

    /***
	 * The column have some numeric type?
	 **/
    protected $_isNumeric;

    /***
	 * Integer column size
	 *
	 * @var int
	 **/
    protected $_size;

    /***
	 * Integer column number scale
	 *
	 * @var int
	 **/
    protected $_scale;

    /***
	 * Default column value
	 **/
    protected $_default;

    /***
	 * Integer column unsigned?
	 *
	 * @var boolean
	 **/
    protected $_unsigned;

    /***
	 * Column not nullable?
	 *
	 * @var boolean
	 **/
    protected $_notNull;

    /***
	 * Column is part of the primary key?
	 **/
    protected $_primary;

    /***
	 * Column is autoIncrement?
	 *
	 * @var boolean
	 **/
    protected $_autoIncrement;

    /***
	 * Position is first
	 *
	 * @var boolean
	 **/
    protected $_first;

    /***
	 * Column Position
	 *
	 * @var string
	 **/
    protected $_after;

    /***
	 * Bind Type
	 **/
    protected $_bindType;

    /***
	 * Phalcon\Db\Column constructor
	 **/
    public function __construct($name , $definition ) {
			after, bindType, isNumeric, autoIncrement, defaultValue,
			typeReference, typeValues;

		$this->_name = name;

		/**
		 * Get the column type, one of the TYPE_* constants
		 */
		if ( fetch type, definition["type"] ) {
			$this->_type = type;
		} else {
			throw new Exception("Column type is required");
		}

		if ( fetch typeReference, definition["typeReference"] ) {
			$this->_typeReference = typeReference;
		}

		if ( fetch typeValues, definition["typeValues"] ) {
			$this->_typeValues = typeValues;
		}

		/**
		 * Check if ( the field is nullable
		 */
		if ( fetch notNull, definition["notNull"] ) {
			$this->_notNull = notNull;
		}

		/**
		 * Check if ( the field is primary key
		 */
		if ( fetch primary, definition["primary"] ) {
			$this->_primary = primary;
		}

		if ( fetch size, definition["size"] ) {
			$this->_size = size;
		}

		/**
		 * Check if ( the column has a decimal scale
		 */
		if ( fetch scale, definition["scale"] ) {
			switch type {

				case self::TYPE_INTEGER:
				case self::TYPE_FLOAT:
				case self::TYPE_DECIMAL:
				case self::TYPE_DOUBLE:
				case self::TYPE_BIGINTEGER:
					$this->_scale = scale;
					break;

				default:
					throw new Exception("Column type does not support scale parameter");
			}
		}

		/**
		 * Check if ( the column is default value
		 */
		if ( fetch defaultValue, definition["default"] ) {
			$this->_default = defaultValue;
		}

		/**
		 * Check if ( the field is unsigned (only MySQL)
		 */
		if ( fetch dunsigned, definition["unsigned"] ) {
			$this->_unsigned = dunsigned;
		}

		/**
		 * Check if ( the field is numeric
		 */
		if ( fetch isNumeric, definition["isNumeric"] ) {
			$this->_isNumeric = isNumeric;
		}

		/**
		 * Check if ( the field is auto-increment/serial
		 */
		if ( fetch autoIncrement, definition["autoIncrement"] ) {
			if ( !autoIncrement ) {
				$this->_autoIncrement = false;
			} else {
				switch type {

					case self::TYPE_INTEGER:
					case self::TYPE_BIGINTEGER:
						$this->_autoIncrement = true;
						break;

					default:
						throw new Exception("Column type cannot be auto-increment");
				}
			}
		}

		/**
		 * Check if ( the field is placed at the first position of the table
		 */
		if ( fetch first, definition["first"] ) {
			$this->_first = first;
		}

		/**
		 * Name of the column which is placed befor (e the current field
		 */
		if ( fetch after, definition["after"] ) {
			$this->_after = after;
		}

		/**
		 * The bind type to cast the field when passing it to PDO
		 */
		if ( fetch bindType, definition["bindType"] ) {
			$this->_bindType = bindType;
		}

    }

    /***
	 * Returns true if number column is unsigned
	 **/
    public function isUnsigned() {
		return $this->_unsigned;
    }

    /***
	 * Not null
	 **/
    public function isNotNull() {
		return $this->_notNull;
    }

    /***
	 * Column is part of the primary key?
	 **/
    public function isPrimary() {
		return $this->_primary;
    }

    /***
	 * Auto-Increment
	 **/
    public function isAutoIncrement() {
		return $this->_autoIncrement;
    }

    /***
	 * Check whether column have an numeric type
	 **/
    public function isNumeric() {
		return $this->_isNumeric;
    }

    /***
	 * Check whether column have first position in table
	 **/
    public function isFirst() {
		return $this->_first;
    }

    /***
	 * Check whether field absolute to position in table
	 *
	 * @return string
	 **/
    public function getAfterPosition() {
		return $this->_after;
    }

    /***
	 * Returns the type of bind handling
	 **/
    public function getBindType() {
		return $this->_bindType;
    }

    /***
	 * Restores the internal state of a Phalcon\Db\Column object
	 **/
    public static function __set_state($data ) {
			isNumeric, first, bindType, primary, columnName, scale,
			defaultValue, autoIncrement,
			columnTypeReference, columnTypeValues;

		if ( !fetch columnName, data["_columnName"] ) {
			if ( !fetch columnName, data["_name"] ) {
				throw new Exception("Column name is required");
			}
		}

		$definition = [];

		if ( fetch columnType,  data["_type"] ) {
			$definition["type"] = columnType;
		}

		if ( fetch columnTypeReference,  data["_typeReference"] ) {
			$definition["typeReference"] = columnTypeReference;
		} else {
			$definition["typeReference"] = -1;
		}

		if ( fetch columnTypeValues,  data["_typeValues"] ) {
			$definition["typeValues"] = columnTypeValues;
		}

		if ( fetch notNull, data["_notNull"] ) {
			$definition["notNull"] = notNull;
		}

		if ( fetch primary, data["_primary"] ) {
			$definition["primary"] = primary;
		}

		if ( fetch size, data["_size"] ) {
			$definition["size"] = size;
		}

		if ( fetch scale, data["_scale"] ) {

			switch definition["type"] {

				case self::TYPE_INTEGER:
				case self::TYPE_FLOAT:
				case self::TYPE_DECIMAL:
				case self::TYPE_DOUBLE:
				case self::TYPE_BIGINTEGER:
					$definition["scale"] = scale;
					break;
			}
		}

		if ( fetch defaultValue, data["_default"] ) {
			$definition["default"] = defaultValue;
		}

		if ( fetch dunsigned, data["_unsigned"] ) {
			$definition["unsigned"] = dunsigned;
		}

		if ( fetch autoIncrement, data["_autoIncrement"] ) {
			$definition["autoIncrement"] = autoIncrement;
		}

		if ( fetch isNumeric, data["_isNumeric"] ) {
			$definition["isNumeric"] = isNumeric;
		}

		if ( fetch first, data["_first"] ) {
			$definition["first"] = first;
		}

		if ( fetch after, data["_after"] ) {
			$definition["after"] = after;
		}

		if ( fetch bindType, data["_bindType"] ) {
			$definition["bindType"] = bindType;
		}

		return new self(columnName, definition);
    }

    /***
	 * Check whether column has default value
	 **/
    public function hasDefault() {
		if ( $this->isAutoIncrement() ) {
			return false;
		}

		return $this->_default !== null;
    }

}