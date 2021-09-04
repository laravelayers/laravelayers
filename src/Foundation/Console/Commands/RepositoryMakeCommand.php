<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Repositories\Repository;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class and a new model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Base class.
     *
     * @var string
     */
    protected $baseClass = Repository::class;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('model')) {
            $stub = '/stubs/repository.stub';
        }

        $stub = $stub ?? '/stubs/repository.plain.stub';

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
            $this->call('admin:make-repository', [
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

        return $rootNamespace.'\Repositories';
    }

    /**
     * Build the class with the given name and execute the console command.
     *
     * Remove the base repository import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $repositoryNamespace = $this->getNamespace($name);

        $replace = [];

        $replace = $this->buildBaseReplacements($replace);

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        $replace["use {$repositoryNamespace}\Repository;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values and execute the console command.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $name = $this->option('mn')
            ? $this->convertClassName($this->option('mn'))
            : $this->getNameInput();

        $name = $this->clearNameFromType($name);

        $class = $this->parseClassName(
            ModelMakeCommand::class, $name, $this->option('mn') ?: false
        );

        if (!class_exists($class)) {
            if (substr($name, 0, 1) == '/' && !$this->option('mn')) {
                $name = substr($name, 1);
            }

            $options = [
                'name' => $name,
                '--template' => null
            ];

            if ($this->option('nc')) {
                $options['--nc'] = null;
            }

            $this->call('make:model', $options);
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $class,
            'DummyModelClass' => class_basename($class),
            'DummyModelVariable' => lcfirst(class_basename($class)),
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
                ['admin', 'a', InputOption::VALUE_NONE, 'Generate a admin repository class and create a new parent repository class.'],
                ['model', 'm', InputOption::VALUE_NONE, 'Create a new model class for the repository.'],
                ['mn', null, InputOption::VALUE_OPTIONAL, 'Change the model class name to the specified one.']
            ]
        );
    }
}
