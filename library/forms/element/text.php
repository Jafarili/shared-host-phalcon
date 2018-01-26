<?php


namespace Phalcon\Forms\Element;

use Phalcon\Tag;
use Phalcon\Forms\Element;


/***
 * Phalcon\Forms\Element\Text
 *
 * Component INPUT[type=text] for forms
 **/

class Text extends Element {

    /***
	 * Renders the element widget
	 *
	 * @param array attributes
	 **/
    public function render($attributes  = null ) {
		return Tag::textField(this->prepareAttributes(attributes));
    }

}