<?php


namespace Phalcon\Mvc\Model;

use Phalcon\DiInterface;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\MetaData\Strategy\Introspection;
use Phalcon\Mvc\Model\MetaData\StrategyInterface;


/***
 * Phalcon\Mvc\Model\MetaData
 *
 * <p>Because Phalcon\Mvc\Model requires meta-data like field names, data types, primary keys, etc.
 * this component collect them and store for further querying by Phalcon\Mvc\Model.
 * Phalcon\Mvc\Model\MetaData can also use adapters to store temporarily or permanently the meta-data.</p>
 *
 * <p>A standard Phalcon\Mvc\Model\MetaData can be used to query model attributes:</p>
 *
 * <code>
 * $metaData = new \Phalcon\Mvc\Model\MetaData\Memory();
 *
 * $attributes = $metaData->getAttributes(
 *     new Robots()
 * );
 *
 * print_r($attributes);
 * </code>
 **/

abstract class MetaData {

    const MODELS_ATTRIBUTES= 0;

    const MODELS_PRIMARY_KEY= 1;

    const MODELS_NON_PRIMARY_KEY= 2;

    const MODELS_NOT_NULL= 3;

    const MODELS_DATA_TYPES= 4;

    const MODELS_DATA_TYPES_NUMERIC= 5;

    const MODELS_DATE_AT= 6;

    const MODELS_DATE_IN= 7;

    const MODELS_IDENTITY_COLUMN= 8;

    const MODELS_DATA_TYPES_BIND= 9;

    const MODELS_AUTOMATIC_DEFAULT_INSERT= 10;

    const MODELS_AUTOMATIC_DEFAULT_UPDATE= 11;

    const MODELS_DEFAULT_VALUES= 12;

    const MODELS_EMPTY_STRING_VALUES= 13;

    const MODELS_COLUMN_MAP= 0;

    const MODELS_REVERSE_COLUMN_MAP= 1;

    protected $_dependencyInjector;

    protected $_strategy;

    protected $_metaData;

    protected $_columnMap;

    /***
	 * Initialize the metadata for certain table
	 **/
    protected final function _initialize($model , $key , $table , $schema ) {

    }

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {

    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {

    }

    /***
	 * Set the meta-data extraction strategy
	 **/
    public function setStrategy($strategy ) {

    }

    /***
	 * Return the strategy to obtain the meta-data
	 **/
    public function getStrategy() {

    }

    /***
	 * Reads the complete meta-data for certain model
	 *
	 *<code>
	 * print_r(
	 *     $metaData->readMetaData(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public final function readMetaData($model ) {

    }

    /***
	 * Reads meta-data for certain model
	 *
	 *<code>
	 * print_r(
	 *     $metaData->readMetaDataIndex(
	 *         new Robots(),
	 *         0
	 *     )
	 * );
	 *</code>
	 **/
    public final function readMetaDataIndex($model , $index ) {

    }

    /***
	 * Writes meta-data for certain model using a MODEL_* constant
	 *
	 *<code>
	 * print_r(
	 *     $metaData->writeColumnMapIndex(
	 *         new Robots(),
	 *         MetaData::MODELS_REVERSE_COLUMN_MAP,
	 *         [
	 *             "leName" => "name",
	 *         ]
	 *     )
	 * );
	 *</code>
	 **/
    public final function writeMetaDataIndex($model , $index , $data ) {

    }

    /***
	 * Reads the ordered/reversed column map for certain model
	 *
	 *<code>
	 * print_r(
	 *     $metaData->readColumnMap(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public final function readColumnMap($model ) {

    }

    /***
	 * Reads column-map information for certain model using a MODEL_* constant
	 *
	 *<code>
	 * print_r(
	 *     $metaData->readColumnMapIndex(
	 *         new Robots(),
	 *         MetaData::MODELS_REVERSE_COLUMN_MAP
	 *     )
	 * );
	 *</code>
	 **/
    public final function readColumnMapIndex($model , $index ) {

    }

    /***
	 * Returns table attributes names (fields)
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getAttributes($model ) {

    }

    /***
	 * Returns an array of fields which are part of the primary key
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getPrimaryKeyAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getPrimaryKeyAttributes($model ) {

    }

    /***
	 * Returns an array of fields which are not part of the primary key
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getNonPrimaryKeyAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getNonPrimaryKeyAttributes($model ) {

    }

    /***
	 * Returns an array of not null attributes
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getNotNullAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getNotNullAttributes($model ) {

    }

    /***
	 * Returns attributes and their data types
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getDataTypes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getDataTypes($model ) {

    }

    /***
	 * Returns attributes which types are numerical
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getDataTypesNumeric(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getDataTypesNumeric($model ) {

    }

    /***
	 * Returns the name of identity field (if one is present)
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getIdentityField(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 *
	 * @param  Phalcon\Mvc\ModelInterface model
	 * @return string
	 **/
    public function getIdentityField($model ) {

    }

    /***
	 * Returns attributes and their bind data types
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getBindTypes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getBindTypes($model ) {

    }

    /***
	 * Returns attributes that must be ignored from the INSERT SQL generation
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getAutomaticCreateAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getAutomaticCreateAttributes($model ) {

    }

    /***
	 * Returns attributes that must be ignored from the UPDATE SQL generation
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getAutomaticUpdateAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getAutomaticUpdateAttributes($model ) {

    }

    /***
	 * Set the attributes that must be ignored from the INSERT SQL generation
	 *
	 *<code>
	 * $metaData->setAutomaticCreateAttributes(
	 *     new Robots(),
	 *     [
	 *         "created_at" => true,
	 *     ]
	 * );
	 *</code>
	 **/
    public function setAutomaticCreateAttributes($model , $attributes ) {

    }

    /***
	 * Set the attributes that must be ignored from the UPDATE SQL generation
	 *
	 *<code>
	 * $metaData->setAutomaticUpdateAttributes(
	 *     new Robots(),
	 *     [
	 *         "modified_at" => true,
	 *     ]
	 * );
	 *</code>
	 **/
    public function setAutomaticUpdateAttributes($model , $attributes ) {

    }

    /***
	 * Set the attributes that allow empty string values
	 *
	 *<code>
	 * $metaData->setEmptyStringAttributes(
	 *     new Robots(),
	 *     [
	 *         "name" => true,
	 *     ]
	 * );
	 *</code>
	 **/
    public function setEmptyStringAttributes($model , $attributes ) {

    }

    /***
	 * Returns attributes allow empty strings
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getEmptyStringAttributes(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getEmptyStringAttributes($model ) {

    }

    /***
	 * Returns attributes (which have default values) and their default values
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getDefaultValues(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getDefaultValues($model ) {

    }

    /***
	 * Returns the column map if any
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getColumnMap(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getColumnMap($model ) {

    }

    /***
	 * Returns the reverse column map if any
	 *
	 *<code>
	 * print_r(
	 *     $metaData->getReverseColumnMap(
	 *         new Robots()
	 *     )
	 * );
	 *</code>
	 **/
    public function getReverseColumnMap($model ) {

    }

    /***
	 * Check if a model has certain attribute
	 *
	 *<code>
	 * var_dump(
	 *     $metaData->hasAttribute(
	 *         new Robots(),
	 *         "name"
	 *     )
	 * );
	 *</code>
	 **/
    public function hasAttribute($model , $attribute ) {

    }

    /***
	 * Checks if the internal meta-data container is empty
	 *
	 *<code>
	 * var_dump(
	 *     $metaData->isEmpty()
	 * );
	 *</code>
	 **/
    public function isEmpty() {

    }

    /***
	 * Resets internal meta-data in order to regenerate it
	 *
	 *<code>
	 * $metaData->reset();
	 *</code>
	 **/
    public function reset() {

    }

}