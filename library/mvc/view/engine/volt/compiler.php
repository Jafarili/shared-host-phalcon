<?php


namespace Phalcon\Mvc\View\Engine\Volt;

use Phalcon\DiInterface;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\View\Engine\Volt\Exception;


/***
 * Phalcon\Mvc\View\Engine\Volt\Compiler
 *
 * This class reads and compiles Volt templates into PHP plain code
 *
 *<code>
 * $compiler = new \Phalcon\Mvc\View\Engine\Volt\Compiler();
 *
 * $compiler->compile("views/partials/header.volt");
 *
 * require $compiler->getCompiledTemplatePath();
 *</code>
 **/

class Compiler {

    protected $_dependencyInjector;

    protected $_view;

    protected $_options;

    protected $_arrayHelpers;

    protected $_level;

    protected $_foreachLevel;

    protected $_blockLevel;

    protected $_exprLevel;

    protected $_extended;

    protected $_autoescape;

    protected $_extendedBlocks;

    protected $_currentBlock;

    protected $_blocks;

    protected $_forElsePointers;

    protected $_loopPointers;

    protected $_extensions;

    protected $_functions;

    protected $_filters;

    protected $_macros;

    protected $_prefix;

    protected $_currentPath;

    protected $_compiledTemplatePath;

    /***
	 * Phalcon\Mvc\View\Engine\Volt\Compiler
	 **/
    public function __construct($view  = null ) {
		if ( gettype($view) == "object" ) {
			$this->_view = view;
		}
    }

    /***
	 * Sets the dependency injector
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the internal dependency injector
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets the compiler options
	 **/
    public function setOptions($options ) {
		$this->_options = options;
    }

    /***
	 * Sets a single compiler option
	 *
	 * @param string option
	 * @param mixed value
	 **/
    public function setOption($option , $value ) {
		$this->_options[option] = value;
    }

    /***
	 * Returns a compiler's option
	 *
	 * @param string option
	 * @return string
	 **/
    public function getOption($option ) {
		if ( fetch value, $this->_options[option] ) {
			return value;
		}
		return null;
    }

    /***
	 * Returns the compiler options
	 **/
    public function getOptions() {
		return $this->_options;
    }

    /***
	 * Fires an event to registered extensions
	 *
	 * @param string name
	 * @param array arguments
	 * @return mixed
	 **/
    public final function fireExtensionEvent($name , $arguments  = null ) {

		$extensions = $this->_extensions;
		if ( gettype($extensions) == "array" ) {
			foreach ( $extensions as $extension ) {

				/**
				 * Check if ( the extension implements the required event name
				 */
				if ( method_exists(extension, name) ) {

					if ( gettype($arguments) == "array" ) {
						$status = call_user_func_array([extension, name], arguments);
					} else {
						$status = call_user_func([extension, name]);
					}

					/**
					 * Only string statuses means the extension process something
					 */
					if ( gettype($status) == "string" ) {
						return status;
					}
				}
			}
		}
    }

    /***
	 * Registers a Volt's extension
	 **/
    public function addExtension($extension ) {
		if ( gettype($extension) != "object" ) {
			throw new Exception("The extension is not valid");
		}

		/**
		 * Initialize the extension
		 */
		if ( method_exists(extension, "initialize") ) {
			extension->initialize(this);
		}

		$this->_extensions[] = extension;
		return this;
    }

    /***
	 * Returns the list of extensions registered in Volt
	 **/
    public function getExtensions() {
		return $this->_extensions;
    }

    /***
	 * Register a new function in the compiler
	 **/
    public function addFunction($name , $definition ) {
		$this->_functions[name] = definition;
		return this;
    }

    /***
	 * Register the user registered functions
	 **/
    public function getFunctions() {
		return $this->_functions;
    }

    /***
	 * Register a new filter in the compiler
	 **/
    public function addFilter($name , $definition ) {
		$this->_filters[name] = definition;
		return this;
    }

    /***
	 * Register the user registered filters
	 **/
    public function getFilters() {
		return $this->_filters;
    }

    /***
	 * Set a unique prefix to be used as prefix for compiled variables
	 **/
    public function setUniquePrefix($prefix ) {
		$this->_prefix = prefix;
		return this;
    }

    /***
	 * Return a unique prefix to be used as prefix for compiled variables and contexts
	 **/
    public function getUniquePrefix() {
		if ( !this->_prefix ) {
			$this->_prefix = unique_path_key(this->_currentPath);
		}

		/**
		 * The user could use a closure generator
		 */
		if ( gettype($this->_prefix) == "object" ) {
			if ( $this->_prefix instanceof \Closure ) {
				$this->_prefix = call_user_func_array(this->_prefix, [this]);
			}
		}

		if ( gettype($this->_prefix) != "string" ) {
			throw new Exception("The unique compilation prefix is invalid");
		}

		return $this->_prefix;
    }

    /***
	 * Resolves attribute reading
	 **/
    public function attributeReader($expr ) {
			level, dependencyInjector, leftCode, right;

		$exprCode = null;

		$left = expr["left"];

		if ( left["type"] == PHVOLT_T_IDENTIFIER ) {

			$variable = left["value"];

			/**
			 * Check if ( the variable is the loop context
			 */
			if ( variable == "loop" ) {
				$level = $this->_for (eachLevel,
					exprCode .= "$" . $this->getUniquePrefix() . level . "loop",
					this->_loopPointers[level] = level;
			} else {

				/**
				 * Services registered in the dependency injector container are available always
				 */
				$dependencyInjector = $this->_dependencyInjector;
				if ( gettype($dependencyInjector) == "object" && dependencyInjector->has(variable) ) {
					$exprCode .= "$this->" . variable;
				} else {
					$exprCode .= "$" . variable;
				}
			}

		} else {
			$leftCode = $this->expression(left), leftType = left["type"];
			if ( leftType != PHVOLT_T_DOT && leftType != PHVOLT_T_FCALL ) {
				$exprCode .= leftCode;
			} else {
				$exprCode .= leftCode;
			}
		}

		$exprCode .= "->";

		$right = expr["right"];

		if ( right["type"] == PHVOLT_T_IDENTIFIER ) {
			$exprCode .= right["value"];
		} else {
			$exprCode .= $this->expression(right);
		}

		return exprCode;
    }

    /***
	 * Resolves function intermediate code into PHP function calls
	 **/
    public function functionCall($expr ) {
			nameType, name, extensions, functions, definition,
			extendedBlocks, block, currentBlock, exprLevel, escapedCode,
			method, arrayHelpers, className;

		$code = null;

		$funcArguments = null;
		if ( fetch funcArguments, expr["arguments"] ) {
			$arguments = $this->expression(funcArguments);
		} else {
			$arguments = "";
		}

		$nameExpr = expr["name"], nameType = nameExpr["type"];

		/**
		 * Check if ( it's a single function
		 */
		if ( nameType == PHVOLT_T_IDENTIFIER ) {

			$name = nameExpr["value"];

			/**
			 * Check if ( any of the registered extensions provide compilation for ( this function
			 */
			$extensions = $this->_extensions;
			if ( gettype($extensions) == "array" ) {

				/**
				 * Notif (y the extensions about being compiling a function
				 */
				$code = $this->fireExtensionEvent("compileFunction", [name, arguments, funcArguments]);
				if ( gettype($code) == "string" ) {
					return code;
				}
			}

			/**
			 * Check if ( it's a user defined function
			 */
			$functions = $this->_functions;
			if ( gettype($functions) == "array" ) {
				if ( fetch definition, functions[name] ) {

					/**
					 * Use the string as function
					 */
					if ( gettype($definition) == "string" ) {
						return definition . "(" . arguments . ")";
					}

					/**
					 * Execute the function closure returning the compiled definition
					 */
					if ( gettype($definition) == "object" ) {

						if ( definition instanceof \Closure ) {
							return call_user_func_array(definition, [arguments, funcArguments]);
						}
					}

					throw new Exception(
						"Invalid definition for ( user function '" . name . "' in " . expr["file"] . " on line " . expr["line"]
					);
				}
			}

			/**
			 * This function includes the previous rendering stage
			 */
			if ( name == "get_content" || name == "content" ) {
				return "$this->getContent()";
			}

			/**
			 * This function includes views of volt or others template engines dynamically
			 */
			if ( name == "partial" ) {
				return "$this->partial(" . arguments . ")";
			}

			/**
			 * This function embeds the parent block in the current block
			 */
			if ( name == "super" ) {
				$extendedBlocks = $this->_extendedBlocks;
				if ( gettype($extendedBlocks) == "array" ) {

					$currentBlock = $this->_currentBlock;
					if ( fetch block, extendedBlocks[currentBlock] ) {

						$exprLevel = $this->_exprLevel;
						if ( gettype($block) == "array" ) {
							$code = $this->_statementListOrExtends(block);
							if ( exprLevel == 1 ) {
								$escapedCode = code;
							} else {
								$escapedCode = addslashes(code);
							}
						} else {
							if ( exprLevel == 1 ) {
								$escapedCode = block;
							} else {
								$escapedCode = addslashes(block);
							}
						}

						/**
						 * If the super() is the first level we don't escape it
						 */
						if ( exprLevel == 1 ) {
							return escapedCode;
						}
						return "'" . escapedCode . "'";
					}
				}
				return "''";
			}

			$method = lcfirst(camelize(name)),
				className = "Phalcon\\Tag";

			/**
			 * Check if ( it's a method in Phalcon\Tag
			 */
			if ( method_exists(className, method) ) {

				$arrayHelpers = $this->_arrayHelpers;
				if ( gettype($arrayHelpers) != "array" ) {
					$arrayHelpers = [
						"link_to": true,
						"image": true,
						"for (m": true,
						"select": true,
						"select_static": true,
						"submit_button": true,
						"radio_field": true,
						"check_field": true,
						"file_field": true,
						"hidden_field": true,
						"password_field": true,
						"text_area": true,
						"text_field": true,
						"email_field": true,
						"date_field": true,
						"tel_field": true,
						"numeric_field": true,
						"image_input": true
					];
					$this->_arrayHelpers = arrayHelpers;
				}

				if ( isset($arrayHelpers[name]) ) {
					return "$this->tag->" . method . "([" . arguments . "])";
				}
				return "$this->tag->" . method . "(" . arguments . ")";
			}

			/**
			 * Get a dynamic URL
			 */
			if ( name == "url" ) {
				return "$this->url->get(" . arguments . ")";
			}

			/**
			 * Get a static URL
			 */
			if ( name == "static_url" ) {
				return "$this->url->getStatic(" . arguments . ")";
			}

			if ( name == "date" ) {
				return "date(" . arguments . ")";
			}

			if ( name == "time" ) {
				return "time()";
			}

			if ( name == "dump" ) {
				return "var_dump(" . arguments . ")";
			}

			if ( name == "version" ) {
				return "Phalcon\\Version::get()";
			}

			if ( name == "version_id" ) {
				return "Phalcon\\Version::getId()";
			}

			/**
			 * Read PHP constants in templates
			 */
			if ( name == "constant" ) {
				return "constant(" . arguments . ")";
			}

			/**
			 * By default it tries to call a macro
			 */
			return "$this->callMacro('" . name . "', [" . arguments . "])";
		}

		return $this->expression(nameExpr) . "(" . arguments . ")";
    }

    /***
	 * Resolves filter intermediate code into a valid PHP expression
	 **/
    public function resolveTest($test , $left ) {

		$type = test["type"];

		/**
		 * Check if ( right part is a single identif (ier
		 */
		if ( type == PHVOLT_T_IDENTIFIER ) {

			$name = test["value"];

			/**
			 * Empty uses the PHP's empty operator
			 */
			if ( name == "empty" ) {
				return "empty(" . left . ")";
			}

			/**
			 * Check if ( a value is even
			 */
			if ( name == "even" ) {
				return "(((" . left . ") % 2) == 0)";
			}

			/**
			 * Check if ( a value is odd
			 */
			if ( name == "odd" ) {
				return "(((" . left . ") % 2) != 0)";
			}

			/**
			 * Check if ( a value is numeric
			 */
			if ( name == "numeric" ) {
				return "is_numeric(" . left . ")";
			}

			/**
			 * Check if ( a value is scalar
			 */
			if ( name == "scalar" ) {
				return "is_scalar(" . left . ")";
			}

			/**
			 * Check if ( a value is iterable
			 */
			if ( name == "iterable" ) {
				return "(is_array(" . left . ") || (" . left . ") instanceof Traversable)";
			}

		}

		/**
		 * Check if ( right part is a function call
		 */
		if ( type == PHVOLT_T_FCALL ) {

			$testName = test["name"];
			if ( fetch name, testName["value"] ) {

				if ( name == "divisibleby" ) {
					return "(((" . left . ") % (" . $this->expression(test["arguments"]) . ")) == 0)";
				}

				/**
				 * Checks if ( a value is equals to other
				 */
				if ( name == "sameas" ) {
					return "(" . left . ") === (" . $this->expression(test["arguments"]) . ")";
				}

				/**
				 * Checks if ( a variable match a type
				 */
				if ( name == "type" ) {
					return "gettype(" . left . ") === (" . $this->expression(test["arguments"]) . ")";
				}
			}
		}

		/**
		 * Fall back to the equals operator
		 */
		return left . " == " . $this->expression(test);
    }

    /***
	 * Resolves filter intermediate code into PHP function calls
	 **/
    final protected function resolveFilter($filter , $left ) {
			extensions, filters, funcArguments, arguments, definition;

		$code = null, type = filter["type"];

		/**
		 * Check if ( the filter is a single identif (ier
		 */
		if ( type == PHVOLT_T_IDENTIFIER ) {
			$name = filter["value"];
		} else {

			if ( type != PHVOLT_T_FCALL ) {

				/**
				 * Unknown filter throw an exception
				 */
				throw new Exception("Unknown filter type in " . filter["file"] . " on line " . filter["line"]);
			}

			$functionName = filter["name"],
				name = functionName["value"];
		}

		$funcArguments = null, arguments = null;

		/**
		 * Resolve arguments
		 */
		if ( fetch funcArguments, filter["arguments"] ) {

			/**
			 * "default" filter is not the first argument, improve this!
			 */
			if ( name != "default" ) {

				$file = filter["file"], line = filter["line"];

				/**
				 * TODO: Implement this function directly
				 */
				array_unshif (t(funcArguments, [
					"expr": [
						"type":  364,
						"value": left,
						"file": file,
						"line": line
					],
					"file": file,
					"line": line
				]);
			}

			$arguments = $this->expression(funcArguments);
		} else {
			$arguments = left;
		}

		/**
		 * Check if ( any of the registered extensions provide compilation for ( this filter
		 */
		$extensions = $this->_extensions;
		if ( gettype($extensions) == "array" ) {

			/**
			 * Notif (y the extensions about being compiling a function
			 */
			$code = $this->fireExtensionEvent("compileFilter", [name, arguments, funcArguments]);
			if ( gettype($code) == "string" ) {
				return code;
			}
		}

		/**
		 * Check if ( it's a user defined filter
		 */
		$filters = $this->_filters;
		if ( gettype($filters) == "array" ) {
			if ( fetch definition, filters[name] ) {

				/**
				 * The definition is a string
				 */
				if ( gettype($definition) == "string" ) {
					return definition . "(" . arguments . ")";
				}

				/**
				 * The definition is a closure
				 */
				if ( gettype($definition) == "object" ) {
					if ( definition instanceof \Closure ) {
						return call_user_func_array(definition, [arguments, funcArguments]);
					}
				}

				/**
				 * Invalid filter definition throw an exception
				 */
				throw new Exception(
					"Invalid definition for ( user filter '" . name . "' in " . filter["file"] . " on line " . filter["line"]
				);
			}
		}

		/**
		 * "length" uses the length method implemented in the Volt adapter
		 */
		if ( name == "length" ) {
			return "$this->length(" . arguments . ")";
		}

		/**
		 * "e"/"escape" filter uses the escaper component
		 */
		if ( name == "e" || name == "escape" ) {
			return "$this->escaper->escapeHtml(" . arguments . ")";
		}

		/**
		 * "escape_css" filter uses the escaper component to filter css
		 */
		if ( name == "escape_css" ) {
			return "$this->escaper->escapeCss(" . arguments . ")";
		}

		/**
		 * "escape_js" filter uses the escaper component to escape javascript
		 */
		if ( name == "escape_js" ) {
			return "$this->escaper->escapeJs(" . arguments . ")";
		}

		/**
		 * "escape_attr" filter uses the escaper component to escape html attributes
		 */
		if ( name == "escape_attr" ) {
			return "$this->escaper->escapeHtmlAttr(" . arguments . ")";
		}

		/**
		 * "trim" calls the "trim" function in the PHP userland
		 */
		if ( name == "trim" ) {
			return "trim(" . arguments . ")";
		}

		/**
		 * "left_trim" calls the "ltrim" function in the PHP userland
		 */
		if ( name == "left_trim" ) {
			return "ltrim(" . arguments . ")";
		}

		/**
		 * "right_trim" calls the "rtrim" function in the PHP userland
		 */
		if ( name == "right_trim" ) {
			return "rtrim(" . arguments . ")";
		}

		/**
		 * "striptags" calls the "strip_tags" function in the PHP userland
		 */
		if ( name == "striptags" ) {
			return "strip_tags(" . arguments . ")";
		}

		/**
		 * "url_encode" calls the "urlencode" function in the PHP userland
		 */
		if ( name == "url_encode" ) {
			return "urlencode(" . arguments . ")";
		}

		/**
		 * "slashes" calls the "addslashes" function in the PHP userland
		 */
		if ( name == "slashes" ) {
			return "addslashes(" . arguments . ")";
		}

		/**
		 * "stripslashes" calls the "stripslashes" function in the PHP userland
		 */
		if ( name == "stripslashes" ) {
			return "stripslashes(" . arguments . ")";
		}

		/**
		 * "nl2br" calls the "nl2br" function in the PHP userland
		 */
		if ( name == "nl2br" ) {
			return "nl2br(" . arguments . ")";
		}

		/**
		 * "keys" uses calls the "array_keys" function in the PHP userland
		 */
		if ( name == "keys" ) {
			return "array_keys(" . arguments . ")";
		}

		/**
		 * "join" uses calls the "join" function in the PHP userland
		 */
		if ( name == "join" ) {
			return "join(" . arguments . ")";
		}

		/**
		 * "lower"/"lowercase" calls the "strtolower" function or "mb_strtolower" if ( the mbstring extension is loaded
		 */
		if ( name == "lower" || name == "lowercase" ) {
			return "Phalcon\\Text::lower(" . arguments . ")";
		}

		/**
		 * "upper"/"uppercase" calls the "strtoupper" function or "mb_strtoupper" if ( the mbstring extension is loaded
		 */
		if ( name == "upper" || name == "uppercase" ) {
			return "Phalcon\\Text::upper(" . arguments . ")";
		}

		/**
		 * "capitalize" filter calls "ucwords"
		 */
		if ( name == "capitalize" ) {
			return "ucwords(" . arguments . ")";
		}

		/**
		 * "sort" calls "sort" method in the engine adapter
		 */
		if ( name == "sort" ) {
			return "$this->sort(" . arguments . ")";
		}

		/**
		 * "json_encode" calls the "json_encode" function in the PHP userland
		 */
		if ( name == "json_encode" ) {
			return "json_encode(" . arguments . ")";
		}

		/**
		 * "json_decode" calls the "json_decode" function in the PHP userland
		 */
		if ( name == "json_decode" ) {
			return "json_decode(" . arguments . ")";
		}

		/**
		 * "foreach (mat" calls the "sprintf" $the as $function PHP userland
		 */
		if ( name == "for (mat" ) ) {
			return "sprintf(" . arguments . ")";
		}

		/**
		 * "abs" calls the "abs" function in the PHP userland
		 */
		if ( name == "abs" ) {
			return "abs(" . arguments . ")";
		}

		/**
		 * "slice" slices string/arrays/traversable objects
		 */
		if ( name == "slice" ) {
			return "$this->slice(" . arguments . ")";
		}

		/**
		 * "default" checks if ( a variable is empty
		 */
		if ( name == "default" ) {
			return "(empty(" . left . ") ? (" . arguments . ") : (" . left . "))";
		}

		/**
		 * This function uses mbstring or iconv to convert strings from one charset to another
		 */
		if ( name == "convert_encoding" ) {
			return "$this->convertEncoding(" . arguments . ")";
		}

		/**
		 * Unknown filter throw an exception
		 */
		throw new Exception("Unknown filter \"" . name . "\" in " . filter["file"] . " on line " . filter["line"]);
    }

    /***
	 * Resolves an expression node in an AST volt tree
	 **/
    final public function expression($expr ) {
			left, leftCode, right, rightCode, type, startCode, endCode, start, end;

		$exprCode = null, $this->_exprLevel++;

		/**
		 * Check if ( any of the registered extensions provide compilation for ( this expression
		 */
		$extensions = $this->_extensions;

		loop {

			if ( gettype($extensions) == "array" ) {

				/**
				 * Notif (y the extensions about being resolving an expression
				 */
				$exprCode = $this->fireExtensionEvent("resolveExpression", [expr]);
				if ( gettype($exprCode) == "string" ) {
					break;
				}
			}

			if ( !fetch type, expr["type"] ) {
				$items = [];
				foreach ( $expr as $singleExpr ) {
					$singleExprCode = $this->expression(singleExpr["expr"]);
					if ( fetch name, singleExpr["name"] ) {
						$items[] = "'" . name . "' => " . singleExprCode;
					} else {
						$items[] = singleExprCode;
					}
				}
				$exprCode = join(", ", items);
				break;
			}

			/**
			 * Attribute reading needs special handling
			 */
			if ( type == PHVOLT_T_DOT ) {
				$exprCode = $this->attributeReader(expr);
				break;
			}

			/**
			 * Left part of expression is always resolved
			 */
			if ( fetch left, expr["left"] ) {
				$leftCode = $this->expression(left);
			}

			/**
			 * Operator "is" also needs special handling
			 */
			if ( type == PHVOLT_T_IS ) {
				$exprCode = $this->resolveTest(expr["right"], leftCode);
				break;
			}

			/**
			 * We don't resolve the right expression for ( filters
			 */
			if ( type == 124 ) {
				$exprCode = $this->resolveFilter(expr["right"], leftCode);
				break;
			}

			/**
			 * From here, right part of expression is always resolved
			 */
			if ( fetch right, expr["right"] ) {
				$rightCode = $this->expression(right);
			}

			$exprCode = null;
			switch type {

				case PHVOLT_T_NOT:
					$exprCode = "!" . rightCode;
					break;

				case PHVOLT_T_MUL:
					$exprCode = leftCode . " * " . rightCode;
					break;

				case PHVOLT_T_ADD:
					$exprCode = leftCode . " + " . rightCode;
					break;

				case PHVOLT_T_SUB:
					$exprCode = leftCode . " - " . rightCode;
					break;

				case PHVOLT_T_DIV:
					$exprCode = leftCode . " / " . rightCode;
					break;

				case 37:
					$exprCode = leftCode . " % " . rightCode;
					break;

				case PHVOLT_T_LESS:
					$exprCode = leftCode . " < " . rightCode;
					break;

				case 61:
					$exprCode = leftCode . " > " . rightCode;
					break;

				case 62:
					$exprCode = leftCode . " > " . rightCode;
					break;

				case 126:
					$exprCode = leftCode . " . " . rightCode;
					break;

				case 278:
					$exprCode = "pow(" . leftCode . ", " . rightCode . ")";
					break;

				case PHVOLT_T_ARRAY:
					if ( isset expr["left"] ) {
						$exprCode = "[" . leftCode . "]";
					} else {
						$exprCode = "[]";
					}
					break;

				case 258:
					$exprCode = expr["value"];
					break;

				case 259:
					$exprCode = expr["value"];
					break;

				case PHVOLT_T_STRING:
					$exprCode = "'" . str_replace("'", "\\'", expr["value"]) . "'";
					break;

				case PHVOLT_T_NULL:
					$exprCode = "null";
					break;

				case PHVOLT_T_FALSE:
					$exprCode = "false";
					break;

				case PHVOLT_T_TRUE:
					$exprCode = "true";
					break;

				case PHVOLT_T_IDENTIFIER:
					$exprCode = "$" . expr["value"];
					break;

				case PHVOLT_T_AND:
					$exprCode = leftCode . " && " . rightCode;
					break;

				case 267:
					$exprCode = leftCode . " || " . rightCode;
					break;

				case PHVOLT_T_LESSEQUAL:
					$exprCode = leftCode . " <= " . rightCode;
					break;

				case 271:
					$exprCode = leftCode . " >= " . rightCode;
					break;

				case 272:
					$exprCode = leftCode . " == " . rightCode;
					break;

				case 273:
					$exprCode = leftCode . " != " . rightCode;
					break;

				case 274:
					$exprCode = leftCode . " === " . rightCode;
					break;

				case 275:
					$exprCode = leftCode . " !== " . rightCode;
					break;

				case PHVOLT_T_RANGE:
					$exprCode = "range(" . leftCode . ", " . rightCode . ")";
					break;

				case PHVOLT_T_FCALL:
					$exprCode = $this->functionCall(expr);
					break;

				case PHVOLT_T_ENCLOSED:
					$exprCode = "(" . leftCode . ")";
					break;

				case PHVOLT_T_ARRAYACCESS:
					$exprCode = leftCode . "[" . rightCode . "]";
					break;

				case PHVOLT_T_SLICE:

					/**
					 * Evaluate the start part of the slice
					 */
					if ( fetch start, expr["start"] ) {
						$startCode = $this->expression(start);
					} else {
						$startCode = "null";
					}

					/**
					 * Evaluate the end part of the slice
					 */
					if ( fetch end, expr["end"] ) {
						$endCode = $this->expression(end);
					} else {
						$endCode = "null";
					}

					$exprCode = "$this->slice(" . leftCode . ", " . startCode . ", " . endCode . ")";
					break;

				case PHVOLT_T_NOT_ISSET:
					$exprCode = "!isset(" . leftCode . ")";
					break;

				case PHVOLT_T_ISSET:
					$exprCode = "isset(" . leftCode . ")";
					break;

				case PHVOLT_T_NOT_ISEMPTY:
					$exprCode = "!empty(" . leftCode . ")";
					break;

				case PHVOLT_T_ISEMPTY:
					$exprCode = "empty(" . leftCode . ")";
					break;

				case PHVOLT_T_NOT_ISEVEN:
					$exprCode = "!(((" . leftCode . ") % 2) == 0)";
					break;

				case PHVOLT_T_ISEVEN:
					$exprCode = "(((" . leftCode . ") % 2) == 0)";
					break;

				case PHVOLT_T_NOT_ISODD:
					$exprCode = "!(((" . leftCode . ") % 2) != 0)";
					break;

				case PHVOLT_T_ISODD:
					$exprCode = "(((" . leftCode . ") % 2) != 0)";
					break;

				case PHVOLT_T_NOT_ISNUMERIC:
					$exprCode = "!is_numeric(" . leftCode . ")";
					break;

				case PHVOLT_T_ISNUMERIC:
					$exprCode = "is_numeric(" . leftCode . ")";
					break;

				case PHVOLT_T_NOT_ISSCALAR:
					$exprCode = "!is_scalar(" . leftCode . ")";
					break;

				case PHVOLT_T_ISSCALAR:
					$exprCode = "is_scalar(" . leftCode . ")";
					break;

				case PHVOLT_T_NOT_ISITERABLE:
					$exprCode = "!(is_array(" . leftCode . ") || (" . leftCode . ") instanceof Traversable)";
					break;

				case PHVOLT_T_ISITERABLE:
					$exprCode = "(is_array(" . leftCode . ") || (" . leftCode . ") instanceof Traversable)";
					break;

				case PHVOLT_T_IN:
					$exprCode = "$this->isIncluded(" . leftCode . ", " . rightCode . ")";
					break;

				case PHVOLT_T_NOT_IN:
					$exprCode = "!$this->isIncluded(" . leftCode . ", " . rightCode . ")";
					break;

				case PHVOLT_T_TERNARY:
					$exprCode = "(" . $this->expression(expr["ternary"]) . " ? " . leftCode . " : " . rightCode . ")";
					break;

				case PHVOLT_T_MINUS:
					$exprCode = "-" . rightCode;
					break;

				case PHVOLT_T_PLUS:
					$exprCode = "+" . rightCode;
					break;

				case PHVOLT_T_RESOLVED_EXPR:
					$exprCode = expr["value"];
					break;

				default:
					throw new Exception("Unknown expression " . type . " in " . expr["file"] . " on line " . expr["line"]);
			}

			break;
		}

		$this->_exprLevel--;

		return exprCode;
    }

    /***
	 * Compiles a block of statements
	 *
	 * @param array statements
	 * @return string|array
	 **/
    final protected function _statementListOrExtends($statements ) {
		boolean isStatementList;

		/**
		 * Resolve the statement list as normal
		 */
		if ( gettype($statements) != "array" ) {
			return statements;
		}

		/**
		 * If all elements in the statement list are arrays we resolve this as a statementList
		 */
		$isStatementList = true;
		if ( !isset statements["type"] ) {
			foreach ( $statements as $statement ) {
				if ( gettype($statement) != "array" ) {
					$isStatementList = false;
					break;
				}
			}
		}

		/**
		 * Resolve the statement list as normal
		 */
		if ( isStatementList === true ) {
			return $this->_statementList(statements);
		}

		/**
		 * Is an array but not a statement list?
		 */
		return statements;
    }

    /***
	 * Compiles a "foreach" intermediate code representation into plain PHP code
	 **/
    public function compileForeach($statement , $extendsMode  = false ) {
			exprCode, bstatement, type, blockStatements, for (Else, code,
			loopContext, iterator, key, if (Expr, variable;

		/**
		 * A valid expression is required
		 */
		if ( !isset statement["expr"] ) {
			throw new Exception("Corrupted statement");
		}

		$compilation = "", for (Else = null;

		$this->_for (eachLevel++;

		$prefix = $this->getUniquePrefix();
		$level = $this->_for (eachLevel;

		/**
		 * prefixLevel is used to prefix every temporal variable
		 */
		$prefixLevel = prefix . level;

		/**
		 * Evaluate common expressions
		 */
		$expr = statement["expr"];
		$exprCode = $this->expression(expr);

		/**
		 * Process the block statements
		 */
		$blockStatements = statement["block_statements"];

		$for (Else = false;
		if ( gettype($blockStatements) == "array" ) {

			foreach ( $blockStatements as $bstatement ) {

				if ( gettype($bstatement) != "array" ) {
					break;
				}

				/**
				 * Check if ( the statement is valid
				 */
				if ( !fetch type, bstatement["type"] ) {
					break;
				}

				if ( type == PHVOLT_T_ELSEFOR ) {
					$compilation .= "<?php $" . prefixLevel . "iterated = false; ?>";
					$for (Else = prefixLevel;
					$this->_for (ElsePointers[level] = for (Else;
					break;
				}

			}
		}

		/**
		 * Process statements block
		 */
		$code = $this->_statementList(blockStatements, extendsMode);

		$loopContext = $this->_loopPointers;

		/**
		 * Generate the loop context for ( the "for (each"
		 */
		if ( isset($loopContext[level]) ) {
			$compilation .= "<?php $" . prefixLevel . "iterator = " . exprCode . "; ";
			$compilation .= "$" . prefixLevel . "incr = 0; ";
			$compilation .= "$" . prefixLevel . "loop = new stdClass(); ";
			$compilation .= "$" . prefixLevel . "loop->self = &$" . prefixLevel . "loop; ";
			$compilation .= "$" . prefixLevel . "loop->length = count($" . prefixLevel . "iterator); ";
			$compilation .= "$" . prefixLevel . "loop->index = 1; ";
			$compilation .= "$" . prefixLevel . "loop->index0 = 1; ";
			$compilation .= "$" . prefixLevel . "loop->revindex = $" . prefixLevel . "loop->length; ";
			$compilation .= "$" . prefixLevel . "loop->revindex0 = $" . prefixLevel . "loop->length - 1; ?>";
			$iterator = "$" . prefixLevel . "iterator";
		} else {
			$iterator = exprCode;
		}

		/**
		 * Foreach statement
		 */
		$variable = statement["variable"];

		/**
		 * Check if ( a "key" variable needs to be calculated
		 */
		if ( fetch key, statement["key"] ) {
			$compilation .= "<?php for (each (" . iterator . " as $" . key . " => $" . variable . ") ) { ";
		} else {
			$compilation .= "<?php for (each (" . iterator . " as $" . variable . ") ) { ";
		}

		/**
		 * Check foreach ( an "if (" $the as $expr block
		 */
		if ( fetch if (Expr, statement["if (_expr"] ) {
			$compilation .= "if ( (" . $this->expression(if (Expr) . ") ) { ?>";
		} else {
			$compilation .= "?>";
		}

		/**
		 * Generate the loop context inside the cycle
		 */
		if ( isset($loopContext[level]) ) {
			$compilation .= "<?php $" . prefixLevel . "loop->first = ($" . prefixLevel . "incr == 0); ";
			$compilation .= "$" . prefixLevel . "loop->index = $" . prefixLevel . "incr + 1; ";
			$compilation .= "$" . prefixLevel . "loop->index0 = $" . prefixLevel . "incr; ";
			$compilation .= "$" . prefixLevel . "loop->revindex = $" . prefixLevel . "loop->length - $" . prefixLevel . "incr; ";
			$compilation .= "$" . prefixLevel . "loop->revindex0 = $" . prefixLevel . "loop->length - ($" . prefixLevel . "incr + 1); ";
			$compilation .= "$" . prefixLevel . "loop->last = ($" . prefixLevel . "incr == ($" . prefixLevel . "loop->length - 1)); ?>";
		}

		/**
		 * Update the for (else var if ( it's iterated at least one time
		 */
		if ( gettype($for (Else) == "string" ) ) {
			$compilation .= "<?php $" . for (Else . "iterated = true; ?>";
		}

		/**
		 * Append the internal block compilation
		 */
		$compilation .= code;

		if ( isset statement["if (_expr"] ) {
			$compilation .= "<?php } ?>";
		}

		if ( gettype($for (Else) == "string" ) ) {
			$compilation .= "<?php } ?>";
		} else {
			if ( isset($loopContext[level]) ) {
				$compilation .= "<?php $" . prefixLevel . "incr++; } ?>";
			} else {
				$compilation .= "<?php } ?>";
			}
		}

		$this->_for (eachLevel--;

		return compilation;
    }

    /***
	 * Generates a 'forelse' PHP code
	 **/
    public function compileForElse() {

		$level = $this->_for (eachLevel;
		if ( fetch prefix, $this->_for (ElsePointers[level] ) ) {
			if ( isset($this->_loopPointers[level]) ) {
				return "<?php $" . prefix . "incr++; } if ( (!$" . prefix . "iterated) ) { ?>";
			}
			return "<?php } if ( (!$" . prefix . "iterated) ) { ?>";
		}
		return "";
    }

    /***
	 * Compiles a 'if' statement returning PHP code
	 **/
    public function compileIf($statement , $extendsMode  = false ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupt statement", statement);
		}

		/**
		 * Process statements in the "true" block
		 */
		$compilation = "<?php if ( (" . $this->expression(expr) . ") ) { ?>" . $this->_statementList(statement["true_statements"], extendsMode);

		/**
		 * Check for ( a "else"/"elseif (" block
		 */
		if ( fetch blockStatements, statement["false_statements"] ) {

			/**
			 * Process statements in the "false" block
			 */
			$compilation .= "<?php } else { ?>" . $this->_statementList(blockStatements, extendsMode);
		}

		$compilation .= "<?php } ?>";

		return compilation;
    }

    /***
	 * Compiles a 'switch' statement returning PHP code
	 **/
    public function compileSwitch($statement , $extendsMode  = false ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupt statement", statement);
		}

		/**
		 * Process statements in the "true" block
		 */
		$compilation = "<?php switch (" . $this->expression(expr) . "): ?>";

		/**
		 * Check for ( a "case"/"default" blocks
		 */
		if ( fetch caseClauses, statement["case_clauses"] ) {
			$lines = $this->_statementList(caseClauses, extendsMode);

			/**
			 * Any output (including whitespace) between a switch statement and the first case will result in
			 * a syntax error. This is the responsibility of the user. However, we can clear empty lines
			 * and whitespaces here to reduce the number of errors.
			 *
			 * http://php.net/control-structures.alternative-syntax
			 */
			 if ( strlen(lines) !== 0 ) {
				/**
				 * (*ANYCRLF) - specif (ies a newline convention: (*CR), (*LF) or (*CRLF)
				 * \h+ - 1+ horizontal whitespace chars
				 * $ - end of line (now, befor (e CR or LF)
				 * m - multiline mode on ($ matches at the end of a line).
				 * u - unicode
				 *
				 * g - global search, - is implicit with preg_replace(), you don't need to include it.
				 */
				$lines = preg_replace("/(*ANYCRLF)^\h+|\h+$|(\h){2,}/mu", "", lines);
			 }

			$compilation .= lines;
		}

		$compilation .= "<?php endswitch ?>";

		return compilation;
    }

    /***
	 * Compiles a "case"/"default" clause returning PHP code
	 **/
    public function compileCase($statement , $caseClause  = true ) {

		if ( unlikely caseClause === false ) {
			/**
			 * "default" statement
			 */
			return "<?php default: ?>";
		}

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupt statement", statement);
		}

		/**
		 * "case" statement
		 */
		return "<?php case " . $this->expression(expr) . ": ?>";
    }

    /***
	 * Compiles a "elseif" statement returning PHP code
	 **/
    public function compileElseIf($statement ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupt statement", statement);
		}

		/**
		 * "elseif (" statement
		 */
		return "<?php } elseif ( (" . $this->expression(expr) . ") ) { ?>";
    }

    /***
	 * Compiles a "cache" statement returning PHP code
	 **/
    public function compileCache($statement , $extendsMode  = false ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupt statement", statement);
		}

		/**
		 * Cache statement
		 */
		$exprCode = $this->expression(expr);
		$compilation = "<?php $_cache[" . $this->expression(expr) . "] = $this->di->get('viewCache'); ";
		if ( fetch lif (etime, statement["lif (etime"] ) {
			$compilation .= "$_cacheKey[" . exprCode . "]";
			if ( lif (etime["type"] == PHVOLT_T_IDENTIFIER ) {
				$compilation .= " = $_cache[" . exprCode . "]->start(" . exprCode . ", $" . lif (etime["value"] . "); ";
			} else {
				$compilation .= " = $_cache[" . exprCode . "]->start(" . exprCode . ", " . lif (etime["value"] . "); ";
			}
		} else {
			$compilation .= "$_cacheKey[" . exprCode . "] = $_cache[" . exprCode."]->start(" . exprCode . "); ";
		}
		$compilation .= "if ( ($_cacheKey[" . exprCode . "] === null) ) { ?>";

		/**
		 * Get the code in the block
		 */
		$compilation .= $this->_statementList(statement["block_statements"], extendsMode);

		/**
		 * Check if ( the cache has a lif (etime
		 */
		if ( fetch lif (etime, statement["lif (etime"] ) {
			if ( lif (etime["type"] == PHVOLT_T_IDENTIFIER ) {
				$compilation .= "<?php $_cache[" . exprCode . "]->save(" . exprCode . ", null, $" . lif (etime["value"] . "); ";
			} else {
				$compilation .= "<?php $_cache[" . exprCode . "]->save(" . exprCode . ", null, " . lif (etime["value"] . "); ";
			}
			$compilation .= "} else { echo $_cacheKey[" . exprCode . "]; } ?>";
		} else {
			$compilation .= "<?php $_cache[" . exprCode . "]->save(" . exprCode . "); } else { echo $_cacheKey[" . exprCode . "]; } ?>";
		}

		return compilation;
    }

    /***
	 * Compiles a "set" statement returning PHP code
	 **/
    public function compileSet($statement ) {

		/**
		 * A valid assignment list is required
		 */
		if ( !fetch assignments, statement["assignments"] ) {
			throw new Exception("Corrupted statement");
		}

		$compilation = "<?php";

		/**
		 * A single set can have several assignments
		 */
		foreach ( $assignments as $assignment ) {

			$exprCode = $this->expression(assignment["expr"]);

			/**
			 * Resolve the expression assigned
			 */
			$target = $this->expression(assignment["variable"]);

			/**
			 * Assignment operator
			 * Generate the right operator
			 */
			switch assignment["op"] {

				case PHVOLT_T_ADD_ASSIGN:
					$compilation .= " " . target . " += " . exprCode . ";";
					break;

				case PHVOLT_T_SUB_ASSIGN:
					$compilation .= " " . target . " -= " . exprCode . ";";
					break;

				case PHVOLT_T_MUL_ASSIGN:
					$compilation .= " " . target . " *= " . exprCode . ";";
					break;

				case PHVOLT_T_DIV_ASSIGN:
					$compilation .= " " . target . " /= " . exprCode . ";";
					break;

				default:
					$compilation .= " " . target . " = " . exprCode . ";";
					break;
			}

		}

		$compilation .= " ?>";
		return compilation;
    }

    /***
	 * Compiles a "do" statement returning PHP code
	 **/
    public function compileDo($statement ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupted statement");
		}

		/**
		 * "Do" statement
		 */
		return "<?php " . $this->expression(expr) . "; ?>";
    }

    /***
	 * Compiles a "return" statement returning PHP code
	 **/
    public function compileReturn($statement ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupted statement");
		}

		/**
		 * "Return" statement
		 */
		return "<?php return " . $this->expression(expr) . "; ?>";
    }

    /***
	 * Compiles a "autoescape" statement returning PHP code
	 **/
    public function compileAutoEscape($statement , $extendsMode ) {

		/**
		 * A valid option is required
		 */
		if ( !fetch autoescape, statement["enable"] ) {
			throw new Exception("Corrupted statement");
		}

		/**
		 * "autoescape" mode
		 */
		$oldAutoescape = $this->_autoescape,
			this->_autoescape = autoescape;

		$compilation = $this->_statementList(statement["block_statements"], extendsMode),
			this->_autoescape = oldAutoescape;

		return compilation;
    }

    /***
	 * Compiles a '{{' '}}' statement returning PHP code
	 *
	 * @param array   statement
	 * @param boolean extendsMode
	 * @return string
	 **/
    public function compileEcho($statement ) {

		/**
		 * A valid expression is required
		 */
		if ( !fetch expr, statement["expr"] ) {
			throw new Exception("Corrupt statement", statement);
		}

		/**
		 * Evaluate common expressions
		 */
		$exprCode = $this->expression(expr);

		if ( expr["type"] == PHVOLT_T_FCALL  ) {

			$name = expr["name"];

			if ( name["type"] == PHVOLT_T_IDENTIFIER ) {

				/**
				 * super() is a function however the return of this function must be output as it is
				 */
				if ( name["value"] == "super" ) {
					return exprCode;
				}
			}
		}

		/**
		 * Echo statement
		 */
		if ( $this->_autoescape ) {
			return "<?= $this->escaper->escapeHtml(" . exprCode . ") ?>";
		}

		return "<?= " . exprCode . " ?>";
    }

    /***
	 * Compiles a 'include' statement returning PHP code
	 **/
    public function compileInclude($statement ) {

		/**
		 * Include statement
		 * A valid expression is required
		 */
		if ( !fetch pathExpr, statement["path"] ) {
			throw new Exception("Corrupted statement");
		}

		/**
		 * Check if ( the expression is a string
		 * If the path is an string try to make an static compilation
		 */
		if ( pathExpr["type"] == 260 ) {

			/**
			 * Static compilation cannot be perfor (med if ( the user passed extra parameters
			 */
			if ( !isset statement["params"]  ) {

				/**
				 * Get the static path
				 */
				$path = pathExpr["value"];

				$finalPath = $this->getFinalPath(path);

				/**
				 * Clone the original compiler
				 * Perfor (m a sub-compilation of the included file
				 * If the compilation doesn't return anything we include the compiled path
				 */
				$subCompiler = clone this;
				$compilation = subCompiler->compile(finalPath, false);
				if ( gettype($compilation) == "null" ) {

					/**
					 * Use file-get-contents to respect the openbase_dir directive
					 */
					$compilation = file_get_contents(subCompiler->getCompiledTemplatePath());
				}

				return compilation;
			}

		}

		/**
		 * Resolve the path's expression
		 */
		$path = $this->expression(pathExpr);

		/**
		 * Use partial
		 */
		if ( !fetch params, statement["params"] ) {
			return "<?php $this->partial(" . path . "); ?>";
		}

		return "<?php $this->partial(" . path . ", " . $this->expression(params) . "); ?>";
    }

    /***
	 * Compiles macros
	 **/
    public function compileMacro($statement , $extendsMode ) {

		/**
		 * A valid name is required
		 */
		if ( !fetch name, statement["name"] ) {
			throw new Exception("Corrupted statement");
		}

		/**
		 * Check if ( the macro is already defined
		 */
		if ( isset($this->_macros[name]) ) {
			throw new Exception("Macro '" . name . "' is already defined");
		}

		/**
		 * Register the macro
		 */
		$this->_macros[name] = name;

		$macroName = "$this->_macros['" . name . "']";

		$code = "<?php ";

		if ( !fetch parameters, statement["parameters"] ) {
			$code .= macroName . " = function() { ?>";
		} else {

			/**
			 * Parameters are always received as an array
			 */
			$code .= macroName . " = function($__p = null) { ";
			foreach ( position, $parameters as $parameter ) {

				$variableName = parameter["variable"];

				$code .= "if ( (isset($__p[" . position . "])) ) { ";
				$code .= "$" . variableName . " = $__p[" . position ."];";
				$code .= " } else { ";
				$code .= "if ( (isset($__p[\"" . variableName."\"])) ) { ";
				$code .= "$" . variableName . " = $__p[\"" . variableName ."\"];";
				$code .= " } else { ";
				if ( fetch defaultValue, parameter["default"] ) {
					$code .= "$" . variableName . " = " . $this->expression(defaultValue) . ";";
				} else {
					$code .= " throw new \\Phalcon\\Mvc\\View\\Exception(\"Macro '" . name . "' was called without parameter: " . variableName . "\"); ";
				}
				$code .= " } } ";
			}

			$code .= " ?>";
		}

		/**
		 * Block statements are allowed
		 */
		if ( fetch blockStatements, statement["block_statements"] ) {

			/**
			 * Process statements block
			 */
			$code .= $this->_statementList(blockStatements, extendsMode) . "<?php }; ";
		}  else {
			$code .= "<?php }; ";
		}

		/**
		 * Bind the closure to the $this object allowing to call services
		 */
		$code .= macroName . " = \\Closure::bind(" . macroName . ", $this); ?>";

		return code;
    }

    /***
	 * Compiles calls to macros
	 *
	 * @param array    statement
	 * @param boolean  extendsMode
	 * @return string
	 **/
    public function compileCall($statement , $extendsMode ) {

    }

    /***
	 * Traverses a statement list compiling each of its nodes
	 **/
    final protected function _statementList($statements , $extendsMode  = false ) {
			statement, tempCompilation, type, blockName, blockStatements,
			blocks, path, finalPath, subCompiler, level;

		/**
		 * Nothing to compile
		 */
		if ( !count(statements) ) {
			return "";
		}

		/**
		 * Increase the statement recursion level in extends mode
		 */
		$extended = $this->_extended;
		$blockMode = extended || extendsMode;
		if ( blockMode === true ) {
			$this->_blockLevel++;
		}

		$this->_level++;

		$compilation = null;

		$extensions = $this->_extensions;
		foreach ( $statements as $statement ) {

			/**
			 * All statements must be arrays
			 */
			if ( gettype($statement) != "array" ) {
				throw new Exception("Corrupted statement");
			}

			/**
			 * Check if ( the statement is valid
			 */
			if ( !isset statement["type"] ) {
				throw new Exception("Invalid statement in " . statement["file"] . " on line " . statement["line"], statement);
			}

			/**
			 * Check if ( extensions have implemented custom compilation for ( this statement
			 */
			if ( gettype($extensions) == "array" ) {

				/**
				 * Notif (y the extensions about being resolving a statement
				 */
				$tempCompilation = $this->fireExtensionEvent("compileStatement", [statement]);
				if ( gettype($tempCompilation) == "string" ) {
					$compilation .= tempCompilation;
					continue;
				}
			}

			/**
			 * Get the statement type
			 */
			$type = statement["type"];

			/**
			 * Compile the statement according to the statement's type
			 */
			switch type {

				case PHVOLT_T_RAW_FRAGMENT:
					$compilation .= statement["value"];
					break;

				case PHVOLT_T_IF:
					$compilation .= $this->compileIf(statement, extendsMode);
					break;

				case PHVOLT_T_ELSEIF:
					$compilation .= $this->compileElseIf(statement);
					break;

				case PHVOLT_T_SWITCH:
					$compilation .= $this->compileSwitch(statement, extendsMode);
					break;

				case PHVOLT_T_CASE:
					$compilation .= $this->compileCase(statement);
					break;

				case PHVOLT_T_DEFAULT:
					$compilation .= $this->compileCase(statement, false);
					break;

				case PHVOLT_T_FOR:
					$compilation .= $this->compileForeach(statement, extendsMode);
					break;

				case PHVOLT_T_SET:
					$compilation .= $this->compileSet(statement);
					break;

				case PHVOLT_T_ECHO:
					$compilation .= $this->compileEcho(statement);
					break;

				case PHVOLT_T_BLOCK:

					/**
					 * Block statement
					 */
					$blockName = statement["name"];


					$blocks = $this->_blocks;
					if ( blockMode ) {

						if ( gettype($blocks) != "array" ) {
							$blocks = [];
						}

						/**
						 * Create a unamed block
						 */
						if ( gettype($compilation) != "null" ) {
							$blocks[] = compilation;
							$compilation = null;
						}

						/**
						 * In extends mode we add the block statements to the blocks variable
						 */
						$blocks[blockName] = blockStatements;
						$this->_blocks = blocks;

					} else {
						if ( gettype($blockStatements) == "array" ) {
							$compilation .= $this->_statementList(blockStatements, extendsMode);
						}
					}
					break;

				case PHVOLT_T_EXTENDS:

					/**
					 * Extends statement
					 */
					$path = statement["path"];

					$finalPath = $this->getFinalPath(path["value"]);

					$extended = true;

					/**
					 * Perfor (m a sub-compilation of the extended file
					 */
					$subCompiler = clone this;
					$tempCompilation = subCompiler->compile(finalPath, extended);

					/**
					 * If the compilation doesn't return anything we include the compiled path
					 */
					if ( gettype($tempCompilation) == "null" ) {
						$tempCompilation = file_get_contents(subCompiler->getCompiledTemplatePath());
					}

					$this->_extended = true;
					$this->_extendedBlocks = tempCompilation;
					$blockMode = extended;
					break;

				case PHVOLT_T_INCLUDE:
					$compilation .= $this->compileInclude(statement);
					break;

				case PHVOLT_T_CACHE:
					$compilation .= $this->compileCache(statement, extendsMode);
					break;

				case PHVOLT_T_DO:
					$compilation .= $this->compileDo(statement);
					break;

				case PHVOLT_T_RETURN:
					$compilation .= $this->compileReturn(statement);
					break;

				case PHVOLT_T_AUTOESCAPE:
					$compilation .= $this->compileAutoEscape(statement, extendsMode);
					break;

				case PHVOLT_T_CONTINUE:
					/**
					 * "Continue" statement
					 */
					$compilation .= "<?php continue; ?>";
					break;

				case PHVOLT_T_BREAK:
					/**
					 * "Break" statement
					 */
					$compilation .= "<?php break; ?>";
					break;

				case 321:
					/**
					 * "Forelse" condition
					 */
					$compilation .= $this->compileForElse();
					break;

				case PHVOLT_T_MACRO:
					/**
					 * Define a macro
					 */
					$compilation .= $this->compileMacro(statement, extendsMode);
					break;

				case 325:
					/**
					 * "Call" statement
					 */
					$compilation .= $this->compileCall(statement, extendsMode);
					break;

				case 358:
					/**
					 * Empty statement
					 */
					break;

				default:
					throw new Exception("Unknown statement " . type . " in " . statement["file"] . " on line " . statement["line"]);

			}
		}

		/**
		 * Reduce the statement level nesting
		 */
		if ( blockMode === true ) {
			$level = $this->_blockLevel;
			if ( level == 1 ) {
				if ( gettype($compilation) != "null" ) {
					$this->_blocks[] = compilation;
				}
			}
			$this->_blockLevel--;
		}

		$this->_level--;

		return compilation;
    }

    /***
	 * Compiles a Volt source code returning a PHP plain version
	 **/
    protected function _compileSource($viewCode , $extendsMode  = false ) {
			finalCompilation, blocks, extendedBlocks, name, block,
			blockCompilation, localBlock, compilation, options, autoescape;

		$currentPath = $this->_currentPath;

		/**
		 * Check for ( compilation options
		 */
		$options = $this->_options;
		if ( gettype($options) == "array" ) {

			/**
			 * Enable autoescape globally
			 */
			if ( fetch autoescape, options["autoescape"] ) {
				if ( gettype($autoescape) != "bool" ) {
					throw new Exception("'autoescape' must be boolean");
				}
				$this->_autoescape = autoescape;
			}
		}

		$intermediate = phvolt_parse_view(viewCode, currentPath);

		/**
		 * The parsing must return a valid array
		 */
		if ( gettype($intermediate) != "array" ) {
			throw new Exception("Invalid intermediate representation");
		}

		$compilation = $this->_statementList(intermediate, extendsMode);

		/**
		 * Check if ( the template is extending another
		 */
		$extended = $this->_extended;
		if ( extended === true ) {

			/**
			 * Multiple-Inheritance is allowed
			 */
			if ( extendsMode === true ) {
				$finalCompilation = [];
			} else {
				$finalCompilation = null;
			}

			$blocks = $this->_blocks;
			$extendedBlocks = $this->_extendedBlocks;

			foreach ( name, $extendedBlocks as $block ) {

				/**
				 * If name is a string then is a block name
				 */
				if ( gettype($name) == "string" ) {

					if ( isset($blocks[name]) ) {
						/**
						 * The block is set in the local template
						 */
						$localBlock = blocks[name],
							this->_currentBlock = name,
							blockCompilation = $this->_statementList(localBlock);
					} else {
						if ( gettype($block) == "array" ) {
							/**
							 * The block is not set local only in the extended template
							 */
							$blockCompilation = $this->_statementList(block);
						} else {
							$blockCompilation = block;
						}
					}

					if ( extendsMode === true ) {
						$finalCompilation[name] = blockCompilation;
					} else {
						$finalCompilation .= blockCompilation;
					}
				} else {

					/**
					 * Here the block is an already compiled text
					 */
					if ( extendsMode === true ) {
						$finalCompilation[] = block;
					} else {
						$finalCompilation .= block;
					}
				}
			}

			return finalCompilation;
		}

		if ( extendsMode === true ) {
			/**
			 * In extends mode we return the template blocks instead of the compilation
			 */
			return $this->_blocks;
		}
		return compilation;
    }

    /***
	 * Compiles a template into a string
	 *
	 *<code>
	 * echo $compiler->compileString('{{ "hello world" }}');
	 *</code>
	 **/
    public function compileString($viewCode , $extendsMode  = false ) {
		$this->_currentPath = "eval code";
		return $this->_compileSource(viewCode, extendsMode);
    }

    /***
	 * Compiles a template into a file forcing the destination path
	 *
	 *<code>
	 * $compiler->compile("views/layouts/main.volt", "views/layouts/main.volt.php");
	 *</code>
	 *
	 * @param string path
	 * @param string compiledPath
	 * @param boolean extendsMode
	 * @return string|array
	 **/
    public function compileFile($path , $compiledPath , $extendsMode  = false ) {

		if ( path == compiledPath ) {
			throw new Exception("Template path and compilation template path cannot be the same");
		}

		/**
		 * Check if ( the template does exist
		 */
		if ( !file_exists(path) ) {
			throw new Exception("Template file " . path . " does not exist");
		}

		/**
		 * Always use file_get_contents instead of read the file directly, this respect the open_basedir directive
		 */
		$viewCode = file_get_contents(path);
		if ( viewCode === false ) {
			throw new Exception("Template file " . path . " could not be opened");
		}

		$this->_currentPath = path;
		$compilation = $this->_compileSource(viewCode, extendsMode);

		/**
		 * We store the file serialized if ( it's an array of blocks
		 */
		if ( gettype($compilation) == "array" ) {
			$finalCompilation = serialize(compilation);
		} else {
			$finalCompilation = compilation;
		}

		/**
		 * Always use file_put_contents to write files instead of write the file
		 * directly, this respect the open_basedir directive
		 */
		if ( file_put_contents(compiledPath, finalCompilation) === false ) {
			throw new Exception("Volt directory can't be written");
		}

		return compilation;
    }

    /***
	 * Compiles a template into a file applying the compiler options
	 * This method does not return the compiled path if the template was not compiled
	 *
	 *<code>
	 * $compiler->compile("views/layouts/main.volt");
	 *
	 * require $compiler->getCompiledTemplatePath();
	 *</code>
	 **/
    public function compile($templatePath , $extendsMode  = false ) {
			compiledExtension, compilation, options, realCompiledPath,
			compiledTemplatePath, templateSepPath;

		/**
		 * Re-initialize some properties already initialized when the object is cloned
		 */
		$this->_extended = false;
		$this->_extendedBlocks = false;
		$this->_blocks = null;
		$this->_level = 0;
		$this->_for (eachLevel = 0;
		$this->_blockLevel = 0;
		$this->_exprLevel = 0;

		$stat = true;
		$compileAlways = false;
		$compiledPath = "";
		$prefix = null;
		$compiledSeparator = "%%";
		$compiledExtension = ".php";
		$compilation = null;

		$options = $this->_options;
		if ( gettype($options) == "array" ) {

			/**
			 * This makes that templates will be compiled always
			 */
			if ( isset options["compileAlways"] ) {
				$compileAlways = options["compileAlways"];
				if ( gettype($compileAlways) != "boolean" ) {
					throw new Exception("'compileAlways' must be a bool value");
				}
			}

			/**
			 * Prefix is prepended to the template name
			 */
			if ( isset options["prefix"] ) {
				$prefix = options["prefix"];
				if ( gettype($prefix) != "string" ) {
					throw new Exception("'prefix' must be a string");
				}
			}

			/**
			 * Compiled path is a directory where the compiled templates will be located
			 */
			if ( isset options["compiledPath"] ) {
				$compiledPath = options["compiledPath"];
				if ( gettype($compiledPath) != "string" ) {
					if ( gettype($compiledPath) != "object" ) {
						throw new Exception("'compiledPath' must be a string or a closure");
					}
				}
			}

			/**
			 * There is no compiled separator by default
			 */
			if ( isset options["compiledSeparator"] ) {
				$compiledSeparator = options["compiledSeparator"];
				if ( gettype($compiledSeparator) != "string" ) {
					throw new Exception("'compiledSeparator' must be a string");
				}
			}

			/**
			 * By default the compile extension is .php
			 */
			if ( isset options["compiledExtension"] ) {
				$compiledExtension = options["compiledExtension"];
				if ( gettype($compiledExtension) != "string" ) {
					throw new Exception("'compiledExtension' must be a string");
				}
			}

			/**
			 * Stat option assumes the compilation of the file
			 */
			if ( isset options["stat"] ) {
				$stat = options["stat"];
			}
		}

		/**
		 * Check if ( there is a compiled path
		 */
		if ( gettype($compiledPath) == "string" ) {

			/**
			 * Calculate the template realpath's
			 */
			if ( !empty compiledPath ) {
				/**
				 * Create the virtual path replacing the directory separator by the compiled separator
				 */
				$templateSepPath = prepare_virtual_path(realpath(templatePath), compiledSeparator);
			} else {
				$templateSepPath = templatePath;
			}

			/**
			 * In extends mode we add an additional 'e' suffix to the file
			 */
			if ( extendsMode === true ) {
				$compiledTemplatePath = compiledPath . prefix . templateSepPath . compiledSeparator . "e" . compiledSeparator . compiledExtension;
			} else {
				$compiledTemplatePath = compiledPath . prefix . templateSepPath . compiledExtension;
			}

		} else {

			/**
			 * A closure can dynamically compile the path
			 */
			if ( gettype($compiledPath) == "object" ) {

				if ( compiledPath instanceof \Closure ) {

					$compiledTemplatePath = call_user_func_array(compiledPath, [templatePath, options, extendsMode]);

					/**
					 * The closure must return a valid path
					 */
					if ( gettype($compiledTemplatePath) != "string" ) {
						throw new Exception("compiledPath closure didn't return a valid string");
					}
				} else {
					throw new Exception("compiledPath must be a string or a closure");
				}
			}
		}

		/**
		 * Use the real path to avoid collisions
		 */
		$realCompiledPath = compiledTemplatePath;

		if ( compileAlways ) {

			/**
			 * Compile always must be used only in the development stage
			 */
			$compilation = $this->compileFile(templatePath, realCompiledPath, extendsMode);
		} else {
			if ( stat === true ) {
				if ( file_exists(compiledTemplatePath) ) {

					/**
					 * Compare modif (ication timestamps to check if ( the file needs to be recompiled
					 */
					if ( compare_mtime(templatePath, realCompiledPath) ) {
						$compilation = $this->compileFile(templatePath, realCompiledPath, extendsMode);
					} else {

						if ( extendsMode === true ) {

							/**
							 * In extends mode we read the file that must contains a serialized array of blocks
							 */
							$blocksCode = file_get_contents(realCompiledPath);
							if ( blocksCode === false ) {
								throw new Exception("Extends compilation file " . realCompiledPath . " could not be opened");
							}

							/**
							 * Unserialize the array blocks code
							 */
							if ( blocksCode ) {
								$compilation = unserialize(blocksCode);
							} else {
								$compilation = [];
							}
						}
					}
				} else {

					/**
					 * The file doesn't exist so we compile the php version for ( the first time
					 */
					$compilation = $this->compileFile(templatePath, realCompiledPath, extendsMode);
				}
			} else {

				/**
				 * Stat is off but the compiled file doesn't exist
				 */
				if ( !file_exists(realCompiledPath) ) {
					/**
					 * The file doesn't exist so we compile the php version for ( the first time
					 */
					$compilation = $this->compileFile(templatePath, realCompiledPath, extendsMode);
				}

			}
		}

		$this->_compiledTemplatePath = realCompiledPath;

		return compilation;
    }

    /***
	 * Returns the path that is currently being compiled
	 **/
    public function getTemplatePath() {
		return $this->_currentPath;
    }

    /***
	 * Returns the path to the last compiled template
	 **/
    public function getCompiledTemplatePath() {
		return $this->_compiledTemplatePath;
    }

    /***
	 * Parses a Volt template returning its intermediate representation
	 *
	 *<code>
	 * print_r(
	 *     $compiler->parse("{{ 3 + 2 }}")
	 * );
	 *</code>
	 *
	 * @param string viewCode
	 * @return array
	 **/
    public function parse($viewCode ) {
		return phvolt_parse_view(viewCode, currentPath);
    }

    /***
	 * Gets the final path with VIEW
	 **/
    protected function getFinalPath($path ) {
		$view = $this->_view;

		if ( gettype($view) == "object" ) {
			$viewsDirs = view->getViewsDir();

			if ( gettype($viewsDirs) == "array" ) {
				foreach ( $viewsDirs as $viewsDir ) {
					if ( file_exists(viewsDir . path) ) {
						return viewsDir . path;
					}
				}

				// Otherwise, take the last viewsDir
				return viewsDir . path;

			} else {
				return viewsDirs . path;
			}
		}

		return path;
    }

}