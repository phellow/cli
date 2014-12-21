<?php
namespace Phellow\Cli;

/**
 * Builds a simple command line table.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/cli
 */
class SimpleTable
{
    /**
     * The left and right padding of a field.
     *
     * @var int
     */
    public $padding = 1;

    /**
     * Indicates if border should be displayed.
     *
     * @var bool
     */
    public $border = true;

    /**
     * All table rows.
     *
     * @var array
     */
    private $rows = [];

    /**
     * All column lengths.
     *
     * @var array
     */
    private $lengths = [];

    /**
     * Indicates if first line is header.
     *
     * @var bool
     */
    private $showHeader = false;

    /**
     * Set the headers.
     *
     * @param array $headers
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->showHeader = true;
        $rows = $this->rows;
        $this->rows = [];
        $this->addRow($headers);
        $this->rows = array_merge($this->rows, $rows);
    }

    /**
     * Add a row.
     *
     * @param array $row
     *
     * @return void
     */
    public function addRow(array $row)
    {
        foreach ($row as $key => $field) {
            $length = strlen($field);
            if (!isset($this->lengths[$key]) || $length > $this->lengths[$key]) {
                $this->lengths[$key] = $length;
            }
        }
        $this->rows[] = $row;
    }

    /**
     * Remove all rows.
     *
     * @return void
     */
    public function clean()
    {
        $this->rows = [];
        $this->lengths = [];
    }

    /**
     * Get the table output.
     *
     * @param bool $asArray
     *
     * @return string|array
     */
    public function getTable($asArray = false)
    {
        $output = [];
        if ($this->border) {
            $borderRow = '+';
            foreach ($this->lengths as $length) {
                $padding = str_repeat('-', $this->padding);
                $borderRow .= $padding;
                $borderRow .= str_repeat('-', $length);
                $borderRow .= $padding . '+';
            }
            $output[] = $borderRow;
        } else {
            $borderRow = null;
        }

        foreach ($this->rows as $idx => $row) {
            $fields = [];
            foreach ($row as $key => $field) {
                $length = $this->lengths[$key];
                $format = '%-' . $length . 's';
                $fields[] = sprintf($format, $field);
            }
            $padding = str_repeat(' ', $this->padding);
            if ($this->border) {
                $left = '|' . $padding;
                $middle = $padding . '|' . $padding;
                $right = $padding . '|';
            } else {
                $left = '';
                $middle = $padding;
                $right = '';
            }
            $row = $left . implode($middle, $fields) . $right;
            $output[] = $row;

            if ($this->showHeader && $idx == 0 && $borderRow) {
                $output[] = $borderRow;
            }
        }
        if ($borderRow) {
            $output[] = $borderRow;
        }

        return $asArray ? $output : implode("\n", $output);
    }
}
