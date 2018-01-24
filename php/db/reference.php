<?php


namespace Phalcon\Db;



/***
 * Phalcon\Db\Reference
 *
 * Allows to define reference constraints on tables
 *
 *<code>
 * $reference = new \Phalcon\Db\Reference(
 *     "field_fk",
 *     [
 *         "referencedSchema"  => "invoicing",
 *         "referencedTable"   => "products",
 *         "columns"           => [
 *             "product_type",
 *             "product_code",
 *         ],
 *         "referencedColumns" => [
 *             "type",
 *             "code",
 *         ],
 *     ]
 * );
 *</code>
 **/

class Reference {

    /***
	 * Constraint name
	 *
	 * @var string
	 **/
    protected $_name;

    protected $_schemaName;

    protected $_referencedSchema;

    /***
	 * Referenced Table
	 *
	 * @var string
	 **/
    protected $_referencedTable;

    /***
	 * Local reference columns
	 *
	 * @var array
	 **/
    protected $_columns;

    /***
	 * Referenced Columns
	 *
	 * @var array
	 **/
    protected $_referencedColumns;

    /***
	 * ON DELETE
	 *
	 * @var array
	 **/
    protected $_onDelete;

    /***
	 * ON UPDATE
	 *
	 * @var array
	 **/
    protected $_onUpdate;

    /***
	 * Phalcon\Db\Reference constructor
	 **/
    public function __construct($name , $definition ) {

    }

    /***
	 * Restore a Phalcon\Db\Reference object from export
	 **/
    public static function __set_state($data ) {

    }

}