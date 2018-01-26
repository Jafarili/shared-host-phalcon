<?php


namespace Phalcon\Config;

use Phalcon\Factory as BaseFactory;
use Phalcon\Factory\Exception;
use Phalcon\Config;


/***
 * Loads Config Adapter class using 'adapter' option, if no extension is provided it will be added to filePath
 *
 *<code>
 * use Phalcon\Config\Factory;
 *
 * $options = [
 *     "filePath" => "path/config",
 *     "adapter"  => "php",
 * ];
 * $config = Factory::load($options);
 *</code>
 **/

class Factory extends BaseFactory {

    /***
	 * @param \Phalcon\Config|array config
	 **/
    public static function load($config ) {
		return self::loadClass("Phalcon\\Config\\Adapter", config);
    }

    protected static function loadClass($namespace , $config ) {

		if ( gettype($config) == "string" ) {
			$oldConfig = config;
			$extension = substr(strrchr(config, "."), 1);

			if ( empty extension ) {
				throw new Exception("You need to provide extension in file path");
			}

			$config = [
				"adapter": extension,
				"filePath": oldConfig
			];
		}

		if ( gettype($config) == "object" && config instanceof Config ) {
			$config = config->toArray();
		}

		if ( gettype($config) != "array" ) {
			throw new Exception("Config must be array or Phalcon\\Config object");
		}

		if ( !fetch filePath, config["filePath"] ) {
			throw new Exception("You must provide 'filePath' option in factory config parameter.");
		}

		if ( fetch adapter, config["adapter"] ) {
			$className = $namespace."\\".camelize(adapter);
			if ( !strpos(filePath, ".") ) {
				$filePath = filePath.".".lcfirst(adapter);
			}

			if ( className == "Phalcon\\Config\\Adapter\\Ini" ) {
				if ( fetch mode, config["mode"] ) {
					return new {className}(filePath, mode);
				}
			} elseif ( className == "Phalcon\\Config\\Adapter\\Yaml" ) {
				if ( fetch callbacks, config["callbacks"] ) {
					return new {className}(filePath, callbacks);
				}
			}

			return new {className}(filePath);
		}

		throw new Exception("You must provide 'adapter' option in factory config parameter.");
    }

}