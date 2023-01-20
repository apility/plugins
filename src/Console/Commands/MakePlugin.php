<?php

namespace Apility\Plugins\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakePlugin extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:plugin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new plugin';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Plugin';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('policy', true);
        }

        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $this->replaceNamespace($stub, $name);

        return $this->replaceClass($stub, $name);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('DummyPlugin', $this->argument('name'), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('policy')) {
            return __DIR__ . '/../../stubs/plugin-with-policy.stub';
        }

        return __DIR__ . '/../../stubs/plugin.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Plugins';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'name', InputArgument::REQUIRED, 'The name of the plugin.',
            ],
        ];
    }

    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Create a new plugin with all resources'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
            ['policy', 'p', InputOption::VALUE_NONE, 'Create a new policy for the plugin'],
        ];
    }
}
