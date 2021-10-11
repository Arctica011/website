<?php


namespace Jdlx\Task;


use App\Models\TaskLog;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Log;

abstract class Task
{
    /**
     * @var Job|null
     */
    protected $job = '';

    /**
     * @var TaskLog
     */
    protected $log;

    /**
     * @var string
     */
    protected $taskType = '';


    public function __construct()
    {
        $this->log = new TaskLog();
        $this->log->save();
    }

    /**
     * @throws \Exception
     */
    public function run($catch = false)
    {
        try {
            $this->initialize();
            if ($this->log->status !== TaskLog::NEW) {
                throw new \Exception("The TaskLog should be initialized in the tasks initialize method");
            }

            $this->doRun();
        } catch (\Exception $e) {
            $this->log->failed($e->getMessage(), $e);
            if($catch) {
                return true;
            }else { // Rethrow the exception so the task runner can fail.
                throw $e;
            }
        }
    }

    /**
     * Initialize the taskLog with an appropriate message
     * and get ready to perform the actual task at hand.
     *
     * @return mixed
     */
    abstract protected function initialize();

    /**
     * Entry point for running the task
     *
     * @return mixed
     */
    abstract protected function doRun();

    /**
     * @return TaskLog
     */
    public function getLog(): TaskLog
    {
        return $this->log;
    }
}
