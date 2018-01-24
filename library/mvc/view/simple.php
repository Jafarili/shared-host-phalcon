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

    }

    /***
	 * Sets views directory. Depending of your platform, always add a trailing slash or backslash
	 **/
    public function setViewsDir($viewsDir ) {

    }

    /***
	 * Gets views directory
	 **/
    public function getViewsDir() {

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

    }

    /***
	 * Loads registered template engines, if none is registered it will use Phalcon\Mvc\View\Engine\Php
	 *
	 * @return array
	 **/
    protected function _loadTemplateEngines() {

    }

    /***
	 * Tries to render the view with every engine registered in the component
	 *
	 * @param string path
	 * @param array  params
	 **/
    protected final function _internalRender($path , $params ) {

    }

    /***
	 * Renders a view
	 *
	 * @param  string path
	 * @param  array  params
	 **/
    public function render($path , $params  = null ) {

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

    }

    /***
	 * Sets the cache options
	 **/
    public function setCacheOptions($options ) {

    }

    /***
	 * Returns the cache options
	 *
	 * @return array
	 **/
    public function getCacheOptions() {

    }

    /***
	 * Create a Phalcon\Cache based on the internal cache options
	 **/
    protected function _createCache() {

    }

    /***
	 * Returns the cache instance used to cache
	 **/
    public function getCache() {

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

    }

    /***
	 * Adds parameters to views (alias of setVar)
	 *
	 *<code>
	 * $this->view->setParamToView("products", $products);
	 *</code>
	 **/
    public function setParamToView($key , $value ) {

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

    }

    /***
	 * Set a single view parameter
	 *
	 *<code>
	 * $this->view->setVar("products", $products);
	 *</code>
	 **/
    public function setVar($key , $value ) {

    }

    /***
	 * Returns a parameter previously set in the view
	 **/
    public function getVar($key ) {

    }

    /***
	 * Returns parameters to views
	 *
	 * @return array
	 **/
    public function getParamsToView() {

    }

    /***
	 * Externally sets the view content
	 *
	 *<code>
	 * $this->view->setContent("<h1>hello</h1>");
	 *</code>
	 **/
    public function setContent($content ) {

    }

    /***
	 * Returns cached output from another view stage
	 **/
    public function getContent() {

    }

    /***
	 * Returns the path of the view that is currently rendered
	 *
	 * @return string
	 **/
    public function getActiveRenderPath() {

    }

    /***
	 * Magic method to pass variables to the views
	 *
	 *<code>
	 * $this->view->products = $products;
	 *</code>
	 **/
    public function __set($key , $value ) {

    }

    /***
	 * Magic method to retrieve a variable passed to the view
	 *
	 *<code>
	 * echo $this->view->products;
	 *</code>
	 **/
    public function __get($key ) {

    }

}