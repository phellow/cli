<?php
namespace Phellow\Cli;

use ScriptNoParams;
use ScriptWithParams;

require_once __DIR__ . '/fixtures/ScriptNoParams.php';
require_once __DIR__ . '/fixtures/ScriptWithParams.php';

/**
 * @coversDefaultClass \Phellow\Cli\Script
 */
class ScriptTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testScriptWithNoParams()
    {
        ob_start();
        $script = new ScriptNoParams();
        $return = $script->start();
        $output = ob_get_clean();
        $this->assertEquals(0, $return);
        $this->assertEquals('simple output', $output);
    }

    /**
     *
     */
    public function testScriptWithNoRegisteredButGivenParams()
    {
        $_SERVER['argv'] = array('--test');
        ob_start();
        $script = new ScriptNoParams();
        $return = $script->start();
        $output = ob_get_clean();
        $this->assertEquals(0, $return);
        $this->assertEquals('simple output', $output);
    }

    /**
     *
     */
    public function testScriptWithNoRegisteredParamsAndHelpOption()
    {
        $params = new Parameters();
        $params->ignoreRegisteredOptions = true;
        $_SERVER['argv'] = array('path/to/file.php', '--help');
        ob_start();
        $script = new ScriptWithParams($params);
        $return = $script->start();
        $output = ob_get_clean();
        $this->assertEquals(0, $return);
        $expected = "Usage: file.php\n\nOptions:\n--help     Show help\n";
        $this->assertEquals($expected, $output);
    }

    /**
     *
     */
    public function testScriptWithRegisteredParamsAndHelpOption()
    {
        $params = new Parameters();
        $params->registerArgument('one', 'first arg');
        $params->registerArgument('two', 'second arg');
        $params->registerOption('opt1', 'o', Parameters::VALUE_NO, 'first opt');
        $params->registerOption('opt2', null, Parameters::VALUE_YES);
        $params->registerOption('opt3', null, Parameters::VALUE_OPTIONAL);
        $_SERVER['argv'] = array('path/to/file.php', '--help');
        ob_start();
        $script = new ScriptWithParams($params);
        $return = $script->start();
        $output = ob_get_clean();
        $this->assertEquals(0, $return);
        $expected = "Usage: file.php <one> <two>\n\n" .
            "Arguments:      \n" .
            "one                first arg \n" .
            "two                second arg\n\n" .
            "Options:        \n" .
            "--opt1 -o          first opt \n" .
            "--opt2=<value>               \n" .
            "--opt3[=<value>]             \n" .
            "--help             Show help \n";
        $this->assertEquals($expected, $output);
    }

    /**
     *
     */
    public function testScriptWithParamsException()
    {
        $params = new Parameters();
        $_SERVER['argv'] = array('file.php', '--test');
        ob_start();
        $script = new ScriptWithParams($params);
        $return = $script->start();
        $output = ob_get_clean();
        $this->assertEquals(1, $return);
    }
}