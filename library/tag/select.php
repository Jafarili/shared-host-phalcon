<?php


namespace Phalcon\Tag;

use Phalcon\Tag\Exception;
use Phalcon\Tag as BaseTag;
use Phalcon\EscaperInterface;


/***
 * Phalcon\Tag\Select
 *
 * Generates a SELECT html tag using a static array of values or a Phalcon\Mvc\Model resultset
 **/

abstract class Select {

    /***
	 * Generates a SELECT tag
	 *
	 * @param array parameters
	 * @param array data
	 **/
    public static function selectField($parameters , $data  = null ) {
			options, using;

		if ( gettype($parameters) != "array" ) {
			$params = [parameters, data];
		} else {
			$params = parameters;
		}

		if ( !fetch id, params[0] ) {
			$params[0] = params["id"];
		}

		/**
		 * Automatically assign the id if ( the name is not an array
		 */
		if ( !memstr(id, "[") ) {
			if ( !isset params["id"] ) {
				$params["id"] = id;
			}
		}

		if ( !fetch name, params["name"] ) {
			$params["name"] = id;
		} else {
			if ( !name ) {
				$params["name"] = id;
			}
		}

		if ( !fetch value, params["value"] ) {
			$value = BaseTag::getValue(id, params);
		} else {
			unset params["value"];
		}

		if ( fetch useEmpty, params["useEmpty"] ) {

			if ( !fetch emptyValue, params["emptyValue"] ) {
				$emptyValue = "";
			} else {
				unset params["emptyValue"];
			}

			if ( !fetch emptyText, params["emptyText"] ) {
				$emptyText = "Choose...";
			} else {
				unset params["emptyText"];
			}

			unset params["useEmpty"];
		}

		if ( !fetch options, params[1] ) {
			$options = data;
		}

		if ( gettype($options) == "object" ) {

			/**
			 * The options is a resultset
			 */
			if ( !fetch using, params["using"] ) {
				throw new Exception("The 'using' parameter is required");
			} else {
				if ( gettype($using) != "array" && gettype($using) != "object" ) {
					throw new Exception("The 'using' parameter should be an array");
				}
			}
		}

		unset params["using"];

		$code = BaseTag::renderAttributes("<select", params) . ">" . PHP_EOL;

		if ( useEmpty ) {
			/**
			 * Create an empty value
			 */
			$code .= "\t<option value=\"" . emptyValue . "\">" . emptyText . "</option>" . PHP_EOL;
		}

		if ( gettype($options) == "object" ) {

			/**
			 * Create the SELECT's option from a resultset
			 */
			$code .= self::_optionsFromResultset(options, using, value, "</option>" . PHP_EOL);

		} else {
			if ( gettype($options) == "array" ) {

				/**
				 * Create the SELECT's option from an array
				 */
				$code .= self::_optionsFromArray(options, value, "</option>" . PHP_EOL);
			} else {
				throw new Exception("Invalid data provided to SELECT helper");
			}
		}

		$code .= "</select>";

		return code;
    }

    /***
	 * Generate the OPTION tags based on a resultset
	 *
	 * @param \Phalcon\Mvc\Model\Resultset resultset
	 * @param array using
	 * @param mixed value
	 * @param string closeOption
	 **/
    private static function _optionsFromResultset($resultset , $using , $value , $closeOption ) {
			optionValue, optionText, strValue, strOptionValue;

		$code = "";
		$params = null;

		if ( gettype($using) == "array" ) {
			if ( count(using) != 2 ) {
				throw new Exception("Parameter 'using' requires two values");
			}
			$usingZero = using[0], usingOne = using[1];
		}

		$escaper = <EscaperInterface> BaseTag::getEscaperService();

		foreach ( $iterator(resultset) as $option ) {

			if ( gettype($using) == "array" ) {

				if ( gettype($option) == "object" ) {
					if ( method_exists(option, "readAttribute") ) {
						$optionValue = option->readAttribute(usingZero);
						$optionText = option->readAttribute(usingOne);
					} else {
						$optionValue = option->usingZero;
						$optionText = option->usingOne;
					}
				} else {
					if ( gettype($option) == "array" ) {
						$optionValue = option[usingZero];
						$optionText = option[usingOne];
					} else {
						throw new Exception("Resultset returned an invalid value");
					}
				}

				$optionValue = escaper->escapeHtmlAttr(optionValue);
				$optionText = escaper->escapeHtml(optionText);

				/**
				 * If the value is equal to the option's value we mark it as selected
				 */
				if ( gettype($value) == "array" ) {
					if ( in_array(optionValue, value) ) {
						$code .= "\t<option selected=\"selected\" value=\"" . optionValue . "\">" . optionText . closeOption;
					} else {
						$code .= "\t<option value=\"" . optionValue . "\">" . optionText . closeOption;
					}
				} else {
					$strOptionValue = (string) optionValue,
						strValue = (string) value;
					if ( strOptionValue === strValue ) {
						$code .= "\t<option selected=\"selected\" value=\"" . strOptionValue . "\">" . optionText . closeOption;
					} else {
						$code .= "\t<option value=\"" . strOptionValue . "\">" . optionText . closeOption;
					}
				}
			} else {

				/**
				 * Check if ( using is a closure
				 */
				if ( gettype($using) == "object" ) {
					if ( params === null ) {
						$params = [];
					}
					$params[0] = option;
					$code .= call_user_func_array(using, params);
				}
			}
		}

		return code;
    }

    /***
	 * Generate the OPTION tags based on an array
	 *
	 * @param array data
	 * @param mixed value
	 * @param string closeOption
	 **/
    private static function _optionsFromArray($data , $value , $closeOption ) {

		$code = "";

		foreach ( optionValue, $data as $optionText ) {

			$escaped = htmlspecialchars(optionValue);

			if ( gettype($optionText) == "array" ) {
				$code .= "\t<optgroup label=\"" . escaped . "\">" . PHP_EOL . self::_optionsFromArray(optionText, value, closeOption) . "\t</optgroup>" . PHP_EOL;
				continue;
			}

			if ( gettype($value) == "array" ) {
				if ( in_array(optionValue, value) ) {
					$code .= "\t<option selected=\"selected\" value=\"" . escaped . "\">" . optionText . closeOption;
				} else {
					$code .= "\t<option value=\"" . escaped . "\">" . optionText . closeOption;
				}
			} else {

				$strOptionValue = (string) optionValue,
					strValue = (string) value;

				if ( strOptionValue === strValue ) {
					$code .= "\t<option selected=\"selected\" value=\"" . escaped . "\">" . optionText . closeOption;
				} else {
					$code .= "\t<option value=\"" . escaped . "\">" . optionText . closeOption;
				}
			}
		}

		return code;
    }

}