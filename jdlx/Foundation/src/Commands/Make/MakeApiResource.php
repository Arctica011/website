<?php


namespace  Jdlx\Commands\Make;


use App\Library\ModelExplorer\Explorer;
use App\Traits\FieldDescriptor;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeApiResource extends ResourceMakeCommand
{
    use WithStubs;
    use ModelUtil;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource prefilled with fields and API docs';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->collection()
            ? __DIR__ . '/stubs/resource-collection.stub'
            : __DIR__ . '/stubs/resource.stub';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildModelReplacements($replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:api:model', ['name' => $modelClass]);
            } else {
                return;
            }
        }

        return array_merge($replace, [
            '{{ model }}' => $this->option('model'),
            '{{ resourceFields }}' => $this->getFields($modelClass),
            '{{ docs }}' => $this->getDocs($modelClass),
        ]);
    }

    protected function getDocs($modelClass)
    {
        $fields = [];
        $dbFields = $this->getFieldDefinitions($modelClass);
        $dbRelated = $this->getRelationShips($modelClass);

        foreach ($dbFields as $field) {
            $name = $field['name'];
            $label = $field['name'];
            $type = $field['type'];
            $foreignKey = $this->partOfForeignKey($modelClass, $name);

            if ($modelClass::isReadable($name)) {
                $props = [
                    "property" => $label,
                    "description" => "",
                ];

                if($field['readOnly']){
                    $props["readOnly"] = "true";
                }

                if($field['writeOnly'] || $foreignKey){
                    $props["writeOnly"] = "true";
                }

                switch ($type){
                    case "datetime":
                        $props["type"] = "string";
                        $props["format"] = "date-time";
                        break;
                    case "timestamp":
                        $props["type"] = "string";
                        $props["format"] = "date-time";
                        break;
                    case "boolean":
                        $props["type"] = "boolean";
                        $props["type"] = "boolean";
                        break;
                    case "number":
                        $props["type"] = "string";
                        $props["format"] = "float";
                        break;
                    case "integer":
                        $props["type"] = "integer";
                        break;
                    case "json":
                        $props["type"] = "object";
                        break;

                    default:
                        $props["type"] = "string";
                }

                $fields[] = $this->makeDoc(["@OA\Property" => $props], 1);

                if($foreignKey && $foreignKey === "id"){
                    $props = [
                        "property" => $dbRelated[$name]['method'],
                        "ref" => "#/components/schemas/".$dbRelated[$name]['class'],
                    ];
                    if($dbRelated[$name]['type'] === "morphTo"){
                        $props["ref"] = "#/components/schemas/Any";
                    }
                    $fields[] = $this->makeDoc(["@OA\Property" => $props], 1);
                }
            }
        }
        $code = "\n".implode(",\n", $fields);
        return $code;
    }

    protected function getFields($modelClass)
    {
        $fields = [];
        $dbFields = $this->getFieldDefinitions($modelClass);
        $dbRelated = $this->getRelationShips($modelClass);

        foreach ($dbFields as $field) {
            $name = $field['name'];
            $label = $field['name'];
            $type = $field['type'];
            $foreignKey = isset($dbRelated[$name]);

            if ($modelClass::isReadable($name)) {
                $assign = "'{$label}'";
                $value = '$this->' . $name;

                switch ($type){
                    case "timestamp":
                    case "datetime":
                        $field = $value;
                        //$value .= "->toRfc3339String()";
                        $value = "is_null({$field}) ? null : {$field}->toRfc3339String()";
                        break;
                    case "json":
                        $value = "is_array({$value}) ? {$value} : json_decode({$value})";
                        break;
                    default:
                }
                $fields[] = "{$assign} => ${value}";
            }

            if($foreignKey){
                $method = $dbRelated[$name]['method'];
                $assign = "'{$dbRelated[$name]['method']}'";
                $type = $dbRelated[$name]['type'];
                if($type === "belongsTo"){
                    $value = "new ". $dbRelated[$name]['class'] . 'Resource($this->'.$method.')';
                }elseif ($type === "morphTo"){
                    $value = "new ". 'MorphResource($this->'.$method.')';
                }

                $fields[] = "{$assign} => ${value}";
            }
        }
        return implode(",\n            ",$fields);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the resource already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource for the given model.'],
            ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection'],
        ];
    }
}
