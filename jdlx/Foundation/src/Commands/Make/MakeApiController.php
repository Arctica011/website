<?php


namespace  Jdlx\Commands\Make;


use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeApiController extends ControllerMakeCommand
{
    use ModelUtil;
    use WithStubs;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class specific to apis';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/stubs/controller.api.stub';
        if ($this->option('parent')) {
            $stub = '/stubs/controller.nested.api.stub';
        } elseif ($this->option('model')) {
            $stub = '/stubs/controller.model.api.stub';
        }

        return $this->resolveStubPath($stub);
    }

    /**
     * Overwritten to ensure we use __DIR__ relative to this file
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers\Api';
    }


    /**
     * Build the model replacement values.
     *
     * @param array $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $model = $this->option('model');
        $resource = $model . "Resource";

        $modelClass = $this->parseModel($model);
        $resourceClass = "App\\Http\\Resources\\" . $resource;

        if (!class_exists($resourceClass)) {
            if ($this->confirm("A {$resourceClass} resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:api:resource', ['name' => $resource, '--model' => $model]);
            }
        }

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:api:model', ['name' => $this->option('model')]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
            '{{ path }}' => Str::kebab(class_basename($modelClass)),
            '{{ tag }}' => class_basename($modelClass),
            '{{ entities }}' => lcfirst(class_basename($modelClass)) . "s",
            '{{ entity }}' => lcfirst(class_basename($modelClass)),
            '{{ modelResource }}' => class_basename($modelClass) . "Resource",
            '{{ modelSchema }}' => class_basename($modelClass),
            '{{ filter_fields }}' => $this->buildFilterDocs($modelClass),
            '{{ with_models }}' => $this->buildRelationship($modelClass),
        ]);
    }

    protected function buildFilterDocs($modelClass)
    {
        $fields = [];
        foreach ($this->getFieldDefinitions($modelClass) as $field) {
            $name = $field['name'];
            $label = $field['name'];
            $type = $field['type'];

            if ($modelClass::filterable($name)) {
                $lines = ["@OA\Parameter" => [
                    "name" => $label,
                    "in" => "query",
                    "required" => false,
                    "description" => "Filter {$label}",
                    "@OA\Schema" => ["type" => "string"]
                ]];

                $fields[] = $this->makeDoc($lines, 1, "     * ", false);
            }
        }
        $code = "\n" . implode(",\n", $fields) . ",";
        return $code;
    }

    protected function buildRelationship($modelClass)
    {
        $fields = $this->getRelationShips($modelClass);
        $names = array_map(function($f){return '"'.$f['method'].'"';}, $fields);
        return implode(", ", $names);
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
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
            ['parent', 'p', InputOption::VALUE_OPTIONAL, 'Generate a nested resource controller class.']
        ];
    }
}
