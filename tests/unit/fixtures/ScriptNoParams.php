<?php
use Phellow\Cli\Script;

/**
 *
 */
class ScriptNoParams extends Script
{

    protected function initParams()
    {
    }

    /**
     * Run this script.
     *
     * @return void
     */
    protected function run()
    {
        $this->out('simple output');
    }
}