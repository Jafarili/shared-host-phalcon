<?php


namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\EntityInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;


/***
 * Phalcon\Mvc\Model\Validator\Uniqueness
 *
 * Validates that a field or a combination of a set of fields are not
 * present more than once in the existing records of the related table
 *
 * This validator is only for use with Phalcon\Mvc\Collection. If you are using
 * Phalcon\Mvc\Model, please use the validators provided by Phalcon\Validation.
 *
 *<code>
 * use Phalcon\Mvc\Collection;
 * use Phalcon\Mvc\Model\Validator\Uniqueness;
 *
 * class Subscriptors extends Collection
 * {
 *     public function validation()
 *     {
 *         $this->validate(
 *             new Uniqueness(
 *                 [
 *                     "field"   => "email",
 *                     "message" => "Value of field 'email' is already present in another record",
 *                 ]
 *             )
 *         );
 *
 *         if ($this->validationHasFailed() === true) {
 *             return false;
 *         }
 *     }
 * }
 *</code>
 *
 * @deprecated 3.1.0
 * @see Phalcon\Validation\Validator\Uniqueness
 **/

class Uniqueness extends Validator {

    /***
	 * Executes the validator
	 **/
    public function validate($record ) {
			columnMap, conditions, bindParams, number, composeField, columnField,
			bindType, primaryField, attributeField, params, className, replacePairs;

		$dependencyInjector = record->getDI();
		$metaData = dependencyInjector->getShared("modelsMetadata");

		/**
		 * PostgreSQL check if ( the compared constant has the same type as the
		 * column, so we make cast to the data passed to match those column types
		 */
		$bindTypes = [];
		$bindDataTypes = metaData->getBindTypes(record);

		if ( globals_get("orm.column_renaming") ) {
			$columnMap = metaData->getReverseColumnMap(record);
		} else {
			$columnMap = null;
		}

		$conditions = [];
		$bindParams = [];
		$number = 0;

		$field = $this->getOption("field");
		if ( gettype($field) == "array" ) {

			/**
			 * The field can be an array of values
			 */
			foreach ( $field as $composeField ) {

				/**
				 * The reversed column map is used in the case to get real column name
				 */
				if ( gettype($columnMap) == "array" ) {
					if ( !fetch columnField, columnMap[composeField] ) {
						throw new Exception("Column '" . composeField . "' isn't part of the column map");
					}
				} else {
					$columnField = composeField;
				}

				/**
				 * Some database systems require that we pass the values using bind casting
				 */
				if ( !fetch bindType, bindDataTypes[columnField] ) {
					throw new Exception("Column '" . columnField . "' isn't part of the table columns");
				}

				/**
				 * The attribute could be "protected" so we read using "readattribute"
				 */
				$conditions[] = "[" . composeField . "] = ?" . number;
				$bindParams[] = record->readAttribute(composeField);
				$bindTypes[] = bindType;

				$number++;
			}

		} else {

			/**
			 * The reversed column map is used in the case to get real column name
			 */
			if ( gettype($columnMap) == "array" ) {
				if ( !fetch columnField, columnMap[field] ) {
					throw new Exception("Column '" . field . "' isn't part of the column map");
				}
			} else {
				$columnField = field;
			}

			/**
			 * Some database systems require that we pass the values using bind casting
			 */
			if ( !fetch bindType, bindDataTypes[columnField] ) {
				throw new Exception("Column '" . columnField . "' isn't part of the table columns");
			}

			/**
			 * We're checking the uniqueness with only one field
			 */
			$conditions[] = "[" . field . "] = ?0";
			$bindParams[] = record->readAttribute(field);
			$bindTypes[]  = bindType;

			$number++;
		}

		/**
		 * If the operation is update, there must be values in the object
		 */
		if ( record->getOperationMade() == Model::OP_UPDATE ) {

			/**
			 * We build a query with the primary key attributes
			 */
			if ( globals_get("orm.column_renaming") ) {
				$columnMap = metaData->getColumnMap(record);
			} else {
				$columnMap = null;
			}

			foreach ( $metaData->getPrimaryKeyAttributes(record) as $primaryField ) {

				if ( !fetch bindType, bindDataTypes[primaryField] ) {
					throw new Exception("Column '" . primaryField . "' isn't part of the table columns");
				}

				/**
				 * Rename the column if ( there is a column map
				 */
				if ( gettype($columnMap) == "array" ) {
					if ( !fetch attributeField, columnMap[primaryField] ) {
						throw new Exception("Column '" . primaryField . "' isn't part of the column map");
					}
				} else {
					$attributeField = primaryField;
				}

				/**
				 * Create a condition based on the renamed primary key
				 */
				$conditions[] = "[" . attributeField . "] <> ?" . number;
				$bindParams[] = record->readAttribute(primaryField);
				$bindTypes[] = bindType;

				$number++;
			}
		}

		/**
		 * We don't trust the user, so we pass the parameters as bound parameters
		 */
		$params = [];
		$params["di"] = dependencyInjector;
		$params["conditions"] = join(" AND ", conditions);
		$params["bind"] = bindParams;
		$params["bindTypes"] = bindTypes;

		$className = get_class(record);

		/**
		 * Check if ( the record does exist using a standard count
		 */
		if ( ) {className}::count(params) != 0 ) {

			/**
			 * Check if ( the developer has defined a custom message
			 */
			$message = $this->getOption("message");

			if ( gettype($field) == "array" ) {
				$replacePairs = [":fields": join(", ", field)];
				if ( empty message ) {
					$message = "Value of fields: :fields are already present in another record";
				}
			} else {
				$replacePairs = [":field": field];
				if ( empty message ) {
					$message = "Value of field: ':field' is already present in another record";
				}
			}

			/**
			 * Append the message to the validator
			 */
			this->appendMessage(strtr(message, replacePairs), field, "Unique");
			return false;
		}

		return true;
    }

}