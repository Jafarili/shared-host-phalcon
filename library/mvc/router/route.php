<?php


namespace Phalcon\Mvc\Router;

use Phalcon\Mvc\Router\Exception;


/***
 * Phalcon\Mvc\Router\Route
 *
 * This class represents every route added to the router
 **/

class Route {

    protected $_pattern;

    protected $_compiledPattern;

    protected $_paths;

    protected $_methods;

    protected $_hostname;

    protected $_converters;

    protected $_id;

    protected $_name;

    protected $_beforeMatch;

    protected $_match;

    protected $_group;

    protected static $_uniqueId;

    /***
	 * Phalcon\Mvc\Router\Route constructor
	 **/
    public function __construct($pattern , $paths  = null , $httpMethods  = null ) {

		// Configure the route (extract parameters, paths, etc)
		this->reConfigure(pattern, paths);

		// Update the HTTP method constraints
		$this->_methods = httpMethods;

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
			$idPattern = "/([\\w0-9\\_\\-]+)";

			// Replace the module part
			if ( memstr(pattern, "/:module") ) {
				$pattern = str_replace("/:module", idPattern, pattern);
			}

			// Replace the controller placeholder
			if ( memstr(pattern, "/:controller") ) {
				$pattern = str_replace("/:controller", idPattern, pattern);
			}

			// Replace the namespace placeholder
			if ( memstr(pattern, "/:namespace") ) {
				$pattern = str_replace("/:namespace", idPattern, pattern);
			}

			// Replace the action placeholder
			if ( memstr(pattern, "/:action") ) {
				$pattern = str_replace("/:action", idPattern, pattern);
			}

			// Replace the params placeholder
			if ( memstr(pattern, "/:params") ) {
				$pattern = str_replace("/:params", "(/.*)*", pattern);
			}

			// Replace the int placeholder
			if ( memstr(pattern, "/:int") ) {
				$pattern = str_replace("/:int", "/([0-9]+)", pattern);
			}
		}

		// Check if ( the pattern has parentheses in order to add the regex delimiters
		if ( memstr(pattern, "(") ) {
			return "#^" . pattern . "$#u";
		}

		// Square brackets are also checked
		if ( memstr(pattern, "[") ) {
			return "#^" . pattern . "$#u";
		}

		return pattern;
    }

    /***
	 * Set one or more HTTP methods that constraint the matching of the route
	 *
	 *<code>
	 * $route->via("GET");
	 *
	 * $route->via(
	 *     [
	 *         "GET",
	 *         "POST",
	 *     ]
	 * );
	 *</code>
	 **/
    public function via($httpMethods ) {
		$this->_methods = httpMethods;
		return this;
    }

    /***
	 * Extracts parameters from a string
	 **/
    public function extractNamedParams($pattern ) {
		char ch, prevCh = '\0';
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
											$route .= "(" . regexp . ")";
										} else {
											$route .= regexp;
										}
										$matches[variable] = tmp;
									} else {
										$route .= "([^/]*)",
											matches[item] = tmp;
									}
								} else {
									$route .= "{" . item . "}";
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
				if ( parenthesesCount == 0 && prevCh != '\\' ) {
					if ( ch == '.' || ch == '+' || ch == '|' || ch == '#' ) {
						$route .= '\\';
					}
				}
				$route .= ch,
					prevCh = ch;
			}
		}

		return [route, matches];
    }

    /***
	 * Reconfigure the route adding a new pattern and a set of paths
	 **/
    public function reConfigure($pattern , $paths  = null ) {
			extracted;

		$routePaths = self::getRoutePaths(paths);

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
	 * Returns routePaths
	 **/
    public static function getRoutePaths($paths  = null ) {
			parts, routePaths, realClassName,
			namespaceName;

		if ( paths !== null ) {
			if ( gettype($paths) == "string" ) {

				$moduleName = null,
					controllerName = null,
					actionName = null;

				// Explode the short paths using the :: separator
				$parts = explode("::", paths);

				// Create the array paths dynamically
				switch count(parts) {

					case 3:
						$moduleName = parts[0],
							controllerName = parts[1],
							actionName = parts[2];
						break;

					case 2:
						$controllerName = parts[0],
							actionName = parts[1];
						break;

					case 1:
						$controllerName = parts[0];
						break;
				}

				$routePaths = [];

				// Process module name
				if ( moduleName !== null ) {
					$routePaths["module"] = moduleName;
				}

				// Process controller name
				if ( controllerName !== null ) {

					// Check if ( we need to obtain the namespace
					if ( memstr(controllerName, "\\") ) {

						// Extract the real class name from the namespaced class
						$realClassName = get_class_ns(controllerName);

						// Extract the namespace from the namespaced class
						$namespaceName = get_ns_class(controllerName);

						// Update the namespace
						if ( namespaceName ) {
							$routePaths["namespace"] = namespaceName;
						}
					} else {
						$realClassName = controllerName;
					}

					// Always pass the controller to lowercase
					$routePaths["controller"] = uncamelize(realClassName);
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

		return routePaths;
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
	 *<code>
	 * $router->add(
	 *     "/login",
	 *     [
     *         "module"     => "admin",
     *         "controller" => "session",
     *     ]
     * )->beforeMatch(
     *     function ($uri, $route) {
     *         // Check if the request was made with Ajax
     *         if ($_SERVER["HTTP_X_REQUESTED_WITH"] === "xmlhttprequest") {
     *             return false;
     *         }
     *
     *         return true;
     *     }
     * );
	 *</code>
	 **/
    public function beforeMatch($callback ) {
		$this->_befor (eMatch = callback;
		return this;
    }

    /***
	 * Returns the 'before match' callback if any
	 **/
    public function getBeforeMatch() {
		return $this->_befor (eMatch;
    }

    /***
	 * Allows to set a callback to handle the request directly in the route
	 *
	 *<code>
	 * $router->add(
	 *     "/help",
	 *     []
	 * )->match(
	 *     function () {
	 *         return $this->getResponse()->redirect("https://support.google.com/", true);
	 *     }
	 * );
	 *</code>
	 **/
    public function match($callback ) {
		$this->_match = callback;
		return this;
    }

    /***
	 * Returns the 'match' callback if any
	 **/
    public function getMatch() {
		return $this->_match;
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
	 * Sets a set of HTTP methods that constraint the matching of the route (alias of via)
	 *
	 *<code>
	 * $route->setHttpMethods("GET");
	 * $route->setHttpMethods(["GET", "POST"]);
	 *</code>
	 **/
    public function setHttpMethods($httpMethods ) {
		$this->_methods = httpMethods;
		return this;
    }

    /***
	 * Returns the HTTP methods that constraint matching the route
	 **/
    public function getHttpMethods() {
		return $this->_methods;
    }

    /***
	 * Sets a hostname restriction to the route
	 *
	 *<code>
	 * $route->setHostname("localhost");
	 *</code>
	 **/
    public function setHostname($hostname ) {
		$this->_hostname = hostname;
		return this;
    }

    /***
	 * Returns the hostname restriction if any
	 **/
    public function getHostname() {
		return $this->_hostname;
    }

    /***
	 * Sets the group associated with the route
	 **/
    public function setGroup($group ) {
		$this->_group = group;
		return this;
    }

    /***
	 * Returns the group associated with the route
	 **/
    public function getGroup() {
		return $this->_group;
    }

    /***
	 * Adds a converter to perform an additional transformation for certain parameter
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

}