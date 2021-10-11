<?php

namespace Jdlx\Task;

use Illuminate\Support\Str;

trait TaskGroup
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tasks()
    {
        return $this->morphToMany('App\Task', 'task_relation');
    }
}
