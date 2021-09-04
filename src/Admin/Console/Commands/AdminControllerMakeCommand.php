<?php

namespace Laravelayers\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravelayers\Admin\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Admin\Controllers\Controller;
use Laravelayers\Foundation\Console\Commands\ControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

class AdminControllerMakeCommand extends ControllerMakeCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:make-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin controller class';

    /**
     * Base class.
     *
     * @var string
     */
    protected $baseClass = Controller::class;

    /**
     * Indicates whether the route has been created.
     *
     * @return bool
     */
    protected $isRoute = false;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath(__DIR__.'/stubs/admin.controller.stub');
    }

    /**
     * Execute the console command.
     *
     * @return bool|null|void
     */
    public function handle()
    {
        if ((! $this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            if ($this->option('lang')) {
                $this->makeLangDirectory();
            }
        }

        if (parent::handle() !== false) {
            $controller =  str_replace(['//', '/'], '\\', 'Admin/' . $this->getNameInput());

            if ($this->getRoute()) {
                $this->call('admin:menu-cache');
            } else {
                if (!$this->isRoute) {
                    $route = "Route::adminResource('" . $this->buildRouteReplacements()['DummyRoutePath'] . "', '" . $controller . "');";

                    $this->comment('Please add the route "' . $route . '" for the controller in /routes/web.php');
                }

                $this->comment('Run "php artisan admin:cache" to reset the menu cache in the admin panel.');
            }
        }
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Admin';
    }

    /**
     * Build the class with the given name and execute the console command.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        $replace = $this->buildBaseReplacements($replace);

        $replace = $this->buildServiceReplacements($replace);

        $replace = $this->buildRouteReplacements($replace);

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        if ($this->option('lang')) {
            $this->makeLangDirectory();
        }

        return str_replace(
            array_keys($replace), array_values($replace), GeneratorCommand::buildClass($name)
        );
    }

    /**
     * Build the base class replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildBaseReplacements(array $replace)
    {
        $replace['DummyBaseClass'] = 'AdminController';
        $replace['DummyFullBaseClass'] = "{$this->getBaseClass()} as {$replace['DummyBaseClass']}";

        return parent::buildBaseReplacements($replace);
    }

    /**
     * Build the service replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildServiceReplacements(array $replace)
    {
        $name = $this->option('sn')
            ? $this->convertClassName($this->option('sn'))
            : $this->getNameInput();

        $name = $this->clearNameFromType($name);

        $class = $this->parseClassName(
            AdminServiceMakeCommand::class, $name, $this->option('sn') ?: false
        );

        if (!class_exists($class)) {
            if (substr($name, 0, 1) == '/' && !$this->option('sn')) {
                $name = substr($name, 1);
            }

            $options = [
                'name' => $name
            ];

            if ($this->option('parent')) {
                $options['--parent'] = null;

                if ($this->option('pn')) {
                    $options['--pn'] = $this->option('pn');
                }
            }

            if ($this->option('nc')) {
                $options['--nc'] = null;
            }

            $this->call('admin:make-service', $options);
        }

        return array_merge($replace, [
            'DummyFullServiceClass' => $class,
            'DummyServiceClass' => class_basename($class),
            'DummyServiceVariable' => lcfirst(class_basename($class)),
        ]);
    }

    /**
     * Build the route replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRouteReplacements(array $replace = [])
    {
        $nameInput = $this->convertNameInputToCamelCase(
            $route['uri'] ?? $this->getNameInputWithoutType()
        );

        $name = basename($nameInput);

        $path = preg_replace("/{$name}\/{$name}[\/]*$/", $name, $nameInput);
        $path = substr(str_replace('//', '/', "/{$path}/"), 1, -1);
        $path = dirname($path) . '/' . str_replace('_', '/', Str::snake(basename($path)));

        $route = $this->getRoute();

        if (!$route) {
            $this->addRoute($path);
        }

        $route = $route['name'] ?? str_replace('/', '.', $path);

        return array_merge($replace, [
            'DummyRouteName' => $route,
            'DummyRoutePath' => $path,
        ]);
    }

    /**
     * Make a new language directory.
     *
     * @return string
     */
    protected function makeLangDirectory()
    {
        $nameInput = $this->convertNameInputToCamelCase(
            $this->getRoute()['uri'] ?? $this->getNameInputWithoutType()
        );

        $path = App::langPath() . '/' . App::getLocale() . '/';
        $path .=  'admin/' . $nameInput;

        $name = $this->files->basename($nameInput);

        $path = preg_replace("/{$name}\/{$name}[\/]*$/", $name, $path);
        $path = dirname( $path) . '/' . str_replace('_', '/', Str::snake(basename($path)));

        $file = $path . '.php';

        $path = dirname($path);

        if (!$this->files->exists($file)) {
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path);
            }

            copy(__DIR__.'/stubs/admin.lang.stub', $file);

            $this->info('Localization File created successfully.');
        }

        return $path;
    }

    /**
     * Get the URI and name of the route without the "admin" prefix if a route exists.
     *
     * @return array
     */
    protected function getRoute()
    {
        if ($route = Route::getRoutes()->getByAction($this->qualifyClass($this->getNameInput()) . '@index')) {
            $prefix = (config('admin.prefix') ?: 'admin');

            $uri = trim(preg_replace('/{[^}]*}\/?/', '', $route->uri()), '/');

            $name = $route->getName();

            return [
                'uri' => preg_replace("/^{$prefix}\//i", '', $uri),
                'name' => preg_replace("/^{$prefix}\.(.*)\.index$/i", '$1', $name)
            ];
        }

        return [];
    }

    /**
     * Add a route for the controller.
     *
     * @param string $path
     * @return bool
     */
    protected function addRoute($path)
    {
        if (!$this->isRoute) {
            $routesFile = file_exists($routesFile = base_path('routes/web.php')) ? $routesFile : '';

            if (!$routesFile || strpos(file_get_contents($routesFile), 'adminResource') === false) {
                $finder = new Finder();

                $finder->files()->in(base_path('routes'));

                foreach ($finder as $file) {
                    if ($file->getExtension() == 'php') {
                        $filePath = $file->getRealPath();

                        if (strpos(file_get_contents($filePath), 'adminResource') !== false) {
                            $routesFile = $filePath;

                            break;
                        }
                    }
                }
            }

            if ($routesFile) {
                $controller = Str::after(
                    $this->qualifyClass($this->getNameInput()),
                    $this->getDefaultNamespace(trim($this->rootNamespace(), '\\'))
                );

                $content = "Route::adminResource('{$path}', 'Admin{$controller}');";

                file_put_contents(
                    $routesFile,
                    "\n{$content}\n",
                    FILE_APPEND | LOCK_EX
                );

                $this->isRoute = true;

                return true;
            }
        }

        return false;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            $this->getDefaultOptions(), [
                ['parent', 'p', InputOption::VALUE_NONE, 'Create a new parent service class.'],
                ['pn', null, InputOption::VALUE_OPTIONAL, 'The name of the parent service class.'],
                ['sn', null, InputOption::VALUE_OPTIONAL, 'Change the service class name to the specified one.'],
                ['lang', 'l', InputOption::VALUE_NONE, 'Make a new language directory.'],
            ]
        );
    }
}
