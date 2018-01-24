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

    }

    protected static function loadClass($namespace , $config ) {

    }

}