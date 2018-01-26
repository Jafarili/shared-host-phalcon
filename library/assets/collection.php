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
		$this->_includedResources = [];
    }

    /***
	 * Adds a resource to the collection
	 **/
    public function add($resource ) {
		this->addResource($resource);

		return this;
    }

    /***
	 * Adds an inline code to the collection
	 **/
    public function addInline($code ) {
		this->addResource(code);

		return this;
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

		$key = $resource->getResourceKey(),
			resources = $this->_includedResources;

		return in_array(key, resources);
    }

    /***
	 * Adds a CSS resource to the collection
	 **/
    public function addCss($path , $local  = null , $filter  = true , $attributes  = null ) {

		if ( gettype($local) == "boolean" ) {
			$collectionLocal = local;
		} else {
			$collectionLocal = $this->_local;
		}

		if ( gettype($attributes) == "array" ) {
			$collectionAttributes = attributes;
		} else {
			$collectionAttributes = $this->_attributes;
		}

		this->add(new ResourceCss(path, collectionLocal, filter, collectionAttributes));

		return this;
    }

    /***
	 * Adds an inline CSS to the collection
	 **/
    public function addInlineCss($content , $filter  = true , $attributes  = null ) {

		if ( gettype($attributes) == "array" ) {
			$collectionAttributes = attributes;
		} else {
			$collectionAttributes = $this->_attributes;
		}

		$this->_codes[] = new InlineCss(content, filter, collectionAttributes);
		return this;
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

		if ( gettype($local) == "boolean" ) {
			$collectionLocal = local;
		} else {
			$collectionLocal = $this->_local;
		}

		if ( gettype($attributes) == "array" ) {
			$collectionAttributes = attributes;
		} else {
			$collectionAttributes = $this->_attributes;
		}

		this->add(new ResourceJs(path, collectionLocal, filter, collectionAttributes));

		return this;
    }

    /***
	 * Adds an inline javascript to the collection
	 **/
    public function addInlineJs($content , $filter  = true , $attributes  = null ) {

		if ( gettype($attributes) == "array" ) {
			$collectionAttributes = attributes;
		} else {
			$collectionAttributes = $this->_attributes;
		}

		$this->_codes[] = new InlineJs(content, filter, collectionAttributes);

		return this;
    }

    /***
	 * Returns the number of elements in the form
	 **/
    public function count() {
		return count(this->_resources);
    }

    /***
	 * Rewinds the internal iterator
	 **/
    public function rewind() {
		$this->_position = 0;
    }

    /***
	 * Returns the current resource in the iterator
	 **/
    public function current() {
		return $this->_resources[this->_position];
    }

    /***
	 * Returns the current position/key in the iterator
	 *
	 * @return int
	 **/
    public function key() {
		return $this->_position;
    }

    /***
	 * Moves the internal iteration pointer to the next position
	 **/
    public function next() {
		$this->_position++;
    }

    /***
	 * Check if the current element in the iterator is valid
	 **/
    public function valid() {
		return isset $this->_resources[this->_position];
    }

    /***
	 * Sets the target path of the file for the filtered/join output
	 **/
    public function setTargetPath($targetPath ) {
		$this->_targetPath = targetPath;
		return this;
    }

    /***
	 * Sets a base source path for all the resources in this collection
	 **/
    public function setSourcePath($sourcePath ) {
		$this->_sourcePath = sourcePath;
		return this;
    }

    /***
	 * Sets a target uri for the generated HTML
	 **/
    public function setTargetUri($targetUri ) {
		$this->_targetUri = targetUri;
		return this;
    }

    /***
	 * Sets a common prefix for all the resources
	 **/
    public function setPrefix($prefix ) {
		$this->_prefix = prefix;
		return this;
    }

    /***
	 * Sets if the collection uses local resources by default
	 **/
    public function setLocal($local ) {
		$this->_local = local;
		return this;
    }

    /***
	 * Sets extra HTML attributes
	 **/
    public function setAttributes($attributes ) {
		$this->_attributes = attributes;
		return this;
    }

    /***
	 * Sets an array of filters in the collection
	 **/
    public function setFilters($filters ) {
		$this->_filters = filters;
		return this;
    }

    /***
	 * Sets the target local
	 **/
    public function setTargetLocal($targetLocal ) {
		$this->_targetLocal = targetLocal;
		return this;
    }

    /***
	 * Sets if all filtered resources in the collection must be joined in a single result file
	 **/
    public function join($join ) {
		$this->_join = join;
		return this;
    }

    /***
	 * Returns the complete location where the joined/filtered collection must be written
	 **/
    public function getRealTargetPath($basePath ) {

		$targetPath = $this->_targetPath;

		/**
		 * A base path foreach ( resources can be $the as $set assets manager
		 */
		$completePath = basePath . targetPath;

		/**
		 * Get the real template path, the target path can optionally don't exist
		 */
		if ( file_exists(completePath) ) {
			return realPath(completePath);
		}

		return completePath;
    }

    /***
	 * Adds a filter to the collection
	 **/
    public function addFilter($filter ) {
		$this->_filters[] = filter;
		return this;
    }

    /***
	 * Adds a resource or inline-code to the collection
	 **/
    protected final function addResource($resource ) {
		if ( !this->has($resource) ) {
			if ( $resource instanceof $Resource ) {
				$this->_resources[] = $resource;
			} else {
				$this->_codes[] = $resource;
			}

			$this->_includedResources[] = $resource->getResourceKey();

			return true;
		}

		return false;
    }

}