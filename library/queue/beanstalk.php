<?php


namespace Phalcon\Queue;

use Phalcon\Queue\Beanstalk\Job;
use Phalcon\Queue\Beanstalk\Exception;


/***
 * Phalcon\Queue\Beanstalk
 *
 * Class to access the beanstalk queue service.
 * Partially implements the protocol version 1.2
 *
 * <code>
 * use Phalcon\Queue\Beanstalk;
 *
 * $queue = new Beanstalk(
 *     [
 *         "host"       => "127.0.0.1",
 *         "port"       => 11300,
 *         "persistent" => true,
 *     ]
 * );
 * </code>
 *
 * @link http://www.igvita.com/2010/05/20/scalable-work-queues-with-beanstalk/
 **/

class Beanstalk {

    /***
	 * Seconds to wait before putting the job in the ready queue.
	 * The job will be in the "delayed" state during this time.
	 *
	 * @const integer
	 **/
    const DEFAULT_DELAY= 0;

    /***
	 * Jobs with smaller priority values will be scheduled before jobs with larger priorities.
	 * The most urgent priority is 0, the least urgent priority is 4294967295.
	 *
	 * @const integer
	 **/
    const DEFAULT_PRIORITY= 100;

    /***
	 * Time to run - number of seconds to allow a worker to run this job.
	 * The minimum ttr is 1.
	 *
	 * @const integer
	 **/
    const DEFAULT_TTR= 86400;

    /***
	 * Default tube name
	 * @const string
	 **/
    const DEFAULT_TUBE= default;

    /***
	 * Default connected host
	 * @const string
	 **/
    const DEFAULT_HOST= 127.0.0.1;

    /***
	 * Default connected port
	 * @const integer
	 **/
    const DEFAULT_PORT= 11300;

    /***
	 * Connection resource
	 * @var resource
	 **/
    protected $_connection;

    /***
	 * Connection options
	 * @var array
	 **/
    protected $_parameters;

    /***
	 * Phalcon\Queue\Beanstalk
	 **/
    public function __construct($parameters ) {
		if ( !isset parameters["host"] ) {
			$parameters["host"] = self::DEFAULT_HOST;
		}

		if ( !isset parameters["port"]  ) {
			$parameters["port"] = self::DEFAULT_PORT;
		}

		if ( !isset parameters["persistent"]  ) {
			$parameters["persistent"] = false;
		}

		$this->_parameters = parameters;
    }

    /***
	 * Makes a connection to the Beanstalkd server
	 **/
    public function connect() {

		$connection = $this->_connection;
		if ( gettype($connection) == "resource" ) {
			this->disconnect();
		}

		$parameters = $this->_parameters;

		/**
		 * Check if ( the connection must be persistent
		 */
		if ( parameters["persistent"] ) {
			$connection = pfsockopen(parameters["host"], parameters["port"], null, null);
		} else {
			$connection = fsockopen(parameters["host"], parameters["port"], null, null);
		}

		if ( gettype($connection) != "resource" ) {
			throw new Exception("Can't connect to Beanstalk server");
		}

		stream_set_timeout(connection, -1, null);

		$this->_connection = connection;

		return connection;
    }

    /***
	 * Puts a job on the queue using specified tube.
	 **/
    public function put($data , $options  = null ) {

		/**
		 * Priority is 100 by default
		 */
		if ( !fetch priority, options["priority"] ) {
			$priority = self::DEFAULT_PRIORITY;
		}

		if ( !fetch delay, options["delay"] ) {
			$delay = self::DEFAULT_DELAY;
		}

		if ( !fetch ttr, options["ttr"] ) {
			$ttr = self::DEFAULT_TTR;
		}

		/**
		 * Data is automatically serialized befor (e be sent to the server
		 */
		$serialized = serialize(data);

		/**
		 * Create the command
		 */
		$length = strlen(serialized);
		this->write("put " . priority . " " . delay . " " . ttr . " " . length . "\r\n" . serialized);

		$response = $this->readStatus();
		$status = response[0];

		if ( status != "INSERTED" && status != "BURIED" ) {
			return false;
		}

		return (int) response[1];
    }

    /***
	 * Reserves/locks a ready job from the specified tube.
	 **/
    public function reserve($timeout  = null ) {

		if ( gettype($timeout) != "null" ) {
			$command = "reserve-with-timeout " . timeout;
		} else {
			$command = "reserve";
		}

		this->write(command);

		$response = $this->readStatus();
		if ( response[0] != "RESERVED" ) {
			return false;
		}

		/**
		 * The job is in the first position
		 * Next is the job length
		 * The body is serialized
		 * Create a beanstalk job abstraction
		 */
		return new Job(this, response[1], unserialize(this->read(response[2])));
    }

    /***
	 * Change the active tube. By default the tube is "default".
	 **/
    public function choose($tube ) {

		this->write("use " . tube);

		$response = $this->readStatus();
		if ( response[0] != "USING" ) {
			return false;
		}

		return response[1];
    }

    /***
	 * The watch command adds the named tube to the watch list for the current connection.
	 **/
    public function watch($tube ) {

		this->write("watch " . tube);

		$response = $this->readStatus();
		if ( response[0] != "WATCHING" ) {
			return false;
		}

		return (int) response[1];
    }

    /***
	 * It removes the named tube from the watch list for the current connection.
	 **/
    public function ignore($tube ) {

		this->write("ignore " . tube);

		$response = $this->readStatus();
		if ( response[0] != "WATCHING" ) {
			return false;
		}

		return (int) response[1];
    }

    /***
	 * Can delay any new job being reserved for a given time.
	 **/
    public function pauseTube($tube , $delay ) {

		this->write("pause-tube " . tube . " " . delay);

		$response = $this->readStatus();
		if ( response[0] != "PAUSED" ) {
			return false;
		}

		return true;
    }

    /***
	 * The kick command applies only to the currently used tube.
	 **/
    public function kick($bound ) {

		this->write("kick " . bound);

		$response = $this->readStatus();
		if ( response[0] != "KICKED" ) {
			return false;
		}

		return (int) response[1];
    }

    /***
	 * Gives statistical information about the system as a whole.
	 **/
    public function stats() {

		this->write("stats");

		$response = $this->readYaml();
		if ( response[0] != "OK" ) {
			return false;
		}

		return response[2];
    }

    /***
	 * Gives statistical information about the specified tube if it exists.
	 **/
    public function statsTube($tube ) {

		this->write("stats-tube " . tube);

		$response = $this->readYaml();
		if ( response[0] != "OK" ) {
			return false;
		}

		return response[2];
    }

    /***
	 * Returns a list of all existing tubes.
	 **/
    public function listTubes() {

		this->write("list-tubes");

		$response = $this->readYaml();
		if ( response[0] != "OK" ) {
			return false;
		}

		return response[2];
    }

    /***
	 * Returns the tube currently being used by the client.
	 **/
    public function listTubeUsed() {

		this->write("list-tube-used");

		$response = $this->readStatus();
		if ( response[0] != "USING" ) {
			return false;
		}

		return response[1];
    }

    /***
	 * Returns a list tubes currently being watched by the client.
	 **/
    public function listTubesWatched() {

		this->write("list-tubes-watched");

		$response = $this->readYaml();
		if ( response[0] != "OK" ) {
			return false;
		}

		return response[2];
    }

    /***
	 * Inspect the next ready job.
	 **/
    public function peekReady() {

		this->write("peek-ready");

		$response = $this->readStatus();
		if ( response[0] != "FOUND" ) {
			return false;
		}

		return new Job(this, response[1], unserialize(this->read(response[2])));
    }

    /***
	 * Return the next job in the list of buried jobs.
	 **/
    public function peekBuried() {

		this->write("peek-buried");

		$response = $this->readStatus();
		if ( response[0] != "FOUND" ) {
			return false;
		}

		return new Job(this, response[1], unserialize(this->read(response[2])));
    }

    /***
	 * Return the next job in the list of buried jobs.
	 **/
    public function peekDelayed() {

		if ( !this->write("peek-delayed") ) {
			return false;
		}

		$response = $this->readStatus();
		if ( response[0] != "FOUND" ) {
			return false;
		}

		return new Job(this, response[1], unserialize(this->read(response[2])));
    }

    /***
	 * The peek commands let the client inspect a job in the system.
	 **/
    public function jobPeek($id ) {

		this->write("peek " . id);

		$response = $this->readStatus();

		if ( response[0] != "FOUND" ) {
			return false;
		}

		return new Job(this, response[1], unserialize(this->read(response[2])));
    }

    /***
	 * Reads the latest status from the Beanstalkd server
	 **/
    final public function readStatus() {
		$status = $this->read();
		if ( status === false ) {
			return [];
		}
		return explode(" ", status);
    }

    /***
	 * Fetch a YAML payload from the Beanstalkd server
	 **/
    final public function readYaml() {

		$response = $this->readStatus();

		$status = response[0];

		if ( count(response) > 1 ) {
			$numberOfBytes = response[1];

			$response = $this->read();

			$data = yaml_parse(response);
		} else {
			$numberOfBytes = 0;

			$data = [];
		}

		return [
			status,
			numberOfBytes,
			data
		];
    }

    /***
	 * Reads a packet from the socket. Prior to reading from the socket will
	 * check for availability of the connection.
	 **/
    public function read($length  = 0 ) {

		$connection = $this->_connection;
		if ( gettype($connection) != "resource" ) {
			$connection = $this->connect();
			if ( gettype($connection) != "resource" ) {
				return false;
			}
		}

		if ( length ) {

			if ( feof(connection) ) {
				return false;
			}

			$data = rtrim(stream_get_line(connection, length + 2), "\r\n");
			if ( stream_get_meta_data(connection)["timed_out"] ) {
				throw new Exception("Connection timed out");
			}
		} else {
			$data = stream_get_line(connection, 16384, "\r\n");
		}


		if ( data === "UNKNOWN_COMMAND" ) {
			throw new Exception("UNKNOWN_COMMAND");
		}

		if ( data === "JOB_TOO_BIG" ) {
			throw new Exception("JOB_TOO_BIG");
		}

		if ( data === "BAD_FORMAT" ) {
			throw new Exception("BAD_FORMAT");
		}

		if ( data === "OUT_OF_MEMORY" ) {
			throw new Exception("OUT_OF_MEMORY");
		}

		return data;
    }

    /***
	 * Writes data to the socket. Performs a connection if none is available
	 **/
    public function write($data ) {

		$connection = $this->_connection;
		if ( gettype($connection) != "resource" ) {
			$connection = $this->connect();
			if ( gettype($connection) != "resource" ) {
				return false;
			}
		}

		$packet = data . "\r\n";
		return fwrite(connection, packet, strlen(packet));
    }

    /***
	 * Closes the connection to the beanstalk server.
	 **/
    public function disconnect() {

		$connection = $this->_connection;
		if ( gettype($connection) != "resource" ) {
			return false;
		}

		fclose(connection);
		$this->_connection = null;

		return true;
    }

    /***
	 * Simply closes the connection.
	 **/
    public function quit() {
		this->write("quit");
		this->disconnect();

		return gettype($this->_connection) != "resource";
    }

}