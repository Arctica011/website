<?php


namespace Jdlx\Commands\Make;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeApiModel extends GeneratorCommand
{
    protected static $readOnlyFields = ["id", "created_at", "updated_at"];
    protected static $writeOnlyFields = ["password"];
    protected static $cantEditFields = ["id", "created_at", "updated_at"];
    protected static $cantSortFields = [];
    protected static $cantFilterField = [];

    use ModelUtil;
    use WithStubs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class specific to apis';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $name = $this->qualifyTraitClass($this->getNameInput());
        $path = $this->getTraitPath($name);

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildTrait($name)));

        $this->info($this->type.' trait created successfully.');


        // Generate the trait

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ($this->alreadyExists($this->getNameInput())) {
            // we do not error out.
            // we simply do not overwrite
            //$this->error($this->type.' already exists!');

            return 1;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     * @return string
     */
    protected function buildTrait($name)
    {

        $replace = [];
        $replace = $this->buildFields($replace);
        $stub = $this->files->get($this->getTraitStub());
        return str_replace(
            array_keys($replace), array_values($replace), $stub
        );
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
        $replace = $this->buildFields($replace);

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Models';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFields($replace)
    {
        try {
            $fields = $this->getDbFields($this->parseModel($this->getNameInput()));
            $related = $this->getRelationShips($this->parseModel($this->getNameInput()));
        } catch (\Exception $e) {
            dump($e);
            $this->error("The table associated with the model does not exist. Create and run a migration");
            exit;
        }

        $prepped = $this->prepFields($fields);
        if (!$this->input->getOption("defaults")) {
            $this->modifyfFields($prepped);
        }

        $name = $this->getNameInput();

        return array_merge($replace, [
            '{{ trait }}' => $name."Crud",
            '{{ trait_namespace }}' =>  $this->getTraitNamespace(),
            '{{ php_doc_props }}' => $this->getDocProps($fields, $related),
            '{{ fieldSetup }}' => $this->getFields($prepped),
            '{{ fillable }}' => $this->getFillable($prepped),
            '{{ casts }}' => $this->getCasts($fields),
            '{{ relationships }}' => $this->getRelated($related),
            '{{ table_name }}' => $this->getTableAssignment(),
            '{{ id_field }}' => $this->getPkAssignment($fields)
        ]);
    }

    protected function getPkAssignment($fields)
    {
        return 'protected $primaryKey = "' . $fields[0]['name'] . '";';
    }


    protected function getTableAssignment()
    {
        $table = $this->getTableInput();

        if (empty($table)) {
            return "";
        }
        return 'protected $table = "' . $table . '";';
    }

    protected function modifyfFields($fields)
    {
        $options = [];
        foreach ($fields as $name => $vars) {
            $options[$name] = " [" . implode(",", $this->fieldOptions($vars)) . "]";
        }

        $options['x'] = "done";

        $fieldName = $this->choice("Select a field to update", $options, "x", null, false);

        if ($fieldName !== "x") {
            $this->updateField($fieldName, $fields[$fieldName]);
            $this->modifyfFields($fields);
        }
    }

    protected function updateField($name, &$field)
    {
        $options = [];
        $default = [];

        foreach ($field as $k => $val) {
            $options[sizeof($options) + 1] = $k;
            if ($val) {
                $default[] = $k;
            }
        }
        $options['x'] = "done";

        $selected = implode(",", $default);
        $q = "Set options for field [<fg=cyan>{$name}</info>] [<fg=yellow>{$selected}</>]";

        $res = $this->choice($q, $options, "x", null, false);
        $key = $options[$res];
        if ($res !== "x") {
            $field[$key] = !$field[$key];
            $this->output;
            $this->updateField($name, $field);
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $template = trim($this->option('template')) ?: "model";
        if ($template) {
            return $this->resolveStubPath("/stubs/{$template}.stub");
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getTraitStub()
    {
        $template = trim($this->option('template')) ?: "model";
        return $this->resolveStubPath("/stubs/{$template}.crud.trait.stub");
    }

    protected function prepFields($fields)
    {
        $prepped = [];
        foreach ($fields as $field) {
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

    function fieldOptions($field)
    {
        return array_keys(array_filter($field, function ($x) {
            return $x === true;
        }));
    }

    protected function getFillable($fieldInfo)
    {
        $fillable = [];
        foreach ($fieldInfo as $name => $options) {
            if (!$options['readOnly']) {
                $fillable[] = "'$name'";
            }
        }
        return implode(", ", $fillable);
    }

    protected function getCasts($fieldInfo)
    {
        $casts = [];
        foreach ($fieldInfo as $info) {
            $name = $info["name"];
            $type = $info["type"];

            if ($type === "json") {
                $casts[] = "'${name}' => 'json'";
            }

            if ($type === "timestamp") {
                $casts[] = "'${name}' => 'datetime'";
            }
        }

        return implode(",\n        ", $casts);
    }

    protected function getFields($fieldInfo)
    {
        $fields = [];

        foreach ($fieldInfo as $name => $options) {
            $args = $this->fieldOptions($options);
            $fields[] = "'{$name}' => ['" . implode("', '", $args) . "']";
        }
        return implode(",\n            ", $fields);
    }

    protected function getDocProps($fieldInfo, $related)
    {
        $props = [];
        foreach ($fieldInfo as $info) {
            $name = $info["name"];
            $type = $info["type"];

            switch ($type) {
                case "integer":
                    $type = "integer";
                    break;
                case "json":
                    $type = "object";
                    break;
                case "datetime":
                case "timestamp":
                    $type = "Carbon";
                    break;
                case "text":
                case "textarea":
                default:
                    $type = "string";
                    break;
            }

            $props[] = "@property {$type} {$name}";
        }

        return implode("\n * ", $props);
    }

    protected function getRelated($fieldInfo)
    {
        $functions = [];
        foreach ($fieldInfo as $field) {
            $stub = "function.{$field['type']}";
            $replacements = [
                "{{ entity }}" => $field['method'],
                "{{ relationship_class }}" => $field['fdn_class'],
                "{{ db_name }}" => $field['db_name'],
            ];

            $functions[] = $this->fromStub($stub, $replacements);
        }

        return implode("\n", $functions);
    }


    /**
     * Resolve the fully-qualified path to the stub.
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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['defaults', 'd', InputOption::VALUE_NONE, 'Skip any questions about modifying field attributes'],
            ['table', 't', InputOption::VALUE_REQUIRED, 'Table name to be used to generate the model'],
            ['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],
            ['template', 'f', InputOption::VALUE_REQUIRED, 'Stub file to be used for generation'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getTableInput()
    {
        return trim($this->option('table'));
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyTraitClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\CrudTraits\\'.$name."Crud"
        );
    }

    protected function getTraitNamespace(){

        $rootNamespace = $this->rootNamespace();

        return $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\CrudTraits';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getTraitPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

}
