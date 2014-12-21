<?php
namespace Phellow\Cli;

/**
 * @coversDefaultClass \Phellow\Cli\SimpleTable
 */
class SimpleTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testNoBorderNoPadding()
    {
        $tab = new SimpleTable();
        $tab->setHeaders(array('pos', 'desc'));
        $tab->border = false;
        $tab->padding = 0;
        $tab->addRow(array('1', 'one'));
        $tab->addRow(array('2', 'two'));
        $tab->addRow(array('3', 'three'));
        $expected = array(
            'posdesc ',
            '1  one  ',
            '2  two  ',
            '3  three',
        );
        $this->assertEquals($expected, $tab->getTable(true));
    }

    /**
     *
     */
    public function testBorderNoPadding()
    {
        $tab = new SimpleTable();
        $tab->setHeaders(array('pos', 'desc'));
        $tab->border = true;
        $tab->padding = 0;
        $tab->addRow(array('1', 'one'));
        $tab->addRow(array('2', 'two'));
        $tab->addRow(array('3', 'three'));
        $expected = array(
            '+---+-----+',
            '|pos|desc |',
            '+---+-----+',
            '|1  |one  |',
            '|2  |two  |',
            '|3  |three|',
            '+---+-----+',
        );
        $this->assertEquals($expected, $tab->getTable(true));
    }

    /**
     *
     */
    public function testBorderWithPadding()
    {
        $tab = new SimpleTable();
        $tab->setHeaders(array('pos', 'desc'));
        $tab->border = true;
        $tab->padding = 1;
        $tab->addRow(array('1', 'one'));
        $tab->addRow(array('2', 'two'));
        $tab->addRow(array('3', 'three'));
        $expected = array(
            '+-----+-------+',
            '| pos | desc  |',
            '+-----+-------+',
            '| 1   | one   |',
            '| 2   | two   |',
            '| 3   | three |',
            '+-----+-------+',
        );
        $this->assertEquals($expected, $tab->getTable(true));
    }

    /**
     *
     */
    public function testBorderWithMorePaddingNoHeaders()
    {
        $tab = new SimpleTable();
        $tab->border = true;
        $tab->padding = 3;
        $tab->addRow(array('1', 'one'));
        $tab->addRow(array('2', 'two'));
        $tab->addRow(array('3', 'three'));
        $expected = array(
            '+-------+-----------+',
            '|   1   |   one     |',
            '|   2   |   two     |',
            '|   3   |   three   |',
            '+-------+-----------+',
        );
        $this->assertEquals(implode("\n", $expected), $tab->getTable());
    }

    /**
     *
     */
    public function testClean()
    {
        $tab = new SimpleTable();
        $tab->border = true;
        $tab->padding = 0;
        $tab->addRow(array('1', 'one'));
        $tab->clean();
        $tab->addRow(array('2', 'two'));
        $tab->addRow(array('3', 'three'));
        $expected = array(
            '+-+-----+',
            '|2|two  |',
            '|3|three|',
            '+-+-----+',
        );
        $this->assertEquals($expected, $tab->getTable(true));
    }
}