<?php
namespace Phellow\Cli;

/**
 * @coversDefaultClass \Phellow\Cli\Progress
 */
class ProgressTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testProgress()
    {
        $progress = new Progress(10);
        $progress->width = 10;
        ob_start();
        $progress->show(5);
        $this->assertEquals("\r[====>     ] 5/10 50%", ob_get_clean());
    }

    /**
     * @depends testProgress
     */
    public function testPattern()
    {
        $progress = new Progress(20, '%current%/%total% %bar%');
        $progress->width = 10;
        ob_start();
        $progress->show(8);
        $this->assertEquals("\r8/20 [===>      ]", ob_get_clean());

        $progress = new Progress(20, '%current%');
        ob_start();
        $progress->show(8);
        $this->assertEquals("\r8", ob_get_clean());

        $progress = new Progress(30, '%total%');
        ob_start();
        $progress->show(8);
        $this->assertEquals("\r30", ob_get_clean());

        $progress = new Progress(10, '%percent%');
        ob_start();
        $progress->show(9);
        $this->assertEquals("\r90%", ob_get_clean());

        $progress = new Progress(10, '%bar%');
        $progress->width = 20;
        ob_start();
        $progress->show(5);
        $this->assertEquals("\r[=========>          ]", ob_get_clean());
    }

    /**
     * @depends testPattern
     */
    public function testLineBreak()
    {
        $progress = new Progress(10, '%bar%');
        $progress->width = 10;

        $progress->lineBreakOnFinish = false;
        ob_start();
        $progress->show(10);
        $this->assertEquals("\r[==========]", ob_get_clean());

        $progress->lineBreakOnFinish = true;
        ob_start();
        $progress->show(10);
        $this->assertEquals("\r[==========]\n", ob_get_clean());
    }
}