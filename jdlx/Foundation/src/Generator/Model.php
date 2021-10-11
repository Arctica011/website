<?php

namespace Jdlx\Generator;

use Illuminate\Support\Str;
use Jdlx\Generator\Path\ClassFile;
use Jdlx\Generator\Path\Directory;

class Model
{
    use ModelUtil;

    protected static $readOnlyFields = ["id", "created_at", "updated_at"];
    protected static $writeOnlyFields = ["password"];
    protected static $cantEditFields = ["id", "created_at", "updated_at"];
    protected static $cantSortFields = [];
    protected static $cantFilterField = [];

    /**
     * Name of the resulting model
     *
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var
     */
    protected $namespace;

    public function __construct($table, $model)
    {
        $this->table = $table;
        $this->model = $model;
    }

    public function getModelName(){
        return $this->model;
    }

    public function exists(){
        return $this->modelExists($this->model);
    }

    public function addStubReplacements(Stub $stub)
    {
        $model = $this->model;
        $this->getFrontEndStubDirectory();
        $stub
            ->generatePlaceholders("entity", $model)
            ->generatePlaceholders("lc_entity", Str::studly($model))
            ->generatePlaceholders("human_entity", ucwords(Str::slug(Str::kebab($model), " ")))
            ->generatePlaceholders("kebab_entity", Str::kebab($model))
            ->generatePlaceholders("camel_entity", Str::camel($model))
            ->generatePlaceholders("model", $model)
            ->generatePlaceholders("lc_model", Str::camel($model))
            ->generatePlaceholders("human_model", ucwords(Str::slug(Str::kebab($model), " ")))
            ->generatePlaceholders("kebab_model", Str::kebab($model))
            ->generatePlaceholders("camel_model", Str::camel($model))
            ->generatePlaceholders("js_root", Path::getFullPath("/webclient"));
    }

    public function getFrontEndStubDirectory(): Directory
    {
        return new Directory(__DIR__ . "/stubs/js/generated");
    }

    public function getFrontEndDestinationDirectory(): string
    {
        return Path::getFullPath("/webclient/src/generated");
    }

    public function getFieldsPath(): ClassFile
    {
        return $this->getGeneratedFQNPath("{$this->model}Fields");
    }

    public function getResourcePath(): ClassFile
    {
        return $this->getGeneratedFQNPath("{$this->model}Resource");
    }

    public function getGlobalPolicyPath(): ClassFile
    {
        return $this->getGeneratedFQNPath("Use{$this->model}PolicyGlobal");
    }

    public function getOwnedPolicyPath(): ClassFile
    {
        return $this->getGeneratedFQNPath("Use{$this->model}PolicyOwned");
    }

    public function getCrudFieldsTraitPath(): ClassFile
    {
        return $this->getGeneratedFQNPath("With{$this->model}CrudFields");
    }

    public function getCrudRoutesTraitPath(): ClassFile
    {
        return $this->getGeneratedFQNPath("With{$this->model}CrudRoutes");
    }

    public function getControllerScaffoldPath(): ClassFile
    {
        return ClassFile::fromFQN("App\\Http\\Controllers\\Api\\{$this->model}Controller");
    }

    public function getModelScaffoldPath(): ClassFile
    {
        return ClassFile::fromFQN("App\\Models\\{$this->model}");
    }

    public function getFactoryScaffoldPath(): ClassFile
    {
        return ClassFile::fromFQN("Database\\Factories\\{$this->model}Factory");
    }

    public function getResourceScaffoldPath(): ClassFile
    {
        return ClassFile::fromFQN("App\\Http\\Resources\\{$this->model}Resource");
    }

    public function getPolicyScaffoldPath(): ClassFile
    {
        return ClassFile::fromFQN("App\\Policies\\{$this->model}Policy");
    }


    protected function getGeneratedFQNPath($class): ClassFile
    {
        return ClassFile::fromFQN($this->getGeneratedFQN($class));
    }

    protected function getGeneratedFQN($class): string
    {
        return $this->getGeneratedNamespace() . "\\" . $class;
    }

    protected function getGeneratedNamespace(): string
    {
        return "App\\Generated\\{$this->model}";
    }

    public function getFieldInfo(): array
    {
        return $this->getDbFields();
    }

    public function getFieldDefinitions($configOnly = false, $withIndex = false): array
    {
        $modelClass = $this->parseModel($this->model);

        $configurations = $modelClass::getFieldConfigurations();
        $sortOrder = array_keys($configurations);
        $fields = $this->getDbFields();
        $indexed = [];

        foreach ($fields as $field) {
            $indexed[$field['name']] = $field;
        }

        $sorted = [];
        foreach ($sortOrder as $index) {
            if ($indexed[$index]) {
                $sorted[$index] = array_merge($indexed[$index], $configurations[$index]);
            }
            unset($indexed[$index]);
        }

        if (!$withIndex) {
            $sorted = array_values($sorted);
            $indexed = array_values($indexed);
        }

        if ($configOnly) {
            return $sorted;
        }

        return array_merge($sorted, $indexed);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }


    public function getFieldAccess(): array
    {
        $prepped = [];
        foreach ($this->getFieldInfo() as $field) {
            $name = $field["name"];
            $prepped[$name] = [
                "editable" => !in_array($name, self::$cantEditFields),
                "readOnly" => in_array($name, self::$readOnlyFields),
                "writeOnly" => in_array($name, self::$writeOnlyFields),
                "sortable" => !in_array($name, self::$cantSortFields),
                "filterable" => !in_array($name, self::$cantFilterField)
            ];
        }
        return $prepped;
    }
}
