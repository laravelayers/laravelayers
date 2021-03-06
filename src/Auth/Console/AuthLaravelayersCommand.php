<?php

namespace Laravelayers\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Laravelayers\Contracts\Auth\User;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;

class AuthLaravelayersCommand extends Command
{
    use TraitGeneratorCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelayers:auth';

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
        $this->makeMigration();

        if (strpos(file_get_contents(base_path('routes/web.php')), 'Route::authLayer(') === false) {
            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__ . '/stubs/make/routes.stub'),
                FILE_APPEND
            );
        }

        $this->info('Authentication scaffolding generated successfully.');
    }

    /**
     * Make the authentication migration.
     *
     * @return void
     */
    protected function makeMigration()
    {
        $table = resolve(User::class)->getModel()
            ->userActions()
            ->getModel()
            ->getTable();

        if (!Schema::hasTable($table)) {
            $this->call('migrate');
        }
    }
}
