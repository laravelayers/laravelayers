<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Services\Service;
use Symfony\Component\Console\Input\InputOption;

class ServiceMakeCommand extends GeneratorCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service layer class, a new repository class and a new model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Base class.
     *
     * @var string
     */
    protected $baseClass = Service::class;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('collection')) {
            $stub = '/stubs/service.collection.decorator.stub';
        } elseif ($this->option('decorator')) {
            $stub =  '/stubs/service.decorator.stub';
        } elseif ($this->option('repository')) {
            $stub = '/stubs/service.stub';
        }

        $stub = $stub ?? '/stubs/service.plain.stub';

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
            $this->call('admin:make-service', [
                'name' => $this->getNameInput(),
                '--parent' => null
            ]);

            return false;
        }

        return parent::handle();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->input && substr($this->getNameInput(), 0, 1) == '/') {
            return $rootNamespace;
        }

        return $rootNamespace.'\Services';
    }

    /**
     * Build the class with the given name and execute the console command.
     *
     * Remove the base service import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $serviceNamespace = $this->getNamespace($name);

        $replace = [];

        $replace = $this->buildBaseReplacements($replace);

        if ($this->option('decorator') || $this->option('collection')) {
            $replace = $this->buildDecoratorReplacements($replace);
            $this->input->setOption('repository', true);
        }

        if ($this->option('repository')) {
            $replace = $this->buildRepositoryReplacements($replace);
        }

        $replace["use {$serviceNamespace}\Service;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the repository replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRepositoryReplacements(array $replace)
    {
        $name = $this->option('rn')
            ? $this->convertClassName($this->option('rn'))
            : $this->getNameInput();

        $name = $this->clearNameFromType($name);

        $class = $this->parseClassName(
            RepositoryMakeCommand::class, $name, $this->option('rn') ?: false
        );

        if (!class_exists($class)) {
            if (substr($name, 0, 1) == '/' && !$this->option('rn')) {
                $name = substr($name, 1);
            }

            $options = [
                'name' => $name,
                '--model' => null
            ];

            if ($this->option('nc')) {
                $options['--nc'] = null;
            }

            $this->call('make:repository', $options);
        }

        return array_merge($replace, [
            'DummyFullRepositoryClass' => $class,
            'DummyRepositoryClass' => class_basename($class),
            'DummyRepositoryVariable' => lcfirst(class_basename($class)),
        ]);
    }

    /**
     * Build the decorator replacement values and execute the console command.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildDecoratorReplacements(array $replace)
    {
        $name = $this->option('dn')
            ? $this->convertClassName($this->option('dn'))
            : $this->getNameInput();

        $name = $this->clearNameFromType($name);

        $class = $this->parseClassName(
            DecoratorMakeCommand::class, $name, $this->option('dn') ?: false
        );

        if (substr($name, 0, 1) == '/' && !$this->option('dn')) {
            $name = substr($name, 1);
        }

        $options = [
            'name' => $name
        ];

        if ($this->option('nc')) {
            $options['--nc'] = null;
        }

        if (!class_exists($class)) {
            $this->call('make:decorator',  $options);
        }

        $replace = array_merge($replace, [
            'DummyFullDecoratorClass' => $class,
            'DummyDecoratorClass' => class_basename($class)
        ]);

        if ($this->option('collection')) {
            $class = preg_replace('/(.*)(Decorator)$/i', '$1Collection$2', $class);

            if (!class_exists($class)) {
                if (substr($name, 0, 1) == '/' && !$this->option('dn')) {
                    $name = substr($name, 1);
                }

                $options['name'] = $name;
                $options['--collection'] = null;

                $this->call('make:decorator', $options);
            }

            $replace = array_merge($replace, [
                'DummyFullCollectionDecoratorClass' => $class,
                'DummyCollectionDecoratorClass' => class_basename($class)
            ]);
        }

        return $replace;
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
                ['admin', 'a', InputOption::VALUE_NONE, 'Generate a admin service class and create a new parent service class.'],
                ['decorator', 'd', InputOption::VALUE_NONE, 'Create a new decorator class for the repository.'],
                ['dn', null, InputOption::VALUE_OPTIONAL, 'Change the decorator class name to the specified one.'],
                ['collection', 'c', InputOption::VALUE_NONE, 'Create a new collection decorator class for the repository.'],
                ['repository', 'r', InputOption::VALUE_NONE, 'Create a new repository class for the service.'],
                ['rn', null, InputOption::VALUE_OPTIONAL, 'Change the repository class name to the specified one.']
            ]
        );
    }
}
