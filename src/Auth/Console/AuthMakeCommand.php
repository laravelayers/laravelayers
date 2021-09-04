<?php

namespace Laravelayers\Auth\Console;

use Illuminate\Auth\Console\AuthMakeCommand as BaseAuthMakeCommand;
//use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Laravelayers\Contracts\Auth\User;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;

//TODO-WHEN-UPDATING-LARAVEL: to Laravel 6.0, extend the Command class and remove BaseAuthMakeCommand class
//class AuthMakeCommand extends Command
class AuthMakeCommand extends BaseAuthMakeCommand
{
    use TraitGeneratorCommand;

    //TODO-WHEN-UPDATING-LARAVEL: to Laravel 6.0, remove line "{--laravel ... }" and "{--views ... }"
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:auth
                    {--laravel : Scaffold basic login and registration views and routes}
                    {--views : Only scaffold the authentication views}
                    {--force : By default, overwrite existing views, run the migration command, and add routes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold login and registration migrations and routes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //TODO-WHEN-UPDATING-LARAVEL: to Laravel 6.0
        //if (!$this->option('views')) {
        if (!$this->option('laravel') && !$this->option('views')) {
            $this->makeMigration();

            if (strpos(file_get_contents(base_path('routes/web.php')), 'Route::authLayer(') === false) {
                file_put_contents(
                    base_path('routes/web.php'),
                    file_get_contents(__DIR__ . '/stubs/make/routes.stub'),
                    FILE_APPEND
                );
            }

            $this->info('Authentication scaffolding generated successfully.');
        } else {
            //TODO-WHEN-UPDATING-LARAVEL: to Laravel 6.0, remove
            parent::handle();
        }
    }

    /**
     * Make the authentication migration.
     *
     * @return void
     */
    protected function makeMigration()
    {
        if ($this->option('force')
            || $this->option('no-interaction')
            || $this->confirm("Execute the migrate Artisan command?")
        ) {
            $table = resolve(User::class)->getModel()
                ->userActions()
                ->getModel()
                ->getTable();

            if ($this->option('force') || !Schema::hasTable($table)) {
                $this->call('migrate');
            }
        }
    }
}
