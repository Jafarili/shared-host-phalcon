<?php


namespace Phalcon\Mvc\View;

use Phalcon\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\ViewBaseInterface;


/***
 * Phalcon\Mvc\View\Engine
 *
 * All the template engine adapters must inherit this class. This provides
 * basic interfacing between the engine and the Phalcon\Mvc\View component.
 **/

abstract class Engine extends Injectable {

    protected $_view;

    /***
	 * Phalcon\Mvc\View\Engine constructor
	 **/
    public function __construct($view , $dependencyInjector  = null ) {

    }

    /***
	 * Returns cached output on another view stage
	 **/
    public function getContent() {

    }

    /***
	 * Renders a partial inside another view
	 *
	 * @param string partialPath
	 * @param array params
	 * @return string
	 **/
    public function partial($partialPath , $params  = null ) {

    }

    /***
	 * Returns the view component related to the adapter
	 **/
    public function getView() {

    }

}