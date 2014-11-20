<?php

require 'ConduitPassphraseQueryRequest.php';

/**
 * Get public keys allowed to connect to the account
 */
class GetPublicKeys {
	/**
	 * The directory where data is stored
	 *
	 * @const string
	 */
	const DATA_DIRECTORY = 'data';

	/**
	 * Gets the path to the servers data file
	 *
	 * @return string The path to the servers data file
	 */
	public static function getServersDataFile () {
		return self::getDataFile('servers.json');
	}

	/**
	 * Gets the path to the specified data file
	 *
	 * @param string $filename The file name to get the path of
	 * @return string The path to the data file
	 */
	public static function getDataFile ($filename) {
		return getenv("HOME")
		       . '/' . self::DATA_DIRECTORY
		       . '/' . $filename;
	}

	/**
	 * Gets SSH public key from DevCentral
	 *
	 * @param int $id The id to the credential stored in passphrase
	 * @return string The SSH public key
	 */
	public static function getPublicKey ($id) {
		$request = new ConduitPassphraseQueryRequest(
			[$id], true, false
		);
		$reply = $request
		         ->query()
		         ->getReply();

		//Gets the first property of the response.data object,
		//then the material.publicKey property
		$data = (Array)$reply->response->data;
		$firstDataItem = array_shift($data);

		$key = $firstDataItem->material->publicKey;
		return trim($key);
	}

	/**
	 * Gets a .ssh/authorized_keys line to give access to the specified server
	 *
	 * @param object $server The server to give access to
	 * @return string The line to add in authorized_keys file to give access to this server
	 *
	 * The expected object has the following properties:
	 *     - int key: the id of the SSH key to fetch from DevCentral
	 *     - array allowedConnectionFrom: an array of the hosts masks to allow to connect, each item a string (mandatory)
	 *     - string|null restrictCommand: if not null, restrict the connection to a single command, automatically executed
	 *     - string comment: the SSH key comment
	 */
	public static function getAuthorizedKey ($server) {
		$line = "";

		//Options
		if ($server->restrictCommand !== null) {
			$line .= self::getAuthorizedKeyCommandOption($server->restrictCommand); 
			$line .= ',';
		}
		$line .= self::getAuthorizedKeyFrom($server->allowedConnectionFrom);
		$line .= ',';
		$line .= self::getAuthorizedKeyRestrictOptions();
		$line .= ' ';

		//Key
		$line .= self::getPublicKey($server->key);
		$line .= ' ';
		$line .= $server->comment;

		return $line;
	}

	/**
	 * Gets command authorized_keys option
	 *
	 * @param string The command to execute on login
	 * @return string The command option
	 */
	public static function getAuthorizedKeyCommandOption ($command) {
		return 'command="' . $command . '"';
	}

	/**
	 * Gets restriction authorized_keys options
	 *
	 * @return string A list of options restricting access
	 */
	public static function getAuthorizedKeyRestrictOptions () {
		return "no-port-forwarding,no-x11-forwarding,no-agent-forwarding";
	}

	/**
	 * Gets authorized_keys from expression
	 *
	 * @param array $patterns a list of pattern matching the authorized_keys from format
	 * @return string the from authorized_keys expression
	 */
	public static function getAuthorizedKeyFrom ($patterns) {
		return "from=\""
		     . implode(',', $patterns)
		     . '"';
	}

	/**
	 * Prints a .ssh/authorized_keys file to give access to the specified servers
	 *
	 * @param array $servers The list of the servers needing to access this account
	 */
	public static function printAuthorizedKeys ($servers) {
		foreach ($servers as $server) {
			echo self::getAuthorizedKey($server);
			echo "\n";
		}
	}

	/**
	 *  Runs the script
	 */
	public static function run () {
		$file = self::getServersDataFile();
		$data = json_decode(file_get_contents($file));
		self::printAuthorizedKeys($data);
	}
}
