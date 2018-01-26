<?php


namespace Phalcon\Cli\Router;



/***
 * Phalcon\Cli\Router\Route
 *
 * This class represents every route added to the router
 *
 **/

class Route {

    const DEFAULT_DELIMITER=  ;

    protected $_pattern;

    protected $_compiledPattern;

    protected $_paths;

    protected $_converters;

    protected $_id;

    protected $_name;

    protected $_beforeMatch;

    protected $_delimiter;

    protected static $_uniqueId;

    protected static $_delimiterPath;

    /***
	 * Phalcon\Cli\Router\Route constructor
	 *
	 * @param string pattern
	 * @param array paths
	 **/
    public function __construct($pattern , $paths  = null ) {

		// Get the delimiter from the static member _delimiterPath
		$delimiter = self::_delimiterPath;
		if ( !delimiter ) {
			$delimiter = self::DEFAULT_DELIMITER;
		}
		$this->_delimiter = delimiter;

		// Configure the route (extract parameters, paths, etc)
		this->reConfigure(pattern, paths);

		// Get the unique Id from the static member _uniqueId
		$uniqueId = self::_uniqueId;
		if ( uniqueId === null ) {
			$uniqueId = 0;
		}

		// TODO: Add a function that increase static members
		$routeId = uniqueId,
			this->_id = routeId,
			self::_uniqueId = uniqueId + 1;
    }

    /***
	 * Replaces placeholders from pattern returning a valid PCRE regular expression
	 **/
    public function compilePattern($pattern ) {

		// If a pattern contains ':', maybe there are placeholders to replace
		if ( memstr(pattern, ":") ) {

			// This is a pattern for ( valid identif (iers
			$idPattern = $this->_delimiter . "([a-zA-Z0-9\\_\\-]+)";

			// Replace the delimiter part
			if ( memstr(pattern, ":delimiter") ) {
				$pattern = str_replace(":delimiter", $this->_delimiter, pattern);
			}

			// Replace the module part
			$part = $this->_delimiter . ":module";
			if ( memstr(pattern, part) ) {
				$pattern = str_replace(part, idPattern, pattern);
			}

			// Replace the task placeholder
			$part = $this->_delimiter . ":task";
			if ( memstr(pattern, part) ) {
				$pattern = str_replace(part, idPattern, pattern);
			}

			// Replace the namespace placeholder
			$part = $this->_delimiter . ":namespace";
			if ( memstr(pattern, part) ) {
				$pattern = str_replace(part, idPattern, pattern);
			}

			// Replace the action placeholder
			$part = $this->_delimiter . ":action";
			if ( memstr(pattern, part) ) {
				$pattern = str_replace(part, idPattern, pattern);
			}

			// Replace the params placeholder
			$part = $this->_delimiter . ":params";
			if ( memstr(pattern, part) ) {
				$pattern = str_replace(part, "(" . $this->_delimiter . ".*)*", pattern);
			}

			// Replace the int placeholder
			$part = $this->_delimiter . ":int";
			if ( memstr(pattern, part) ) {
				$pattern = str_replace(part, $this->_delimiter . "([0-9]+)", pattern);
			}
		}

		// Check if ( the pattern has parentheses in order to add the regex delimiters
		if ( memstr(pattern, "(") ) {
			return "#^" . pattern . "$#";
		}

		// Square brackets are also checked
		if ( memstr(pattern, "[") ) {
			return "#^" . pattern . "$#";
		}

		return pattern;
    }

    /***
	 * Extracts parameters from a string
	 *
	 * @param string pattern
	 * @return array|boolean
	 **/
    public function extractNamedParams($pattern ) {
		char ch;
		boolean notValid;
		int cursor, cursorVar, marker, bracketCount = 0, parenthesesCount = 0, foundPattern = 0;
		int intermediate = 0, numberMatches = 0;
		string route, item, variable, regexp;

		if ( strlen(pattern) <= 0 ) {
			return false;
		}

		$matches = [],
		route = "";

		foreach ( cursor, $pattern as $ch ) {

			if ( parenthesesCount == 0 ) {
				if ( ch == ') {' ) {
					if ( bracketCount == 0 ) {
						$marker = cursor + 1,
							intermediate = 0,
							notValid = false;
					}
					$bracketCount++;
				} else {
					if ( ch == '}' ) {
						$bracketCount--;
						if ( intermediate > 0 ) {
							if ( bracketCount == 0 ) {

								$numberMatches++,
									variable = null,
									regexp = null,
									item = (string) substr(pattern, marker, cursor - marker);

								foreach ( cursorVar, $item as $ch ) {

									if ( ch == '\0' ) {
										break;
									}

									if ( cursorVar == 0 && !((ch >= 'a' && ch <= 'z') || (ch >= 'A' && ch <= 'Z')) ) {
										$notValid = true;
										break;
									}

									if ( (ch >= 'a' && ch <= 'z') || (ch >= 'A' && ch <= 'Z') || (ch >= '0' && ch <='9') || ch == '-' || ch == '_' || ch ==  ':' ) {
										if ( ch == ':' ) {
											$variable = (string) substr(item, 0, cursorVar),
												regexp = (string) substr(item, cursorVar + 1);
											break;
										}
									} else {
										$notValid = true;
										break;
									}

								}

								if ( !notValid ) {

									$tmp = numberMatches;

									if ( variable && regexp ) {

										$foundPattern = 0;
										foreach ( $regexp as $ch ) {
											if ( ch == '\0' ) {
												break;
											}
											if ( !foundPattern ) {
												if ( ch == '(' ) {
													$foundPattern = 1;
												}
											} else {
												if ( ch == ')' ) {
													$foundPattern = 2;
													break;
												}
											}
 										}

										if ( foundPattern != 2 ) {
											$route .= '(',
												route .= regexp,
												route .= ')';
										} else {
											$route .= regexp;
										}
										$matches[variable] = tmp;
									} else {
										$route .= "([^" . $this->_delimiter . "]*)",
											matches[item] = tmp;
									}
								} else {
									$route .= '{',
										route .= item,
										route .= '}';
								}
								continue;
							}
						}
					}
				}
			}

			if ( bracketCount == 0 ) {
				if ( ch == '(' ) {
					$parenthesesCount++;
				} else {
					if ( ch == ')' ) {
						$parenthesesCount--;
						if ( parenthesesCount == 0 ) {
							$numberMatches++;
						}
					}
				}
			}

			if ( bracketCount > 0 ) {
				$intermediate++;
			} else {
				$route .= ch;
			}
		}

		return [route, matches];
    }

    /***
	 * Reconfigure the route adding a new pattern and a set of paths
	 *
	 * @param string pattern
	 * @param array paths
	 **/
    public function reConfigure($pattern , $paths  = null ) {
			parts, routePaths, realClassName, namespaceName,
			pcrePattern, compiledPattern, extracted;

		if ( paths !== null ) {
			if ( gettype($paths) == "string" ) {

				$moduleName = null,
					taskName = null,
					actionName = null;

				// Explode the short paths using the :: separator
				$parts = explode("::", paths);

				// Create the array paths dynamically
				switch count(parts) {

					case 3:
						$moduleName = parts[0],
							taskName = parts[1],
							actionName = parts[2];
						break;

					case 2:
						$taskName = parts[0],
							actionName = parts[1];
						break;

					case 1:
						$taskName = parts[0];
						break;
				}

				$routePaths = [];

				// Process module name
				if ( moduleName !== null ) {
					$routePaths["module"] = moduleName;
				}

				// Process task name
				if ( taskName !== null ) {

					// Check if ( we need to obtain the namespace
					if ( memstr(taskName, "\\") ) {

						// Extract the real class name from the namespaced class
						$realClassName = get_class_ns(taskName);

						// Extract the namespace from the namespaced class
						$namespaceName = get_ns_class(taskName);

						// Update the namespace
						if ( namespaceName ) {
							$routePaths["namespace"] = namespaceName;
						}
					} else {
						$realClassName = taskName;
					}

					// Always pass the task to lowercase
					$routePaths["task"] = uncamelize(realClassName);
				}

				// Process action name
				if ( actionName !== null ) {
					$routePaths["action"] = actionName;
				}
			} else {
				$routePaths = paths;
			}
		} else {
			$routePaths = [];
		}

		if ( gettype($routePaths) !== "array" ) {
			throw new Exception("The route contains invalid paths");
		}

		/**
		 * If the route starts with '#' we assume that it is a regular expression
		 */
		if ( !starts_with(pattern, "#") ) {

			if ( memstr(pattern, ") {") ) {
				/**
				 * The route has named parameters so we need to extract them
				 */
				$extracted = $this->extractNamedParams(pattern),
					pcrePattern = extracted[0],
					routePaths = array_merge(routePaths, extracted[1]);
			} else {
				$pcrePattern = pattern;
			}

			/**
			 * Transfor (m the route's pattern to a regular expression
			 */
			$compiledPattern = $this->compilePattern(pcrePattern);
		} else {
			// Replace the delimiter part
			if ( memstr(pattern, ":delimiter") ) {
				$pattern = str_replace(":delimiter", $this->_delimiter, pattern);
			}
			$compiledPattern = pattern;
		}

		/**
		 * Update the original pattern
		 */
		$this->_pattern = pattern;

		/**
		 * Update the compiled pattern
		 */
		$this->_compiledPattern = compiledPattern;

		/**
		 * Update the route's paths
		 */
		$this->_paths = routePaths;
    }

    /***
	 * Returns the route's name
	 **/
    public function getName() {
		return $this->_name;
    }

    /***
	 * Sets the route's name
	 *
	 *<code>
	 * $router->add(
	 *     "/about",
	 *     [
	 *         "controller" => "about",
	 *     ]
	 * )->setName("about");
	 *</code>
	 **/
    public function setName($name ) {
		$this->_name = name;
		return this;
    }

    /***
	 * Sets a callback that is called if the route is matched.
	 * The developer can implement any arbitrary conditions here
	 * If the callback returns false the route is treated as not matched
	 *
	 * @param callback callback
	 * @return \Phalcon\Cli\Router\Route
	 **/
    public function beforeMatch($callback ) {
		$this->_befor (eMatch = callback;
		return this;
    }

    /***
	 * Returns the 'before match' callback if any
	 *
	 * @return mixed
	 **/
    public function getBeforeMatch() {
		return $this->_befor (eMatch;
    }

    /***
	 * Returns the route's id
	 **/
    public function getRouteId() {
		return $this->_id;
    }

    /***
	 * Returns the route's pattern
	 **/
    public function getPattern() {
		return $this->_pattern;
    }

    /***
	 * Returns the route's compiled pattern
	 **/
    public function getCompiledPattern() {
		return $this->_compiledPattern;
    }

    /***
	 * Returns the paths
	 **/
    public function getPaths() {
		return $this->_paths;
    }

    /***
	 * Returns the paths using positions as keys and names as values
	 **/
    public function getReversedPaths() {

		$reversed = [];
		foreach ( path, $this->_paths as $position ) {
			$reversed[position] = path;
		}
		return reversed;
    }

    /***
	 * Adds a converter to perform an additional transformation for certain parameter
	 *
	 * @param string name
	 * @param callable converter
	 * @return \Phalcon\Cli\Router\Route
	 **/
    public function convert($name , $converter ) {
		$this->_converters[name] = converter;
		return this;
    }

    /***
	 * Returns the router converter
	 **/
    public function getConverters() {
		return $this->_converters;
    }

    /***
	 * Resets the internal route id generator
	 **/
    public static function reset() {
		$self::_uniqueId = null;
    }

    /***
	 * Set the routing delimiter
	 **/
    public static function delimiter($delimiter  = null ) {
		$self::_delimiterPath = delimiter;
    }

    /***
	 * Get routing delimiter
	 **/
    public static function getDelimiter() {

		$delimiter = self::_delimiterPath;
		if ( !delimiter ) {
			$delimiter = self::DEFAULT_DELIMITER;
		}

		return delimiter;
    }

}