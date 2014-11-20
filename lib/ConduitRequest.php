<?php

require 'ArcConduitQuery.php';

class ConduitRequest {
	private $method;

	public function __construct ($method) {
		$this->method = $method;
	}

	public function getMethod () {
		return $this->method;
	}

	public function query () {
		return (new ArcConduitQuery($this))->run();
	}

	public function getJSONRepresentation () {
		return json_encode($this);
	}
}
