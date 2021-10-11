<?php


namespace Jdlx\Task;

use \Illuminate\Console\OutputStyle;
use App\Models\TaskLogEntry;
use Illuminate\Console\Command;
use Psr\Log\LogLevel;

class CommandTaskLogListener implements TaskLogListener
{
    /**
     * @var Command $command
     */
    protected $command;

    /**
     * CommandTaskLogListener constructor.
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    function onEntry(TaskLogEntry $entry)
    {
        $message = $entry->message;
        $output = $this->command->getOutput();

        switch ($entry->severity) {
            case LogLevel::DEBUG:
                if ($output->isVerbose()) {
                    $output->comment($message);
                }
                break;
            case LogLevel::NOTICE:
                if ($output->isVerbose()) {
                    $output->info($message);
                }
                break;
            case LogLevel::INFO:
                $this->printProgress($message, $output);
                break;

            case LogLevel::WARNING:
            case LogLevel::ALERT:
                $output->warning($message);
                break;

            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                $output->error($message);
                break;
        }
    }

    protected function printProgress(string $message, OutputStyle $output)
    {
        $output->block($message, null, 'fg=cyan', '-- ', false);
    }


}
