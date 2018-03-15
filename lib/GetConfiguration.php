<?php

/**
 * Zemke-Rhyne
 * Support tools for a Docker <--> Phabricator bridge
 *
 * (c) Sébastien Santoro aka Dereckson, 2015
 * Released under BSD license.
 */

/**
 * Class GetConfiguration
 *
 * Allows to get configuration from a template
 */
class GetConfiguration {
    ///
    /// Properties
    ///

    /**
     * The configuration template content
     * @var string
     */
    public $template;

    ///
    /// Templating methods
    ///

    /**
     * Prints configuration from specified configuration template
     * replacing expressions by relevant variables and credentials.
     *
     * @return string the configuration
     */
    public function printConfiguration () {
        echo static::substituteTemplateVariables($this->template);
    }

    /**
     * Substitutes template variables in the specified text
     *
     * @param string $text The template text
     * @return string The substitued text
     */
    public static function substituteTemplateVariables ($text) {
        $text = static::substituteExec($text);
        return $text;
    }

    /**
     * Substitutes %%`command`%% by the output of the command.
     *
     * @param string $text The template text
     * @return string The substitued text
     */
    public static function substituteExec ($text) {
        return preg_replace_callback(
            '/%%`(.*)`%%/',
            function ($matches) {
                return trim(`$matches[1]`);
            },
            $text
        );
    }

    ///
    /// Script procedural code
    ///

    /**
     *  Runs the script
     */
    public static function run ($file) {
        $instance = new self;
        $instance->template = file_get_contents($file);
        $instance->printConfiguration();
    }
}
