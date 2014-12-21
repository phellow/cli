<?php
namespace Phellow\Cli;

use InputFake;

require_once __DIR__ . '/fixtures/InputFake.php';

function fgets($handle)
{
    return InputFake::getInput();
}

/**
 * @coversDefaultClass \Phellow\Cli\Input
 */
class InputTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testGet()
    {
        $input = new Input();
        ob_start();
        InputFake::$inputs[] = 'user input';
        $result = $input->get('This is a message');
        $this->assertEquals('This is a message', ob_get_clean());
        $this->assertEquals('user input', $result);
    }

    /**
     *
     */
    public function testConfirm()
    {
        $input = new Input();
        ob_start();
        InputFake::$inputs[] = 'y';
        $result = $input->confirm('What?');
        $this->assertEquals('What?(y|n)', ob_get_clean());
        $this->assertTrue($result);

        ob_start();
        InputFake::$inputs[] = 'n';
        $result = $input->confirm('What?');
        $this->assertEquals('What?(y|n)', ob_get_clean());
        $this->assertFalse($result);

        ob_start();
        InputFake::$inputs[] = 'invalid';
        InputFake::$inputs[] = 'yes';
        $result = $input->confirm('What?', 'yes', 'no');
        $this->assertEquals('What?(yes|no)What?(yes|no)', ob_get_clean());
        $this->assertTrue($result);
    }
}