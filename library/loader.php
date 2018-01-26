<?php


namespace Phalcon;

use Phalcon\Events\ManagerInterface;
use Phalcon\Events\EventsAwareInterface;


/***
 * Phalcon\Loader
 *
 * This component helps to load your project classes automatically based on some conventions
 *
 *<code>
 * use Phalcon\Loader;
 *
 * // Creates the autoloader
 * $loader = new Loader();
 *
 * // Register some namespaces
 * $loader->registerNamespaces(
 *     [
 *         "Example\\Base"    => "vendor/example/base/",
 *         "Example\\Adapter" => "vendor/example/adapter/",
 *         "Example"          => "vendor/example/",
 *     ]
 * );
 *
 * // Register autoloader
 * $loader->register();
 *
 * // Requiring this class will automatically include file vendor/example/adapter/Some.php
 * $adapter = new \Example\Adapter\Some();
 *</code>
 **/

class Loader {

    protected $_eventsManager;

    protected $_foundPath;

    protected $_checkedPath;

    protected $_classes;

    protected $_extensions;

    protected $_namespaces;

    protected $_directories;

    protected $_files;

    protected $_registered;

    /***
	 * Sets the events manager
	 **/
    public function setEventsManager($eventsManager ) {
		$this->_eventsManager = eventsManager;
    }

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager() {
		return $this->_eventsManager;
    }

    /***
	 * Sets an array of file extensions that the loader must try in each attempt to locate the file
	 **/
    public function setExtensions($extensions ) {
		$this->_extensions = extensions;
		return this;
    }

    /***
	 * Returns the file extensions registered in the loader
	 **/
    public function getExtensions() {
		return $this->_extensions;
    }

    /***
	 * Register namespaces and their related directories
	 **/
    public function registerNamespaces($namespaces , $merge  = false ) {

		$preparedNamespaces = $this->prepareNamespace(namespaces);

		if ( merge ) {
			foreach ( name, $preparedNamespaces as $paths ) {
				if ( !isset($this->_namespaces[name]) ) {
					$this->_namespaces[name] = [];
				}

				$this->_namespaces[name] = array_merge(this->_namespaces[name], paths);
			}
		} else {
			$this->_namespaces = preparedNamespaces;
		}

		return this;
    }

    protected function prepareNamespace($namespace ) {

		$prepared = [];
		foreach ( name, $$namespace as $paths ) {
			if ( gettype($paths) != "array" ) {
				$localPaths = [paths];
			} else {
				$localPaths = paths;
			}

			$prepared[name] = localPaths;
		}

		return prepared;
    }

    /***
	 * Returns the namespaces currently registered in the autoloader
	 **/
    public function getNamespaces() {
		return $this->_namespaces;
    }

    /***
	 * Register directories in which "not found" classes could be found
	 **/
    public function registerDirs($directories , $merge  = false ) {
		if ( merge ) {
			$this->_directories = array_merge(this->_directories, directories);
		} else {
			$this->_directories = directories;
		}

		return this;
    }

    /***
	 * Returns the directories currently registered in the autoloader
	 **/
    public function getDirs() {
		return $this->_directories;
    }

    /***
	 * Registers files that are "non-classes" hence need a "require". This is very useful for including files that only
	 * have functions
	 **/
    public function registerFiles($files , $merge  = false ) {
		if ( merge ) {
			$this->_files = array_merge(this->_files, files);
		} else {
			$this->_files = files;
		}

		return this;
    }

    /***
	 * Returns the files currently registered in the autoloader
	 **/
    public function getFiles() {
		return $this->_files;
    }

    /***
	 * Register classes and their locations
	 **/
    public function registerClasses($classes , $merge  = false ) {
		if ( merge ) {
			$this->_classes = array_merge(this->_classes, classes);
		} else {
			$this->_classes = classes;
		}

		return this;
    }

    /***
	 * Returns the class-map currently registered in the autoloader
	 **/
    public function getClasses() {
		return $this->_classes;
    }

    /***
	 * Register the autoload method
	 **/
    public function register($prepend  = null ) {
		if ( $this->_registered === false ) {
			/**
			 * Loads individual files added using Loader->registerFiles()
			 */
			this->loadFiles();

			/**
			 * Registers directories & namespaces to PHP's autoload
			 */
			spl_autoload_register([this, "autoLoad"], true, prepend);

			$this->_registered = true;
		}
		return this;
    }

    /***
	 * Unregister the autoload method
	 **/
    public function unregister() {
		if ( $this->_registered === true ) {
			spl_autoload_unregister([this, "autoLoad"]);
			$this->_registered = false;
		}
		return this;
    }

    /***
	 * Checks if a file exists and then adds the file by doing virtual require
	 **/
    public function loadFiles() {

		foreach ( $this->_files as $filePath ) {
			if ( gettype($this->_eventsManager) == "object" ) {
				$this->_checkedPath = filePath;
					this->_eventsManager->fire("loader:befor (eCheckPath", this, filePath);
			}

			/**
			 * Check if ( the file specif (ied even exists
			 */
			if ( is_file(filePath) ) {

				/**
				 * Call 'pathFound' event
				 */
				if ( gettype($this->_eventsManager) == "object" ) {
					$this->_foundPath = filePath;
					this->_eventsManager->fire("loader:pathFound", this, filePath);
				}

				/**
				 * Simulate a require
				 */
				require filePath;
			}
		}
    }

    /***
	 * Autoloads the registered classes
	 **/
    public function autoLoad($className ) {
			directories, ns, namespaces, nsPrefix,
			directory, fileName, extension, nsClassName;

		$eventsManager = $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("loader:befor (eCheckClass", this, className);
		}

		/**
		 * First we check for ( static paths for ( classes
		 */
		$classes = $this->_classes;
		if ( fetch filePath, classes[className] ) {
			if ( gettype($eventsManager) == "object" ) {
				$this->_foundPath = filePath;
				eventsManager->fire("loader:pathFound", this, filePath);
			}
			require filePath;
			return true;
		}

		$extensions = $this->_extensions;

		$ds = DIRECTORY_SEPARATOR,
			ns = "\\";

		/**
		 * Checking in namespaces
		 */
		$namespaces = $this->_namespaces;

		foreach ( nsPrefix, $namespaces as $directories ) {

			/**
			 * The class name must start with the current namespace
			 */
			if ( !starts_with(className, nsPrefix) ) {
				continue;
			}

			/**
			 * Append the namespace separator to the prefix
			 */
			$fileName = substr(className, strlen(nsPrefix . ns));

			if ( !fileName ) {
				continue;
			}

			$fileName = str_replace(ns, ds, fileName);

			foreach ( $directories as $directory ) {
				/**
				 * Add a trailing directory separator if ( the user for (got to do that
				 */
				$fixedDirectory = rtrim(directory, ds) . ds;

				foreach ( $extensions as $extension ) {

					$filePath = fixedDirectory . fileName . "." . extension;

					/**
					 * Check if ( a events manager is available
					 */
					if ( gettype($eventsManager) == "object" ) {
						$this->_checkedPath = filePath;
						eventsManager->fire("loader:befor (eCheckPath", this);
					}

					/**
					 * This is probably a good path, let's check if ( the file exists
					 */
					if ( is_file(filePath) ) {

						if ( gettype($eventsManager) == "object" ) {
							$this->_foundPath = filePath;
							eventsManager->fire("loader:pathFound", this, filePath);
						}

						/**
						 * Simulate a require
						 */
						require filePath;

						/**
						 * Return true mean success
						 */
						return true;
					}
				}
			}
		}

		/**
		 * Change the namespace separator by directory separator too
		 */
		$nsClassName = str_replace("\\", ds, className);

		/**
		 * Checking in directories
		 */
		$directories = $this->_directories;

		foreach ( $directories as $directory ) {

			/**
			 * Add a trailing directory separator if ( the user for (got to do that
			 */
			$fixedDirectory = rtrim(directory, ds) . ds;

			foreach ( $extensions as $extension ) {

				/**
				 * Create a possible path for ( the file
				 */
				$filePath = fixedDirectory . nsClassName . "." . extension;

				if ( gettype($eventsManager) == "object" ) {
					$this->_checkedPath = filePath;
					eventsManager->fire("loader:befor (eCheckPath", this, filePath);
				}

				/**
				 * Check in every directory if ( the class exists here
				 */
				if ( is_file(filePath) ) {

					/**
					 * Call 'pathFound' event
					 */
					if ( gettype($eventsManager) == "object" ) {
						$this->_foundPath = filePath;
						eventsManager->fire("loader:pathFound", this, filePath);
					}

					/**
					 * Simulate a require
					 */
					require filePath;

					/**
					 * Return true meaning success
					 */
					return true;
				}
			}
		}

		/**
		 * Call 'afterCheckClass' event
		 */
		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("loader:afterCheckClass", this, className);
		}

		/**
		 * Cannot find the class, return false
		 */
		return false;
    }

    /***
	 * Get the path when a class was found
	 **/
    public function getFoundPath() {
		return $this->_foundPath;
    }

    /***
	 * Get the path the loader is checking for a path
	 **/
    public function getCheckedPath() {
		return $this->_checkedPath;
    }

}