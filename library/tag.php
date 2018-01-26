<?php


namespace Phalcon;

use Phalcon\Tag\Select;
use Phalcon\Tag\Exception;
use Phalcon\Mvc\UrlInterface;


/***
 * Phalcon\Tag
 *
 * Phalcon\Tag is designed to simplify building of HTML tags.
 * It provides a set of helpers to generate HTML in a dynamic way.
 * This component is an abstract class that you can extend to add more helpers.
 **/

class Tag {

    const HTML32= 1;

    const HTML401_STRICT= 2;

    const HTML401_TRANSITIONAL= 3;

    const HTML401_FRAMESET= 4;

    const HTML5= 5;

    const XHTML10_STRICT= 6;

    const XHTML10_TRANSITIONAL= 7;

    const XHTML10_FRAMESET= 8;

    const XHTML11= 9;

    const XHTML20= 10;

    const XHTML5= 11;

    /***
	 * Pre-assigned values for components
	 **/
    protected static $_displayValues;

    /***
	 * HTML document title
	 **/
    protected static $_documentTitle;

    protected static $_documentAppendTitle;

    protected static $_documentPrependTitle;

    protected static $_documentTitleSeparator;

    protected static $_documentType;

    /***
	 * Framework Dispatcher
	 **/
    protected static $_dependencyInjector;

    protected static $_urlService;

    protected static $_dispatcherService;

    protected static $_escaperService;

    protected static $_autoEscape;

    /***
	 * Obtains the 'escaper' service if required
	 *
	 * @param array params
	 * @return EscaperInterface
	 **/
    public static function getEscaper($params ) {

		if ( !fetch autoescape, params["escape"] ) {
			$autoescape = self::_autoEscape;
		}

		if ( !autoescape ) {
			return null;
		}

		return self::getEscaperService();
    }

    /***
	 * Renders parameters keeping order in their HTML attributes
	 **/
    public static function renderAttributes($code , $attributes ) {

		$order = [
			"rel"    : null,
			"type"   : null,
			"for ("    : null,
			"src"    : null,
			"href"   : null,
			"action" : null,
			"id"     : null,
			"name"   : null,
			"value"  : null,
			"class"  : null
		];

		$attrs = [];
		foreach ( key, $order as $value ) {
			if ( fetch attribute, attributes[key] ) {
				$attrs[key] = attribute;
			}
		}

		foreach ( key, $attributes as $value ) {
			if ( !isset($attrs[key]) ) {
				$attrs[key] = value;
			}
		}

		$escaper = <EscaperInterface> self::getEscaper(attributes);

		unset attrs["escape"];

		$newCode = code;
		foreach ( key, $attrs as $value ) {
			if ( gettype($key) == "string" && value !== null ) {
				if ( gettype($value) == "array" || gettype($value) == "resource" ) {
					throw new Exception("Value at index: '" . key . "' type: '" . gettype(value) . "' cannot be rendered");
				}
				if ( escaper ) {
					$escaped = escaper->escapeHtmlAttr(value);
				} else {
					$escaped = value;
				}
				$newCode .= " " . key . "=\"" . escaped . "\"";
			}
		}

		return newCode;
    }

    /***
	 * Sets the dependency injector container.
	 **/
    public static function setDI($dependencyInjector ) {
		$self::_dependencyInjector = dependencyInjector;
    }

    /***
	 * Internally gets the request dispatcher
	 **/
    public static function getDI() {
		$di = self::_dependencyInjector;
		if ( gettype($di) != "object" ) {
			$di = Di::getDefault();
		}
		return di;
    }

    /***
	 * Returns a URL service from the default DI
	 **/
    public static function getUrlService() {

		$url = self::_urlService;
		if ( gettype($url) != "object" ) {

			$dependencyInjector = self::getDI();

			if ( gettype($dependencyInjector) != "object" ) {
				throw new Exception("A dependency injector container is required to obtain the 'url' service");
			}

			$url = <UrlInterface> dependencyInjector->getShared("url"),
				self::_urlService = url;
		}
		return url;
    }

    /***
	 * Returns an Escaper service from the default DI
	 **/
    public static function getEscaperService() {

		$escaper = self::_escaperService;
		if ( gettype($escaper) != "object" ) {

			$dependencyInjector = self::getDI();

			if ( gettype($dependencyInjector) != "object" ) {
				throw new Exception("A dependency injector container is required to obtain the 'escaper' service");
			}

			$escaper = <EscaperInterface> dependencyInjector->getShared("escaper"),
				self::_escaperService = escaper;
		}
		return escaper;
    }

    /***
	 * Set autoescape mode in generated html
	 **/
    public static function setAutoescape($autoescape ) {
		$self::_autoEscape = autoescape;
    }

    /***
	 * Assigns default values to generated tags by helpers
	 *
	 * <code>
	 * // Assigning "peter" to "name" component
	 * Phalcon\Tag::setDefault("name", "peter");
	 *
	 * // Later in the view
	 * echo Phalcon\Tag::textField("name"); // Will have the value "peter" by default
	 * </code>
	 *
	 * @param string id
	 * @param string value
	 **/
    public static function setDefault($id , $value ) {
		if ( value !== null ) {
			if ( gettype($value) == "array" || gettype($value) == "object" ) {
				throw new Exception("Only scalar values can be assigned to UI components");
			}
		}
		$self::_displayValues[id] = value;
    }

    /***
	 * Assigns default values to generated tags by helpers
	 *
	 * <code>
	 * // Assigning "peter" to "name" component
	 * Phalcon\Tag::setDefaults(
	 *     [
	 *         "name" => "peter",
	 *     ]
	 * );
	 *
	 * // Later in the view
	 * echo Phalcon\Tag::textField("name"); // Will have the value "peter" by default
	 * </code>
	 **/
    public static function setDefaults($values , $merge  = false ) {
		if ( merge && typeof self::_displayValues == "array" ) {
			$self::_displayValues = array_merge(self::_displayValues, values);
		} else {
			$self::_displayValues = values;
		}
    }

    /***
	 * Alias of Phalcon\Tag::setDefault
	 *
	 * @param string id
	 * @param string value
	 **/
    public static function displayTo($id , $value ) {
		self::setDefault(id, value);
    }

    /***
	 * Check if a helper has a default value set using Phalcon\Tag::setDefault or value from $_POST
	 *
	 * @param string name
	 * @return boolean
	 **/
    public static function hasValue($name ) {
		return isset self::_displayValues[name] || isset _POST[name];
    }

    /***
	 * Every helper calls this function to check whether a component has a predefined
	 * value using Phalcon\Tag::setDefault or value from $_POST
	 *
	 * @param string name
	 * @param array params
	 * @return mixed
	 **/
    public static function getValue($name , $params  = null ) {

		if ( !params || !fetch value, params["value"] ) {
			/**
			 * Check if ( there is a predefined value for ( it
			 */
			if ( !fetch value, self::_displayValues[name] ) {
				/**
				 * Check if ( there is a post value for ( the item
				 */
				if ( !fetch value, _POST[name] ) {
					return null;
				}
			}
		}

		return value;
    }

    /***
	 * Resets the request and internal values to avoid those fields will have any default value.
	 * @deprecated Will be removed in 4.0.0
	 **/
    public static function resetInput() {
		$self::_displayValues = [],
			self::_documentTitle = null,
			self::_documentAppendTitle = [],
			self::_documentPrependTitle = [],
			self::_documentTitleSeparator = null;
    }

    /***
	 * Builds a HTML A tag using framework conventions
	 *
	 *<code>
	 * echo Phalcon\Tag::linkTo("signup/register", "Register Here!");
	 *
	 * echo Phalcon\Tag::linkTo(
	 *     [
	 *         "signup/register",
	 *         "Register Here!"
	 *     ]
	 * );
	 *
	 * echo Phalcon\Tag::linkTo(
	 *     [
	 *         "signup/register",
	 *         "Register Here!",
	 *         "class" => "btn-primary",
	 *     ]
	 * );
	 *
	 * echo Phalcon\Tag::linkTo("http://phalconphp.com/", "Phalcon", false);
	 *
	 * echo Phalcon\Tag::linkTo(
	 *     [
	 *         "http://phalconphp.com/",
	 *         "Phalcon Home",
	 *         false,
	 *     ]
	 * );
	 *
	 * echo Phalcon\Tag::linkTo(
	 *     [
	 *         "http://phalconphp.com/",
	 *         "Phalcon Home",
	 *         "local" => false,
	 *     ]
	 * );
	 *
	 * echo Phalcon\Tag::linkTo(
	 *     [
	 *         "action" => "http://phalconphp.com/",
	 *         "text"   => "Phalcon Home",
	 *         "local"  => false,
	 *         "target" => "_new"
	 *     ]
	 * );
	 *
	 *</code>
	 *
	 * @param array|string parameters
	 * @param string text
	 * @param boolean local
	 **/
    public static function linkTo($parameters , $text  = null , $local  = true ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters, text, local];
		} else {
			$params = parameters;
		}

		if ( !fetch action, params[0] ) {
			if ( !fetch action, params["action"] ) {
				$action = "";
			} else {
				unset params["action"];
			}
		}

		if ( !fetch text, params[1] ) {
			if ( !fetch text, params["text"] ) {
				$text = "";
			} else {
				unset params["text"];
			}
		}

		if ( !fetch local, params[2] ) {
			if ( !fetch local, params["local"] ) {
				$local = true;
			} else {
				unset params["local"];
			}
		}

		if ( fetch query, params["query"] ) {
			unset params["query"];
		} else  {
			$query = null;
		}

		$url = self::getUrlService(),
			params["href"] = url->get(action, query, local),
			code = self::renderAttributes("<a", params),
			code .= ">" . text . "</a>";

		return code;
    }

    /***
	 * Builds generic INPUT tags
	 *
	 * @param string type
	 * @param array parameters
	 * @param boolean asValue
	 * @return string
	 **/
    static protected final function _inputField($type , $parameters , $asValue  = false ) {

		$params = [];

		if ( gettype($parameters) != "array" ) {
			$params[] = parameters;
		} else {
			$params = parameters;
		}

		if ( asValue == false ) {

			if ( !fetch id, params[0] ) {
				$params[0] = params["id"];
			}

			if ( fetch name, params["name"] ) {
				if ( empty name ) {
					$params["name"] = id;
				}
			} else {
				$params["name"] = id;
			}

			/**
			 * Automatically assign the id if ( the name is not an array
			 */
			if ( gettype($id) == "string" ) {
				if ( !memstr(id, "[") && !isset params["id"] ) {
					$params["id"] = id;
				}
			}

			$params["value"] = self::getValue(id, params);

		} else {
			/**
			 * Use the "id" as value if ( the user hadn't set it
			 */
			if ( !isset params["value"] ) {
				if ( fetch value, params[0] ) {
					$params["value"] = value;
				}
			}
		}

		$params["type"] = type,
			code = self::renderAttributes("<input", params);

		/**
		 * Check if ( Doctype is XHTML
		 */
		if ( self::_documentType > self::HTML5 ) {
			$code .= " />";
		} else {
			$code .= ">";
		}

		return code;
    }

    /***
	 * Builds INPUT tags that implements the checked attribute
	 *
	 * @param string type
	 * @param array parameters
	 * @return string
	 **/
    static protected final function _inputFieldChecked($type , $parameters ) {

		if (  gettype($parameters) != "array" ) {
			$params = [parameters];
		} else {
			$params = parameters;
		}

		if ( !isset($params[0]) ) {
			$params[0] = params["id"];
		}

		$id = params[0];
		if ( !isset params["name"] ) {
			$params["name"] = id;
		} else {
			$name = params["name"];
			if ( empty name ) {
				$params["name"] = id;
			}
		}

		/**
		* Automatically assign the id if ( the name is not an array
		*/
		if ( !strpos(id, "[") ) {
			if ( !isset params["id"] ) {
				$params["id"] = id;
			}
		}

		/**
		 * Automatically check inputs
		 */
		if ( fetch currentValue, params["value"] ) {
			unset params["value"];

			$value = self::getValue(id, params);

			if ( value != null && currentValue == value ) {
				$params["checked"] = "checked";
			}
			$params["value"] = currentValue;
		} else {
			$value = self::getValue(id, params);

			/**
			* Evaluate the value in POST
			*/
			if ( value != null ) {
				$params["checked"] = "checked";
			}

			/**
			* Update the value anyways
			*/
			$params["value"] = value;
		}

		$params["type"] = type,
			code = self::renderAttributes("<input", params);

		/**
		 * Check if ( Doctype is XHTML
		 */
		if ( self::_documentType > self::HTML5 ) {
			$code .= " />";
		} else {
			$code .= ">";
		}

		return code;
    }

    /***
	 * Builds a HTML input[type="color"] tag
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function colorField($parameters ) {
		return self::_inputField("color", parameters);
    }

    /***
	 * Builds a HTML input[type="text"] tag
	 *
	 * <code>
	 * echo Phalcon\Tag::textField(
	 *     [
	 *         "name",
	 *         "size" => 30,
	 *     ]
	 * );
	 * </code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function textField($parameters ) {
		return self::_inputField("text", parameters);
    }

    /***
	 * Builds a HTML input[type="number"] tag
	 *
	 * <code>
	 * echo Phalcon\Tag::numericField(
	 *     [
	 *         "price",
	 *         "min" => "1",
	 *         "max" => "5",
	 *     ]
	 * );
	 * </code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function numericField($parameters ) {
		return self::_inputField("number", parameters);
    }

    /***
	* Builds a HTML input[type="range"] tag
	*
	* @param array parameters
	* @return string
	**/
    public static function rangeField($parameters ) {
		return self::_inputField("range", parameters);
    }

    /***
	 * Builds a HTML input[type="email"] tag
	 *
	 * <code>
	 * echo Phalcon\Tag::emailField("email");
	 * </code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function emailField($parameters ) {
		return self::_inputField("email", parameters);
    }

    /***
	 * Builds a HTML input[type="date"] tag
	 *
	 * <code>
	 * echo Phalcon\Tag::dateField(
	 *     [
	 *         "born",
	 *         "value" => "14-12-1980",
	 *     ]
	 * );
	 * </code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function dateField($parameters ) {
		return self::_inputField("date", parameters);
    }

    /***
	* Builds a HTML input[type="datetime"] tag
	*
	* @param array parameters
	* @return string
	**/
    public static function dateTimeField($parameters ) {
		return self::_inputField("datetime", parameters);
    }

    /***
	* Builds a HTML input[type="datetime-local"] tag
	*
	* @param array parameters
	* @return string
	**/
    public static function dateTimeLocalField($parameters ) {
		return self::_inputField("datetime-local", parameters);
    }

    /***
	 * Builds a HTML input[type="month"] tag
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function monthField($parameters ) {
		return self::_inputField("month", parameters);
    }

    /***
	 * Builds a HTML input[type="time"] tag
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function timeField($parameters ) {
		return self::_inputField("time", parameters);
    }

    /***
	 * Builds a HTML input[type="week"] tag
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function weekField($parameters ) {
		return self::_inputField("week", parameters);
    }

    /***
	 * Builds a HTML input[type="password"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::passwordField(
	 *     [
	 *         "name",
	 *         "size" => 30,
	 *     ]
	 * );
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function passwordField($parameters ) {
		return self::_inputField("password", parameters);
    }

    /***
	 * Builds a HTML input[type="hidden"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::hiddenField(
	 *     [
	 *         "name",
	 *         "value" => "mike",
	 *     ]
	 * );
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function hiddenField($parameters ) {
		return self::_inputField("hidden", parameters);
    }

    /***
	 * Builds a HTML input[type="file"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::fileField("file");
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function fileField($parameters ) {
		return self::_inputField("file", parameters);
    }

    /***
	 * Builds a HTML input[type="search"] tag
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function searchField($parameters ) {
		return self::_inputField("search", parameters);
    }

    /***
	* Builds a HTML input[type="tel"] tag
	*
	* @param array parameters
	* @return string
	**/
    public static function telField($parameters ) {
		return self::_inputField("tel", parameters);
    }

    /***
	 * Builds a HTML input[type="url"] tag
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function urlField($parameters ) {
		return self::_inputField("url", parameters);
    }

    /***
	 * Builds a HTML input[type="check"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::checkField(
	 *     [
	 *         "terms",
	 *         "value" => "Y",
	 *     ]
	 * );
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ check_field("terms") }}
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function checkField($parameters ) {
		return self::_inputFieldChecked("checkbox", parameters);
    }

    /***
	 * Builds a HTML input[type="radio"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::radioField(
	 *     [
	 *         "weather",
	 *         "value" => "hot",
	 *     ]
	 * );
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ radio_field("Save") }}
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function radioField($parameters ) {
		return self::_inputFieldChecked("radio", parameters);
    }

    /***
	 * Builds a HTML input[type="image"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::imageInput(
	 *     [
	 *         "src" => "/img/button.png",
	 *     ]
	 * );
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ image_input("src": "/img/button.png") }}
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function imageInput($parameters ) {
		return self::_inputField("image", parameters, true);
    }

    /***
	 * Builds a HTML input[type="submit"] tag
	 *
	 *<code>
	 * echo Phalcon\Tag::submitButton("Save")
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ submit_button("Save") }}
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function submitButton($parameters ) {
		return self::_inputField("submit", parameters, true);
    }

    /***
	 * Builds a HTML SELECT tag using a PHP array for options
	 *
	 *<code>
	 * echo Phalcon\Tag::selectStatic(
	 *     "status",
	 *     [
	 *         "A" => "Active",
	 *         "I" => "Inactive",
	 *     ]
	 * );
	 *</code>
	 *
	 * @param array parameters
	 * @param array data
	 * @return string
	 **/
    public static function selectStatic($parameters , $data  = null ) {
		return Select::selectField(parameters, data);
    }

    /***
	 * Builds a HTML SELECT tag using a Phalcon\Mvc\Model resultset as options
	 *
	 *<code>
	 * echo Phalcon\Tag::select(
	 *     [
	 *         "robotId",
	 *         Robots::find("type = "mechanical""),
	 *         "using" => ["id", "name"],
	 *     ]
	 * );
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ select("robotId", robots, "using": ["id", "name"]) }}
	 *</code>
	 *
	 * @param array parameters
	 * @param array data
	 * @return string
	 **/
    public static function select($parameters , $data  = null ) {
		return Select::selectField(parameters, data);
    }

    /***
	 * Builds a HTML TEXTAREA tag
	 *
	 *<code>
	 * echo Phalcon\Tag::textArea(
	 *     [
	 *         "comments",
	 *         "cols" => 10,
	 *         "rows" => 4,
	 *     ]
	 * );
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ text_area("comments", "cols": 10, "rows": 4) }}
	 *</code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function textArea($parameters ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters];
		} else {
			$params = parameters;
		}

		if ( !isset($params[0]) ) {
			if ( isset params["id"] ) {
				$params[0] = params["id"];
			}
		}

		$id = params[0];
		if ( !isset params["name"] ) {
			$params["name"] = id;
		} else {
			$name = params["name"];
			if ( empty name ) {
				$params["name"] = id;
			}
		}

		if ( !isset params["id"] ) {
			$params["id"] = id;
		}

		if ( isset params["value"] ) {
			$content = params["value"];
			unset params["value"];
		} else {
			$content = self::getValue(id, params);
		}

		$code = self::renderAttributes("<textarea", params),
			code .= ">" . content . "</textarea>";

		return code;
    }

    /***
	 * Builds a HTML FORM tag
	 *
	 * <code>
	 * echo Phalcon\Tag::form("posts/save");
	 *
	 * echo Phalcon\Tag::form(
	 *     [
	 *         "posts/save",
	 *         "method" => "post",
	 *     ]
	 * );
	 * </code>
	 *
	 * Volt syntax:
	 * <code>
	 * {{ form("posts/save") }}
	 * {{ form("posts/save", "method": "post") }}
	 * </code>
	 *
	 * @param array parameters
	 * @return string
	 **/
    public static function form($parameters ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters];
		} else {
			$params = parameters;
		}

		if ( !fetch paramsAction, params[0] ) {
		}

		/**
		 * By default the method is POST
		 */
		if ( !isset params["method"] ) {
			$params["method"] = "post";
		}

		$action = null;

		if ( !empty paramsAction ) {
			$action = self::getUrlService()->get(paramsAction);
		}

		/**
		 * Check for ( extra parameters
		 */
		if ( fetch parameters, params["parameters"] ) {
			$action .= "?" . parameters;
		}

		if ( !empty action ) {
			$params["action"] = action;
		}

		$code = self::renderAttributes("<for (m", params),
			code .= ">";

		return code;
    }

    /***
	 * Builds a HTML close FORM tag
	 **/
    public static function endForm() {
		return "</for (m>";
    }

    /***
	 * Set the title of view content
	 *
	 *<code>
	 * Phalcon\Tag::setTitle("Welcome to my Page");
	 *</code>
	 **/
    public static function setTitle($title ) {
		$self::_documentTitle = title;
    }

    /***
	 * Set the title separator of view content
	 *
	 *<code>
	 * Phalcon\Tag::setTitleSeparator("-");
	 *</code>
	 **/
    public static function setTitleSeparator($titleSeparator ) {
		$self::_documentTitleSeparator = titleSeparator;
    }

    /***
	 * Appends a text to current document title
	 **/
    public static function appendTitle($title ) {
		if ( typeof self::_documentAppendTitle == "null" ) {
			$self::_documentAppendTitle = [];
		}

		if ( gettype($title) == "array" ) {
			$self::_documentAppendTitle = title ;
		} else {
			$self::_documentAppendTitle[] = title ;
		}
    }

    /***
	 * Prepends a text to current document title
	 **/
    public static function prependTitle($title ) {
		if ( typeof self::_documentPrependTitle == "null" ) {
			$self::_documentPrependTitle = [];
		}

		if ( gettype($title) == "array" ) {
			$self::_documentPrependTitle = title ;
		} else {
			$self::_documentPrependTitle[] = title ;
		}
    }

    /***
	 * Gets the current document title.
	 * The title will be automatically escaped.
	 *
	 * <code>
	 * echo Phalcon\Tag::getTitle();
	 * </code>
	 *
	 * <code>
	 * {{ get_title() }}
	 * </code>
	 **/
    public static function getTitle($tags  = true ) {

		$escaper = <EscaperInterface> self::getEscaper(["escape": true]);
		$items = [];
		$output = "";
		$documentTitle = escaper->escapeHtml(self::_documentTitle);
		$documentTitleSeparator = escaper->escapeHtml(self::_documentTitleSeparator);

		if ( typeof self::_documentAppendTitle == "null" ) {
			$self::_documentAppendTitle = [];
		}

		$documentAppendTitle = self::_documentAppendTitle;

		if ( typeof self::_documentPrependTitle == "null" ) {
			$self::_documentPrependTitle = [];
		}

		$documentPrependTitle = self::_documentPrependTitle;

		if ( !empty documentPrependTitle ) {
			foreach ( $tmp as $title ) {
				$items[] = escaper->escapeHtml(title);
			}
		}

		if ( !empty documentTitle ) {
			$items[] = documentTitle;
		}

		if ( !empty documentAppendTitle ) {
			foreach ( $documentAppendTitle as $title ) {
				$items[] = escaper->escapeHtml(title);
			}
		}

		if ( empty documentTitleSeparator ) {
			$documentTitleSeparator = "";
		}

		if ( !empty items ) {
			$output = implode(documentTitleSeparator, items);
		}

		if ( tags ) {
			return "<title>" . output . "</title>" . PHP_EOL;
		}

		return output;
    }

    /***
	 * Gets the current document title separator
	 *
	 * <code>
	 * echo Phalcon\Tag::getTitleSeparator();
	 * </code>
	 *
	 * <code>
	 * {{ get_title_separator() }}
	 * </code>
	 **/
    public static function getTitleSeparator() {
		return self::_documentTitleSeparator;
    }

    /***
	 * Builds a LINK[rel="stylesheet"] tag
	 *
	 * <code>
	 * echo Phalcon\Tag::stylesheetLink("http://fonts.googleapis.com/css?family=Rosario", false);
	 * echo Phalcon\Tag::stylesheetLink("css/style.css");
	 * </code>
	 *
	 * Volt Syntax:
	 *<code>
	 * {{ stylesheet_link("http://fonts.googleapis.com/css?family=Rosario", false) }}
	 * {{ stylesheet_link("css/style.css") }}
	 *</code>
	 *
	 * @param array parameters
	 * @param boolean local
	 * @return string
	 **/
    public static function stylesheetLink($parameters  = null , $local  = true ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters, local];
		} else {
			$params = parameters;
		}

		if ( isset($params[1]) ) {
			$local = (boolean) params[1];
		} else {
			if ( isset params["local"] ) {
				$local = (boolean) params["local"];
				unset params["local"];
			}
		}

		if ( !isset params["type"] ) {
			$params["type"] = "text/css";
		}

		if ( !isset params["href"] ) {
			if ( isset($params[0]) ) {
				$params["href"] = params[0];
			} else {
				$params["href"] = "";
			}
		}

		/**
		 * URLs are generated through the "url" service
		 */
		if ( local === true ) {
			$params["href"] = self::getUrlService()->getStatic(params["href"]);
		}

		if ( !isset params["rel"] ) {
			$params["rel"] = "stylesheet";
		}

		$code = self::renderAttributes("<link", params);

		/**
		 * Check if ( Doctype is XHTML
		 */
		if ( self::_documentType > self::HTML5 ) {
			$code .= " />" . PHP_EOL;
		} else {
			$code .= ">" . PHP_EOL;
		}

		return code;
    }

    /***
	 * Builds a SCRIPT[type="javascript"] tag
	 *
	 * <code>
	 * echo Phalcon\Tag::javascriptInclude("http://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js", false);
	 * echo Phalcon\Tag::javascriptInclude("javascript/jquery.js");
	 * </code>
	 *
	 * Volt syntax:
	 * <code>
	 * {{ javascript_include("http://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js", false) }}
	 * {{ javascript_include("javascript/jquery.js") }}
	 * </code>
	 *
	 * @param array parameters
	 * @param boolean local
	 * @return string
	 **/
    public static function javascriptInclude($parameters  = null , $local  = true ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters, local];
		} else {
			$params = parameters;
		}

		if ( isset($params[1]) ) {
			$local = (boolean) params[1];
		} else {
			if ( isset params["local"] ) {
				$local = (boolean) params["local"];
				unset params["local"];
			}
		}

		if ( !isset params["type"] ) {
			$params["type"] = "text/javascript";
		}

		if ( !isset params["src"] ) {
			if ( isset($params[0]) ) {
				$params["src"] = params[0];
			} else {
				$params["src"] = "";
			}
		}

		/**
		 * URLs are generated through the "url" service
		 */
		if ( local === true ) {
			$params["src"] = self::getUrlService()->getStatic(params["src"]);
		}

		$code = self::renderAttributes("<script", params),
			code .= "></script>" . PHP_EOL;

		return code;
    }

    /***
	 * Builds HTML IMG tags
	 *
	 * <code>
	 * echo Phalcon\Tag::image("img/bg.png");
	 *
	 * echo Phalcon\Tag::image(
	 *     [
	 *         "img/photo.jpg",
	 *         "alt" => "Some Photo",
	 *     ]
	 * );
	 * </code>
	 *
	 * Volt Syntax:
	 * <code>
	 * {{ image("img/bg.png") }}
	 * {{ image("img/photo.jpg", "alt": "Some Photo") }}
	 * {{ image("http://static.mywebsite.com/img/bg.png", false) }}
	 * </code>
	 *
	 * @param  array parameters
	 * @param  boolean local
	 * @return string
	 **/
    public static function image($parameters  = null , $local  = true ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters];
		} else {
			$params = parameters;
			if ( isset($params[1]) ) {
				$local = (boolean) params[1];
			}
		}

		if ( !isset params["src"] ) {
			if ( fetch src, params[0] ) {
				$params["src"] = src;
			} else {
				$params["src"] = "";
			}
		}

		/**
		 * Use the "url" service if ( the URI is local
		 */
		if ( local ) {
			$params["src"] = self::getUrlService()->getStatic(params["src"]);
		}

		$code = self::renderAttributes("<img", params);

		/**
		 * Check if ( Doctype is XHTML
		 */
		if ( self::_documentType > self::HTML5 ) {
			$code .= " />";
		} else {
			$code .= ">";
		}

		return code;
    }

    /***
	 * Converts texts into URL-friendly titles
	 *
	 *<code>
	 * echo Phalcon\Tag::friendlyTitle("These are big important news", "-")
	 *</code>
	 **/
    public static function friendlyTitle($text , $separator  = - , $lowercase  = true , $replace  = null ) {

		if ( extension_loaded("iconv") ) {
			/**
			 * Save the old locale and set the new locale to UTF-8
			 */
			$locale = setlocale(LC_ALL, "en_US.UTF-8"),
				text = iconv("UTF-8", "ASCII//TRANSLIT", text);
		}

		if ( replace ) {

			if ( gettype($replace) != "array" && gettype($replace) != "string") {
				throw new Exception("Parameter replace must be an array or a string");
			}
			if ( gettype($replace) == "array" ) {
				foreach ( $replace as $search ) {
					$text = str_replace(search, " ", text);
				}
			} else {
				$text = str_replace(replace, " ", text);
			}
		}

		$friendly = preg_replace("/[^a-zA-Z0-9\\/_|+ -]/", "", text);
		if ( lowercase ) {
			$friendly = strtolower(friendly);
		}

		$friendly = preg_replace("/[\\/_|+ -]+/", separator, friendly),
			friendly = trim(friendly, separator);

		if ( extension_loaded("iconv") ) {
			/**
			 * Revert back to the old locale
			 */
			setlocale(LC_ALL, locale);
		}
		return friendly;
    }

    /***
	 * Set the document type of content
	 **/
    public static function setDocType($doctype ) {
		if ( (doctype < self::HTML32 || doctype > self::XHTML5) ) {
			$self::_documentType = self::HTML5;
		} else {
			$self::_documentType = doctype;
		}
    }

    /***
	 * Get the document type declaration of content
	 **/
    public static function getDocType() {
		{
			case 1:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">" . PHP_EOL;
			/* no break */

			case 2:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/html4/strict.dtd\">" . PHP_EOL;
			/* no break */

			case 3:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/html4/loose.dtd\">" . PHP_EOL;
			/* no break */

			case 4:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/html4/frameset.dtd\">" . PHP_EOL;
			/* no break */

			case 6:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">" . PHP_EOL;
			/* no break */

			case 7:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"" . PHP_EOL."\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">" . PHP_EOL;
			/* no break */

			case 8:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">" . PHP_EOL;
			/* no break */

			case 9:  return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">" . PHP_EOL;
			/* no break */

			case 10: return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 2.0//EN\"" . PHP_EOL . "\t\"http://www.w3.org/MarkUp/DTD/xhtml2.dtd\">" . PHP_EOL;
			/* no break */

			case 5:
			case 11: return "<!DOCTYPE html>" . PHP_EOL;
			/* no break */
		}

		return "";
    }

    /***
	 * Builds a HTML tag
	 **/
    public static function tagHtml($tagName , $parameters  = null , $selfClose  = false , $onlyStart  = false , $useEol  = false ) {

		if ( gettype($parameters) != "array" ) {
			$params = [parameters];
		} else {
			$params = parameters;
		}

		$localCode = self::renderAttributes("<" . tagName, params);

		/**
		 * Check if ( Doctype is XHTML
		 */
		if ( self::_documentType > self::HTML5 ) {
			if ( selfClose ) {
				$localCode .= " />";
			} else {
				$localCode .= ">";
			}
		} else {
			if ( onlyStart ) {
				$localCode .= ">";
			} else {
				$localCode .= "></" . tagName . ">";
			}
		}

		if ( useEol ) {
			$localCode .= PHP_EOL;
		}

		return localCode;
    }

    /***
	 * Builds a HTML tag closing tag
	 *
	 *<code>
	 * echo Phalcon\Tag::tagHtmlClose("script", true);
	 *</code>
	 **/
    public static function tagHtmlClose($tagName , $useEol  = false ) {
		if ( useEol ) {
			return "</" . tagName . ">" . PHP_EOL;
		}
		return "</" . tagName . ">";
    }

}