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

    }

    protected static function loadClass($namespace , $config ) {

    }

}