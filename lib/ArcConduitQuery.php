<?php

/**
 * Zemke-Rhyne
 * Support tools for a Docker <--> Phabricator bridge
 *
 * (c) Sébastien Santoro aka Dereckson, 2014
 * Released under BSD license.
 */

/**
 * Class ArcConduitQuery
 */
class ArcConduitQuery {
	///
	/// Private members
	///

	/**
	 * The request to send through conduit
	 *
	 * @var ConduitRequest
	 */
	private $request;

	/**
	 * The conduit reply
	 *
	 * @var object
	 */
	private $reply = null;

	///
	/// Constructor
	///

	/**
	 * Initializes a new instance of the ArcConduitQuery class
	 *
	 * @param ConduitRequest $request The request to send through conduit
	 */
	public function __construct ($request) {
		$this->request = $request;
	}

	///
	/// Helper functions to run software
	///

	/**
	 * Gets an array of descriptors for proc_open
	 *
	 * @return array descriptors for stdin, stdout and stderr
	 */
	private static function getDescriptors () {
		return [
			0 => ["pipe", "r"],                                  //stdin
			1 => ["pipe", "w"],                                  //stdout
			2 => ["file", static::class . "-error.log", "a"]     //stderr
		];
	}

	/**
	 * Runs a command, passing a string to stdin and returns stdout
	 *
	 * @param $command The command to run
	 * @param $input The string to pass to stdin
	 * @return string The stdout content returned by the application
	 *
	 * @throws Exception on command process spawning failure
	 */
	private static function runCommand ($command, $input) {
		$process = proc_open(
			$command,
			self::getDescriptors(),
			$pipes
		);

		if (!is_resource($process)) {
			throw new Exception("Can't call $command");
		}

		// -> stdin
		fwrite($pipes[0], $input);
		fclose($pipes[0]);

		// <- stdout
		$output = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		proc_close($process);
		return $output;
	}

	///
	/// Methods to call arc call-conduit
	///

	/**
	 * Gets arc command with the relevant parameters
	 *
	 * @return string The arc call-conduit <method> command
	 */
	private function getArcCommand () {
		return "arc call-conduit " . $this->request->getMethod();
	}

	/**
	 * Runs the arc command and stores the reply
	 *
	 * @return $this
	 * @throws Exception on command process spawning failure
	 */
	public function run () {
		$reply = self::runCommand(
			$this->getArcCommand(),
			$this->request->getJSONRepresentation()
		);
		$this->reply = json_decode($reply);
		return $this;
	}

	///
	/// Methods to access to properties
	///

	/**
	 * Gets the reply of the conduit query
	 *
	 * @return object The conduit reply
	 */
	public function getReply () {
		return $this->reply;
	}
}
