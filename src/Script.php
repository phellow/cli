<?php
namespace Phellow\Cli;

/**
 * Helps to quickly build cli scripts.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/cli
 */
abstract class Script
{
    /**
     * @var Parameters
     */
    protected $params = null;

    /**
     * Initialize parameters object.
     *
     * This method can return an object of Phellow\Cli\Parameters to
     * register options and arguments. This object can then
     * be accessed via $this->params. If no value is returned, an
     * empty object will be created.
     *
     * @return Parameters|null
     */
    abstract protected function initParams();

    /**
     * Run this script.
     *
     * @return void
     */
    abstract protected function run();

    /**
     * Print out a message.
     *
     * @param string $message
     *
     * @return void
     */
    protected function out($message)
    {
        echo $message;
    }

    /**
     * Print out a line.
     *
     * @param string $line
     *
     * @return void
     */
    protected function outln($line)
    {
        echo $line . "\n";
    }

    /**
     * Start this script.
     *
     * @return int Exit code.
     */
    public function start()
    {
        try {
            $this->params = $this->initParams();

            if (!$this->params instanceof Parameters) {
                $this->params = new Parameters();
                $this->params->ignoreRegisteredOptions = true;
            }
            $this->params->registerOption('help', null, Parameters::VALUE_NO, 'Show help');
            $this->params->parse();

            if ($this->params->get('help')) {
                $this->showHelp($this->params);
            } else {
                $this->run();
            }
            return 0;
        } catch (\Exception $ex) {
            $this->handleException($ex);
            return 1;
        }
    }

    /**
     * Show the help message.
     *
     * @param Parameters $params
     *
     * @return void
     */
    protected function showHelp(Parameters $params)
    {
        $table = new SimpleTable();
        $table->border = false;
        $table->padding = 3;

        $executedFile = basename($params->getExecutedFile());

        $arguments = $params->getRegisteredArguments();
        $argsUsage = '';
        $argsRows = [];
        foreach ($arguments as $arg) {
            $argsUsage .= ' <' . $arg['name'] . '>';
            $argsRows[] = [$arg['name'], $arg['description']];
        }
        $this->outln('Usage: ' . $executedFile . $argsUsage);

        // show possible arguments
        if ($argsRows) {
            $table->addRow([]);
            $table->addRow(['Arguments:']);
            foreach ($argsRows as $row) {
                $table->addRow($row);
            }
        }

        // show possible options
        $options = $params->getRegisteredOptions();
        if ($options) {
            $table->addRow([]);
            $table->addRow(['Options:']);
            foreach ($options as $opt) {
                $val = '';
                switch ($opt['valueFlag']) {
                    case Parameters::VALUE_YES:
                        $val = '=<value>';
                        break;
                    case Parameters::VALUE_OPTIONAL:
                        $val = '[=<value>]';
                        break;
                }
                $def = '--' . $opt['name'] . $val;
                if ($opt['short']) {
                    $def .= ' -' . $opt['short'] . $val;
                }
                $table->addRow([$def, $opt['description']]);
            }
        }

        $this->outln($table->getTable());
    }

    /**
     * Handles the thrown Exception.
     *
     * @param \Exception $exception The exception thrown.
     *
     * @return void
     */
    protected function handleException(\Exception $exception)
    {
        $code = $exception->getCode();
        $output = 'Error' . ($code ? ' (' . $code . ')' : '') . ': ' . $exception->getMessage();
        $this->outln($output);
    }
}
