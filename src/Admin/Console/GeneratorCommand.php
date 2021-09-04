<?php

namespace Laravelayers\Admin\Console;

trait GeneratorCommand
{
    /**
     * Initialize the custom stub path.
     *
     * @return string
     */
    protected function initCustomStubPath()
    {
        return 'stubs/admin';
    }

    /**
     * Write a string as information output.
     *
     * @param  string  $string
     * @param  null|int|string  $verbosity
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        $this->line("Admin {$string}", 'info', $verbosity);
    }
}
