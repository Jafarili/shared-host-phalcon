<?php


namespace Phalcon\Image;

use Phalcon\Factory as BaseFactory;
use Phalcon\Factory\Exception;
use Phalcon\Config;


/***
 * Loads Image Adapter class using 'adapter' option
 *
 *<code>
 * use Phalcon\Image\Factory;
 *
 * $options = [
 *     "width"   => 200,
 *     "height"  => 200,
 *     "file"    => "upload/test.jpg",
 *     "adapter" => "imagick",
 * ];
 * $image = Factory::load($options);
 *</code>
 **/

class Factory extends BaseFactory {

    /***
	 * @param \Phalcon\Config|array config
	 **/
    public static function load($config ) {
		return self::loadClass("Phalcon\\Image\\Adapter", config);
    }

    protected static function loadClass($namespace , $config ) {

		if ( gettype($config) == "object" && config instanceof Config ) {
			$config = config->toArray();
		}

		if ( gettype($config) != "array" ) {
			throw new Exception("Config must be array or Phalcon\\Config object");
		}

		if ( !fetch file, config["file"] ) {
			throw new Exception("You must provide 'file' option in factory config parameter.");
		}

		if ( fetch adapter, config["adapter"] ) {
			$className = $namespace."\\".camelize(adapter);

			if ( fetch width, config["width"] ) {
				if ( fetch height, config["height"] ) {
					return new {className}(file, width, height);
				}

				return new {className}(file, width);
			}

			return new {className}(file);
		}

		throw new Exception("You must provide 'adapter' option in factory config parameter.");
    }

}