<?php


namespace Phalcon\Cli;

use Phalcon\Application as BaseApplication;
use Phalcon\DiInterface;
use Phalcon\Cli\Router\Route;
use Phalcon\Events\ManagerInterface;
use Phalcon\Cli\Console\Exception;


/***
 * Phalcon\Cli\Console
 *
 * This component allows to create CLI applications using Phalcon
 **/

class Console extends BaseApplication {

    protected $_arguments;

    protected $_options;

    /***
	 * Merge modules with the existing ones
	 *
	 *<code>
	 * $application->addModules(
	 *     [
	 *         "admin" => [
	 *             "className" => "Multiple\\Admin\\Module",
	 *             "path"      => "../apps/admin/Module.php",
	 *         ],
	 *     ]
	 * );
	 *</code>
	 **/
    public function addModules($modules ) {

    }

    /***
	 * Handle the whole command-line tasks
	 **/
    public function handle($arguments  = null ) {

    }

    /***
	 * Set an specific argument
	 **/
    public function setArgument($arguments  = null , $str  = true , $shift  = true ) {

    }

}