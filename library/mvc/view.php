<?php


namespace Phalcon\Mvc;

use Phalcon\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\View\Exception;
use Phalcon\Mvc\ViewInterface;
use Phalcon\Cache\BackendInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;


/***
 * Phalcon\Mvc\View
 *
 * Phalcon\Mvc\View is a class for working with the "view" portion of the model-view-controller pattern.
 * That is, it exists to help keep the view script separate from the model and controller scripts.
 * It provides a system of helpers, output filters, and variable escaping.
 *
 * <code>
 * use Phalcon\Mvc\View;
 *
 * $view = new View();
 *
 * // Setting views directory
 * $view->setViewsDir("app/views/");
 *
 * $view->start();
 *
 * // Shows recent posts view (app/views/posts/recent.phtml)
 * $view->render("posts", "recent");
 * $view->finish();
 *
 * // Printing views output
 * echo $view->getContent();
 * </code>
 **/

class View extends Injectable {

    /***
	 * Render Level: To the main layout
	 *
	 **/
    const LEVEL_MAIN_LAYOUT= 5;

    /***
	 * Render Level: Render to the templates "after"
	 *
	 **/
    const LEVEL_AFTER_TEMPLATE= 4;

    /***
	 * Render Level: To the controller layout
	 *
	 **/
    const LEVEL_LAYOUT= 3;

    /***
	 * Render Level: To the templates "before"
	 *
	 **/
    const LEVEL_BEFORE_TEMPLATE= 2;

    /***
	 * Render Level: To the action view
	 **/
    const LEVEL_ACTION_VIEW= 1;

    /***
	 * Render Level: No render any view
	 *
	 **/
    const LEVEL_NO_RENDER= 0;

    /***
	 * Cache Mode
	 **/
    const CACHE_MODE_NONE= 0;

    const CACHE_MODE_INVERSE= 1;

    protected $_options;

    protected $_basePath;

    protected $_content;

    protected $_renderLevel;

    protected $_currentRenderLevel;

    protected $_disabledLevels;

    protected $_viewParams;

    protected $_layout;

    protected $_layoutsDir;

    protected $_partialsDir;

    protected $_viewsDirs;

    protected $_templatesBefore;

    protected $_templatesAfter;

    protected $_engines;

    /***
	 * @var array
	 **/
    protected $_registeredEngines;

    protected $_mainView;

    protected $_controllerName;

    protected $_actionName;

    protected $_params;

    protected $_pickView;

    protected $_cache;

    protected $_cacheLevel;

    protected $_activeRenderPaths;

    protected $_disabled;

    /***
	 * Phalcon\Mvc\View constructor
	 **/
    public function __construct($options ) {
		$this->_options = options;
    }

    /***
	 * Checks if a path is absolute or not
	 **/
    protected final function _isAbsolutePath($path ) {
		if ( PHP_OS == "WINNT" ) {
			return strlen(path) >= 3 && path[1] == ':' && path[2] == '\\';
		}

		return strlen(path) >= 1 && path[0] == '/';
    }

    /***
	 * Sets the views directory. Depending of your platform,
	 * always add a trailing slash or backslash
	 **/
    public function setViewsDir($viewsDir ) {

		if ( gettype($viewsDir) != "string" && gettype($viewsDir) != "array" ) {
			throw new Exception("Views directory must be a string or an array");
		}

		$directorySeparator = DIRECTORY_SEPARATOR;
		if ( gettype($viewsDir) == "string" ) {

			if ( substr(viewsDir, -1) != directorySeparator ) {
				$viewsDir = viewsDir . directorySeparator;
			}

			$this->_viewsDirs = viewsDir;
		} else {

			$newViewsDir = [];
			foreach ( position, $viewsDir as $directory ) {

				if ( gettype($directory) != "string" ) {
					throw new Exception("Views directory item must be a string");
				}

				if ( substr(directory, -1) != directorySeparator ) {
					$newViewsDir[position] = directory . directorySeparator;
				} else {
					$newViewsDir[position] = directory;
				}
			}

			$this->_viewsDirs = newViewsDir;
		}

		return this;
    }

    /***
	 * Gets views directory
	 **/
    public function getViewsDir() {
		return $this->_viewsDirs;
    }

    /***
	 * Sets the layouts sub-directory. Must be a directory under the views directory.
	 * Depending of your platform, always add a trailing slash or backslash
	 *
	 *<code>
	 * $view->setLayoutsDir("../common/layouts/");
	 *</code>
	 **/
    public function setLayoutsDir($layoutsDir ) {
		$this->_layoutsDir = layoutsDir;
		return this;
    }

    /***
	 * Gets the current layouts sub-directory
	 **/
    public function getLayoutsDir() {
		return $this->_layoutsDir;
    }

    /***
	 * Sets a partials sub-directory. Must be a directory under the views directory.
	 * Depending of your platform, always add a trailing slash or backslash
	 *
	 *<code>
	 * $view->setPartialsDir("../common/partials/");
	 *</code>
	 **/
    public function setPartialsDir($partialsDir ) {
		$this->_partialsDir = partialsDir;
		return this;
    }

    /***
	 * Gets the current partials sub-directory
	 **/
    public function getPartialsDir() {
		return $this->_partialsDir;
    }

    /***
	 * Sets base path. Depending of your platform, always add a trailing slash or backslash
	 *
	 * <code>
	 * 	$view->setBasePath(__DIR__ . "/");
	 * </code>
	 **/
    public function setBasePath($basePath ) {
		$this->_basePath = basePath;
		return this;
    }

    /***
	 * Gets base path
	 **/
    public function getBasePath() {
		return $this->_basePath;
    }

    /***
	 * Sets the render level for the view
	 *
	 * <code>
	 * // Render the view related to the controller only
	 * $this->view->setRenderLevel(
	 *     View::LEVEL_LAYOUT
	 * );
	 * </code>
	 **/
    public function setRenderLevel($level ) {
		$this->_renderLevel = level;
		return this;
    }

    /***
	 * Disables a specific level of rendering
	 *
	 *<code>
	 * // Render all levels except ACTION level
	 * $this->view->disableLevel(
	 *     View::LEVEL_ACTION_VIEW
	 * );
	 *</code>
	 **/
    public function disableLevel($level ) {
		if ( gettype($level) == "array" ) {
			$this->_disabledLevels = level;
		} else {
			$this->_disabledLevels[level] = true;
		}
		return this;
    }

    /***
	 * Sets default view name. Must be a file without extension in the views directory
	 *
	 * <code>
	 * // Renders as main view views-dir/base.phtml
	 * $this->view->setMainView("base");
	 * </code>
	 **/
    public function setMainView($viewPath ) {
		$this->_mainView = viewPath;
		return this;
    }

    /***
	 * Returns the name of the main view
	 **/
    public function getMainView() {
		return $this->_mainView;
    }

    /***
	 * Change the layout to be used instead of using the name of the latest controller name
	 *
	 * <code>
	 * $this->view->setLayout("main");
	 * </code>
	 **/
    public function setLayout($layout ) {
		$this->_layout = layout;
		return this;
    }

    /***
	 * Returns the name of the main view
	 **/
    public function getLayout() {
		return $this->_layout;
    }

    /***
	 * Sets a template before the controller layout
	 **/
    public function setTemplateBefore($templateBefore ) {
		if ( gettype($templateBefor (e) != "array" ) ) {
			$this->_templatesBefor (e = [templateBefor (e];
		} else {
			$this->_templatesBefor (e = templateBefor (e;
		}
		return this;
    }

    /***
	 * Resets any "template before" layouts
	 **/
    public function cleanTemplateBefore() {
		$this->_templatesBefor (e = [];
		return this;
    }

    /***
	 * Sets a "template after" controller layout
	 **/
    public function setTemplateAfter($templateAfter ) {
		if ( gettype($templateAfter) != "array" ) {
			$this->_templatesAfter = [templateAfter];
		} else {
			$this->_templatesAfter = templateAfter;
		}
		return this;
    }

    /***
	 * Resets any template before layouts
	 **/
    public function cleanTemplateAfter() {
		$this->_templatesAfter = [];
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
		if ( merge ) {
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

		if ( !fetch value, $this->_viewParams[key] ) {
			return null;
		}

		return value;
    }

    /***
	 * Returns parameters to views
	 **/
    public function getParamsToView() {
		return $this->_viewParams;
    }

    /***
	 * Gets the name of the controller rendered
	 **/
    public function getControllerName() {
		return $this->_controllerName;
    }

    /***
	 * Gets the name of the action rendered
	 **/
    public function getActionName() {
		return $this->_actionName;
    }

    /***
	 * Gets extra parameters of the action rendered
	 *
	 * @deprecated Will be removed in 4.0.0
	 **/
    public function getParams() {
		return $this->_params;
    }

    /***
	 * Starts rendering process enabling the output buffering
	 **/
    public function start() {
		ob_start();
		$this->_content = null;
		return this;
    }

    /***
	 * Loads registered template engines, if none is registered it will use Phalcon\Mvc\View\Engine\Php
	 **/
    protected function _loadTemplateEngines() {
			engineService, extension;

		$engines = $this->_engines;

		/**
		 * If the engines aren't initialized 'engines' is false
		 */
		if ( engines === false ) {

			$dependencyInjector = <DiInterface> $this->_dependencyInjector;

			$engines = [];
			$registeredEngines = $this->_registeredEngines;
			if ( gettype($registeredEngines) != "array" ) {

				/**
				 * We use Phalcon\Mvc\View\Engine\Php as default
				 */
				$engines[".phtml"] = new PhpEngine(this, dependencyInjector);
			} else {

				if ( gettype($dependencyInjector) != "object" ) {
					throw new Exception("A dependency injector container is required to obtain the application services");
				}

				$arguments = [this, dependencyInjector];
				foreach ( extension, $registeredEngines as $engineService ) {

					if ( gettype($engineService) == "object" ) {

						/**
						 * Engine can be a closure
						 */
						if ( engineService instanceof \Closure ) {
							$engines[extension] = call_user_func_array(engineService, arguments);
						} else {
							$engines[extension] = engineService;
						}

					} else {

						/**
						 * Engine can be a string representing a service in the DI
						 */
						if ( gettype($engineService) != "string" ) {
							throw new Exception("Invalid template engine registration for ( extension: " . extension);
						}

						$engines[extension] = dependencyInjector->getShared(engineService, arguments);
					}
				}
			}

			$this->_engines = engines;
		}

		return engines;
    }

    /***
	 * Checks whether view exists on registered extensions and render it
	 *
	 * @param array engines
	 * @param string viewPath
	 * @param boolean silence
	 * @param boolean mustClean
	 * @param \Phalcon\Cache\BackendInterface $cache
	 **/
    protected function _engineRender($engines , $viewPath , $silence , $mustClean , $cache  = null ) {
		boolean notExists;
		int renderLevel, cacheLevel;
			viewOptions, cacheOptions, cachedView, viewParams, eventsManager,
			extension, engine, viewEnginePath, viewEnginePaths;

		$notExists = true,
			basePath = $this->_basePath,
			viewParams = $this->_viewParams,
			eventsManager = <ManagerInterface> $this->_eventsManager,
			viewEnginePaths = [];

		foreach ( $this->getViewsDirs() as $viewsDir ) {

			if ( !this->_isAbsolutePath(viewPath) ) {
				$viewsDirPath = basePath . viewsDir . viewPath;
			} else {
				$viewsDirPath = viewPath;
			}

			if ( gettype($cache) == "object" ) {

				$renderLevel = (int) $this->_renderLevel,
					cacheLevel = (int) $this->_cacheLevel;

				if ( renderLevel >= cacheLevel ) {

					/**
					 * Check if ( the cache is started, the first time a cache is started we start the
					 * cache
					 */
					if ( !cache->isStarted() ) {

						$key = null,
							lif (etime = null;

						$viewOptions = $this->_options;

						/**
						 * Check if ( the user has defined a dif (ferent options to the default
						 */
						if ( fetch cacheOptions, viewOptions["cache"] ) {
							if ( gettype($cacheOptions) == "array" ) {
							}
						}

						/**
						 * If a cache key is not set we create one using a md5
						 */
						if ( key === null ) {
							$key = md5(viewPath);
						}

						/**
						 * We start the cache using the key set
						 */
						$cachedView = cache->start(key, lif (etime);
						if ( cachedView !== null ) {
							$this->_content = cachedView;
							return null;
						}
					}

					/**
					 * This method only returns true if ( the cache has not expired
					 */
					if ( !cache->isFresh() ) {
						return null;
					}
				}
			}

			/**
			 * Views are rendered in each engine
			 */
			foreach ( extension, $engines as $engine ) {

				$viewEnginePath = viewsDirPath . extension;
				if ( file_exists(viewEnginePath) ) {

					/**
					 * Call befor (eRenderView if ( there is an events manager available
					 */
					if ( gettype($eventsManager) == "object" ) {
						$this->_activeRenderPaths = [viewEnginePath];
						if ( eventsManager->fire("view:befor (eRenderView", this, viewEnginePath) === false ) ) {
							continue;
						}
					}

					engine->render(viewEnginePath, viewParams, mustClean);

					/**
					 * Call afterRenderView if ( there is an events manager available
					 */
					$notExists = false;
					if ( gettype($eventsManager) == "object" ) {
						eventsManager->fire("view:afterRenderView", this);
					}
					break;
				}

				$viewEnginePaths[] = viewEnginePath;
			}
		}

		if ( notExists === true ) {
			/**
			 * Notif (y about not found views
			 */
			if ( gettype($eventsManager) == "object" ) {
				$this->_activeRenderPaths = viewEnginePaths;
				eventsManager->fire("view:notFoundView", this, viewEnginePath);
			}

			if ( !silence ) {
				throw new Exception("View '" . viewPath . "' was not found in any of the views directory");
			}
		}
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
		return this;
    }

    /***
	 * Checks whether view exists
	 **/
    public function exists($view ) {

		$basePath = $this->_basePath,
			engines = $this->_registeredEngines;

		if ( gettype($engines) != "array" ) {
			$engines = [".phtml": "Phalcon\\Mvc\\View\\Engine\\Php"],
				this->_registeredEngines = engines;
		}

		foreach ( $this->getViewsDirs() as $viewsDir ) {
			foreach ( extension, $engines as $_ ) {
				if ( file_exists(basePath . viewsDir . view . extension) ) {
					return true;
				}
			}
		}

		return false;
    }

    /***
	 * Executes render process from dispatching data
	 *
	 *<code>
	 * // Shows recent posts view (app/views/posts/recent.phtml)
	 * $view->start()->render("posts", "recent")->finish();
	 *</code>
	 *
	 * @param string controllerName
	 * @param string actionName
	 * @param array params
	 **/
    public function render($controllerName , $actionName , $params  = null ) {
		boolean silence, mustClean;
		int renderLevel;
			engines, renderView, pickViewAction, eventsManager,
			disabledLevels, templatesBefor (e, templatesAfter,
			templateBefor (e, templateAfter, cache;

		$this->_currentRenderLevel = 0;

		/**
		 * If the view is disabled we simply update the buffer from any output produced in the controller
		 */
		if ( $this->_disabled !== false ) {
			$this->_content = ob_get_contents();
			return false;
		}

		$this->_controllerName = controllerName,
			this->_actionName = actionName;

		if ( gettype($params) == "array" ) {
			this->setVars(params);
		}

		/**
		 * Check if ( there is a layouts directory set
		 */
		$layoutsDir = $this->_layoutsDir;
		if ( !layoutsDir ) {
			$layoutsDir = "layouts/";
		}

		/**
		 * Check if ( the user has defined a custom layout
		 */
		$layout = $this->_layout;
		if ( layout ) {
			$layoutName = layout;
		} else {
			$layoutName = controllerName;
		}

		/**
		 * Load the template engines
		 */
		$engines = $this->_loadTemplateEngines();

		/**
		 * Check if ( the user has picked a view dif (ferent than the automatic
		 */
		$pickView = $this->_pickView;

		if ( pickView === null ) {
			$renderView = controllerName . "/" . actionName;
		} else {

			/**
			 * The 'picked' view is an array, where the first element is controller and the second the action
			 */
			$renderView = pickView[0];
			if ( layoutName === null ) {
				if ( fetch pickViewAction, pickView[1] ) {
					$layoutName = pickViewAction;
				}
			}
		}

		/**
		 * Start the cache if ( there is a cache level enabled
		 */
		if ( $this->_cacheLevel ) {
			$cache = $this->getCache();
		} else {
			$cache = null;
		}

		$eventsManager = <ManagerInterface> $this->_eventsManager;

		/**
		 * Create a virtual symbol table.
		 * Variables are shared across symbol tables in PHP5
		 */
		if ( PHP_MAJOR_VERSION == 5 ) {
			create_symbol_table();
		}

		/**
		 * Call befor (eRender if ( there is an events manager
		 */
		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("view:befor (eRender", this) === false ) ) {
				return false;
			}
		}

		/**
		 * Get the current content in the buffer maybe some output from the controller?
		 */
		$this->_content = ob_get_contents();

		$mustClean = true,
			silence = true;

		/**
		 * Disabled levels allow to avoid an specif (ic level of rendering
		 */
		$disabledLevels = $this->_disabledLevels;

		/**
		 * Render level will tell use when to stop
		 */
		$renderLevel = (int) $this->_renderLevel;
		if ( renderLevel ) {

			/**
			 * Inserts view related to action
			 */
			if ( renderLevel >= self::LEVEL_ACTION_VIEW ) {
				if ( !isset disabledLevels[self::LEVEL_ACTION_VIEW] ) {
					$this->_currentRenderLevel = self::LEVEL_ACTION_VIEW;
					this->_engineRender(engines, renderView, silence, mustClean, cache);
				}
			}

			/**
			 * Inserts templates befor (e layout
			 */
			if ( renderLevel >= self::LEVEL_BEFORE_TEMPLATE  ) {
				if ( !isset disabledLevels[self::LEVEL_BEFORE_TEMPLATE] ) {
					$this->_currentRenderLevel = self::LEVEL_BEFORE_TEMPLATE;

					$templatesBefor (e = $this->_templatesBefor (e;

					$silence = false;
					foreach ( $templatesBeforeach (e as $templateBeforeach (e ) {
						this->_engineRender(engines, layoutsDir . templateBefor (e, silence, mustClean, cache);
					}
					$silence = true;
				}
			}

			/**
			 * Inserts controller layout
			 */
			if ( renderLevel >= self::LEVEL_LAYOUT ) {
				if ( !isset disabledLevels[self::LEVEL_LAYOUT] ) {
					$this->_currentRenderLevel = self::LEVEL_LAYOUT;
					this->_engineRender(engines, layoutsDir . layoutName, silence, mustClean, cache);
				}
			}

			/**
			 * Inserts templates after layout
			 */
			if ( renderLevel >= self::LEVEL_AFTER_TEMPLATE ) {
				if ( !isset disabledLevels[self::LEVEL_AFTER_TEMPLATE] ) {
					$this->_currentRenderLevel = self::LEVEL_AFTER_TEMPLATE;

					$templatesAfter = $this->_templatesAfter;

					$silence = false;
					foreach ( $templatesAfter as $templateAfter ) {
						this->_engineRender(engines, layoutsDir . templateAfter, silence, mustClean, cache);
					}
					$silence = true;
				}
			}

			/**
			 * Inserts main view
			 */
			if ( renderLevel >= self::LEVEL_MAIN_LAYOUT ) {
				if ( !isset disabledLevels[self::LEVEL_MAIN_LAYOUT] ) {
					$this->_currentRenderLevel = self::LEVEL_MAIN_LAYOUT;
					this->_engineRender(engines, $this->_mainView, silence, mustClean, cache);
				}
			}

			$this->_currentRenderLevel = 0;

			/**
			 * Store the data in the cache
			 */
			if ( gettype($cache) == "object" ) {
				if ( cache->isStarted() && cache->isFresh() ) {
					cache->save();
				} else {
					cache->stop();
				}
			}
		}

		/**
		 * Call afterRender event
		 */
		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("view:afterRender", this);
		}

		return this;
    }

    /***
	 * Choose a different view to render instead of last-controller/last-action
	 *
	 * <code>
	 * use Phalcon\Mvc\Controller;
	 *
	 * class ProductsController extends Controller
	 * {
	 *    public function saveAction()
	 *    {
	 *         // Do some save stuff...
	 *
	 *         // Then show the list view
	 *         $this->view->pick("products/list");
	 *    }
	 * }
	 * </code>
	 **/
    public function pick($renderView ) {

		if ( gettype($renderView) == "array" ) {
			$pickView = renderView;
		} else {

			$layout = null;
			if ( memstr(renderView, "/") ) {
				$parts = explode("/", renderView), layout = parts[0];
			}

			$pickView = [renderView];
			if ( layout !== null ) {
				$pickView[] = layout;
			}
		}

		$this->_pickView = pickView;
		return this;
    }

    /***
	 * Renders a partial view
	 *
	 * <code>
	 * // Retrieve the contents of a partial
	 * echo $this->getPartial("shared/footer");
	 * </code>
	 *
	 * <code>
	 * // Retrieve the contents of a partial with arguments
	 * echo $this->getPartial(
	 *     "shared/footer",
	 *     [
	 *         "content" => $html,
	 *     ]
	 * );
	 * </code>
	 **/
    public function getPartial($partialPath , $params  = null ) {
		ob_start();
		this->partial(partialPath, params);
		return ob_get_clean();
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
		 * If the developer pass an array of variables we create a new virtual symbol table
		 */
		if ( gettype($params) == "array" ) {

			/**
			 * Merge the new params as parameters
			 */
			$viewParams = $this->_viewParams;
			$this->_viewParams = array_merge(viewParams, params);

			/**
			 * Create a virtual symbol table
			 */
			create_symbol_table();
		}

		/**
		 * Partials are looked up under the partials directory
		 * We need to check if ( the engines are loaded first, this method could be called outside of 'render'
		 * Call engine render, this $every as $checks registered engine foreach ( the partial
		 */
		this->_engineRender(this->_loadTemplateEngines(), $this->_partialsDir . partialPath, false, false);

		/**
		 * Now we need to restore the original view parameters
		 */
		if ( gettype($params) == "array" ) {
			/**
			 * Restore the original view params
			 */
			$this->_viewParams = viewParams;
		}
    }

    /***
	 * Perform the automatic rendering returning the output as a string
	 *
	 * <code>
	 * $template = $this->view->getRender(
	 *     "products",
	 *     "show",
	 *     [
	 *         "products" => $products,
	 *     ]
	 * );
	 * </code>
	 *
	 * @param string controllerName
	 * @param string actionName
	 * @param array params
	 * @param mixed configCallback
	 * @return string
	 **/
    public function getRender($controllerName , $actionName , $params  = null , $configCallback  = null ) {

		/**
		 * We must to clone the current view to keep the old state
		 */
		$view = clone this;

		/**
		 * The component must be reset to its defaults
		 */
		view->reset();

		/**
		 * Set the render variables
		 */
		if ( gettype($params) == "array" ) {
			view->setVars(params);
		}

		/**
		 * Perfor (m extra configurations over the cloned object
		 */
		if ( gettype($configCallback) == "object" ) {
			call_user_func_array(configCallback, [view]);
		}

		/**
		 * Start the output buffering
		 */
		view->start();

		/**
		 * Perfor (m the render passing only the controller and action
		 */
		view->render(controllerName, actionName);

		/**
		 * Stop the output buffering
		 */
		ob_end_clean();

		/**
		 * Get the processed content
		 */
		return view->getContent();
    }

    /***
	 * Finishes the render process by stopping the output buffering
	 **/
    public function finish() {
		ob_end_clean();
		return this;
    }

    /***
	 * Create a Phalcon\Cache based on the internal cache options
	 **/
    protected function _createCache() {
			viewOptions, cacheOptions;

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the view cache services");
		}

		$cacheService = "viewCache";

		$viewOptions = $this->_options;

		if ( fetch cacheOptions, viewOptions["cache"] ) {
			if ( isset cacheOptions["service"] ) {
				$cacheService = cacheOptions["service"];
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
	 * Check if the component is currently caching the output content
	 **/
    public function isCaching() {
		return $this->_cacheLevel > 0;
    }

    /***
	 * Returns the cache instance used to cache
	 **/
    public function getCache() {
		if ( !this->_cache || gettype($this->_cache) != "object" ) {
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

			$viewOptions = $this->_options;
			if ( gettype($viewOptions) != "array" ) {
				$viewOptions = [];
			}

			/**
			 * Get the default cache options
			 */
			if ( !fetch cacheOptions, viewOptions["cache"] ) {
				$cacheOptions = [];
			}

			foreach ( key, $options as $value ) {
				$cacheOptions[key] = value;
			}

			/**
			 * Check if ( the user has defined a default cache level or use self::LEVEL_MAIN_LAYOUT as default
			 */
			if ( fetch cacheLevel, cacheOptions["level"] ) {
				$this->_cacheLevel = cacheLevel;
			} else {
				$this->_cacheLevel = self::LEVEL_MAIN_LAYOUT;
			}

			$viewOptions["cache"] = cacheOptions;
			$this->_options = viewOptions;
		} else {

			/**
			 * If 'options' isn't an array we enable the cache with default options
			 */
			if ( options ) {
				$this->_cacheLevel = self::LEVEL_MAIN_LAYOUT;
			} else {
				$this->_cacheLevel = self::LEVEL_NO_RENDER;
			}
		}

		return this;
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
	 * Returns the path (or paths) of the views that are currently rendered
	 **/
    public function getActiveRenderPath() {
		int viewsDirsCount;

		$viewsDirsCount = count(this->getViewsDirs()),
			activeRenderPath = $this->_activeRenderPaths;

		if ( viewsDirsCount === 1 ) {
			if ( gettype($activeRenderPath) == "array" ) {
				if ( count(activeRenderPath) ) {
					$activeRenderPath = activeRenderPath[0];
				}
			}
		}

		if ( gettype($activeRenderPath) == "null" ) {
			$activeRenderPath = "";
		}

		return activeRenderPath;
    }

    /***
	 * Disables the auto-rendering process
	 **/
    public function disable() {
		$this->_disabled = true;
		return this;
    }

    /***
	 * Enables the auto-rendering process
	 **/
    public function enable() {
		$this->_disabled = false;
		return this;
    }

    /***
	 * Resets the view component to its factory default values
	 **/
    public function reset() {
		$this->_disabled = false,
			this->_engines = false,
			this->_cache = null,
			this->_renderLevel = self::LEVEL_MAIN_LAYOUT,
			this->_cacheLevel = self::LEVEL_NO_RENDER,
			this->_content = null,
			this->_templatesBefor (e = [],
			this->_templatesAfter = [];
		return this;
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

    /***
	 * Whether automatic rendering is enabled
	 **/
    public function isDisabled() {
		return $this->_disabled;
    }

    /***
	 * Magic method to retrieve if a variable is set in the view
	 *
	 *<code>
	 * echo isset($this->view->products);
	 *</code>
	 **/
    public function __isset($key ) {
		return isset $this->_viewParams[key];
    }

    /***
	 * Gets views directories
	 **/
    protected function getViewsDirs() {
		if ( gettype($this->_viewsDirs) === "string" ) {
			return [this->_viewsDirs];
		}

		return $this->_viewsDirs;
    }

}