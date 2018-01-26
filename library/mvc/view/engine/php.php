<?php


namespace Phalcon\Mvc\View\Engine;

use Phalcon\Mvc\View\Engine;


/***
 * Phalcon\Mvc\View\Engine\Php
 *
 * Adapter to use PHP itself as templating engine
 **/

class Php extends Engine {

    /***
	 * Renders a view using the template engine
	 **/
    public function render($path , $params , $mustClean  = false ) {

		if ( mustClean === true ) {
			ob_clean();
		}

		/**
		 * Create the variables in local symbol table
		 */
		if ( gettype($params) == "array" ) {
			foreach ( key, $params as $value ) {
				${key} = value;
			}
		}

		/**
		 * Require the file
		 */
		require path;

		if ( mustClean === true ) {
			this->_view->setContent(ob_get_contents());
		}
    }

}