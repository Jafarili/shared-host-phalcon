<?php


namespace Phalcon\Assets;



/***
 * Phalcon\Assets\Resource
 *
 * Represents an asset resource
 *
 *<code>
 * $resource = new \Phalcon\Assets\Resource("js", "javascripts/jquery.js");
 *</code>
 **/

class Resource {

    /***
	 * @var string
	 **/
    protected $_type;

    /***
	 * @var string
	 **/
    protected $_path;

    /***
	 * @var boolean
	 **/
    protected $_local;

    /***
	 * @var boolean
	 **/
    protected $_filter;

    /***
	 * @var array | null
	 **/
    protected $_attributes;

    protected $_sourcePath;

    protected $_targetPath;

    protected $_targetUri;

    /***
	 * Phalcon\Assets\Resource constructor
	 *
	 * @param string type
	 * @param string path
	 * @param boolean local
	 * @param boolean filter
	 * @param array attributes
	 **/
    public function __construct($type , $path , $local  = true , $filter  = true , $attributes  = null ) {

    }

    /***
	 * Sets the resource's type
	 **/
    public function setType($type ) {

    }

    /***
	 * Sets the resource's path
	 **/
    public function setPath($path ) {

    }

    /***
	 * Sets if the resource is local or external
	 **/
    public function setLocal($local ) {

    }

    /***
	 * Sets if the resource must be filtered or not
	 **/
    public function setFilter($filter ) {

    }

    /***
	 * Sets extra HTML attributes
	 **/
    public function setAttributes($attributes ) {

    }

    /***
	 * Sets a target uri for the generated HTML
	 **/
    public function setTargetUri($targetUri ) {

    }

    /***
	 * Sets the resource's source path
	 **/
    public function setSourcePath($sourcePath ) {

    }

    /***
	 * Sets the resource's target path
	 **/
    public function setTargetPath($targetPath ) {

    }

    /***
	 * Returns the content of the resource as an string
	 * Optionally a base path where the resource is located can be set
	 **/
    public function getContent($basePath  = null ) {

    }

    /***
	 * Returns the real target uri for the generated HTML
	 **/
    public function getRealTargetUri() {

    }

    /***
	 * Returns the complete location where the resource is located
	 **/
    public function getRealSourcePath($basePath  = null ) {

    }

    /***
	 * Returns the complete location where the resource must be written
	 **/
    public function getRealTargetPath($basePath  = null ) {

    }

    /***
	 * Gets the resource's key.
	 **/
    public function getResourceKey() {

    }

}