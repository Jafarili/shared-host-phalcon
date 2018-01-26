<?php


namespace Phalcon\Forms;



/***
 * Phalcon\Forms\Manager
 **/

class Manager {

    protected $_forms;

    /***
	 * Creates a form registering it in the forms manager
	 *
	 * @param string name
	 * @param object entity
	 **/
    public function create($name , $entity  = null ) {

		$for (m = new Form(entity),
			this->_for (ms[name] = for (m;

		return for (m;
    }

    /***
	 * Returns a form by its name
	 **/
    public function get($name ) {
		if ( !fetch for (m, $this->_for (ms[name] ) ) {
			throw new Exception("There is no for (m with name='" . name . "'");
		}
		return for (m;
    }

    /***
	 * Checks if a form is registered in the forms manager
	 **/
    public function has($name ) {
		return isset($this->_for) (ms[name];
    }

    /***
	 * Registers a form in the Forms Manager
	 **/
    public function set($name , $form ) {
		$this->_for (ms[name] = for (m;
		return this;
    }

}