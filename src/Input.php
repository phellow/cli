<?php
namespace Phellow\Cli;

/**
 * Handles command line inputs.
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/cli
 */
class Input
{

    /**
     * Force the user to input a text.
     *
     * @param string $message Massage to display before user can type.
     *
     * @return string
     */
    public function get($message = null)
    {
        if ($message) {
            echo $message;
        }
        return trim(fgets(STDIN));
    }

    /**
     * Force the user to input yes or no.
     *
     * If user inputs another text than yes or no,
     * this method will ask again for an answer.
     *
     * @param string $message Massage to display before user can type.
     * @param string $yes     Answer for yes.
     * @param string $no      Answer for no.
     *
     * @return bool
     */
    public function confirm($message = null, $yes = 'y', $no = 'n')
    {
        $input = $this->get($message . '(' . $yes . '|' . $no . ')');
        if ($input == $yes) {
            return true;
        } elseif ($input == $no) {
            return false;
        }
        return $this->confirm($message, $yes, $no);
    }
}
