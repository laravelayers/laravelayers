<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand as RoutingControllerMakeCommand;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Controllers\Controller;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends RoutingControllerMakeCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Base class.
     *
     * @var string
     */
    protected $baseClass = Controller::class;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('parent')
            || $this->option('model')
        ) {
            return parent::getStub();
        }

        if ($this->option('resource')) {
            $stub = '/stubs/controller.stub';
        } elseif ($this->option('service')) {
            $stub = '/stubs/controller.service.stub';
        }

        $stub = $stub ?? '/stubs/controller.plain.stub';

        return $this->resolveStubPath(__DIR__.$stub);
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        if ($this->hasOption('admin') && $this->option('admin')) {
            $this->call('admin:make-controller', [
                'name' => $this->getNameInput(),
                '--parent' => null,
                '--lang' => null
            ]);

            return false;
        }

        return parent::handle();
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

        if ($this->option('service')) {
            $replace = $this->buildServiceReplacements($replace);
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
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
            ServiceMakeCommand::class, $name, $this->option('sn') ?: false
        );

        if (!class_exists($class)) {
            if (substr($name, 0, 1) == '/' && !$this->option('sn')) {
                $name = substr($name, 1);
            }

            $options = [
                'name' => $name,
                '--decorator' => null,
            ];

            if ($this->option('nc')) {
                $options['--nc'] = null;
            }

            $this->call('make:service', $options);
        }

        return array_merge($replace, [
            'DummyFullServiceClass' => $class,
            'DummyServiceClass' => class_basename($class),
            'DummyServiceVariable' => lcfirst(class_basename($class)),
        ]);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            parent::getOptions(), $this->getDefaultOptions(), [
                ['admin', 'a', InputOption::VALUE_NONE, 'Generate a admin controller class and create a new parent service class.'],
                ['service', 's', InputOption::VALUE_NONE, 'Create a new service class for the controller.'],
                ['sn', null, InputOption::VALUE_OPTIONAL, 'Change the service class name to the specified one.']
            ]
        );
    }
}
