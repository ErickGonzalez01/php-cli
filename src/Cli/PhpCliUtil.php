<?php

namespace PhpCli\Cli;

use PhpCli\Exception\PhpCliException;
use PhpCli\Lib\ProgressBar;

class PhpCliUtil
{
    /**
     * File pointer to file being locked
     * 
     * @var type 
     */
    private $lock;

    public function getMemoryUsage()
    {
        $size = memory_get_usage(true);
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * Create a file lock to prevent running on top of
     * another instance of the script
     * 
     * @param type $file 
     */
    protected function obtainLock($file)
    {
        // Don't run on top of another instance
        $this->lock = fopen($file, 'r');
        if ($this->lock === false || !flock($this->lock, LOCK_EX + LOCK_NB, $block) || $block) {
            throw new PhpCliException("Another instance is already running." . PHP_EOL);
            exit(1);
        }
    }

    /**
     * Initialize a progress bar
     * 
     * @param mixed $total   number of times we're going to call set
     * @param int   $message message to prefix the bar with
     * @param int   $options overrides for default options
     * 
     * @static
     */
    public function progressStart($total, $message = null, $options = null)
    {
        echo ProgressBar::start($total, $message = null, $options = null);
    }

    /**
     * Increment the progress bar 
     */
    public function progressNext()
    {
        echo ProgressBar::next();
    }

    /**
     * Start the progress bar indicator
     */
    public function progressStop()
    {
        echo ProgressBar::finish();
    }

    /**
     * Get input from a user
     * 
     * @param  string $message
     * @return string 
     */
    public function promptInput($message)
    {
        fwrite(STDOUT, "$message: ");
        $input = trim(fgets(STDIN));

        return $input;
    }
}
