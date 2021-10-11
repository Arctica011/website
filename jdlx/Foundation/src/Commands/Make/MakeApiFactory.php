<?php


namespace  Jdlx\Commands\Make;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApiFactory extends FactoryMakeCommand
{
    use ModelUtil;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory';



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
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = 'Models\\'.$this->getModelInput();

        $namespaceModel = $model
            ? $this->qualifyClass($model)
            : trim($this->rootNamespace(), '\\').'\\Models';

        $model = class_basename($namespaceModel);

        $replace = [
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ fillers }}' => $this->getFactoryFields()
        ];

        return str_replace(
            array_keys($replace), array_values($replace), GeneratorCommand::buildClass($name)
        );
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function getFactoryFields()
    {
        $fields = $this->getDbFields($this->parseModel($this->getModelInput()));
        $fillers = [];
        foreach ($fields as $field){
            $name = $field["name"];
            $type = $field["type"];

            $assign = "'".$name."' => ";
            $generator = null;
            if($name === "id" && $type === "integer"){
                continue;
            }else if(($name === "id" || Str::endsWith($name, "_id")) && $type === "text"){
                $generator = '$faker->uuid';
            }else if(Str::endsWith($name, "name")){
                $generator = '$faker->name';
            }
            else if($type === "string" && Str::contains($name, "email")){
                $generator = '$faker->unique()->safeEmail';
            }else {
                switch($type){
                    case 'integer': $generator = '$faker->randomNumber(2)'; break;
                    case 'text': $generator = ' $faker->words(1, true)'; break;
                    case 'textarea': $generator = '$faker->paragraph'; break;
                    case 'timestamp': $generator = '$faker->dateTimeBetween(\'-1 years\', \'-1 hour\')'; break;
                    case 'datetime': $generator = '$faker->dateTimeBetween(\'-1 years\', \'-1 hour\')'; break;
                    case 'boolean': $generator = '$faker->boolean'; break;
                    case 'json': $generator = '["foo" => "bar"]'; break;
                    default: $generator = "''";
                }
            }

            $fillers[] = $assign . $generator;
        }

        return implode(",\n            ", $fillers);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace(
            ['\\', '/'], '', $this->getNameInput()
        );

        return $this->laravel->databasePath()."/factories/{$name}.php";
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
    protected function getNameInput()
    {
        return $this->getModelInput() . "Factory";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The name of the model the factory is for'],
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
