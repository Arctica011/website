<?php


namespace Jdlx\Task;


use App\Models\TaskLogEntry;

interface TaskLogListener
{
    function onEntry(TaskLogEntry $entry);
}
