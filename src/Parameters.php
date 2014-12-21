<?php
namespace Phellow\Cli;

/**
 * Handles all command line options and arguments.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/cli
 */
class Parameters
{
    const VALUE_NO = 0;
    const VALUE_YES = 1;
    const VALUE_OPTIONAL = 2;

    /**
     * True to ignore registered options and parse all given parameters.
     *
     * @param bool
     */
    public $ignoreRegisteredOptions = false;

    /**
     * Starts to parse arguments from this index.
     *
     * @param int
     */
    public $argumentStartIndex = 0;

    /**
     * Registered arguments.
     *
     * @var array
     */
    protected $registerdArgs = [];

    /**
     * Registered option names.
     *
     * @var array
     */
    protected $registerdOptNames = [];

    /**
     * Registered option shorts.
     *
     * @var array
     */
    protected $registerdOptShorts = [];

    /**
     * Registered option descriptions.
     *
     * @var array
     */
    protected $registerdOptDescriptions = [];

    /**
     * The executed file.
     *
     * @var string
     */
    protected $executedFile;

    /**
     * Parsed parameters.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Register an argument.
     *
     * @param string $name
     * @param string $description
     *
     * @return void
     */
    public function registerArgument($name, $description = null)
    {
        $this->registerdArgs[$name] = $description;
    }

    /**
     * Register an option.
     *
     * @param string $name        The name of the option.
     * @param string $short       The short name of the option.
     * @param int    $valueFlag   Indicates if option has a value.
     * @param string $description The description.
     *
     * @return void
     */
    public function registerOption($name, $short = null, $valueFlag = self::VALUE_NO, $description = null)
    {
        $this->registerdOptNames[$name] = $valueFlag;
        if ($short) {
            if (strlen($short) > 1) {
                throw new \Exception('Short option name must have only one letter');
            }
            $this->registerdOptShorts[$short] = $name;
        }
        if ($description) {
            $this->registerdOptDescriptions[$name] = $description;
        }
    }

    /**
     * Parse options and arguments.
     *
     * @return void
     */
    public function parse()
    {
        $i = 0;
        $nextIndex = 0;
        $this->parameters = [];
        $possibleArgs = array_keys($this->registerdArgs);

        if (isset($_SERVER['argv'])) {
            foreach ($_SERVER['argv'] as $arg) {
                $matches = [];
                if (preg_match('/^(-{1,2})([a-z0-9_-]+)(?:|(=)(.*))$/i', $arg, $matches)) {
                    $isShort = $matches[1] == '-';
                    $key = $matches[2];
                    $valueGiven = isset($matches[3]);
                    $value = isset($matches[4]) ? $matches[4] : null;

                    if ($isShort) {
                        $names = str_split($key);
                    } else {
                        $names = array($key);
                    }

                    foreach ($names as $name) {
                        if ($isShort) {
                            if (isset($this->registerdOptShorts[$name])) {
                                $name = $this->registerdOptShorts[$name];
                            } elseif (!$this->ignoreRegisteredOptions) {
                                throw new \Exception('unregistered short option -' . $name);
                            }
                        }

                        if (!isset($this->registerdOptNames[$name]) && !$this->ignoreRegisteredOptions) {
                            throw new \Exception('unregistered option --' . $name);
                        }

                        if ($this->ignoreRegisteredOptions) {
                            $this->parameters[$name] = $valueGiven ? $value : true;
                        } else {
                            $valueFlag = $this->registerdOptNames[$name];
                            switch ($valueFlag) {
                                case self::VALUE_NO:
                                    if ($valueGiven) {
                                        throw new \Exception(
                                            'the option --' . $name . ' must not have a value. Use only --' . $name
                                        );
                                    }
                                    $this->parameters[$name] = true;
                                    break;

                                case self::VALUE_YES:
                                    if (!$valueGiven) {
                                        throw new \Exception(
                                            'the option --' . $name . ' must have a value. Use --' . $name . '=<value>'
                                        );
                                    }
                                    $this->parameters[$name] = $value;
                                    break;

                                case self::VALUE_OPTIONAL:
                                    $this->parameters[$name] = $valueGiven ? $value : true;
                                    break;

                                default:
                                    throw new \Exception('unknown value flag for option --' . $name);
                            }
                        }
                    }
                } else {
                    if ($i == 0) {
                        $this->executedFile = $arg;
                    } elseif ($i > $this->argumentStartIndex) {
                        $key = $i - 1 - $this->argumentStartIndex;
                        if (isset($possibleArgs[$key])) {
                            $this->parameters[$possibleArgs[$key]] = $arg;
                        } else {
                            $this->parameters[$nextIndex++] = $arg;
                        }
                    }
                    $i++;
                }
            }
        }
    }

    /**
     * Get option or argument.
     *
     * @param string|int $name      Name or index of the option or argument.
     * @param bool       $mandatory True to fail if no value exists.
     *
     * @return string
     */
    public function get($name, $mandatory = false)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
        if ($mandatory) {
            throw new \Exception('no value given for parameter "' . $name . '"');
        }
        return null;
    }

    /**
     * Get all options and arguments.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->parameters;
    }

    /**
     * Get the executed file.
     *
     * @return string
     */
    public function getExecutedFile()
    {
        return $this->executedFile;
    }

    /**
     * Get all registered arguments.
     *
     * @return array
     */
    public function getRegisteredArguments()
    {
        $arguments = [];
        foreach ($this->registerdArgs as $name => $description) {
            $arguments[] = array(
                'name'        => $name,
                'description' => $description,
            );
        }
        return $arguments;
    }

    /**
     * Get all registered options.
     *
     * @return array
     */
    public function getRegisteredOptions()
    {
        $options = [];
        $shorts = array_flip($this->registerdOptShorts);
        foreach ($this->registerdOptNames as $name => $valueFlag) {
            $options[] = array(
                'name'        => $name,
                'short'       => isset($shorts[$name]) ? $shorts[$name] : null,
                'valueFlag'   => $valueFlag,
                'description' => isset($this->registerdOptDescriptions[$name]) ?
                    $this->registerdOptDescriptions[$name] :
                    null,
            );
        }
        return $options;
    }
}
