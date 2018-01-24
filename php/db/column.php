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
    const TYPE_INTEGER= 0;;

    /***
	 * Date abstract type
	 **/
    const TYPE_DATE= 1;;

    /***
	 * Varchar abstract type
	 **/
    const TYPE_VARCHAR= 2;;

    /***
	 * Decimal abstract type
	 **/
    const TYPE_DECIMAL= 3;;

    /***
	 * Datetime abstract type
	 **/
    const TYPE_DATETIME= 4;;

    /***
	 * Char abstract type
	 **/
    const TYPE_CHAR= 5;;

    /***
	 * Text abstract data type
	 **/
    const TYPE_TEXT= 6;;

    /***
	 * Float abstract data type
	 **/
    const TYPE_FLOAT= 7;;

    /***
	 * Boolean abstract data type
	 **/
    const TYPE_BOOLEAN= 8;;

    /***
	 * Double abstract data type
	 **/
    const TYPE_DOUBLE= 9;;

    /***
	 * Tinyblob abstract data type
	 **/
    const TYPE_TINYBLOB= 10;;

    /***
	 * Blob abstract data type
	 **/
    const TYPE_BLOB= 11;;

    /***
	 * Mediumblob abstract data type
	 **/
    const TYPE_MEDIUMBLOB= 12;;

    /***
	 * Longblob abstract data type
	 **/
    const TYPE_LONGBLOB= 13;;

    /***
	 * Big integer abstract data type
	 **/
    const TYPE_BIGINTEGER= 14;;

    /***
	 * Json abstract type
	 **/
    const TYPE_JSON= 15;;

    /***
	 * Jsonb abstract type
	 **/
    const TYPE_JSONB= 16;;

    /***
	 * Datetime abstract type
	 **/
    const TYPE_TIMESTAMP= 17;;

    /***
	 * Bind Type Null
	 **/
    const BIND_PARAM_NULL= 0;;

    /***
	 * Bind Type Integer
	 **/
    const BIND_PARAM_INT= 1;;

    /***
	 * Bind Type String
	 **/
    const BIND_PARAM_STR= 2;;

    /***
	 * Bind Type Blob
	 **/
    const BIND_PARAM_BLOB= 3;;

    /***
	 * Bind Type Bool
	 **/
    const BIND_PARAM_BOOL= 5;;

    /***
	 * Bind Type Decimal
	 **/
    const BIND_PARAM_DECIMAL= 32;;

    /***
	 * Skip binding by type
	 **/
    const BIND_SKIP= 1024;;

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

    }

    /***
	 * Returns true if number column is unsigned
	 **/
    public function isUnsigned() {

    }

    /***
	 * Not null
	 **/
    public function isNotNull() {

    }

    /***
	 * Column is part of the primary key?
	 **/
    public function isPrimary() {

    }

    /***
	 * Auto-Increment
	 **/
    public function isAutoIncrement() {

    }

    /***
	 * Check whether column have an numeric type
	 **/
    public function isNumeric() {

    }

    /***
	 * Check whether column have first position in table
	 **/
    public function isFirst() {

    }

    /***
	 * Check whether field absolute to position in table
	 *
	 * @return string
	 **/
    public function getAfterPosition() {

    }

    /***
	 * Returns the type of bind handling
	 **/
    public function getBindType() {

    }

    /***
	 * Restores the internal state of a Phalcon\Db\Column object
	 **/
    public static function __set_state($data ) {

    }

    /***
	 * Check whether column has default value
	 **/
    public function hasDefault() {

    }

}