<?php

require '../lib/GetConfiguration.php';

class GetConfigurationTest extends PHPUnit_Framework_TestCase {

    public function testPrintConfiguration () {
        $this->expectOutputString('foo');
        $instance = new GetConfiguration();
        $instance->template = 'foo';
        $instance->printConfiguration();
    }

    public function testSubstituteTemplateVariables () {
        //Empty string
        $this->assertEquals('', GetConfiguration::substituteTemplateVariables(''));

        //Equivalent to an empty string
        $this->assertEquals('', GetConfiguration::substituteTemplateVariables(null));
        $this->assertEquals('', GetConfiguration::substituteTemplateVariables(false));

        //Nothing to substitute
        $this->assertEquals('foo', GetConfiguration::substituteTemplateVariables('foo'));
    }

    /**
     * @requires OS Linux|BSD|CYGWIN|Darwin
     */
    public function testSubstituteExec () {
        $this->assertEquals('', GetConfiguration::substituteExec(''));

        $template = '%%`echo -n ""`%%';
        $this->assertEquals('', GetConfiguration::substituteExec($template));

        $template = '%%`echo ""`%%';
        $this->assertEquals('', GetConfiguration::substituteExec($template));

        $template = 'File is %%`ls GetConfigurationTest.php`%%.';
        $this->assertEquals('File is GetConfigurationTest.php.', GetConfiguration::substituteExec($template));
    }

}
