<?php


namespace Phalcon\Mvc\View;

use Phalcon\Di\Injectable;
use Phalcon\Mvc\View\Exception;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\View\EngineInterface;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;


/***
 * Phalcon\Mvc\View\Simple
 *
 * This component allows to render views without hierarchical levels
 *
 *<code>
 * use Phalcon\Mvc\View\Simple as View;
 *
 * $view = new View();
 *
 * // Render a view
 * echo $view->render(
 *     "templates/my-view",
 *     [
 *         "some" => $param,
 *     ]
 * );
 *
 * // Or with filename with extension
 * echo $view->render(
 *     "templates/my-view.volt",
 *     [
 *         "parameter" => $here,
 *     ]
 * );
 *</code>
 **/

class Simple extends Injectable {

    protected $_options;

    protected $_viewsDir;

    protected $_partialsDir;

    protected $_viewParams;

    /***
	 * @var \Phalcon\Mvc\View\EngineInterface[]|false
	 **/
    protected $_engines;

    /***
	 * @var array|null
	 **/
    protected $_registeredEngines;

    protected $_activeRenderPath;

    protected $_content;

    protected $_cache;

    protected $_cacheOptions;

    /***
	 * Phalcon\Mvc\View\Simple constructor
	 **/
    public function __construct($options ) {
		$this->_options = options;
    }

    /***
	 * Sets views directory. Depending of your platform, always add a trailing slash or backslash
	 **/
    public function setViewsDir($viewsDir ) {
		$this->_viewsDir = viewsDir;
    }

    /***
	 * Gets views directory
	 **/
    public function getViewsDir() {
		return $this->_viewsDir;
    }

    /***
	 * Register templating engines
	 *
	 *<code>
	 * $this->view->registerEngines(
	 *     [
	 *         ".phtml" => "Phalcon\\Mvc\\View\\Engine\\Php",
	 *         ".volt"  => "Phalcon\\Mvc\\View\\Engine\\Volt",
	 *         ".mhtml" => "MyCustomEngine",
	 *     ]
	 * );
	 *</code>
	 **/
    public function registerEngines($engines ) {
		$this->_registeredEngines = engines;
    }

    /***
	 * Loads registered template engines, if none is registered it will use Phalcon\Mvc\View\Engine\Php
	 *
	 * @return array
	 **/
    protected function _loadTemplateEngines() {
			engineService, engineObject;

		/**
		 * If the engines aren't initialized 'engines' is false
		 */
		$engines = $this->_engines;
		if ( engines === false ) {

			$dependencyInjector = $this->_dependencyInjector;

			$engines = [];

			$registeredEngines = $this->_registeredEngines;
			if ( gettype($registeredEngines) != "array" ) {

				/**
				 * We use Phalcon\Mvc\View\Engine\Php as default
				 * Use .phtml as extension for ( the PHP engine
				 */
				$engines[".phtml"] = new PhpEngine(this, dependencyInjector);

			} else {

				if ( gettype($dependencyInjector) != "object" ) {
					throw new Exception("A dependency injector container is required to obtain the application services");
				}

				/**
				 * Arguments for ( instantiated engines
				 */
				$arguments = [this, dependencyInjector];

				foreach ( extension, $registeredEngines as $engineService ) {

					if ( gettype($engineService) == "object" ) {
						/**
						 * Engine can be a closure
						 */
						if ( engineService instanceof \Closure ) {
							$engineObject = call_user_func_array(engineService, arguments);
						} else {
							$engineObject = engineService;
						}
					} else {
						/**
						 * Engine can be a string representing a service in the DI
						 */
						if ( gettype($engineService) == "string" ) {
							$engineObject = dependencyInjector->getShared(engineService, arguments);
						} else {
							throw new Exception("Invalid template engine registration for ( extension: " . extension);
						}
					}

					$engines[extension] = engineObject;
				}
			}

			$this->_engines = engines;
		} else {
			$engines = $this->_engines;
		}

		return engines;
    }

    /***
	 * Tries to render the view with every engine registered in the component
	 *
	 * @param string path
	 * @param array  params
	 **/
    protected final function _internalRender($path , $params ) {

		$eventsManager = $this->_eventsManager;

		if ( gettype($eventsManager) == "object" ) {
			$this->_activeRenderPath = path;
		}

		/**
		 * Call befor (eRender if ( there is an events manager
		 */
		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("view:befor (eRender", this) === false ) ) {
				return null;
			}
		}

		$notExists = true,
			mustClean = true;

		$viewsDirPath =  $this->_viewsDir . path;

		/**
		 * Load the template engines
		 */
		$engines = $this->_loadTemplateEngines();

		/**
		 * Views are rendered in each engine
		 */
		foreach ( extension, $engines as $engine ) {

			if ( file_exists(viewsDirPath . extension) ) {
				$viewEnginePath = viewsDirPath . extension;
			} else {

				/**
				 * if ( passed filename with engine extension
				 */
				if ( extension && substr(viewsDirPath, -strlen(extension)) == extension && file_exists(viewsDirPath) ) {
					$viewEnginePath = viewsDirPath;
				} else {
					$viewEnginePath = "";
				}
			}

			if ( viewEnginePath ) {

				/**
				 * Call befor (eRenderView if ( there is an events manager available
				 */
				if ( gettype($eventsManager) == "object" ) {
					if ( eventsManager->fire("view:befor (eRenderView", this, viewEnginePath) === false ) ) {
						continue;
					}
				}

				engine->render(viewEnginePath, params, mustClean);

				/**
				 * Call afterRenderView if ( there is an events manager available
				 */
				$notExists = false;
				if ( gettype($eventsManager) == "object" ) {
					eventsManager->fire("view:afterRenderView", this);
				}
				break;
			}
		}

		/**
		 * Always throw an exception if ( the view does not exist
		 */
		if ( notExists === true ) {
			throw new Exception("View '" . viewsDirPath . "' was not found in the views directory");
		}

		/**
		 * Call afterRender event
		 */
		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("view:afterRender", this);
		}

    }

    /***
	 * Renders a view
	 *
	 * @param  string path
	 * @param  array  params
	 **/
    public function render($path , $params  = null ) {

		/**
		 * Create/Get a cache
		 */
		$cache = $this->getCache();

		if ( gettype($cache) == "object" ) {

			/**
			 * Check if ( the cache is started, the first time a cache is started we start the cache
			 */
			if ( cache->isStarted() === false ) {

				$key = null, lif (etime = null;

				/**
				 * Check if ( the user has defined a dif (ferent options to the default
				 */
				$cacheOptions = $this->_cacheOptions;
				if ( gettype($cacheOptions) == "array" ) {
				}

				/**
				 * If a cache key is not set we create one using a md5
				 */
				if ( key === null ) {
					$key = md5(path);
				}

				/**
				 * We start the cache using the key set
				 */
				$content = cache->start(key, lif (etime);
				if ( content !== null ) {
					$this->_content = content;
					return content;
				}
			}

		}

		/**
		 * Create a virtual symbol table
		 */
		create_symbol_table();

		ob_start();

		$viewParams = $this->_viewParams;

		/**
		 * Merge parameters
		 */
		if ( gettype($params) == "array" ) {
			if ( gettype($viewParams) == "array" ) {
				$mergedParams = array_merge(viewParams, params);
			} else {
				$mergedParams = params;
			}
		} else {
			$mergedParams = viewParams;
		}

		/**
		 * internalRender is also reused by partials
		 */
		this->_internalRender(path, mergedParams);

		/**
		 * Store the data in output into the cache
		 */
		if ( gettype($cache) == "object" ) {
			if ( cache->isStarted() && cache->isFresh() ) {
				cache->save();
			} else {
				cache->stop();
			}
		}

		ob_end_clean();

		return $this->_content;
    }

    /***
	 * Renders a partial view
	 *
	 * <code>
	 * // Show a partial inside another view
	 * $this->partial("shared/footer");
	 * </code>
	 *
	 * <code>
	 * // Show a partial inside another view with parameters
	 * $this->partial(
	 *     "shared/footer",
	 *     [
	 *         "content" => $html,
	 *     ]
	 * );
	 * </code>
	 **/
    public function partial($partialPath , $params  = null ) {

		/**
		 * Start output buffering
		 */
		ob_start();

		/**
		 * If the developer pass an array of variables we create a new virtual symbol table
		 */
		if ( gettype($params) == "array" ) {

			$viewParams = $this->_viewParams;

			/**
			 * Merge or assign the new params as parameters
			 */
			if ( gettype($viewParams) == "array" ) {
				$mergedParams = array_merge(viewParams, params);
			} else {
				$mergedParams = params;
			}

			/**
			 * Create a virtual symbol table
			 */
			create_symbol_table();

		} else {
			$mergedParams = params;
		}

		/**
		 * Call engine render, this $every as $checks registered engine foreach ( the partial
		 */
		this->_internalRender(partialPath, mergedParams);

		/**
		 * Now we need to restore the original view parameters
		 */
		if ( gettype($params) == "array" ) {
			/**
			 * Restore the original view params
			 */
			$this->_viewParams = viewParams;
		}

		ob_end_clean();

		/**
		 * Content is output to the parent view
		 */
		echo $this->_content;
    }

    /***
	 * Sets the cache options
	 **/
    public function setCacheOptions($options ) {
		$this->_cacheOptions = options;
		return this;
    }

    /***
	 * Returns the cache options
	 *
	 * @return array
	 **/
    public function getCacheOptions() {
		return $this->_cacheOptions;
    }

    /***
	 * Create a Phalcon\Cache based on the internal cache options
	 **/
    protected function _createCache() {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the view cache services");
		}

		$cacheService = "viewCache";

		$cacheOptions = $this->_cacheOptions;
		if ( gettype($cacheOptions) == "array" ) {
			if ( isset cacheOptions["service"] ) {
			}
		}

		/**
		 * The injected service must be an object
		 */
		$viewCache = <BackendInterface> dependencyInjector->getShared(cacheService);
		if ( gettype($viewCache) != "object" ) {
			throw new Exception("The injected caching service is invalid");
		}

		return viewCache;
    }

    /***
	 * Returns the cache instance used to cache
	 **/
    public function getCache() {
		if ( $this->_cache && gettype($this->_cache) != "object" ) {
			$this->_cache = $this->_createCache();
		}

		return $this->_cache;
    }

    /***
	 * Cache the actual view render to certain level
	 *
	 *<code>
	 * $this->view->cache(
	 *     [
	 *         "key"      => "my-key",
	 *         "lifetime" => 86400,
	 *     ]
	 * );
	 *</code>
	 **/
    public function cache($options  = true ) {
		if ( gettype($options) == "array" ) {
			$this->_cache = true,
				this->_cacheOptions = options;
		} else {
			if ( options ) {
				$this->_cache = true;
			} else {
				$this->_cache = false;
			}
		}
		return this;
    }

    /***
	 * Adds parameters to views (alias of setVar)
	 *
	 *<code>
	 * $this->view->setParamToView("products", $products);
	 *</code>
	 **/
    public function setParamToView($key , $value ) {
		$this->_viewParams[key] = value;
		return this;
    }

    /***
	 * Set all the render params
	 *
	 *<code>
	 * $this->view->setVars(
	 *     [
	 *         "products" => $products,
	 *     ]
	 * );
	 *</code>
	 **/
    public function setVars($params , $merge  = true ) {
		if ( merge && gettype($this->_viewParams) == "array" ) {
			$this->_viewParams = array_merge(this->_viewParams, params);
		} else {
			$this->_viewParams = params;
		}

		return this;
    }

    /***
	 * Set a single view parameter
	 *
	 *<code>
	 * $this->view->setVar("products", $products);
	 *</code>
	 **/
    public function setVar($key , $value ) {
		$this->_viewParams[key] = value;
		return this;
    }

    /***
	 * Returns a parameter previously set in the view
	 **/
    public function getVar($key ) {
		var	value;
		if ( fetch value, $this->_viewParams[key] ) {
			return value;
		}
		return null;
    }

    /***
	 * Returns parameters to views
	 *
	 * @return array
	 **/
    public function getParamsToView() {
		return $this->_viewParams;
    }

    /***
	 * Externally sets the view content
	 *
	 *<code>
	 * $this->view->setContent("<h1>hello</h1>");
	 *</code>
	 **/
    public function setContent($content ) {
		$this->_content = content;
		return this;
    }

    /***
	 * Returns cached output from another view stage
	 **/
    public function getContent() {
		return $this->_content;
    }

    /***
	 * Returns the path of the view that is currently rendered
	 *
	 * @return string
	 **/
    public function getActiveRenderPath() {
		return $this->_activeRenderPath;
    }

    /***
	 * Magic method to pass variables to the views
	 *
	 *<code>
	 * $this->view->products = $products;
	 *</code>
	 **/
    public function __set($key , $value ) {
		$this->_viewParams[key] = value;
    }

    /***
	 * Magic method to retrieve a variable passed to the view
	 *
	 *<code>
	 * echo $this->view->products;
	 *</code>
	 **/
    public function __get($key ) {
		if ( fetch value, $this->_viewParams[key] ) {
			return value;
		}

		return null;
    }

}