<?php


namespace Phalcon\Db\Profiler;



/***
 * Phalcon\Db\Profiler\Item
 *
 * This class identifies each profile in a Phalcon\Db\Profiler
 *
 **/

class Item {

    /***
	 * SQL statement related to the profile
	 *
	 * @var string
	 **/
    protected $_sqlStatement;

    /***
	 * SQL variables related to the profile
	 *
	 * @var array
	 **/
    protected $_sqlVariables;

    /***
	 * SQL bind types related to the profile
	 *
	 * @var array
	 **/
    protected $_sqlBindTypes;

    /***
	 * Timestamp when the profile started
	 *
	 * @var double
	 **/
    protected $_initialTime;

    /***
	 * Timestamp when the profile ended
	 *
	 * @var double
	 **/
    protected $_finalTime;

    /***
	 * Returns the total time in seconds spent by the profile
	 **/
    public function getTotalElapsedSeconds() {

    }

}