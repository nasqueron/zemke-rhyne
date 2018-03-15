<?php

/**
 * Zemke-Rhyne
 * Support tools for a Docker <--> Phabricator bridge
 *
 * (c) Sébastien Santoro aka Dereckson, 2014
 * Released under BSD license.
 */

require 'ConduitRequest.php';

/**
 * Class ConduitPassphraseQueryRequest
 *
 * Represents a request to send to conduit
 */
class ConduitPassphraseQueryRequest extends ConduitRequest {
    /**
     * The IDs of the credentials to fetch
     *
     * @var array
     */
    public $ids;

    /**
     * Indicates if the public keys are to be retrieved
     *
     * @var bool
     */
    public $needPublicKeys = false;

    /**
     * Indicates if the private keys or passwords are to be retrieved
     *
     * @var bool
     */
    public $needSecrets = false;

    /**
     * Initializes a new instance of the ConduitPassphraseQueryRequest class
     *
     * @param array $ids The IDs of the credentials to fetch
     * @param bool $needPublicKeys If true, retrieves the public keys
     * @param bool $needSecrets If true, retrieves the private keys and passwords
     */
    public function __construct ($ids, $needPublicKeys = false, $needSecrets = true) {
        $this->ids = $ids;
        $this->needPublicKeys = $needPublicKeys;
        $this->needSecrets = $needSecrets;

        parent::__construct('passphrase.query');
    }
}
