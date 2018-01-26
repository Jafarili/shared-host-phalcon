<?php


namespace Phalcon\Forms\Element;

use Phalcon\Tag;
use Phalcon\Forms\Element;


/***
 * Phalcon\Forms\Element\Submit
 *
 * Component INPUT[type=submit] for forms
 **/

class Submit extends Element {

    /***
	 * Renders the element widget
	 *
	 * @param array attributes
	 **/
    public function render($attributes  = null ) {
		return Tag::submitButton(this->prepareAttributes(attributes));
    }

}