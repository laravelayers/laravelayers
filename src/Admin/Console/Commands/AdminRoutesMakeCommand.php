<?php

namespace Laravelayers\Admin\Console\Commands;

use Illuminate\Console\Command;

class AdminRoutesMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:make-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic admin routes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $stub = '/stubs/admin.routes.stub';
        $stub = file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;

        if (strpos(file_get_contents(base_path('routes/web.php')), 'Route::adminResource(') === false) {
            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents($stub),
                FILE_APPEND | LOCK_EX
            );

            $this->info('Admin routes scaffolding generated successfully.');
        } else {
            $this->info('Admin routes scaffolding have already been generated.');
        }
    }
}
