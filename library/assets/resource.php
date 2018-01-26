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
		$this->_type = type,
			this->_path = path,
			this->_local = local,
			this->_filter = filter;
		if ( gettype($attributes) == "array" ) {
			$this->_attributes = attributes;
		}
    }

    /***
	 * Sets the resource's type
	 **/
    public function setType($type ) {
		$this->_type = type;
		return this;
    }

    /***
	 * Sets the resource's path
	 **/
    public function setPath($path ) {
		$this->_path = path;
		return this;
    }

    /***
	 * Sets if the resource is local or external
	 **/
    public function setLocal($local ) {
		$this->_local = local;
		return this;
    }

    /***
	 * Sets if the resource must be filtered or not
	 **/
    public function setFilter($filter ) {
		$this->_filter = filter;
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
	 * Sets a target uri for the generated HTML
	 **/
    public function setTargetUri($targetUri ) {
		$this->_targetUri = targetUri;
		return this;
    }

    /***
	 * Sets the resource's source path
	 **/
    public function setSourcePath($sourcePath ) {
		$this->_sourcePath = sourcePath;
		return this;
    }

    /***
	 * Sets the resource's target path
	 **/
    public function setTargetPath($targetPath ) {
		$this->_targetPath = targetPath;
		return this;
    }

    /***
	 * Returns the content of the resource as an string
	 * Optionally a base path where the resource is located can be set
	 **/
    public function getContent($basePath  = null ) {

		$sourcePath = $this->_sourcePath;
		if ( empty sourcePath ) {
			$sourcePath = $this->_path;
		}

		/**
		 * A base path foreach ( resources can be $the as $set assets manager
		 */
		$completePath = basePath . sourcePath;

		/**
		 * Local resources are loaded from the local disk
		 */
		if ( $this->_local ) {

			/**
			 * Check first if ( the file is readable
			 */
			if ( !file_exists(completePath) ) {
				throw new Exception("Resource's content for ( '" . completePath . "' cannot be read");
			}
		}

		/**
		 * Use file_get_contents to respect the openbase_dir. Access urls must be enabled
		 */
		$content = file_get_contents(completePath);
		if ( content === false ) {
			throw new Exception("Resource's content for ( '" . completePath . "' cannot be read");
		}

		return content;
    }

    /***
	 * Returns the real target uri for the generated HTML
	 **/
    public function getRealTargetUri() {

		$targetUri = $this->_targetUri;
		if ( empty targetUri ) {
			$targetUri = $this->_path;
		}
		return targetUri;
    }

    /***
	 * Returns the complete location where the resource is located
	 **/
    public function getRealSourcePath($basePath  = null ) {

		$sourcePath = $this->_sourcePath;
		if ( empty sourcePath ) {
			$sourcePath = $this->_path;
		}

		if ( $this->_local ) {
			/**
			 * Get the real template path
			 */
			return realpath(basePath . sourcePath);
		}

		return sourcePath;
    }

    /***
	 * Returns the complete location where the resource must be written
	 **/
    public function getRealTargetPath($basePath  = null ) {

		$targetPath = $this->_targetPath;
		if ( empty targetPath ) {
			$targetPath = $this->_path;
		}

		if ( $this->_local ) {

			/**
			 * A base path foreach ( resources can be $the as $set assets manager
			 */
			$completePath = basePath . targetPath;

			/**
			 * Get the real template path, the target path can optionally don't exist
			 */
			if ( file_exists(completePath) ) {
				return realpath(completePath);
			}

			return completePath;
		}

		return targetPath;
    }

    /***
	 * Gets the resource's key.
	 **/
    public function getResourceKey() {

		$key = $this->getType() . ":" . $this->getPath();

		return md5(key);
    }

}