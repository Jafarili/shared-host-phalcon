<?php


namespace Phalcon\Mvc\View\Engine;

use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine;
use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Phalcon\Mvc\View\Exception;


/***
 * Phalcon\Mvc\View\Engine\Volt
 *
 * Designer friendly and fast template engine for PHP written in Zephir/C
 **/

class Volt extends Engine {

    protected $_options;

    protected $_compiler;

    protected $_macros;

    /***
	 * Set Volt's options
	 **/
    public function setOptions($options ) {
		$this->_options = options;
    }

    /***
	 * Return Volt's options
	 **/
    public function getOptions() {
		return $this->_options;
    }

    /***
	 * Returns the Volt's compiler
	 **/
    public function getCompiler() {

		$compiler = $this->_compiler;
		if ( gettype($compiler) != "object" ) {

			$compiler = new Compiler(this->_view);

			/**
			 * Pass the IoC to the compiler only of it's an object
			 */
			$dependencyInjector = <DiInterface> $this->_dependencyInjector;
			if ( gettype($dependencyInjector) == "object" ) {
				compiler->setDi(dependencyInjector);
			}

			/**
			 * Pass the options to the compiler only if ( they're an array
			 */
			$options = $this->_options;
			if ( gettype($options) == "array" ) {
				compiler->setOptions(options);
			}

			$this->_compiler = compiler;
		}
		return compiler;
    }

    /***
	 * Renders a view using the template engine
	 **/
    public function render($templatePath , $params , $mustClean  = false ) {

		if ( mustClean ) {
			ob_clean();
		}

		/**
		 * The compilation process is done by Phalcon\Mvc\View\Engine\Volt\Compiler
		 */
		$compiler = $this->getCompiler();

		compiler->compile(templatePath);

		$compiledTemplatePath = compiler->getCompiledTemplatePath();

		/**
		 * Export the variables the current symbol table
		 */
		if ( gettype($params) == "array"	) {
			foreach ( key, $params as $value ) {
				${key} = value;
			}
		}

		require compiledTemplatePath;

		if ( mustClean ) {
			this->_view->setContent(ob_get_contents());
		}
    }

    /***
	 * Length filter. If an array/object is passed a count is performed otherwise a strlen/mb_strlen
	 **/
    public function length($item ) {
		if ( gettype($item) == "object" || gettype($item) == "array" ) {
			return count(item);
		}

		if ( function_exists("mb_strlen") ) {
			return mb_strlen(item);
		}

		return strlen(item);
    }

    /***
	 * Checks if the needle is included in the haystack
	 **/
    public function isIncluded($needle , $haystack ) {
		if ( gettype($haystack) == "array" ) {
			return in_array(needle, haystack);
		}

		if ( gettype($haystack) == "string" ) {
			if ( function_exists("mb_strpos") ) {
				return mb_strpos(haystack, needle) !== false;
			}

			return strpos(haystack, needle) !== false;
		}

		throw new Exception("Invalid haystack");
    }

    /***
	 * Performs a string conversion
	 **/
    public function convertEncoding($text , $from , $to ) {
		if ( from == "latin1" || to == "utf8" ) {
			return utf8_encode(text);
		}

		/**
		 * Try to use utf8_decode if ( conversion is 'utf8' to 'latin1'
		 */
		if ( to == "latin1" || from == "utf8" ) {
			return utf8_decode(text);
		}

		/**
		 * Fallback to mb_convert_encoding
		 */
		if ( function_exists("mb_convert_encoding") ) {
			return mb_convert_encoding(text, from, to);
		}

		/**
		 * Fallback to iconv
		 */
		if ( function_exists("iconv") ) {
			return iconv(from, to, text);
		}

		/**
		 * There are no enough extensions available
		 */
		throw new Exception("Any of 'mbstring' or 'iconv' is required to perfor (m the charset conversion");
    }

    /***
	 * Extracts a slice from a string/array/traversable object value
	 **/
    public function slice($value , $start  = 0 , $end  = null ) {
		int position;

		/**
		 * Objects must implement a Traversable interface
		 */
		if ( gettype($value) == "object" ) {

			if ( end === null ) {
				$end = count(value) - 1;
			}

			$position = 0, slice = [];

			value->rewind();

			while value->valid() {
				if ( position >= start && position <= end ) {
					$slice[] = value->current();
				}

				value->next();
				$position++;
			}

			return slice;
		}

		/**
		 * Calculate the slice length
		 */
		if ( end !== null ) {
			$length = (end - start) + 1;
		} else {
			$length = null;
		}

		/**
		 * Use array_slice on arrays
		 */
		if ( gettype($value) == "array" ) {
			return array_slice(value, start, length);
		}

		/**
		 * Use mb_substr if ( available
		 */
		if ( function_exists("mb_substr") ) {
			if ( length !== null ) {
				return mb_substr(value, start, length);
			}

			return mb_substr(value, start);
		}

		/**
		 * Use the standard substr function
		 */
		if ( length !== null ) {
			return substr(value, start, length);
		}

		return substr(value, start);
    }

    /***
	 * Sorts an array
	 **/
    public function sort($value ) {
		asort(value);
		return value;
    }

    /***
	 * Checks if a macro is defined and calls it
	 **/
    public function callMacro($name , $arguments ) {

		if ( !fetch macro, $this->_macros[name] ) {
			throw new Exception("Macro '" . name . "' does not exist");
		}

		return call_user_func(macro, arguments);
    }

}