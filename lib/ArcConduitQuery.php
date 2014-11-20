<?php

class ArcConduitQuery {
	private $request;
	private $reply = null;

	public function __construct ($request) {
		$this->request = $request;
	}

	private static function getDescriptors () {
		return [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["file", static::class . "-error.log", "a"],
		];
	}

	private function getArCcommand () {
		return "arc call-conduit " . $this->request->getMethod();
	}

	public function run () {
		$reply = self::runCommand(
			$this->getArCcommand(),
			$this->request->getJSONRepresentation()
		);
		$this->reply = json_decode($reply);
		return $this;
	}

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

	public function getReply () {
		return $this->reply;
	}
}
