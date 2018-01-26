<?php


namespace Phalcon\Db;

use Phalcon\Db\Profiler\Item;


/***
 * Phalcon\Db\Profiler
 *
 * Instances of Phalcon\Db can generate execution profiles
 * on SQL statements sent to the relational database. Profiled
 * information includes execution time in milliseconds.
 * This helps you to identify bottlenecks in your applications.
 *
 * <code>
 * use Phalcon\Db\Profiler;
 * use Phalcon\Events\Event;
 * use Phalcon\Events\Manager;
 *
 * $profiler = new Profiler();
 * $eventsManager = new Manager();
 *
 * $eventsManager->attach(
 *     "db",
 *     function (Event $event, $connection) use ($profiler) {
 *         if ($event->getType() === "beforeQuery") {
 *             $sql = $connection->getSQLStatement();
 *
 *             // Start a profile with the active connection
 *             $profiler->startProfile($sql);
 *         }
 *
 *         if ($event->getType() === "afterQuery") {
 *             // Stop the active profile
 *             $profiler->stopProfile();
 *         }
 *     }
 * );
 *
 * // Set the event manager on the connection
 * $connection->setEventsManager($eventsManager);
 *
 *
 * $sql = "SELECT buyer_name, quantity, product_name
 * FROM buyers LEFT JOIN products ON
 * buyers.pid=products.id";
 *
 * // Execute a SQL statement
 * $connection->query($sql);
 *
 * // Get the last profile in the profiler
 * $profile = $profiler->getLastProfile();
 *
 * echo "SQL Statement: ", $profile->getSQLStatement(), "\n";
 * echo "Start Time: ", $profile->getInitialTime(), "\n";
 * echo "Final Time: ", $profile->getFinalTime(), "\n";
 * echo "Total Elapsed Time: ", $profile->getTotalElapsedSeconds(), "\n";
 * </code>
 **/

class Profiler {

    /***
	 * All the Phalcon\Db\Profiler\Item in the active profile
	 *
	 * @var \Phalcon\Db\Profiler\Item[]
	 **/
    protected $_allProfiles;

    /***
	 * Active Phalcon\Db\Profiler\Item
	 *
	 * @var Phalcon\Db\Profiler\Item
	 **/
    protected $_activeProfile;

    /***
	 * Total time spent by all profiles to complete
	 *
	 * @var float
	 **/
    protected $_totalSeconds;

    /***
	 * Starts the profile of a SQL sentence
	 *
	 * @param string sqlStatement
	 * @return \Phalcon\Db\Profiler
	 **/
    public function startProfile($sqlStatement , $sqlVariables  = null , $sqlBindTypes  = null ) {

		$activeProfile = new Item();

		activeProfile->setSqlStatement(sqlStatement);

		if ( gettype($sqlVariables) == "array" ) {
			activeProfile->setSqlVariables(sqlVariables);
		}

		if ( gettype($sqlBindTypes) == "array" ) {
			activeProfile->setSqlBindTypes(sqlBindTypes);
		}

		activeProfile->setInitialTime(microtime(true));

		if ( method_exists(this, "befor (eStartProfile") ) ) {
			this->) {"befor (eStartProfile"}(activeProfile);
		}

		$this->_activeProfile = activeProfile;

		return this;
    }

    /***
	 * Stops the active profile
	 **/
    public function stopProfile() {

		$finalTime = microtime(true),
			activeProfile = <Item> $this->_activeProfile;

		activeProfile->setFinalTime(finalTime);

		$initialTime = activeProfile->getInitialTime(),
			this->_totalSeconds = $this->_totalSeconds + (finalTime - initialTime),
			this->_allProfiles[] = activeProfile;

		if ( method_exists(this, "afterEndProfile") ) {
			this->{"afterEndProfile"}(activeProfile);
		}

		return this;
    }

    /***
	 * Returns the total number of SQL statements processed
	 **/
    public function getNumberTotalStatements() {
		return count(this->_allProfiles);
    }

    /***
	 * Returns the total time in seconds spent by the profiles
	 **/
    public function getTotalElapsedSeconds() {
		return $this->_totalSeconds;
    }

    /***
	 * Returns all the processed profiles
	 **/
    public function getProfiles() {
		return $this->_allProfiles;
    }

    /***
	 * Resets the profiler, cleaning up all the profiles
	 **/
    public function reset() {
		$this->_allProfiles = [];
		return this;
    }

    /***
	 * Returns the last profile executed in the profiler
	 **/
    public function getLastProfile() {
		return $this->_activeProfile;
    }

}