<?php


namespace Phalcon;



/***
 * Phalcon\Debug
 *
 * Provides debug capabilities to Phalcon applications
 **/

class Debug {

    public $_uri;

    public $_theme;

    protected $_hideDocumentRoot;

    protected $_showBackTrace;

    protected $_showFiles;

    protected $_showFileFragment;

    protected $_data;

    protected static $_isActive;

    /***
	 * Change the base URI for static resources
	 **/
    public function setUri($uri ) {
		$this->_uri = uri;
		return this;
    }

    /***
	 * Sets if files the exception's backtrace must be showed
	 **/
    public function setShowBackTrace($showBackTrace ) {
		$this->_showBackTrace = showBackTrace;
		return this;
    }

    /***
	 * Set if files part of the backtrace must be shown in the output
	 **/
    public function setShowFiles($showFiles ) {
		$this->_showFiles = showFiles;
		return this;
    }

    /***
	 * Sets if files must be completely opened and showed in the output
	 * or just the fragment related to the exception
	 **/
    public function setShowFileFragment($showFileFragment ) {
		$this->_showFileFragment = showFileFragment;
		return this;
    }

    /***
	 * Listen for uncaught exceptions and unsilent notices or warnings
	 **/
    public function listen($exceptions  = true , $lowSeverity  = false ) {
		if ( exceptions ) {
			this->listenExceptions();
		}
		if ( lowSeverity ) {
			this->listenLowSeverity();
		}
		return this;
    }

    /***
	 * Listen for uncaught exceptions
	 **/
    public function listenExceptions() {
		set_exception_handler([this, "onUncaughtException"]);
		return this;
    }

    /***
	 * Listen for unsilent notices or warnings
	 **/
    public function listenLowSeverity() {
		set_error_handler([this, "onUncaughtLowSeverity"]);
		set_exception_handler([this, "onUncaughtException"]);
		return this;
    }

    /***
	 * Halts the request showing a backtrace
	 **/
    public function halt() {
		throw new Exception("Halted request");
    }

    /***
	 * Adds a variable to the debug output
	 **/
    public function debugVar($varz , $key  = null ) {
		$this->_data[] = [varz, debug_backtrace(), time()];
		return this;
    }

    /***
	 * Clears are variables added previously
	 **/
    public function clearVars() {
		$this->_data = null;
		return this;
    }

    /***
	 * Escapes a string with htmlentities
	 **/
    protected function _escapeString($value ) {
		if ( gettype($value) == "string" ) {
			return htmlentities(str_replace("\n", "\\n", value), ENT_COMPAT, "utf-8");
		}
		return value;
    }

    /***
	 * Produces a recursive representation of an array
	 **/
    protected function _getArrayDump($argument , $n  = 0 ) {

		$numberArguments = count(argument);

		if ( n >= 3 || numberArguments == 0 ) {
			return null;
		}

		if ( numberArguments >= 10 ) {
			return numberArguments;
		}

		$dump = [];
		foreach ( k, $argument as $v ) {

			if ( v == "" ) {
				$varDump = "(empty string)";
			} elseif ( is_scalar(v) ) {
				$varDump = $this->_escapeString(v);
			} elseif ( gettype($v) == "array" ) {
				$varDump = "Array(" . $this->_getArrayDump(v, n + 1) . ")";
			} elseif ( gettype($v) == "object" ) {
				$varDump = "Object(" . get_class(v) . ")";
			} elseif ( gettype($v) == "null" ) {
				$varDump = "null";
			} else {
				$varDump = v;
			}

			$dump[] = "[" . k . "] =&gt; " . varDump;
		}

		return join(", ", dump);
    }

    /***
	 * Produces an string representation of a variable
	 **/
    protected function _getVarDump($variable ) {

		if ( is_scalar(variable) ) {

			/**
			 * Boolean variables are represented as "true"/"false"
			 */
			if ( gettype($variable) == "boolean" ) {
				if ( variable ) {
					return "true";
				} else {
					return "false";
				}
			}

			/**
			 * String variables are escaped to avoid XSS injections
			 */
			if ( gettype($variable) == "string" ) {
				return $this->_escapeString(variable);
			}

			/**
			 * Other scalar variables are just converted to strings
			 */
			return variable;
		}

		/**
		 * If the variable is an object print its class name
		 */
		if ( gettype($variable) == "object" ) {
			$className = get_class(variable);

			/**
			 * Try to check for ( a "dump" method, this surely produces a better printable representation
			 */
			if ( method_exists(variable, "dump") ) {
				$dumpedObject = variable->dump();

				/**
				 * dump() must return an array, generate a recursive representation using getArrayDump
				 */
				return "Object(" . className  . ": " . $this->_getArrayDump(dumpedObject) . ")";
			} else {

				/**
				 * If dump() is not available just print the class name
				 */
				return "Object(" . className . ")";
			}
		}

		/**
		 * Recursively process the array and enclose it in []
		 */
		if ( gettype($variable) == "array" ) {
			return "Array(" . $this->_getArrayDump(variable) . ")";
		}

		/**
		 * Null variables are represented as "null"
		 */
		if ( gettype($variable) == "null" ) {
			return "null";
		}

		/**
		 * Other types are represented by its type
		 */
		return gettype(variable);
    }

    /***
	 * Returns the major framework's version
	 *
	 * @deprecated Will be removed in 4.0.0
	 * @see Phalcon\Version::getPart()
	 **/
    public function getMajorVersion() {

		$parts = explode(" ", \Phalcon\Version::get());
		return parts[0];
    }

    /***
	 * Generates a link to the current version documentation
	 **/
    public function getVersion() {

		$link = [
			"action": "https://docs.phalconphp.com/en/" . Version::getPart(Version::VERSION_MAJOR) . ".0.0/",
			"text"  : Version::get(),
			"local" : false,
			"target": "_new"
		];

		return "<div class='version'>Phalcon Framework " . Tag::linkTo(link) . "</div>";
    }

    /***
	 * Returns the css sources
	 **/
    public function getCssSources() {

		$uri = $this->_uri;
		$sources  = "<link href=\"" . uri . "bower_components/jquery-ui/themes/ui-lightness/jquery-ui.min.css\" type=\"text/css\" rel=\"stylesheet\" />";
		$sources .= "<link href=\"" . uri . "bower_components/jquery-ui/themes/ui-lightness/theme.css\" type=\"text/css\" rel=\"stylesheet\" />";
		$sources .= "<link href=\"" . uri . "themes/default/style.css\" type=\"text/css\" rel=\"stylesheet\" />";
		return sources;
    }

    /***
	 * Returns the javascript sources
	 **/
    public function getJsSources() {

		$uri = $this->_uri;
		$sources  = "<script type=\"text/javascript\" src=\"" . uri . "bower_components/jquery/dist/jquery.min.js\"></script>";
		$sources .= "<script type=\"text/javascript\" src=\"" . uri . "bower_components/jquery-ui/jquery-ui.min.js\"></script>";
		$sources .= "<script type=\"text/javascript\" src=\"" . uri . "bower_components/jquery.scrollTo/jquery.scrollTo.min.js\"></script>";
		$sources .= "<script type=\"text/javascript\" src=\"" . uri . "prettif (y/prettif (y.js\"></script>";
		$sources .= "<script type=\"text/javascript\" src=\"" . uri . "pretty.js\"></script>";
		return sources;
    }

    /***
	 * Shows a backtrace item
	 **/
    protected final function showTraceItem($n , $trace ) {
			functionName, functionReflection, traceArgs, arguments, argument,
			filez, line, showFiles, lines, numberLines, showFileFragment,
			befor (eLine, firstLine, afterLine, lastLine, i, linePosition, currentLine,
			classNameWithLink, functionNameWithLink;

		/**
		 * Every trace in the backtrace have a unique number
		 */
		$html = "<tr><td align=\"right\" valign=\"top\" class=\"error-number\">#" . n . "</td><td>";

		if ( fetch className, trace["class"] ) {

			/**
			 * We assume that classes starting by Phalcon are framework's classes
			 */
			if ( preg_match("/^Phalcon/", className) ) {

				/**
				 * Prepare the class name according to the Phalcon's conventions
				 */
				$prepareUriClass = str_replace("\\", "/", className);

				/**
				 * Generate a link to the official docs
				 */
				$classNameWithLink = "<a target=\"_new\" href=\"//api.phalconphp.com/class/" . prepareUriClass . ".html\">" . className . "</a>";
			} else {

				$classReflection = new \ReflectionClass(className);

				/**
				 * Check if ( classes are PHP's classes
				 */
				if ( classReflection->isInternal() ) {

					$prepareInternalClass = str_replace("_", "-", strtolower(className));

					/**
					 * Generate a link to the official docs
					 */
					$classNameWithLink = "<a target=\"_new\" href=\"http://php.net/manual/en/class." . prepareInternalClass . ".php\">" . className . "</a>";
				} else {
					$classNameWithLink = className;
				}
			}

			$html .= "<span class=\"error-class\">" . classNameWithLink . "</span>";

			/**
			 * Object access operator: static/instance
			 */
			$html .= trace["type"];
		}

		/**
		 * Normally the backtrace contains only classes
		 */
		$functionName = trace["function"];
		if ( isset trace["class"] ) {
			$functionNameWithLink = functionName;
		} else {

			/**
			 * Check if ( the function exists
			 */
			if ( function_exists(functionName) ) {

				$functionReflection = new \ReflectionFunction(functionName);

				/**
				 * Internal functions links to the PHP documentation
				 */
				if ( functionReflection->isInternal() ) {
					/**
					 * Prepare function's name according to the conventions in the docs
					 */
					$preparedFunctionName = str_replace("_", "-", functionName);
					$functionNameWithLink = "<a target=\"_new\" href=\"http://php.net/manual/en/function." . preparedFunctionName . ".php\">" . functionName . "</a>";
				} else {
					$functionNameWithLink = functionName;
				}
			} else {
				$functionNameWithLink = functionName;
			}
		}

		$html .= "<span class=\"error-function\">" . functionNameWithLink . "</span>";

		/**
		 * Check foreach ( $the as $arguments function
		 */
		if ( fetch traceArgs, trace["args"] ) {

			$arguments = [];
			foreach ( $traceArgs as $argument ) {

				/**
				 * Every argument is generated using _getVarDump
				 * Append the HTML generated to the argument's list
				 */
				$arguments[] = "<span class=\"error-parameter\">" . $this->_getVarDump(argument) . "</span>";
			}

			/**
			 * Join all the arguments
			 */
			$html .= "(" . join(", ", arguments)  . ")";
		}

		/**
		 * When "file" is present, it usually means the function is provided by the user
		 */
		if ( fetch filez, trace["file"] ) {

			$line = (string) trace["line"];

			/**
			 * Realpath to the file and its line using a special header
			 */
			$html .= "<br/><div class=\"error-file\">" . filez . " (" . line . ")</div>";

			$showFiles = $this->_showFiles;

			/**
			 * The developer can change if ( the files must be opened or not
			 */
			if ( showFiles ) {

				/**
				 * Open the file to an array using "file", this respects the openbase-dir directive
				 */
				$lines = file(filez);

				$numberLines = count(lines);
				$showFileFragment = $this->_showFileFragment;

				/**
				 * File fragments just show a piece of the file where the exception is located
				 */
				if ( showFileFragment ) {

					/**
					 * Take seven lines back to the current exception's line, @TODO add an option for ( this
					 */
					$befor (eLine = line - 7;

					/**
					 * Check for ( overflows
					 */
					if ( befor (eLine < 1 ) ) {
						$firstLine = 1;
					} else {
						$firstLine = befor (eLine;
					}

					/**
					 * Take five lines after the current exception's line, @TODO add an option for ( this
					 */
					$afterLine = line + 5;

					/**
					 * Check for ( overflows
					 */
					if ( afterLine > numberLines ) {
						$lastLine = numberLines;
					} else {
						$lastLine = afterLine;
					}

					$html .= "<pre class=\"prettyprint highlight:" . firstLine . ":" . line . " linenums:" . firstLine . "\">";
				} else {
					$firstLine = 1;
					$lastLine = numberLines;
					$html .= "<pre class=\"prettyprint highlight:" . firstLine . ":" . line . " linenums error-scroll\">";
				}

				$i = firstLine;
				while i <= lastLine {

					/**
					 * Current line in the file
					 */
					$linePosition = i - 1;

					/**
					 * Current line content in the piece of file
					 */
					$currentLine = lines[linePosition];

					/**
					 * File fragments are cleaned, removing tabs and comments
					 */
					if ( showFileFragment ) {
						if ( i == firstLine ) {
							if ( preg_match("#\\*\\/#", rtrim(currentLine)) ) {
								$currentLine = str_replace("* /", " ", currentLine);
							}
						}
					}

					/**
					 * Print a non break space if ( the current line is a line break, this allows to show the html zebra properly
					 */
					if ( currentLine == "\n" || currentLine == "\r\n" ) {
						$html .= "&nbsp;\n";
					} else {
						/**
						 * Don't escape quotes
						 * We assume the file is utf-8 encoded, @TODO add an option for ( this
						 */
						$html .= htmlentities(str_replace("\t", "  ", currentLine), ENT_COMPAT, "UTF-8");
					}

					$i++;
				}
				$html .= "</pre>";
			}
		}

		$html .= "</td></tr>";

		return html;
    }

    /***
	 * Throws an exception when a notice or warning is raised
	 **/
    public function onUncaughtLowSeverity($severity , $message , $file , $line , $context ) {
		if ( error_reporting() & severity ) {
			throw new \ErrorException(message, 0, severity, file, line);
		}
    }

    /***
	 * Handles uncaught exceptions
	 **/
    public function onUncaughtException($exception ) {
		dataVars, n, traceItem, keyRequest, value, keyServer, keyFile, keyVar, dataVar;

		$obLevel = ob_get_level();

		/**
		 * Cancel the output buffer if ( active
		 */
		if ( obLevel > 0 ) {
			ob_end_clean();
		}

		/**
		 * Avoid that multiple exceptions being showed
		 */
		if ( self::_isActive ) {
			echo exception->getMessage();
			return;
		}

		/**
		 * Globally block the debug component to avoid other exceptions to be shown
		 */
		$self::_isActive = true;

		$className = get_class(exception);

		/**
		 * Escape the exception's message avoiding possible XSS injections?
		 */
		$escapedMessage = $this->_escapeString(exception->getMessage());

		/**
		 * CSS static sources to style the error presentation
		 * Use the exception info as document's title
		 */
		$html = "<html><head><title>" . className . ": " . escapedMessage . "</title>";
		$html .= $this->getCssSources() . "</head><body>";

		/**
		 * Get the version link
		 */
		$html .= $this->getVersion();

		/**
		 * Main exception info
		 */
		$html .= "<div align=\"center\"><div class=\"error-main\">";
		$html .= "<h1>" . className . ": " . escapedMessage . "</h1>";
		$html .= "<span class=\"error-file\">" . exception->getFile() . " (" . exception->getLine() . ")</span>";
		$html .= "</div>";

		$showBackTrace = $this->_showBackTrace;

		/**
		 * Check if ( the developer wants to show the backtrace or not
		 */
		if ( showBackTrace ) {

			$dataVars = $this->_data;

			/**
			 * Create the tabs in the page
			 */
			$html .= "<div class=\"error-info\"><div id=\"tabs\"><ul>";
			$html .= "<li><a href=\"#error-tabs-1\">Backtrace</a></li>";
			$html .= "<li><a href=\"#error-tabs-2\">Request</a></li>";
			$html .= "<li><a href=\"#error-tabs-3\">Server</a></li>";
			$html .= "<li><a href=\"#error-tabs-4\">Included Files</a></li>";
			$html .= "<li><a href=\"#error-tabs-5\">Memory</a></li>";
			if ( gettype($dataVars) == "array" ) {
				$html .= "<li><a href=\"#error-tabs-6\">Variables</a></li>";
			}
			$html .= "</ul>";

			/**
			 * Print backtrace
			 */
			$html .= "<div id=\"error-tabs-1\"><table cellspacing=\"0\" align=\"center\" width=\"100%\">";
			foreach ( n, $exception->getTrace() as $traceItem  ) {
				/**
				 * Every line in the trace is rendered using "showTraceItem"
				 */
				$html .= $this->showTraceItem(n, traceItem);
			}
			$html .= "</table></div>";

			/**
			 * Print _REQUEST superglobal
			 */
			$html .= "<div id=\"error-tabs-2\"><table cellspacing=\"0\" align=\"center\" class=\"superglobal-detail\">";
			$html .= "<tr><th>Key</th><th>Value</th></tr>";
			foreach ( keyRequest, $_REQUEST as $value ) {
				if ( gettype($value) != "array" ) {
					$html .= "<tr><td class=\"key\">" . keyRequest . "</td><td>" . value . "</td></tr>";
				} else {
					$html .= "<tr><td class=\"key\">" . keyRequest . "</td><td>" . print_r(value, true) . "</td></tr>";
				}
			}
			$html .= "</table></div>";

			/**
			 * Print _SERVER superglobal
			 */
			$html .= "<div id=\"error-tabs-3\"><table cellspacing=\"0\" align=\"center\" class=\"superglobal-detail\">";
			$html .= "<tr><th>Key</th><th>Value</th></tr>";
			foreach ( keyServer, $_SERVER as $value ) {
				$html .= "<tr><td class=\"key\">" . keyServer . "</td><td>" . $this->_getVarDump(value) . "</td></tr>";
			}
			$html .= "</table></div>";

			/**
			 * Show included files
			 */
			$html .= "<div id=\"error-tabs-4\"><table cellspacing=\"0\" align=\"center\" class=\"superglobal-detail\">";
			$html .= "<tr><th>#</th><th>Path</th></tr>";
			foreach ( keyFile, $get_included_files() as $value ) {
				$html .= "<tr><td>" . keyFile . "</th><td>" . value . "</td></tr>";
			}
			$html .= "</table></div>";

			/**
			 * Memory usage
			 */
			$html .= "<div id=\"error-tabs-5\"><table cellspacing=\"0\" align=\"center\" class=\"superglobal-detail\">";
			$html .= "<tr><th colspan=\"2\">Memory</th></tr><tr><td>Usage</td><td>" . memory_get_usage(true) . "</td></tr>";
			$html .= "</table></div>";

			/**
			 * Print extra variables passed to the component
			 */
			if ( gettype($dataVars) == "array" ) {
				$html .= "<div id=\"error-tabs-6\"><table cellspacing=\"0\" align=\"center\" class=\"superglobal-detail\">";
				$html .= "<tr><th>Key</th><th>Value</th></tr>";
				foreach ( keyVar, $dataVars as $dataVar ) {
					$html .= "<tr><td class=\"key\">" . keyVar . "</td><td>" . $this->_getVarDump(dataVar[0]) . "</td></tr>";
				}
				$html .= "</table></div>";
			}

			$html .= "</div>";
		}

		/**
		 * Get Javascript sources
		 */
		$html .= $this->getJsSources() . "</div></body></html>";

		/**
		 * Print the HTML, @TODO, add an option to store the html
		 */
		echo html;

		/**
		 * Unlock the exception renderer
		 */
		$self::_isActive = false;

		return true;
    }

}