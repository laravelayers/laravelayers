<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class StubPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stub:publish 
                            {--force : Overwrite any existing files} 
                            {--a|admin : Publish all admin stubs that are available for customization}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all stubs that are available for customization';

    /**
     * The directory from which files will be copied.
     *
     * @var string
     */
    protected $dir = __DIR__;

    /**
     * The directory from which the files will be copied.
     *
     * @var string
     */
    protected $to = '/foundation';

    /**
     * String as information output.
     *
     * @var string
     */
    protected $info = 'Stubs published successfully.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->hasOption('admin') && $this->option('admin')) {
            if ($this->option('force')) {
                $options = ['--force' => null];
            }

            $this->call('admin:stub-publish', $options ?? []);
        } else {
            if (!is_dir($stubsPath = $this->laravel->basePath('stubs'))) {
                (new Filesystem)->makeDirectory($stubsPath);
            }

            if (!is_dir($stubsPath = $stubsPath . $this->to)) {
                (new Filesystem)->makeDirectory($stubsPath);
            }

            foreach ($this->getStubs($stubsPath) as $from => $to) {
                if (!file_exists($to) || $this->option('force')) {
                    file_put_contents($to, file_get_contents($from));
                }
            }

            $this->info($this->info);
        }
    }

    /**
     * Get the stubs.
     *
     * @param $path
     * @return array
     */
    public function getStubs($path)
    {
        foreach (glob($this->dir.'/stubs/*.stub') as $file) {
            $files[$file] = $path . '/' . basename($file);
        }

        return $files ?? [];
    }
}
