<?php


namespace Phalcon\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\CombinedFieldsValidator;
use Phalcon\Validation\Exception;
use Phalcon\Validation\Message;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\CollectionInterface;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Collection;


/***
 * Phalcon\Validation\Validator\Uniqueness
 *
 * Check that a field is unique in the related table
 *
 * <code>
 * use Phalcon\Validation;
 * use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
 *
 * $validator = new Validation();
 *
 * $validator->add(
 *     "username",
 *     new UniquenessValidator(
 *         [
 *             "model"   => new Users(),
 *             "message" => ":field must be unique",
 *         ]
 *     )
 * );
 * </code>
 *
 * Different attribute from the field:
 * <code>
 * $validator->add(
 *     "username",
 *     new UniquenessValidator(
 *         [
 *             "model"     => new Users(),
 *             "attribute" => "nick",
 *         ]
 *     )
 * );
 * </code>
 *
 * In model:
 * <code>
 * $validator->add(
 *     "username",
 *     new UniquenessValidator()
 * );
 * </code>
 *
 * Combination of fields in model:
 * <code>
 * $validator->add(
 *     [
 *         "firstName",
 *         "lastName",
 *     ],
 *     new UniquenessValidator()
 * );
 * </code>
 *
 * It is possible to convert values before validation. This is useful in
 * situations where values need to be converted to do the database lookup:
 *
 * <code>
 * $validator->add(
 *     "username",
 *     new UniquenessValidator(
 *         [
 *             "convert" => function (array $values) {
 *                 $values["username"] = strtolower($values["username"]);
 *
 *                 return $values;
 *             }
 *         ]
 *     )
 * );
 * </code>
 **/

class Uniqueness extends CombinedFieldsValidator {

    private $columnMap;

    /***
	 * Executes the validation
	 **/
    public function validate($validation , $field ) {

		if ( !this->isUniqueness(validation, field) ) {

			$label   = $this->getOption("label"),
				message = $this->getOption("message");

			if ( empty label ) {
				$label = validation->getLabel(field);
			}

			if ( empty message ) {
				$message = validation->getDefaultMessage("Uniqueness");
			}

			validation->appendMessage(
				new Message(strtr(message, [":field": label]), field, "Uniqueness", $this->getOption("code"))
			);
			return false;
		}

		return true;
    }

    protected function isUniqueness($validation , $field ) {

		if ( gettype($field) != "array" ) {
			$singleField = field,
				field = [];

			$field[] = singleField;
		}

		$values = [],
			convert = $this->getOption("convert");

		foreach ( $field as $singleField ) {
			$values[singleField] = validation->getValue(singleField);
		}

		if ( convert != null ) {
			$values = {convert}(values);

			if ( !is_array(values) ) {
				throw new Exception("Value conversion must return an array");
			}
		}

		$record = $this->getOption("model");

		if ( empty record || gettype($record) != "object" ) {
			// check validation getEntity() method
			$record = validation->getEntity();
			if ( empty record ) {
				throw new Exception("Model of record must be set to property \"model\"");
			}
		}

		$isModel = record instanceof ModelInterface,
			isDocument = record instanceof CollectionInterface;

		if ( isModel ) {
			$params = $this->isUniquenessModel(record, field, values);
		} elseif ( isDocument ) {
			$params = $this->isUniquenessCollection(record, field, values);
		} else {
			throw new Exception("The uniqueness validator works only with Phalcon\\Mvc\\Model or Phalcon\\Mvc\\Collection");
		}

		$className = get_class(record);

		return {className}::count(params) == 0;
    }

    /***
	 * The column map is used in the case to get real column name
	 **/
    protected function getColumnNameReal($record , $field ) {
		if ( globals_get("orm.column_renaming") && !this->columnMap ) {
			$this->columnMap = record->getDI()
				->getShared("modelsMetadata")
				->getColumnMap(record);
		}

		if ( gettype($this->columnMap) == "array" && isset($this->columnMap[field]) ) {
			return $this->columnMap[field];
		}

		return field;
    }

    /***
	 * Uniqueness method used for model
	 **/
    protected function isUniquenessModel($record , $field , $values ) {
			fieldExcept, singleExcept, notInValues, exceptConditions, value, except;

		$exceptConditions = [],
			index  = 0,
			params = [
				"conditions": [],
				"bind":       []
			],
			except = $this->getOption("except");

		foreach ( $field as $singleField ) {
			$fieldExcept = null,
				notInValues = [],
				value = values[singleField];

			$attribute = $this->getOption("attribute", singleField);
			$attribute = $this->getColumnNameReal(record, attribute);

			if ( value != null ) {
				$params["conditions"][] = attribute . " = ?" . index;
				$params["bind"][] = value;
				$index++;
			} else {
				$params["conditions"][] = attribute . " IS NULL";
			}

			if ( except ) {
				if ( gettype($except) == "array" && array_keys(except) !== range(0, count(except) - 1) ) {
					foreach ( singleField, $except as $fieldExcept ) {
						$attribute = $this->getColumnNameReal(record, $this->getOption("attribute", singleField));
						if ( gettype($fieldExcept) == "array" ) {
							foreach ( $fieldExcept as $singleExcept ) {
								$notInValues[] = "?" . index;
								$params["bind"][] = singleExcept;
								$index++;
							}
							$exceptConditions[] = attribute . " NOT IN (" . join(",", notInValues) . ")";
						} else {
							$exceptConditions[] = attribute . " <> ?" . index;
							$params["bind"][] = fieldExcept;
							$index++;
						}
					}
				} elseif ( count(field) == 1 ) {
					$attribute = $this->getColumnNameReal(record, $this->getOption("attribute", field[0]));
					if ( gettype($except) == "array" ) {
						foreach ( $except as $singleExcept ) {
							$notInValues[] = "?" . index;
							$params["bind"][] = singleExcept;
							$index++;
						}
						$exceptConditions[] = attribute . " NOT IN (" . join(",", notInValues) . ")";
					} else {
						$params["conditions"][] = attribute . " <> ?" . index;
						$params["bind"][] = except;
						$index++;
					}
				} elseif ( count(field) > 1 ) {
					foreach ( $field as $singleField ) {
						$attribute = $this->getColumnNameReal(record, $this->getOption("attribute", singleField));
						if ( gettype($except) == "array" ) {
							foreach ( $except as $singleExcept ) {
								$notInValues[] = "?" . index;
								$params["bind"][] = singleExcept;
								$index++;
							}
							$exceptConditions[] = attribute . " NOT IN (" . join(",", notInValues) . ")";
						} else {
							$params["conditions"][] = attribute . " <> ?" . index;
							$params["bind"][] = except;
							$index++;
						}
					}
				}
			}
		}

		/**
		 * If the operation is update, there must be values in the object
		 */
		if ( record->getDirtyState() == Model::DIRTY_STATE_PERSISTENT ) {
			$metaData = record->getDI()->getShared("modelsMetadata");

			foreach ( $metaData->getPrimaryKeyAttributes(record) as $primaryField ) {
				$params["conditions"][] = $this->getColumnNameReal(record, primaryField) . " <> ?" . index;
				$params["bind"][] = record->readAttribute(primaryField);
				$index++;
			}
		}

		if ( !empty exceptConditions ) {
			$params["conditions"][] = "(" . join(" OR ", exceptConditions) . ")";
		}

		$params["conditions"] = join(" AND ", params["conditions"]);

		return params;
    }

    /***
	 * Uniqueness method used for collection
	 **/
    protected function isUniquenessCollection($record , $field , $values ) {

		$exceptConditions = [];
		$params = ["conditions" : []];

		 foreach ( $field as $singleField ) {
			$fieldExcept = null;
			$notInValues = [];
			$value = values[singleField];

			$except = $this->getOption("except");

			if ( value != null ) {
				$params["conditions"][singleField] = value;
			} else {
				$params["conditions"][singleField] = null;
			}

			if ( except ) {
				if ( gettype($except) == "array" && count(field) > 1 ) {
					if ( isset($except[singleField]) ) {
						$fieldExcept = except[singleField];
					}
				}

				if ( fieldExcept != null ) {
					if ( gettype($fieldExcept) == "array" ) {
						foreach ( $fieldExcept as $singleExcept ) {
							$notInValues[] = singleExcept;
						}
						array arrayValue = ["$nin": notInValues];
						$exceptConditions[singleField] = arrayValue;
					} else {
						array arrayValue = ["$ne": fieldExcept];
						$exceptConditions[singleField] = arrayValue;
					}
				} elseif ( gettype($except) == "array" && count(field) == 1 ) {
					foreach ( $except as $singleExcept ) {
						$notInValues[] = singleExcept;
					}
					array arrayValue = ["$nin": notInValues];
					$params["conditions"][singleField] = arrayValue;
				} elseif ( count(field) == 1 ) {
					array arrayValue = ["$ne": except];
					$params["conditions"][singleField] = arrayValue;
				}
			}
		}

		if ( record->getDirtyState() == Collection::DIRTY_STATE_PERSISTENT ) {
			array arrayValue = ["$ne": record->getId()];
			$params["conditions"]["_id"] = arrayValue;
		}

		if ( !empty exceptConditions ) {
			$params["conditions"]["$or"] = [exceptConditions];
		}

		return params;
    }

}