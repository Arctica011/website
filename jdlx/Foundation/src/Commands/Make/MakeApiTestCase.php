<?php


namespace Jdlx\Commands\Make;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApiTestCase extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api:testcase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud TestCase';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TestCase';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/stubs/testcase.api.stub';

        return $this->resolveStubPath($stub);
    }


    /**
     * Overwritten to ensure we use __DIR__ relative to this file
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
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
        $this->checkForFactory();

        $stub = parent::buildClass($name);

        return $this->replaceModelAndPath($stub);
    }


    protected function checkForFactory()
    {
        $name = str_replace(
            ['\\', '/'], '', $this->getModelInput()."Factory"
        );

        $path = $this->laravel->databasePath()."/factories/{$name}.php";

        if(!file_exists($path)){
            if ($this->confirm("A {$name} does not exist. Do you want to generate it?", true)) {
                $this->call('make:api:factory', ['model' => $this->getModelInput()]);
            }
        }
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceModelAndPath(&$stub)
    {
        $model = "\\App\\Models\\" . $this->getModelInput();
        $resource = "\\App\\Http\\Resources\\" . $this->getModelInput()."Resource";
        $path =  $this->getRoute();

        $stub = str_replace('{{ model }}', $model, $stub);
        $stub = str_replace('{{ path }}', $path, $stub);
        $stub = str_replace('{{ resource }}', $resource, $stub);

        return $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return "Tests\\Feature\\" . $this->getModelInput();
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst("Tests\\", '', $name);
        $base = preg_replace("/app$/m","tests",$this->laravel['path']);
        return $base.'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getModelInput()
    {
        return trim($this->argument('model'));
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getRoute()
    {
        $model = preg_replace("/s$/", "", Str::kebab($this->getModelInput()));
        return "/api/${model}";
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return $this->getModelInput()."CrudTest";
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return "Test";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The name of the class, should reference a model'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],

        ];
    }
}
