<?php


namespace Phalcon\Mvc\Model\MetaData;

use Phalcon\Mvc\Model\MetaData;
use Phalcon\Mvc\Model\Exception;


/***
 * Phalcon\Mvc\Model\MetaData\Apcu
 *
 * Stores model meta-data in the APCu cache. Data will erased if the web server is restarted
 *
 * By default meta-data is stored for 48 hours (172800 seconds)
 *
 * You can query the meta-data by printing apcu_fetch('$PMM$') or apcu_fetch('$PMM$my-app-id')
 *
 *<code>
 * $metaData = new \Phalcon\Mvc\Model\Metadata\Apcu(
 *     [
 *         "prefix"   => "my-app-id",
 *         "lifetime" => 86400,
 *     ]
 * );
 *</code>
 **/

class Apcu extends MetaData {

    protected $_prefix;

    protected $_ttl;

    protected $_metaData;

    /***
	 * Phalcon\Mvc\Model\MetaData\Apcu constructor
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {

    }

    /***
	 * Reads meta-data from APCu
	 **/
    public function read($key ) {

    }

    /***
	 * Writes the meta-data to APCu
	 **/
    public function write($key , $data ) {

    }

}