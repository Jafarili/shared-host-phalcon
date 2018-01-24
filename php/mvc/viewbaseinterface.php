<?php


namespace Phalcon\Mvc;



/***
 * Phalcon\Mvc\ViewInterface
 *
 * Interface for Phalcon\Mvc\View and Phalcon\Mvc\View\Simple
 **/

interface ViewBaseInterface {

    /***
	 * Sets views directory. Depending of your platform, always add a trailing slash or backslash
	 **/
    public function setViewsDir($viewsDir ); 

    /***
	 * Gets views directory
	 **/
    public function getViewsDir(); 

    /***
	 * Adds parameters to views (alias of setVar)
	 **/
    public function setParamToView($key , $value ); 

    /***
	 * Adds parameters to views
	 *
	 * @param string key
	 * @param mixed value
	 **/
    public function setVar($key , $value ); 

    /***
	 * Returns parameters to views
	 **/
    public function getParamsToView(); 

    /***
	 * Returns the cache instance used to cache
	 **/
    public function getCache(); 

    /***
	 * Cache the actual view render to certain level
	 **/
    public function cache($options  = true ); 

    /***
	 * Externally sets the view content
	 **/
    public function setContent($content ); 

    /***
	 * Returns cached output from another view stage
	 **/
    public function getContent(); 

    /***
	 * Renders a partial view
	 **/
    public function partial($partialPath , $params  = null ); 

}