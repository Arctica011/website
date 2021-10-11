<?php


namespace Jdlx\Commands\Crud;


use Illuminate\Console\Command;
use Jdlx\Generator\Model;
use Jdlx\Generator\ModelGenerator;
use Jdlx\Generator\ModelUtil;
use Jdlx\Generator\Path;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ScaffoldComponentCrudCommand extends Command
{
    use ModelUtil;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:scaffold:component';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new component for a given model and page';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $model = new Model($this->getTableInput(), $this->getModelInput());
        $generator = new ModelGenerator($model);
        $namespace = $this->getNamespaceInput();
        $path = $this->getPathInput();

        $types = $this->getTypeInput();
        if(in_array("all", $types)){
            $types = ModelGenerator::COMPONENT_TYPES;
        }

        foreach ($types as $type){
            if(!in_array($type, ModelGenerator::COMPONENT_TYPES)){
                $this->warn("Invalid type {$type}, not generated");
            }
            $dest = $generator->generateFrontEndComponent($type, $namespace, $path, $this->option("force"));
            $dest = Path::toRelative(getcwd(), $dest);
            $this->info("${type}Component generated at [{$dest}]");

        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getArguments()
    {
        $types = implode("|", [...ModelGenerator::COMPONENT_TYPES, "all"]);
        return [
            ['table', InputArgument::REQUIRED, 'The table for which we need to build'],
            ['type', InputArgument::IS_ARRAY, "The type of page [{$types}]"]
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
            ['namespace', 'ns', InputOption::VALUE_REQUIRED, 'Change the modelname on the filename to this'],
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The name of the model to be generated'],
            ['path', 'p', InputOption::VALUE_REQUIRED, 'Directory within pages this should be placed'],
            ['force', 'f', InputOption::VALUE_NONE, 'Force update the scaffolding'],
        ];
    }

    protected function getTableInput()
    {
        return $this->argument("table");
    }

    protected function getTypeInput()
    {
        return $this->argument("type");
    }

    protected function getModelInput()
    {
        $default = $this->tableToModel($this->getTableInput());
        return $this->option("model") ?? $default;
    }

    protected function getNamespaceInput()
    {
        $default = $this->tableToModel($this->getModelInput());
        return $this->option("namespace") ?? $default;
    }

    protected function getPathInput()
    {
        return $this->option("path") ?? "";
    }
}
