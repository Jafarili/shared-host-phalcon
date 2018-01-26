<?php


namespace Phalcon\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData;
use Phalcon\Mvc\Model\Exception;


/***
 * Phalcon\Mvc\Model\MetaData\Files
 *
 * Stores model meta-data in PHP files.
 *
 *<code>
 * $metaData = new \Phalcon\Mvc\Model\Metadata\Files(
 *     [
 *         "metaDataDir" => "app/cache/metadata/",
 *     ]
 * );
 *</code>
 **/

class Files extends MetaData {

    protected $_metaDataDir;

    protected $_metaData;

    /***
	 * Phalcon\Mvc\Model\MetaData\Files constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {
		if ( gettype($options) == "array" ) {
			if ( fetch metaDataDir, options["metaDataDir"] ) {
				$this->_metaDataDir = metaDataDir;
			}
		}
    }

    /***
	 * Reads meta-data from files
	 *
	 * @param string key
	 * @return mixed
	 **/
    public function read($key ) {
		$path = $this->_metaDataDir . prepare_virtual_path(key, "_") . ".php";
		if ( file_exists(path) ) {
			return require path;
		}
		return null;
    }

    /***
	 * Writes the meta-data to files
	 *
	 * @param string key
	 * @param array data
	 **/
    public function write($key , $data ) {

		$path = $this->_metaDataDir . prepare_virtual_path(key, "_") . ".php";
		if ( file_put_contents(path, "<?php return " . var_export(data, true) . "; ") === false ) {
			throw new Exception("Meta-Data directory cannot be written");
		}
    }

}