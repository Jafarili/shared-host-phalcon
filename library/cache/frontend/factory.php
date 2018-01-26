<?php


namespace Phalcon\Cache\Frontend;

use Phalcon\Cache\FrontendInterface;
use Phalcon\Factory\Exception;
use Phalcon\Factory as BaseFactory;
use Phalcon\Config;


/***
 * Loads Frontend Cache Adapter class using 'adapter' option
 *
 *<code>
 * use Phalcon\Cache\Frontend\Factory;
 *
 * $options = [
 *     "lifetime" => 172800,
 *     "adapter"  => "data",
 * ];
 * $frontendCache = Factory::load($options);
 *</code>
 **/

class Factory extends BaseFactory {

    /***
	 * @param \Phalcon\Config|array config
	 **/
    public static function load($config ) {
		return self::loadClass("Phalcon\\Cache\\Frontend", config);
    }

    protected static function loadClass($namespace , $config ) {

		if ( gettype($config) == "object" && config instanceof Config ) {
			$config = config->toArray();
		}

		if ( gettype($config) != "array" ) {
			throw new Exception("Config must be array or Phalcon\\Config object");
		}

		if ( fetch adapter, config["adapter"] ) {
			unset config["adapter"];
			$className = $namespace."\\".camelize(adapter);

			if ( className == "Phalcon\\Cache\\Frontend\\None" ) {
				return new {className}();
			} else {
				return new {className}(config);
			}
		}

		throw new Exception("You must provide 'adapter' option in factory config parameter.");
    }

}