<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\Annotation;
use Phalcon\Annotations\Exception;


/***
 * Phalcon\Annotations\Annotation
 *
 * Represents a single annotation in an annotations collection
 **/

class Annotation {

    /***
	 * Annotation Name
	 * @var string
	 **/
    protected $_name;

    /***
	 * Annotation Arguments
	 * @var string
	 **/
    protected $_arguments;

    /***
	 * Annotation ExprArguments
	 * @var string
	 **/
    protected $_exprArguments;

    /***
	 * Phalcon\Annotations\Annotation constructor
	 **/
    public function __construct($reflectionData ) {

		$this->_name = reflectionData["name"];

		/**
		 * Process annotation arguments
		 */
		if ( fetch exprArguments, reflectionData["arguments"] ) {
			$arguments = [];
			foreach ( $exprArguments as $argument ) {
				$resolvedArgument =  $this->getExpression(argument["expr"]);
				if ( fetch name, argument["name"] ) {
					$arguments[name] = resolvedArgument;
				} else {
					$arguments[] = resolvedArgument;
				}
			}
			$this->_arguments = arguments;
			$this->_exprArguments = exprArguments;
		}
    }

    /***
	 * Returns the annotation's name
	 **/
    public function getName() {
		return $this->_name;
    }

    /***
	 * Resolves an annotation expression
	 *
	 * @param array expr
	 * @return mixed
	 **/
    public function getExpression($expr ) {

		$type = expr["type"];
		switch type {

			case PHANNOT_T_INTEGER:
			case PHANNOT_T_DOUBLE:
			case PHANNOT_T_STRING:
			case PHANNOT_T_IDENTIFIER:
				$value = expr["value"];
				break;

			case PHANNOT_T_NULL:
				$value = null;
				break;

			case PHANNOT_T_FALSE:
				$value = false;
				break;

			case PHANNOT_T_TRUE:
				$value = true;
				break;

			case PHANNOT_T_ARRAY:
				$arrayValue = [];
				for ( item in expr["items"] ) {
					$resolvedItem = $this->getExpression(item["expr"]);
					if ( fetch name, item["name"] ) {
						$arrayValue[name] = resolvedItem;
					} else {
						$arrayValue[] = resolvedItem;
					}
				}
				return arrayValue;

			case PHANNOT_T_ANNOTATION:
				return new Annotation(expr);

			default:
				throw new Exception("The expression ". type. " is unknown");
		}

		return value;
    }

    /***
	 * Returns the expression arguments without resolving
	 *
	 * @return array
	 **/
    public function getExprArguments() {
		return $this->_exprArguments;
    }

    /***
	 * Returns the expression arguments
	 *
	 * @return array
	 **/
    public function getArguments() {
		return $this->_arguments;
    }

    /***
	 * Returns the number of arguments that the annotation has
	 **/
    public function numberArguments() {
		return count(this->_arguments);
    }

    /***
	 * Returns an argument in a specific position
	 *
	 * @param int|string position
	 * @return mixed
	 **/
    public function getArgument($position ) {
		if ( fetch argument, $this->_arguments[position] ) {
			return argument;
		}
    }

    /***
	 * Returns an argument in a specific position
	 *
	 * @param int|string position
	 * @return boolean
	 **/
    public function hasArgument($position ) {
		return isset $this->_arguments[position];
    }

    /***
	 * Returns a named argument
	 *
	 * @return mixed
	 **/
    public function getNamedArgument($name ) {
		if ( fetch argument, $this->_arguments[name] ) {
			return argument;
		}
    }

    /***
	 * Returns a named parameter
	 *
	 * @return mixed
	 **/
    public function getNamedParameter($name ) {
		return $this->getNamedArgument(name);
    }

}