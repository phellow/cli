<?php
namespace Phellow\Cli;

/**
 * @coversDefaultClass \Phellow\Cli\Parameters
 */
class ParametersTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testRegisteredArguments()
    {
        $params = new Parameters();
        $params->registerArgument('testA', 'first test');
        $params->registerArgument('testB', 'second test');
        $args = $params->getRegisteredArguments();
        $expected = array(
            array(
                'name'        => 'testA',
                'description' => 'first test',
            ),
            array(
                'name'        => 'testB',
                'description' => 'second test',
            )
        );
        $this->assertEquals($expected, $args);
    }

    /**
     *
     */
    public function testRegisteredOptions()
    {
        $params = new Parameters();
        $params->registerOption('first', 'f', Parameters::VALUE_YES, 'desc 1');
        $params->registerOption('second', null, Parameters::VALUE_NO, 'desc 2');
        $params->registerOption('third', null, Parameters::VALUE_OPTIONAL);
        $options = $params->getRegisteredOptions();
        $expected = array(
            array(
                'name'        => 'first',
                'short'       => 'f',
                'valueFlag'   => Parameters::VALUE_YES,
                'description' => 'desc 1',
            ),
            array(
                'name'        => 'second',
                'short'       => null,
                'valueFlag'   => Parameters::VALUE_NO,
                'description' => 'desc 2',
            ),
            array(
                'name'        => 'third',
                'short'       => null,
                'valueFlag'   => Parameters::VALUE_OPTIONAL,
                'description' => null,
            ),
        );
        $this->assertEquals($expected, $options);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidOption()
    {
        $params = new Parameters();
        $params->registerOption('test', 'test', Parameters::VALUE_YES);
    }

    /**
     * @depends testRegisteredArguments
     * @depends testRegisteredOptions
     */
    public function testParse()
    {
        $params = new Parameters();
        $params->registerArgument('action');
        $params->registerOption('help', 'h', Parameters::VALUE_NO);
        $params->registerOption('verbose', 'v', Parameters::VALUE_NO);
        $params->registerOption('no', null, Parameters::VALUE_NO);
        $params->registerOption('name', null, Parameters::VALUE_YES);
        $params->registerOption('name2', null, Parameters::VALUE_YES);
        $params->registerOption('opt1', null, Parameters::VALUE_OPTIONAL);
        $params->registerOption('opt2', null, Parameters::VALUE_OPTIONAL);

        $_SERVER['argv'] = array(
            'file.php',
            'doIt',
            '-hv',
            '--name=Christian',
            '--opt1',
            '--opt2=value',
        );

        $params->parse();
        $this->assertEquals('file.php', $params->getExecutedFile());
        $this->assertEquals('doIt', $params->get('action'));
        $this->assertTrue($params->get('help'));
        $this->assertTrue($params->get('verbose'));
        $this->assertNull($params->get('no'));
        $this->assertEquals('Christian', $params->get('name'));
        $this->assertNull($params->get('name2'));
        $this->assertTrue($params->get('opt1'));
        $this->assertEquals('value', $params->get('opt2'));
        $this->assertNull($params->get('nope'));
    }

    /**
     * @depends testParse
     * @expectedException \Exception
     */
    public function testParseUnregisteredOption()
    {
        $params = new Parameters();

        $_SERVER['argv'] = array(
            'file.php',
            '--nope',
        );
        $params->parse();
    }

    /**
     * @depends testParse
     * @expectedException \Exception
     */
    public function testParseUnregisteredShortOption()
    {
        $params = new Parameters();

        $_SERVER['argv'] = array(
            'file.php',
            '-h',
        );
        $params->parse();
    }

    /**
     * @depends testParse
     */
    public function testParseIgnoreRegistered()
    {
        $params = new Parameters();
        $params->ignoreRegisteredOptions = true;

        $_SERVER['argv'] = array(
            'file.php',
            '-h',
            '--name=val',
        );
        $params->parse();
        $this->assertTrue($params->get('h'));
        $this->assertEquals('val', $params->get('name'));
    }

    /**
     * @depends testParse
     * @expectedException \Exception
     */
    public function testParseErrorOptionHasValue()
    {
        $params = new Parameters();
        $params->registerOption('help', null, Parameters::VALUE_NO);

        $_SERVER['argv'] = array(
            'file.php',
            '--help=val',
        );
        $params->parse();
    }

    /**
     * @depends testParse
     * @expectedException \Exception
     */
    public function testParseErrorOptionHasNoValue()
    {
        $params = new Parameters();
        $params->registerOption('name', null, Parameters::VALUE_YES);

        $_SERVER['argv'] = array(
            'file.php',
            '--name',
        );
        $params->parse();
    }

    /**
     * @depends testParse
     * @expectedException \Exception
     */
    public function testParseInvalidValueFlag()
    {
        $params = new Parameters();
        $params->registerOption('name', null, 99);

        $_SERVER['argv'] = array(
            'file.php',
            '--name',
        );
        $params->parse();
    }

    /**
     * @depends testParse
     */
    public function testArgs()
    {
        $params = new Parameters();

        $_SERVER['argv'] = array(
            'file.php',
            'one',
            'two',
        );
        $params->parse();
        $this->assertEquals('one', $params->get(0));
        $this->assertEquals('two', $params->get(1));
        $this->assertNull($params->get(2));

        $params->argumentStartIndex = 1;
        $params->parse();
        $this->assertEquals('two', $params->get(0));
        $this->assertNull($params->get(1));
        $params->argumentStartIndex = 0;

        $params->registerArgument('first');
        $params->parse();
        $this->assertEquals('one', $params->get('first'));
        $this->assertEquals('two', $params->get(0));
        $this->assertNull($params->get(1));

        $params->argumentStartIndex = 1;
        $params->parse();
        $this->assertEquals('two', $params->get('first'));
        $this->assertNull($params->get(0));
    }

    /**
     * @depends testParse
     * @expectedException \Exception
     */
    public function testGetMandatory()
    {
        $params = new Parameters();
        $params->registerOption('name', null, Parameters::VALUE_YES);

        $_SERVER['argv'] = array(
            'file.php',
        );

        $params->get('name', true);
    }

    /**
     * @depends testParse
     */
    public function testGetAll()
    {
        $params = new Parameters();
        $params->registerOption('help', 'h', Parameters::VALUE_NO);
        $params->registerOption('name', null, Parameters::VALUE_YES);

        $_SERVER['argv'] = array(
            'file.php',
            '-h',
            '--name=Christian',
        );

        $params->parse();
        $expected = array(
            'help' => true,
            'name' => 'Christian',
        );
        $this->assertEquals($expected, $params->getAll());
    }
}