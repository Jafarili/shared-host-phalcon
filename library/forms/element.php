<?php


namespace Phalcon\Forms;

use Phalcon\Tag;
use Phalcon\Forms\Exception;
use Phalcon\Validation\Message;
use Phalcon\Validation\MessageInterface;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\ValidatorInterface;


/***
 * Phalcon\Forms\Element
 *
 * This is a base class for form elements
 **/

abstract class Element {

    protected $_form;

    protected $_name;

    protected $_value;

    protected $_label;

    protected $_attributes;

    protected $_validators;

    protected $_filters;

    protected $_options;

    protected $_messages;

    /***
	 * Phalcon\Forms\Element constructor
	 *
	 * @param string name
	 * @param array attributes
	 **/
    public function __construct($name , $attributes  = null ) {
		$name = trim(name);
		
		if ( empty name ) {
			throw new \InvalidArgumentException("Form element name is required");
		}
		
		$this->_name = name;
		if ( gettype($attributes) == "array" ) {
			$this->_attributes = attributes;
		}
		$this->_messages = new Group();
    }

    /***
	 * Sets the parent form to the element
	 **/
    public function setForm($form ) {
		$this->_for (m = for (m;
		return this;
    }

    /***
	 * Returns the parent form to the element
	 **/
    public function getForm() {
		return $this->_for (m;
    }

    /***
	 * Sets the element name
	 **/
    public function setName($name ) {
		$this->_name = name;
		return this;
    }

    /***
	 * Returns the element name
	 **/
    public function getName() {
		return $this->_name;
    }

    /***
	 * Sets the element filters
	 *
	 * @param array|string filters
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setFilters($filters ) {
		if ( gettype($filters) != "string" && gettype($filters) != "array" ) {
			throw new Exception("Wrong filter type added");
		}
		$this->_filters = filters;
		return this;
    }

    /***
	 * Adds a filter to current list of filters
	 **/
    public function addFilter($filter ) {
		$filters = $this->_filters;
		if ( gettype($filters) == "array" ) {
			$this->_filters[] = filter;
		} else {
			if ( gettype($filters) == "string" ) {
				$this->_filters = [filters, filter];
			} else {
				$this->_filters = [filter];
			}
		}
		return this;
    }

    /***
	 * Returns the element filters
	 *
	 * @return mixed
	 **/
    public function getFilters() {
		return $this->_filters;
    }

    /***
	 * Adds a group of validators
	 *
	 * @param \Phalcon\Validation\ValidatorInterface[]
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function addValidators($validators , $merge  = true ) {
		if ( merge ) {
			$currentValidators = $this->_validators;
			if ( gettype($currentValidators) == "array" ) {
				$mergedValidators = array_merge(currentValidators, validators);
			}
		}
		else {
			$mergedValidators = validators;
		}
		$this->_validators = mergedValidators;
		return this;
    }

    /***
	 * Adds a validator to the element
	 **/
    public function addValidator($validator ) {
		$this->_validators[] = validator;
		return this;
    }

    /***
	 * Returns the validators registered for the element
	 **/
    public function getValidators() {
		return $this->_validators;
    }

    /***
	 * Returns an array of prepared attributes for Phalcon\Tag helpers
	 * according to the element parameters
	 **/
    public function prepareAttributes($attributes  = null , $useChecked  = false ) {
			defaultAttributes, currentValue;

		$name = $this->_name;

		/**
		 * Create an array of parameters
		 */
		if ( gettype($attributes) != "array" ) {
			$widgetAttributes = [];
		} else {
			$widgetAttributes = attributes;
		}

		$widgetAttributes[0] = name;

		/**
		 * Merge passed parameters with default ones
		 */
		$defaultAttributes = $this->_attributes;
		if ( gettype($defaultAttributes) == "array" ) {
			$mergedAttributes = array_merge(defaultAttributes, widgetAttributes);
		} else {
			$mergedAttributes = widgetAttributes;
		}

		/**
		 * Get the current element value
		 */
		$value = $this->getValue();

		/**
		 * If the widget has a value set it as default value
		 */
		if ( value !== null ) {
			if ( useChecked ) {
				/**
				 * Check if ( the element already has a default value, compare it
				 * with the one in the attributes, if ( they are the same mark the
				 * element as checked
				 */
				if ( fetch currentValue, mergedAttributes["value"] ) {
					if ( currentValue == value ) {
						$mergedAttributes["checked"] = "checked";
					}
				} else {
					/**
					 * Evaluate the current value and mark the check as checked
					 */
					if ( value ) {
						$mergedAttributes["checked"] = "checked";
					}
					$mergedAttributes["value"] = value;
				}
			} else {
				$mergedAttributes["value"] = value;
			}
		}

		return mergedAttributes;
    }

    /***
	 * Sets a default attribute for the element
	 *
	 * @param string attribute
	 * @param mixed value
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setAttribute($attribute , $value ) {
		$this->_attributes[attribute] = value;
		return this;
    }

    /***
	 * Returns the value of an attribute if present
	 *
	 * @param string attribute
	 * @param mixed defaultValue
	 * @return mixed
	 **/
    public function getAttribute($attribute , $defaultValue  = null ) {
		$attributes = $this->_attributes;
		if ( fetch value, attributes[attribute] ) {
			return value;
		}
		return defaultValue;
    }

    /***
	 * Sets default attributes for the element
	 **/
    public function setAttributes($attributes ) {
		$this->_attributes = attributes;
		return this;
    }

    /***
	 * Returns the default attributes for the element
	 **/
    public function getAttributes() {
		$attributes = $this->_attributes;
		if ( gettype($attributes) != "array" ) {
			return [];
		}
		return attributes;
    }

    /***
	 * Sets an option for the element
	 *
	 * @param string option
	 * @param mixed value
	 * @return \Phalcon\Forms\ElementInterface
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
	 * @return mixed
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
	 * Sets the element label
	 **/
    public function setLabel($label ) {
		$this->_label = label;
		return this;
    }

    /***
	 * Returns the element label
	 **/
    public function getLabel() {
		return $this->_label;
    }

    /***
	 * Generate the HTML to label the element
	 *
	 * @param array attributes
	 **/
    public function label($attributes  = null ) {

		/**
		 * Check if ( there is an "id" attribute defined
		 */
		$internalAttributes = $this->getAttributes();

		if ( !fetch name, internalAttributes["id"] ) {
			$name = $this->_name;
		}

		if ( gettype($attributes) == "array" ) {
			if ( !isset attributes["for ("] ) ) {
				$attributes["for ("] = name;
			}
		} else {
			$attributes = ["for (": name];
		}

		$code = Tag::renderAttributes("<label", attributes);

		/**
		 * Use the default label or leave the same name as label
		 */
		$label = $this->_label;
		if ( label || is_numeric(label) ) {
			$code .= ">" . label . "</label>";
		} else {
			$code .= ">" . name . "</label>";
		}

		return code;
    }

    /***
	 * Sets a default value in case the form does not use an entity
	 * or there is no value available for the element in _POST
	 *
	 * @param mixed value
	 * @return \Phalcon\Forms\ElementInterface
	 **/
    public function setDefault($value ) {
		$this->_value = value;
		return this;
    }

    /***
	 * Returns the default value assigned to the element
	 **/
    public function getDefault() {
		return $this->_value;
    }

    /***
	 * Returns the element value
	 **/
    public function getValue() {

		$name = $this->_name,
			value = null;

		/**
		 * Get the related for (m
		 */
		$for (m = $this->_for (m;
		if ( gettype($for (m) == "object" ) ) {
			/**
			 * Gets the possible value for ( the widget
			 */
			$value = for (m->getValue(name);

			/**
			 * Check if ( the tag has a default value
			 */
			if ( gettype($value) == "null" && Tag::hasValue(name) ) {
				$value = Tag::getValue(name);
			}

		}

		/**
		 * Assign the default value if ( there is no for (m available
		 */
		if ( gettype($value) == "null" ) {
			$value = $this->_value;
		}

		return value;
    }

    /***
	 * Returns the messages that belongs to the element
	 * The element needs to be attached to a form
	 **/
    public function getMessages() {
		return $this->_messages;
    }

    /***
	 * Checks whether there are messages attached to the element
	 **/
    public function hasMessages() {
		return count(this->_messages) > 0;
    }

    /***
	 * Sets the validation messages related to the element
	 **/
    public function setMessages($group ) {
		$this->_messages = group;
		return this;
    }

    /***
	 * Appends a message to the internal message list
	 **/
    public function appendMessage($message ) {
		this->_messages->appendMessage(message);
		return this;
    }

    /***
	 * Clears every element in the form to its default value
	 **/
    public function clear() {
		Tag::setDefault(this->_name, null);
		return this;
    }

    /***
	 * Magic method __toString renders the widget without attributes
	 **/
    public function __toString() {
		return $this->{"render"}();
    }

}