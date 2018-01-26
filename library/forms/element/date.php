<?php


namespace Phalcon\Forms\Element;

use Phalcon\Tag;
use Phalcon\Forms\Element;


/***
 * Phalcon\Forms\Element\Date
 *
 * Component INPUT[type=date] for forms
 **/

class Date extends Element {

    /***
	 * Renders the element widget returning html
	 *
	 * @param array attributes
	 **/
    public function render($attributes  = null ) {
		return Tag::dateField(this->prepareAttributes(attributes));
    }

}