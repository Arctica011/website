<?php


namespace  Jdlx\Commands\Make;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApi extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(Re)generate all resources related to an API';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('resource', true);
            $this->input->setOption('testcase', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('resource', true);
        }

        $this->createModel();

        if ($this->option('resource')) {
            $this->createResource();
        }

        if ($this->option('controller')) {
            $this->createController();
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('testcase')) {
            $this->createTestCase();
        }
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createModel()
    {
        $model = $this->argument('model');

        $this->call('make:api:model', [
            'name' => $model,
            '--force' => $this->option('force'),
            '--defaults' => $this->option('defaults'),
        ]);
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createResource()
    {
        $model = $this->argument('model');

        $this->call('make:api:resource', [
            'name' => $model . "Resource",
            '--model' => $model,
            '--force' => $this->option('force')
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $model = $this->argument('model');

        $this->call('make:api:controller', array_filter([
            'name' => "{$model}Controller",
            '--model' => $model,
            '--force' => $this->option('force')
        ]));
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $model = Str::studly(class_basename($this->argument('model')));

        $this->call('make:api:factory', [
            'model' => "{$model}",
            '--force' => $this->option('force')
        ]);
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createTestCase()
    {
        $model = Str::studly(class_basename($this->argument('model')));

        $this->call('make:api:testcase', [
            'model' => "{$model}",
            '--force' => $this->option('force')
        ]);
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a, factory, resource, controller and testcase for the model'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Create a resource for the model'],
            ['testcase', 't', InputOption::VALUE_NONE, 'Create a testcase for the model'],
            ['force', null, InputOption::VALUE_NONE, 'Force everything to be overwritten'],
            ['defaults', null, InputOption::VALUE_NONE, 'Stick to defaults'],
        ];
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
}
