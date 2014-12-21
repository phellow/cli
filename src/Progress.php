<?php
namespace Phellow\Cli;

/**
 * Creates a progress bar.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/cli
 */
class Progress
{
    /**
     * If true, a line break will be added if progress is finished.
     *
     * @var bool
     */
    public $lineBreakOnFinish = true;

    /**
     * The total number.
     *
     * @var float
     */
    public $total = 0;

    /**
     * The width of the progress bar.
     *
     * @var integer
     */
    public $width = 30;

    /**
     * @var string
     */
    protected $pattern = null;

    /**
     * You can change the style of the progress bar by setting the pattern parameter.
     * The pattern is a string with different markups. The following markups are possible:
     * %bar%     => The progress bar (e.g. "[====>  ]")
     * %percent% => The current percent (e.g. "50%")
     * %current% => The current number (e.g. "1")
     * %total%   => The total number (e.g. "10")
     *
     * The pattern "%bar% %current%/%total% %percent%" would display a progress bar like:
     * "[==========>         ] 6/10 60%"
     *
     * @param float  $total
     * @param string $pattern
     */
    public function __construct($total, $pattern = null)
    {
        $this->total = $total;
        if ($pattern === null) {
            $pattern = '%bar% %current%/%total% %percent%';
        }
        $this->pattern = $pattern;
    }

    /**
     * Show the progress bar of the given number.
     *
     * @param float $number
     *
     * @return void
     */
    public function show($number)
    {
        $percent = $number / $this->total;
        $percent = max(0, $percent);
        $percent = min(1, $percent);

        $progress = floor($percent * $this->width);
        $remaining = $this->width - $progress;

        $line = "\r" . $this->pattern;

        $bar = '[' . str_repeat('=', $progress - 1) . ($remaining > 0 ? '>' : '=') . str_repeat(' ', $remaining) . ']';
        $line = str_replace('%bar%', $bar, $line);
        $line = str_replace('%percent%', round($percent * 100) . '%', $line);
        $line = str_replace('%current%', $number, $line);
        $line = str_replace('%total%', $this->total, $line);

        if ($this->lineBreakOnFinish && $remaining <= 0) {
            $line .= "\n";
        }

        echo $line;
    }
}
