<?php


namespace  Jdlx\Commands\Make;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApiFront extends GeneratorCommand
{
    use ModelUtil;
    use WithStubs;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;


    protected $directory;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api:front';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create scaffolding for front-end';


    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $model = $this->argument('model');
        $this->directory = $this->argument('directory') ?? Str::kebab($model);


        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        $modelPath = parent::getPath($this->qualifyModel($model));
        if (!$this->alreadyExists($modelPath)) {
            $this->error('Model doesn\'t exist!');
            return false;
        }


        $this->directory = $this->getPath($this->directory);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->directory)) {
            $this->error('Destination directory already exists!');
            return false;
        }

        $vars = [
            "{{ entity }}" => $model,
            "{{ lc_entity }}" => Str::kebab($model),
            "{{ human_entity }}" => ucwords(Str::slug($model, " ")),
            "{{ kebab_entity }}" => Str::kebab($model),
            "{{ camel_entity }}" => Str::camel($model),
        ];
        $this->writeStub("with{$model}", 'front/withEntity', $vars);
        $this->writeStub("{$model}CrudRoutes", 'front/EntityCrudRoutes', $vars);

        $this->writeStub("components/{$model}AddCard", 'front/components/EntityAddCard', $vars);
        $this->writeStub("components/{$model}Details", 'front/components/EntityDetails', $vars);
        $this->writeStub("components/{$model}EditCard", 'front/components/EntityEditCard', $vars);
        $this->writeStub("components/{$model}Form", 'front/components/EntityForm', $vars);
        $this->writeStub("components/{$model}List", 'front/components/EntityList', $vars);
        $this->writeStub("components/{$model}AddCard", 'front/components/EntityAddCard', $vars);
        $this->writeStub("components/{$model}ViewCard", 'front/components/EntityViewCard', $vars);


        $this->writeModel($model, $vars, $this->qualifyModel($model));

        $this->info($this->type . ' created successfully.');
    }

    protected function writeModel($model, $vars, $class)
    {
        $fields = $this->getFieldDefinitions($class, true, true);
        $relationships = $this->getRelationShips($class);

        foreach ($relationships as $field => $relationship){
            if(isset($fields[$field])){
                $fields[$field]['writeOnly'] = false;
                $fields[$field]['readOnly'] = true;
                $fields[$field]['type'] = $relationship['type'];
                $fields[$field]['model'] = $relationship['class'];
                $fields[$field]['namespace'] = $relationship['method'];
                $fields[$field]['label'] = 'name';
                $fields[$field]['name'] = $relationship['method'];
            }
            if($fields[$field]['type'] === "morphTo") {
                $typeField = preg_replace("/_id$/m", "_type", $field);
                $fields[$typeField]['writeOnly'] = true;
            }
        }

        //dump($fields);


        $vars["{{ fields }}"] = json_encode(array_values($fields), JSON_PRETTY_PRINT);


        $this->writeStub($model . "Model", 'front/EntityModel', $vars);
    }

    protected function writeStub($destination, $stub, $vars)
    {
        $dest =$this->directory."/".$destination.".js";
        $content = $this->fromStub($stub, $vars);
        $this->makeDirectory($dest);
        $this->files->put($dest, $content);
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return $this->files->exists($path);
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($directory)
    {
        return $this->laravel['path'] . '/../webclient/src/lib/models/' . str_replace('\\', '/', $directory);
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
            ['directory', InputArgument::OPTIONAL, 'An alternative directory name for the model'],
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

    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }
}
