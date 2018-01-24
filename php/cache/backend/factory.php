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

    }

    protected static function loadClass($namespace , $config ) {

    }

}