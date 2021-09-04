<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Symfony\Component\Console\Input\InputOption;

class DecoratorMakeCommand extends GeneratorCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:decorator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new decorator class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Decorator';

    /**
     * Base class.
     *
     * @var string
     */
    protected $baseClass = DataDecorator::class;

    /**
     * Base collection class.
     *
     * @var string
     */
    protected $baseCollectionClass = CollectionDecorator::class;

    /**
     * Get the base collection class.
     *
     * @return string
     */
    public function getBaseCollectionClass()
    {
        return str_replace('/', '\\', trim($this->baseCollectionClass, '/'));
    }

    /**
     * Set the base collection Zclass.
     *
     * @param string $class
     * @return $this
     */
    public function setBaseCollectionClass($class)
    {
        $this->baseCollectionClass = $class;

        return $this;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('collection')) {
            $stub = '/stubs/collection.decorator.stub';
        }

        $stub = $stub ?? '/stubs/decorator.stub';

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
            $args = ['name' => $this->getNameInput()];

            if ($this->option('collection')) {
                $args['--collection'] = null;
            }

            $this->call('admin:make-decorator', $args);

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
        return $rootNamespace.'\Decorators';
    }

    /**
     * Get the type of class being generated.
     *
     * @return string
     */
    public function getType()
    {
        if ($this->input && $this->option('collection')) {
            $this->type = 'CollectionDecorator';
        }

        return $this->type;
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

        if ($this->option('collection') && !$this->option('rp')) {
            $this->setBaseClass($this->getBaseCollectionClass());
        }

        $replace = $this->buildBaseReplacements($replace);

        $replace["use {$decoratorNamespace}\Decorator;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
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
                ['admin', 'a', InputOption::VALUE_NONE, 'Generate a admin decorator class.'],
                ['collection', 'c', InputOption::VALUE_NONE, 'Create a collection decorator class.']
            ]
        );
    }
}
