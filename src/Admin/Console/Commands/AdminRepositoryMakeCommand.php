<?php

namespace Laravelayers\Admin\Console\Commands;

use Laravelayers\Admin\Console\GeneratorCommand as TraitGeneratorCommand;
use Laravelayers\Foundation\Console\Commands\RepositoryMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class AdminRepositoryMakeCommand extends RepositoryMakeCommand
{
    use TraitGeneratorCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:make-repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin repository class';

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

        return $rootNamespace.'\Repositories\Admin';
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
                RepositoryMakeCommand::class, $name, $this->option('pn') ?: false
            );

            if (!class_exists($class)) {
                if (!file_exists($this->getPath($class))) {
                    $options = [
                        'name' => ltrim($name, !$this->option('pn') ? '/' : ''),
                        '--model' => null
                    ];

                    if ($this->option('nc')) {
                        $options['--nc'] = null;
                    }

                    $this->call('make:repository', $options);
                }
            }
        }

        if (!empty($class)) {
            $this->setBaseClass($class);
        } else {
            $this->input->setOption('model', true);
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
                ['parent', 'p', InputOption::VALUE_NONE, 'Create a new parent repository class.'],
                ['pn', null, InputOption::VALUE_OPTIONAL, 'The name of the parent repository class.'],
                ['model', 'm', InputOption::VALUE_NONE, 'Create a new model class for the repository.'],
                ['mn', null, InputOption::VALUE_OPTIONAL, 'Change the model class name to the specified one.']
            ]
        );
    }
}
