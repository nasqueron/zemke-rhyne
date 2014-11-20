<?php

require 'ConduitRequest.php';

class ConduitPassphraseQueryRequest extends ConduitRequest {
	public function __construct ($ids, $needPublicKeys = false, $needSecrets = true) {
		$this->ids = $ids;
		$this->needPublicKeys = $needPublicKeys;
		$this->needSecrets = $needSecrets;

		parent::__construct('passphrase.query');
	}
}
