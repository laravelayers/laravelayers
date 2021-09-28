<?php

namespace Laravelayers\Foundation\Console;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait GeneratorCommand
{
    /**
     * Get the type of class being generated.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the base class.
     *
     * @return string
     */
    public function getBaseClass()
    {
        return str_replace('/', '\\', trim($this->baseClass, '/'));
    }

    /**
     * Set the base class.
     *
     * @param string $class
     * @return $this
     */
    public function setBaseClass($class)
    {
        $this->baseClass = $class;

        return $this;
    }

    /**
     * Build the base class replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildBaseReplacements(array $replace)
    {
        if ($this->option('rp')) {
            $this->setBaseClass(
                $this->convertClassName(
                    $this->option('rp')
                )
            );
        }

        $replace = array_merge($replace, [
            'DummyFullBaseClass' => $replace['DummyFullBaseClass'] ?? $this->getBaseClass(),
            'DummyBaseClass' => $replace['DummyBaseClass'] ?? class_basename($this->getBaseClass())
        ]);

        if ($replace['DummyBaseClass'] == class_basename($this->getNameInput())) {
            $replace['DummyBaseClass'] = Str::studly('Base_' . class_basename($this->getBaseClass()));
            $replace['DummyFullBaseClass'] = "{$this->getBaseClass()} as {$replace['DummyBaseClass']}";
        }

        return $replace;
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        $customPath = trim($this->initCustomStubPath(), '/') . '/' . trim(basename($stub));

        return file_exists($customPath = $this->laravel->basePath($customPath))
            ? $customPath
            : (strpos($stub, '/') === false ? __DIR__ : '').$stub;
    }

    /**
     * Initialize the custom stub path.
     *
     * @return string
     */
    protected function initCustomStubPath()
    {
        return 'stubs/foundation';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $input = !empty($this->nameInput) ? $this->nameInput : parent::getNameInput();
        $input = $this->convertClassName($input);

        return $this->clearNameFromType($input) . (!$this->option('nc') ? $this->getType() : '');
    }

    /**
     * Get the desired class name without type from the input.
     *
     * @return string
     */
    protected function getNameInputWithoutType()
    {
        return $this->clearNameFromType($this->getNameInput());
    }

    /**
     * Convert name input to camel case.
     *
     * @param string $value
     * @param string $separator
     * @return string
     */
    protected function convertNameInputToCamelCase($value, $separator = '/')
    {
        $inputs = explode('/', $value);

        foreach($inputs as $key => $input) {
            $inputs[$key] = Str::camel($input);
        }

        return implode($inputs, '/');
    }

    /**
     * Convert the class name.
     *
     * @param string $name
     * @return string
     */
    protected function convertClassName($name)
    {
        if (!$this->option('nc')) {
            $name = Str::camel($name);

            $names = array_map(function ($item) {
                return ucfirst($item);
            }, explode('/', $name));

            $name = implode('/', $names);
        }

        return $name;
    }

    /**
     * Get the fully-qualified class by name.
     *
     * @param string $class
     * @param string $name
     * @param bool $default
     * @return string
     */
    protected function parseClassName($class, $name, $root = false)
    {
        $command = resolve($class);

        if ($root && substr($name, 0, 1) != '/') {
            $root = false;
        }

        $name = $command->clearNameFromType($name) . $command->getType();
        $name = str_replace('/', '\\', trim($name, '/'));

        $rootNamespace = trim($this->rootNamespace(), '\\');

        if ($root) {
            return $rootNamespace . '\\' . $name;
        }

        return $command->getDefaultNamespace($rootNamespace) . '\\' . $name;
    }

    /**
     * Clear name from type.
     *
     * @param string $name
     * @return string
     */
    public function clearNameFromType($name)
    {
        return preg_replace('/'. $this->type .'$/i', '', $name);
    }

    /**
     * Get the default console command options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ['rp', null, InputOption::VALUE_OPTIONAL, 'Replace the parent class.'],
            ['nc', null, InputOption::VALUE_NONE, 'Do not convert class name from input.']
        ];
    }
}
