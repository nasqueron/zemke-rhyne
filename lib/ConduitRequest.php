<?php

/**
 * Zemke-Rhyne
 * Support tools for a Docker <--> Phabricator bridge
 *
 * (c) Sébastien Santoro aka Dereckson, 2014
 * Released under BSD license.
 */

require 'ArcConduitQuery.php';

/**
 * Class ConduitRequest
 *
 * Represents a Conduit request
 */
class ConduitRequest {
    /**
     * The conduit method to call
     *
     * @var string
     */
    private $method;

    /**
     * Initializes a new instance of the ConduitRequest class
     *
     * @param $method The conduit method to call
     */
    public function __construct ($method) {
        $this->method = $method;
    }

    /**
     * Queries conduit
     *
     * @return $this
     */
    public function query () {
        return (new ArcConduitQuery($this))->run();
    }

    /**
     * Gets the conduit method to call
     *
     * @return string The conduit method
     */
    public function getMethod () {
        return $this->method;
    }

    /**
     * Gets the JSON representation of the request
     *
     * @return string The JSON representation
     */
    public function getJSONRepresentation () {
        return json_encode($this);
    }
}
