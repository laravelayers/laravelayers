<?php

namespace Laravelayers\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Laravelayers\Admin\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Console\Commands\ServiceMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class AdminServiceMakeCommand extends ServiceMakeCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:make-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin service layer class and a new admin repository class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath(__DIR__.'/stubs/admin.service.stub');
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

        return $rootNamespace.'\Services\Admin';
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

        $replace = $this->buildDecoratorReplacements($replace);

        $replace = $this->buildRepositoryReplacements($replace);

        $replace["use {$serviceNamespace}\Service;\n"] = '';

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
        if ($this->option('parent')) {
            $name = $this->option('pn')
                ? $this->convertClassName($this->option('pn'))
                : $this->getNameInput();

            $class = $this->parseClassName(
                ServiceMakeCommand::class, $name, $this->option('pn') ?: false
            );

            if (!class_exists($class)) {
                if (!file_exists($this->getPath($class))) {
                    $options = [
                        'name' => ltrim($name, !$this->option('pn') ? '/' : ''),
                        '--repository' => null
                    ];

                    if ($this->option('nc')) {
                        $options['--nc'] = null;
                    }

                    $this->call('make:service', $options);
                }
            }
        }

        if (!empty($class)) {
            $this->setBaseClass($class);
        }

        return parent::buildBaseReplacements($replace);
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
            AdminRepositoryMakeCommand::class, $name, $this->option('rn') ?: false
        );

        if (!class_exists($class)) {
            if (substr($name, 0, 1) == '/' && !$this->option('rn')) {
                $name = substr($name, 1);
            }

            $options = [
                'name' => $name
            ];

            if ($this->option('parent')) {
                $options['--parent'] = null;
            }

            if ($this->option('nc')) {
                $options['--nc'] = null;
            }

            $this->call('admin:make-repository', $options);
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

        $class = $this->parseClassName(AdminDecoratorMakeCommand::class, $name, $this->option('dn') ?: false);

        if (substr($name , 0, 1) == '/' && !$this->option('dn')) {
            $name  = substr($name , 1);
        }

        $options = [];

        if ($this->option('parent')) {
            $options['--parent'] = null;
        }

        if ($this->option('nc')) {
            $options['--nc'] = null;
        }

        if (!class_exists($class)) {
            $options['name'] = $name;

            $this->call('admin:make-decorator', $options);
        }

        $replace = array_merge($replace, [
            'DummyFullDecoratorClass' => $class,
            'DummyDecoratorClass' => class_basename($class)
        ]);

        $class = preg_replace('/(.*)(Decorator)$/i', '$1Collection$2', $class);

        if (!class_exists($class)) {
            if (substr($name, 0, 1) == '/' && !$this->option('dn')) {
                $name = substr($name, 1);
            }

            $options['name'] = $name;
            $options['--collection'] = null;

            $this->call('admin:make-decorator', $options);
        }

        return array_merge($replace, [
            'DummyFullCollectionDecoratorClass' => $class,
            'DummyCollectionDecoratorClass' => class_basename($class)
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
            $this->getDefaultOptions(), [
                ['parent', 'p', InputOption::VALUE_NONE, 'Create a new parent service class.'],
                ['pn', null, InputOption::VALUE_OPTIONAL, 'The name of the parent service class.'],
                ['dn', null, InputOption::VALUE_OPTIONAL, 'Change the decorator class name to the specified one.'],
                ['rn', null, InputOption::VALUE_OPTIONAL, 'Change the repository class name to the specified one.']
            ]
        );
    }
}
