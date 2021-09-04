<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class JsMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new plugin in /resources/js/plugins';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = trim($this->argument('name'));
        $name = preg_replace('/^(.*).js$/', '$1', $name);

        if ($this->option('app')) {
            $name = basename($name);
        }

        $dir = base_path("resources/js/");

        if (substr($name, 0, 1) != '/') {
            $dir .= "plugins/";
        }

        if ($this->option('app')) {
            $name = basename($name);
        }

        $dir .= trim(trim(dirname($name),  '.'), '/');

        $name = Str::camel(basename($name));

        $file = rtrim($dir, '/') . "/{$name}.js";

        if (!file_exists(dirname($file))) {
            File::makeDirectory(dirname($file.'/'), 0755, true);
        }

        if ($this->option('app')) {
            $entries = base_path('resources/js/plugins/plugins.js');

            $content =
                "import { DummyName } from './dummyName.js';\n" .
                "Foundation.plugin(DummyName, 'DummyName');\n";

            if (file_exists($entries)) {
                $content = "\n{$content}";
            }

            if (!file_exists($entries) || strpos(file_get_contents($entries), "{$name}") === false) {
                file_put_contents($entries, $this->replaceName($content, $name), FILE_APPEND);

                $this->info('The entries file scaffolding generated successfully.');
            }

            $appFile = base_path("resources/js/app.js");

            if (strpos(file_get_contents($appFile), './plugins/plugins.js') === false) {
                file_put_contents(
                    base_path('./resources/js/app.js'),
                    "\nrequire('./plugins/plugins.js');\n",
                    FILE_APPEND
                );

                $this->info('The entries file is included in the app.js.');
            }
        }

        $stub = '/stubs/js.plugin.stub';
        $stub = file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;

        if (!file_exists($file)) {
            $content = file_get_contents($stub);

            if (!empty($entries)) {
                $content = str_replace("Foundation.plugin(DummyName, 'DummyName');\n", '', $content);
            }

            $content = $this->replaceName($content, $name);

            file_put_contents($file, $content, FILE_APPEND);

            $this->info('Js plugin scaffolding generated successfully.');
        } else {
            $this->info('Js plugin scaffolding have already been generated.');
        }
    }

    /**
     * Replace the name for the given string.
     *
     * @param string $string
     * @param string $name
     * @return string
     */
    protected function replaceName($string, $name)
    {
        return str_replace(
            ['dummyName', 'DummyName'],
            [$name, ucfirst($name)],
            $string
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the plugin']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['app', null, InputOption::VALUE_NONE, 'Include the plugin in /resources/js/app.js.']
        ];
    }
}
