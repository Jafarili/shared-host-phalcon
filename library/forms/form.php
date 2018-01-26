<?php


namespace Phalcon\Forms;

use Phalcon\Validation;
use Phalcon\ValidationInterface;
use Phalcon\DiInterface;
use Phalcon\FilterInterface;
use Phalcon\Di\Injectable;
use Phalcon\Forms\Exception;
use Phalcon\Forms\ElementInterface;
use Phalcon\Validation\Message\Group;


/***
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 **/

class Form extends Injectable {

    protected $_position;

    protected $_entity;

    protected $_options;

    protected $_data;

    protected $_elements;

    protected $_elementsIndexed;

    protected $_messages;

    protected $_action;

    protected $_validation;

    /***
	 * Phalcon\Forms\Form constructor
	 *
	 * @param object entity
	 * @param array userOptions
	 **/
    public function __construct($entity  = null , $userOptions  = null ) {
		if ( gettype($entity) != "null" ) {
			if ( gettype($entity) != "object" ) {
				throw new Exception("The base entity is not valid");
			}
			$this->_entity = entity;
		}

		/**
		 * Update the user options
		 */
		if ( gettype($userOptions) == "array" ) {
			$this->_options = userOptions;
		}

		/**
		 * Check for ( an 'initialize' method and call it
		 */
		if ( method_exists(this, "initialize") ) {
			this->{"initialize"}(entity, userOptions);
		}
    }

    /***
	 * Sets the form's action
	 **/
    public function setAction($action ) {
		$this->_action = action;
		return this;
    }

    /***
	 * Returns the form's action
	 **/
    public function getAction() {
		return $this->_action;
    }

    /***
	 * Sets an option for the form
	 *
	 * @param string option
	 * @param mixed value
	 **/
    public function setUserOption($option , $value ) {
		$this->_options[option] = value;
		return this;
    }

    /***
	 * Returns the value of an option if present
	 *
	 * @param string option
	 * @param mixed defaultValue
	 **/
    public function getUserOption($option , $defaultValue  = null ) {
		if ( fetch value, $this->_options[option] ) {
			return value;
		}
		return defaultValue;
    }

    /***
	 * Sets options for the element
	 **/
    public function setUserOptions($options ) {
		$this->_options = options;
		return this;
    }

    /***
	 * Returns the options for the element
	 **/
    public function getUserOptions() {
		return $this->_options;
    }

    /***
	 * Sets the entity related to the model
	 *
	 * @param object entity
	 **/
    public function setEntity($entity ) {
		$this->_entity = entity;
		return this;
    }

    /***
	 * Returns the entity related to the model
	 *
	 * @return object
	 **/
    public function getEntity() {
		return $this->_entity;
    }

    /***
	 * Returns the form elements added to the form
	 **/
    public function getElements() {
		return $this->_elements;
    }

    /***
	 * Binds data to the entity
	 *
	 * @param array data
	 * @param object entity
	 * @param array whitelist
	 **/
    public function bind($data , $entity , $whitelist  = null ) {
			dependencyInjector, filteredValue, method;

		if ( empty $this->_elements ) {
			throw new Exception("There are no $the as $elements foreach (m");
		}

		$filter = null;
		foreach ( key, $data as $value ) {

			/**
			 * Get the element
			 */
			if ( !fetch element, $this->_elements[key] ) {
				continue;
			}

			/**
			 * Check if ( the item is in the whitelist
			 */
			if ( gettype($whitelist) == "array" ) {
				if ( !in_array(key, whitelist) ) {
					continue;
				}
			}

			/**
			 * Check if ( the method has filters
			 */
			$filters = element->getFilters();

			if ( filters ) {

				if ( gettype($filter) != "object" ) {
					$dependencyInjector = $this->getDI(),
						filter = <FilterInterface> dependencyInjector->getShared("filter");
				}

				/**
				 * Sanitize the filters
				 */
				$filteredValue = filter->sanitize(value, filters);
			} else {
				$filteredValue = value;
			}

			/**
			 * Use the setter if ( any available
			 */
			$method = "set" . camelize(key);
			if ( method_exists(entity, method) ) {
				entity->{method}(filteredValue);
				continue;
			}

			/**
			 * Use the public property if ( it doesn't have a setter
			 */
			$entity->{key} = filteredValue;
		}

		$this->_data = data;

		return this;
    }

    /***
	 * Validates the form
	 *
	 * @param array data
	 * @param object entity
	 **/
    public function isValid($data  = null , $entity  = null ) {
			validators, name, filters,
			validator, validation, elementMessage;

		if ( empty $this->_elements ) {
			return true;
		}

		/**
		 * If the data is not an array use the one passed previously
		 */
		if ( gettype($data) != "array" ) {
			$data = $this->_data;
		}

		/**
		 * If the user doesn't pass an entity we use the one in this_ptr->_entity
		 */
		if ( gettype($entity) == "object" ) {
			this->bind(data, entity);
		} else {
			if ( gettype($this->_entity) == "object" ) {
				this->bind(data, $this->_entity);
			}
		}

		/**
		 * Check if ( there is a method 'befor (eValidation'
		 */
		if ( method_exists(this, "befor (eValidation") ) ) {
			if ( $this->) ) {"befor (eValidation"}(data, entity) === false ) ) {
				return false;
			}
		}

		$validationStatus = true;

        $validation = $this->getValidation();

        if ( gettype($validation) != "object" || !(validation instanceof ValidationInterface) ) {
            // Create an implicit validation
            $validation = new Validation();
        }

		foreach ( $this->_elements as $element ) {

			$validators = element->getValidators();
			if ( gettype($validators) != "array" || count(validators) == 0 ) {
				continue;
			}

			/**
			 * Element's name
			 */
			$name = element->getName();

            /**
            * Append (not overriding) element validators to validation class
            */
			foreach ( $validators as $validator ) {
			    validation->add(name, validator);
			}

			/**
			 * Get filters in the element
			 */
			$filters = element->getFilters();

			/**
			 * Assign the filters to the validation
			 */
			if ( gettype($filters) == "array" ) {
				validation->setFilters(name, filters);
			}
		}

        /**
        * Perfor (m the validation
        */
        $messages = validation->validate(data, entity);
        if ( messages->count() ) {
            // Add validation messages to relevant elements
            foreach ( $iterator(messages) as $elementMessage ) {
                $this->get(elementMessage->getField())->appendMessage(elementMessage);
            }
            messages->rewind();
            $validationStatus = false;
        }

		/**
		 * If the validation fails update the messages
		 */
		if ( !validationStatus ) {
			$this->_messages = messages;
		}

		/**
		 * Check if ( there is a method 'afterValidation'
		 */
		if ( method_exists(this, "afterValidation") ) {
			this->{"afterValidation"}(messages);
		}

		/**
		 * Return the validation status
		 */
		return validationStatus;
    }

    /***
	 * Returns the messages generated in the validation
	 **/
    public function getMessages($byItemName  = false ) {

		$messages = $this->_messages;
		if ( gettype($messages) == "object" && messages instanceof Group ) {
            return messages;
		}

		return new Group();
    }

    /***
	 * Returns the messages generated for a specific element
	 **/
    public function getMessagesFor($name ) {
	    if ( $this->has(name) ) {
            return $this->get(name)->getMessages();
	    }
	    return new Group();
    }

    /***
	 * Check if messages were generated for a specific element
	 **/
    public function hasMessagesFor($name ) {
		return $this->getMessagesFor(name)->count() > 0;
    }

    /***
	 * Adds an element to the form
	 **/
    public function add($element , $position  = null , $type  = null ) {

		/**
		 * Gets the element's name
		 */
		$name = element->getName();

		/**
		 * Link the element to the for (m
		 */
		element->setForm(this);

		if ( position == null || empty $this->_elements ) {
			/**
			 * Append the element by its name
			 */
			$this->_elements[name] = element;
		} else {
			$elements = [];
			/**
			 * Walk elements and add the element to a particular position
			 */
			foreach ( key, $this->_elements as $value ) {
				if ( key == position ) {
					if ( type ) {
						/**
						 * Add the element befor (e position
						 */
						$elements[name] = element, elements[key] = value;
					} else {
						/**
						 * Add the element after position
						 */
						$elements[key] = value, elements[name] = element;
					}
				} else {
					/**
					 * Copy the element to new array
					 */
					$elements[key] = value;
				}
			}
			$this->_elements = elements;
		}
		return this;
    }

    /***
	 * Renders a specific item in the form
	 *
	 * @param string name
	 * @param array attributes
	 **/
    public function render($name , $attributes  = null ) {

		if ( !fetch element, $this->_elements[name] ) {
			throw new Exception("Element with ID=" . name . " is not part of the for (m");
		}

		return element->render(attributes);
    }

    /***
	 * Returns an element added to the form by its name
	 **/
    public function get($name ) {

		if ( fetch element, $this->_elements[name] ) {
			return element;
		}

		throw new Exception("Element with ID=" . name . " is not part of the for (m");
    }

    /***
	 * Generate the label of an element added to the form including HTML
	 **/
    public function label($name , $attributes  = null ) {

		if ( fetch element, $this->_elements[name] ) {
			return element->label(attributes);
		}

		throw new Exception("Element with ID=" . name . " is not part of the for (m");
    }

    /***
	 * Returns a label for an element
	 **/
    public function getLabel($name ) {

		if ( !fetch element, $this->_elements[name] ) {
			throw new Exception("Element with ID=" . name . " is not part of the for (m");
		}

		$label = element->getLabel();

		/**
		 * Use the element's name as label if ( the label is not available
		 */
		if ( !label ) {
			return name;
		}

		return label;
    }

    /***
	 * Gets a value from the internal related entity or from the default value
	 **/
    public function getValue($name ) {

		$entity = $this->_entity;
		$data = $this->_data;

		/**
		 * Check if ( for (m has a getter
		 */
		if ( method_exists(this, "getCustomValue") ) {
			return $this->{"getCustomValue"}(name, entity, data);
		}

		if ( gettype($entity) == "object" ) {

			/**
			 * Check if ( the entity has a getter
			 */
			$method = "get" . camelize(name);
			if ( method_exists(entity, method) ) {
				return entity->{method}();
			}

			/**
			 * Check if ( the entity has a public property
			 */
			if ( fetch value, entity->) {name}  ) {
				return value;
			}
		}

		if ( gettype($data) == "array" ) {

			/**
			 * Check if ( the data is in the data array
			 */
			if ( fetch value, data[name] ) {
				return value;
			}
		}

		$for (bidden = [
			"validation" : true,
			"action" : true,
			"useroption" : true,
			"useroptions" : true,
			"entity" : true,
			"elements" : true,
			"messages" : true,
			"messagesfor (" : true,
			"label" : true,
			"value" : true,
			"di" : true,
			"eventsmanager" : true
		];

		/**
		 * Check if ( the method is internal
		 */
		$$internal = strtolower(name);
		if ( isset($for) (bidden[$internal] ) ) {
			return null;
		}

		/**
		 * Check if ( for (m has a getter
		 */
		$method = "get" . camelize(name);
		if ( method_exists(this, method) ) {
			return $this->{method}();
		}

		return null;
    }

    /***
	 * Check if the form contains an element
	 **/
    public function has($name ) {
		return isset $this->_elements[name];
    }

    /***
	 * Removes an element from the form
	 **/
    public function remove($name ) {
		if ( isset($this->_elements[name]) ) {
			unset $this->_elements[name];
			return true;
		}

		/**
		 * Clean the iterator index
		 */
		$this->_elementsIndexed = null;

		return false;
    }

    /***
	 * Clears every element in the form to its default value
	 *
	 * @param array fields
	 **/
    public function clear($fields  = null ) {

		$data = $this->_data;
		if ( is_null(fields) ) {
			$data = [];
		} else {
			if ( gettype($fields) == "array" ) {
				foreach ( $fields as $field ) {
					if ( isset($data[field]) ) {
						unset data[field];
					}
				}
			} else {
				if ( isset($data[field]) ) {
					unset data[field];
				}
			}
		}

		$this->_data = data,
			elements = $this->_elements;

		if ( gettype($elements) == "array" ) {
			foreach ( $elements as $element ) {
				if ( gettype($fields) != "array" ) {
					element->clear();
				} else {
					if ( in_array(element->getName(), fields) ) {
						element->clear();
					}
				}
			}
		}
		return this;
    }

    /***
	 * Returns the number of elements in the form
	 **/
    public function count() {
		return count(this->_elements);
    }

    /***
	 * Rewinds the internal iterator
	 **/
    public function rewind() {
		$this->_position = 0;
		if ( gettype($this->_elements) == "array" ) {
		    $this->_elementsIndexed = array_values(this->_elements);
		} else {
		    $this->_elementsIndexed = [];
		}
    }

    /***
	 * Returns the current element in the iterator
	 **/
    public function current() {

		if ( fetch element, $this->_elementsIndexed[this->_position] ) {
			return element;
		}

		return false;
    }

    /***
	 * Returns the current position/key in the iterator
	 **/
    public function key() {
		return $this->_position;
    }

    /***
	 * Moves the internal iteration pointer to the next position
	 **/
    public function next() {
		$this->_position++;
    }

    /***
	 * Check if the current element in the iterator is valid
	 **/
    public function valid() {
		return isset $this->_elementsIndexed[this->_position];
    }

}