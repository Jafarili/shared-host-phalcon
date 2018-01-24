<?php


namespace Phalcon\Assets;

use Phalcon\Assets\Resource;
use Phalcon\Assets\FilterInterface;
use Phalcon\Assets\Inline;
use Phalcon\Assets\Resource\Css as ResourceCss;
use Phalcon\Assets\Resource\Js as ResourceJs;
use Phalcon\Assets\Inline\Js as InlineJs;
use Phalcon\Assets\Inline\Css as InlineCss;


/***
 * Phalcon\Assets\Collection
 *
 * Represents a collection of resources
 **/

class Collection {

    protected $_prefix;

    protected $_local;

    protected $_resources;

    protected $_codes;

    protected $_position;

    protected $_filters;

    protected $_attributes;

    protected $_join;

    protected $_targetUri;

    protected $_targetPath;

    protected $_targetLocal;

    protected $_sourcePath;

    protected $_includedResources;

    /***
	 * Phalcon\Assets\Collection constructor
	 **/
    public function __construct() {

    }

    /***
	 * Adds a resource to the collection
	 **/
    public function add($resource ) {

    }

    /***
	 * Adds an inline code to the collection
	 **/
    public function addInline($code ) {

    }

    /***
	 * Checks this the resource is added to the collection.
	 *
	 * <code>
	 * use Phalcon\Assets\Resource;
	 * use Phalcon\Assets\Collection;
	 *
	 * $collection = new Collection();
	 * $resource = new Resource("js", "js/jquery.js");
	 * $collection->add($resource);
	 * $collection->has($resource); // true
	 * </code>
	 **/
    public function has($resource ) {

    }

    /***
	 * Adds a CSS resource to the collection
	 **/
    public function addCss($path , $local  = null , $filter  = true , $attributes  = null ) {

    }

    /***
	 * Adds an inline CSS to the collection
	 **/
    public function addInlineCss($content , $filter  = true , $attributes  = null ) {

    }

    /***
	 * Adds a javascript resource to the collection
	 *
	 * @param string path
	 * @param boolean local
	 * @param boolean filter
	 * @param array attributes
	 * @return \Phalcon\Assets\Collection
	 **/
    public function addJs($path , $local  = null , $filter  = true , $attributes  = null ) {

    }

    /***
	 * Adds an inline javascript to the collection
	 **/
    public function addInlineJs($content , $filter  = true , $attributes  = null ) {

    }

    /***
	 * Returns the number of elements in the form
	 **/
    public function count() {

    }

    /***
	 * Rewinds the internal iterator
	 **/
    public function rewind() {

    }

    /***
	 * Returns the current resource in the iterator
	 **/
    public function current() {

    }

    /***
	 * Returns the current position/key in the iterator
	 *
	 * @return int
	 **/
    public function key() {

    }

    /***
	 * Moves the internal iteration pointer to the next position
	 **/
    public function next() {

    }

    /***
	 * Check if the current element in the iterator is valid
	 **/
    public function valid() {

    }

    /***
	 * Sets the target path of the file for the filtered/join output
	 **/
    public function setTargetPath($targetPath ) {

    }

    /***
	 * Sets a base source path for all the resources in this collection
	 **/
    public function setSourcePath($sourcePath ) {

    }

    /***
	 * Sets a target uri for the generated HTML
	 **/
    public function setTargetUri($targetUri ) {

    }

    /***
	 * Sets a common prefix for all the resources
	 **/
    public function setPrefix($prefix ) {

    }

    /***
	 * Sets if the collection uses local resources by default
	 **/
    public function setLocal($local ) {

    }

    /***
	 * Sets extra HTML attributes
	 **/
    public function setAttributes($attributes ) {

    }

    /***
	 * Sets an array of filters in the collection
	 **/
    public function setFilters($filters ) {

    }

    /***
	 * Sets the target local
	 **/
    public function setTargetLocal($targetLocal ) {

    }

    /***
	 * Sets if all filtered resources in the collection must be joined in a single result file
	 **/
    public function join($join ) {

    }

    /***
	 * Returns the complete location where the joined/filtered collection must be written
	 **/
    public function getRealTargetPath($basePath ) {

    }

    /***
	 * Adds a filter to the collection
	 **/
    public function addFilter($filter ) {

    }

    /***
	 * Adds a resource or inline-code to the collection
	 **/
    protected final function addResource($resource ) {

    }

}