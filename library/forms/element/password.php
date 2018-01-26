<?php


namespace Phalcon\Forms\Element;

use Phalcon\Tag;
use Phalcon\Forms\Element;


/***
 * Phalcon\Forms\Element\Password
 *
 * Component INPUT[type=password] for forms
 **/

class Password extends Element {

    /***
	 * Renders the element widget returning html
	 *
	 * @param array $attributes
	 **/
    public function render($attributes  = null ) {
		return Tag::passwordField(this->prepareAttributes(attributes));
    }

}