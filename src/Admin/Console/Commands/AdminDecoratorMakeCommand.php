<?php

namespace Laravelayers\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Laravelayers\Admin\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Admin\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Console\Commands\DecoratorMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class AdminDecoratorMakeCommand extends DecoratorMakeCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:make-decorator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin decorator class';

    /**
     * Base collection class.
     *
     * @var string
     */
    protected $baseCollectionClass = CollectionDecorator::class;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('collection')) {
            $stub = '/stubs/admin.collection.decorator.stub';
        }

        $stub = $stub ?? '/stubs/admin.decorator.stub';

        return $this->resolveStubPath(__DIR__.$stub);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Decorators\Admin';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base decorator import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $decoratorNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('collection')) {
            $this->setBaseClass($this->getBaseCollectionClass());
        }

        $replace = $this->buildBaseReplacements($replace);

        $replace["use {$decoratorNamespace}\Decorator;\n"] = '';

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
        if ($this->option('parent') && !$this->option('collection')) {
            $name = $this->option('pn')
                ? $this->convertClassName($this->option('pn'))
                : $this->getNameInput();

            $class = $this->parseClassName(
                DecoratorMakeCommand::class, $name, $this->option('pn') ?: false
            );

            if (!class_exists($class)) {
                if (!file_exists($this->getPath($class))) {
                    $this->call('make:decorator', [
                        'name' => ltrim($name, !$this->option('pn') ? '/' : ''),
                    ]);
                }
            }
        }

        if (!empty($class)) {
            $this->setBaseClass($class);
        }

        return parent::buildBaseReplacements($replace);
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
                ['collection', 'c', InputOption::VALUE_NONE, 'Generate a collection decorator class.'],
                ['parent', 'p', InputOption::VALUE_NONE, 'Create a new parent data decorator class.'],
                ['pn', null, InputOption::VALUE_OPTIONAL, 'The name of the parent data decorator class.']
            ]
        );
    }
}
