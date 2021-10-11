<?php


namespace Jdlx\Commands\Crud;


use Illuminate\Console\Command;
use Jdlx\Generator\Model;
use Jdlx\Generator\ModelGenerator;
use Jdlx\Generator\ModelUtil;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenerateCrudCommand extends Command
{
    protected static $readOnlyFields = ["id", "created_at", "updated_at"];
    protected static $writeOnlyFields = ["password"];
    protected static $cantEditFields = ["id", "created_at", "updated_at"];
    protected static $cantSortFields = [];
    protected static $cantFilterField = [];

    use ModelUtil;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(Re)generate all traits and models to support CRUD based on a table';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $model = new Model($this->getTableInput(), $this->getModelInput());
        $generator = new ModelGenerator($model);
        $generator->generate();

        $scaffold = $this->option("scaffold");
        $force = $this->option("force");

        if($scaffold){
            $generator->scaffold($force, true);
        }

    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['table', InputArgument::REQUIRED, 'The table for which we need to build']
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
            ['defaults', 'd', InputOption::VALUE_NONE, 'Skip any questions about modifying field attributes'],
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The name of the model to be generated'],
            ['scaffold', 's', InputOption::VALUE_NONE, 'Generate the scaffolding'],
            ['force', 'f', InputOption::VALUE_NONE, 'Force update the scaffolding'],
        ];
    }

    protected function getTableInput()
    {
        return $this->argument("table");
    }

    protected function getModelInput()
    {
        $default = $this->tableToModel($this->getTableInput());
        return $this->option("model") ?? $default;
    }
}
