<?php
use Phellow\Cli\Parameters;
use Phellow\Cli\Script;

/**
 *
 */
class ScriptWithParams extends Script
{

    private $parameters = null;

    /**
     *
     */
    public function __construct(Parameters $params)
    {
        $this->parameters = $params;
    }

    /**
     *
     */
    protected function initParams()
    {
        return $this->parameters;
    }

    /**
     *
     */
    protected function run()
    {
        $this->outln('output line');
    }
}