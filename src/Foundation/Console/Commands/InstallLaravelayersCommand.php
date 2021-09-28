<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Laravelayers\Contracts\Auth\User;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Console\Presets\Foundation;

class InstallLaravelayersCommand extends Command
{
    use TraitGeneratorCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelayers:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish frontend scaffolding and install the required NPM packages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $isInstall = !$this->confirm("Do you want to update only assets without installing a preset?");

        if ($isInstall) {
            Foundation::install();
        } else {
            Foundation::updateAssets();
        }

        if ($this->option('no-interaction')
            || $this->confirm("Do you want to install authentication scaffolding now?")
        ) {
            Artisan::call('laravelayers:auth');
        }

        if ($this->option('no-interaction')
            || $this->confirm("Do you want to install admin routes scaffolding now?")
        ) {
            Artisan::call('admin:make-routes');
        }

        $this->info('Laravelayers-foundation scaffolding installed successfully.');

        if ($isInstall) {
            $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
        }
    }
}
