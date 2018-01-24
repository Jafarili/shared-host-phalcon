<?php


namespace Phalcon;

interface FactoryInterface {

    /***
	 * @param \Phalcon\Config|array config
	 **/
    public static function load($config ); 

}