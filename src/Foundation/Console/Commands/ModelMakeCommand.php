<?php

namespace Laravelayers\Foundation\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as IlluminateModelMakeCommand;
use Laravelayers\Foundation\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Models\Model;
use Laravelayers\Foundation\Models\Pivot;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends IlluminateModelMakeCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class in App/Models';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Base class.
     *
     * @var string
     */
    protected $baseClass = Model::class;

    /**
     * Get the type of class being generated.
     *
     * @return string
     */
    public function getType()
    {
        return '';
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $this->call('make:factory', [
            'name' => $this->argument('name').'Factory',
            '--model' => 'Models\\' . $this->getNameInput(),
        ]);
    }

    /**
     * Create a controller for the service and create a service for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $options = [
            'name' => $this->getNameInput(),
            '--service' => null
        ];

        if ($this->option('resource')) {
            $options['--resource'] = null;
        }

        $this->call('make:controller', $options);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('template')) {
            $stub = '/stubs/model.stub';
        }

        $stub = $stub ?? '/stubs/model.plain.stub';

        return $this->resolveStubPath(__DIR__.$stub);
    }

    /**
     * Get the defarult namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->input && substr($this->getNameInput(), 0, 1) == '/') {
            return $rootNamespace;
        }

        return $rootNamespace.'\Models';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base model import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $modelNamespace = $this->getNamespace($name);

        $replace = [
            'DummyTable' => snake_case(str_replace($modelNamespace.'\\', '', $name)),
            "use {$modelNamespace}\Model;\n" => '',
        ];

        if ($this->option('pivot')) {
            $this->input->setOption('rp', Pivot::class);
        }

        $replace = $this->buildBaseReplacements($replace);

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
                ['template', 't', InputOption::VALUE_NONE, 'Generate a model template.']
            ]
        );
    }
}
