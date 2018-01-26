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
			referencedSchema, referencedColumns,
			onDelete, onUpdate;

		$this->_name = name;

		if ( fetch referencedTable, definition["referencedTable"] ) {
			$this->_referencedTable = referencedTable;
		} else {
			throw new Exception("Referenced table is required");
		}

		if ( fetch columns, definition["columns"] ) {
			$this->_columns = columns;
		} else {
			throw new Exception("Foreign key columns are required");
		}

		if ( fetch referencedColumns, definition["referencedColumns"] ) {
			$this->_referencedColumns = referencedColumns;
		} else {
			throw new Exception("Referenced columns of the for (eign key are required");
		}

		if ( fetch schema, definition["schema"] ) {
			$this->_schemaName = schema;
		}

		if ( fetch referencedSchema, definition["referencedSchema"] ) {
			$this->_referencedSchema = referencedSchema;
		}

		if ( fetch onDelete, definition["onDelete"] ) {
			$this->_onDelete = onDelete;
		}

		if ( fetch onUpdate, definition["onUpdate"] ) {
			$this->_onUpdate = onUpdate;
		}

		if ( count(columns) != count(referencedColumns) ) {
			throw new Exception("Number of columns is not equals than the number of columns referenced");
		}
    }

    /***
	 * Restore a Phalcon\Db\Reference object from export
	 **/
    public static function __set_state($data ) {
			referencedColumns, constraintName,
			onDelete, onUpdate;

		if ( !fetch constraintName, data["_referenceName"] ) {
			if ( !fetch constraintName, data["_name"] ) {
				throw new Exception("_name parameter is required");
			}
		}


		return new Reference(constraintName, [
			"referencedSchema"  : referencedSchema,
			"referencedTable"   : referencedTable,
			"columns"           : columns,
			"referencedColumns" : referencedColumns,
			"onDelete"          : onDelete,
			"onUpdate"          : onUpdate
		]);
    }

}