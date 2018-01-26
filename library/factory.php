<?php


namespace Phalcon;

use Phalcon\Factory\Exception;
use Phalcon\Config;
abstract class Factory {

    protected static function loadClass($namespace , $config ) {

		if ( gettype($config) == "object" && config instanceof Config ) {
			$config = config->toArray();
		}

		if ( gettype($config) != "array" ) {
			throw new Exception("Config must be array or Phalcon\\Config object");
		}

		if ( fetch adapter, config["adapter"] ) {
			unset config["adapter"];
			$className = $namespace."\\".adapter;

			return new {className}(config);
		}

		throw new Exception("You must provide 'adapter' option in factory config parameter.");
    }

}