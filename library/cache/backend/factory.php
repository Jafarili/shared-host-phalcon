<?php


namespace Phalcon\Cache\Backend;

use Phalcon\Factory as BaseFactory;
use Phalcon\Factory\Exception;
use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\Frontend\Factory as FrontendFactory;
use Phalcon\Config;


/***
 * Loads Backend Cache Adapter class using 'adapter' option, if frontend will be provided as array it will call Frontend Cache Factory
 *
 *<code>
 * use Phalcon\Cache\Backend\Factory;
 * use Phalcon\Cache\Frontend\Data;
 *
 * $options = [
 *     "prefix"   => "app-data",
 *     "frontend" => new Data(),
 *     "adapter"  => "apc",
 * ];
 * $backendCache = Factory::load($options);
 *</code>
 **/

class Factory extends BaseFactory {

    /***
	 * @param \Phalcon\Config|array config
	 **/
    public static function load($config ) {
		return self::loadClass("Phalcon\\Cache\\Backend", config);
    }

    protected static function loadClass($namespace , $config ) {

		if ( gettype($config) == "object" && config instanceof Config ) {
			$config = config->toArray();
		}

		if ( gettype($config) != "array" ) {
			throw new Exception("Config must be array or Phalcon\\Config object");
		}

		if ( !fetch frontend, config["frontend"] ) {
			throw new Exception("You must provide 'frontend' option in factory config parameter.");
		}

		if ( fetch adapter, config["adapter"] ) {
			unset config["adapter"];
			unset config["frontend"];
			if ( gettype($frontend) == "array" || frontend instanceof Config ) {
				$frontend = FrontendFactory::load(frontend);
			}
			$className = $namespace."\\".camelize(adapter);

			return new {className}(frontend, config);
		}

		throw new Exception("You must provide 'adapter' option in factory config parameter.");
    }

}