<?php


namespace Phalcon\Logger;

use Phalcon\Factory as BaseFactory;
use Phalcon\Factory\Exception;
use Phalcon\Config;


/***
 * Loads Logger Adapter class using 'adapter' option
 *
 *<code>
 * use Phalcon\Logger\Factory;
 *
 * $options = [
 *     "name"    => "log.txt",
 *     "adapter" => "file",
 * ];
 * $logger = Factory::load($options);
 *</code>
 **/

class Factory extends BaseFactory {

    /***
	 * @param \Phalcon\Config|array config
	 **/
    public static function load($config ) {
		return self::loadClass("Phalcon\\Logger\\Adapter", config);
    }

    protected static function loadClass($namespace , $config ) {

		if ( gettype($config) == "object" && config instanceof Config ) {
			$config = config->toArray();
		}

		if ( gettype($config) != "array" ) {
			throw new Exception("Config must be array or Phalcon\\Config object");
		}

		if ( fetch adapter, config["adapter"] ) {
			$className = $namespace."\\".camelize(adapter);

			if ( className != "Phalcon\\Logger\\Adapter\\Firephp" ) {
				unset config["adapter"];
				if ( !fetch name, config["name"] ) {
					throw new Exception("You must provide 'name' option in factory config parameter.");
				}
				unset config["name"];

				return new {className}(name, config);
			}

			return new {className}();
		}

		throw new Exception("You must provide 'adapter' option in factory config parameter.");
    }

}