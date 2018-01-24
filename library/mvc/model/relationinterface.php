<?php


namespace Phalcon\Mvc\Model;



/***
 * Phalcon\Mvc\Model\RelationInterface
 *
 * Interface for Phalcon\Mvc\Model\Relation
 **/

interface RelationInterface {

    /***
	 * Sets the intermediate model dat for has-*-through relations
	 *
	 * @param string|array intermediateFields
	 * @param string intermediateModel
	 * @param string|array intermediateReferencedFields
	 **/
    public function setIntermediateRelation($intermediateFields , $intermediateModel , $intermediateReferencedFields ); 

    /***
	 * Check if records returned by getting belongs-to/has-many are implicitly cached during the current request
	 **/
    public function isReusable(); 

    /***
	 * Returns the relations type
	 **/
    public function getType(); 

    /***
	 * Returns the referenced model
	 **/
    public function getReferencedModel(); 

    /***
	 * Returns the fields
	 *
	 * @return string|array
	 **/
    public function getFields(); 

    /***
	 * Returns the referenced fields
	 *
	 * @return string|array
	 **/
    public function getReferencedFields(); 

    /***
	 * Returns the options
	 *
	 * @return string|array
	 **/
    public function getOptions(); 

    /***
	 * Returns an option by the specified name
	 * If the option doesn't exist null is returned
	 **/
    public function getOption($name ); 

    /***
	 * Check whether the relation act as a foreign key
	 **/
    public function isForeignKey(); 

    /***
	 * Returns the foreign key configuration
	 *
	 * @return string|array
	 **/
    public function getForeignKey(); 

    /***
	 * Check whether the relation is a 'many-to-many' relation or not
	 **/
    public function isThrough(); 

    /***
	 * Gets the intermediate fields for has-*-through relations
	 *
	 * @return string|array
	 **/
    public function getIntermediateFields(); 

    /***
	 * Gets the intermediate model for has-*-through relations
	 **/
    public function getIntermediateModel(); 

    /***
	 * Gets the intermediate referenced fields for has-*-through relations
	 *
	 * @return string|array
	 **/
    public function getIntermediateReferencedFields(); 

}