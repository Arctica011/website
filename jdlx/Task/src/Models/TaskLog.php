<?php

namespace Jdlx\Task\Models;

use App\Library\Task\TaskLogListener;
use App\Traits\FieldDescriptor;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LogLevel;

/**
 * @property string id
 * @property string type
 * @property string step
 * @property string status
 * @property object payload
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * Class Tasks
 * @package DalPraS\OAuth2\Client\Resources
 */
class TaskLog extends Model
{
    public const NEW = "NEW";
    public const IN_PROGRESS = "IN_PROGRESS";
    public const FAILED = "FAILED";
    public const COMPLETED = "COMPLETED";

    use UsesUuid;
    use FieldDescriptor;

    /**
     * Listeners to that will be triggered once a task log is created
     *
     * @var array[TaskLogListener]
     */
    protected $listeners = [];

    /**
     * Meta data about the model use for code generation
     * and other automation.
     *
     * @var array
     */
    protected static $fieldSetup = [
        'id' => ['readOnly', 'sortable', 'filterable'],
        'type' => ['editable', 'sortable', 'filterable'],
        'step' => ['editable', 'sortable', 'filterable'],
        'status' => ['editable', 'sortable', 'filterable'],
        'payload' => ['editable', 'sortable', 'filterable'],
        'created_at' => ['readOnly', 'sortable', 'filterable'],
        'updated_at' => ['readOnly', 'sortable', 'filterable']
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'step', 'status', 'payload'
    ];

    /**
     * Cast attributes
     *
     * @var array
     */
    protected $casts = [
        //'payload' => 'json'
    ];


    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::NEW,
        'type' => 'undefined',
        'step' => "1"
    ];

    /**
     * Only complete tasks.
     *
     * @param $query
     * @return mixed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::COMPLETED);
    }

    /**
     * Only complete tasks.
     *
     * @param $query
     * @return mixed
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::FAILED);
    }


    /**
     * Only complete tasks.
     *
     * @param $query
     * @return mixed
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::IN_PROGRESS);
    }

    /**
     * A collection of TaskLog entries representing the progress
     * of the task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany("\App\Models\TaskLogEntry");
    }

    /**
     * The subjects to which this taskLog is attached.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function subjects()
    {
        return $this->morphedByMany('App\Models\TaskLog', 'task_logs_relation');
    }

    /**
     * Initialize the task and write the opening log message
     *
     * @param string $type
     * @param object $payload
     * @param string $msg
     */
    public function initialize($type, $payload, $msg = null)
    {
        $this->type = $type;
        $this->payload = $payload;

        if (empty($msg)) {
            $msg = "Task initialized";
        }

        $this->addLog(LogLevel::INFO, $msg);
        $this->save();
    }

    /**
     * Register progress on the task. Optionally override the task
     * payload, or provide additional context the messge.
     *
     * @param string $msg The progress message
     * @param object $context Additional information as part of this step
     * @param object $payload Override the task payload
     * @return $this
     */
    public function progress($msg, $context = null, $payload = null)
    {
        if (!empty($payload)) {
            $this->payload = $payload;
        }
        $this->status = self::IN_PROGRESS;
        $this->addLog(LogLevel::INFO, $msg, $context);
        $this->save();

        return $this;
    }

    /**
     * Register the task as completed. Optionally override the task
     * payload, or provide additional context the message.
     *
     * @param string $msg The progress message
     * @param object $context Additional information as part of this step
     * @param object $payload Override the task payload
     * @return $this
     */
    public function complete($msg = null, $context = null, $payload = null)
    {
        if (!empty($payload)) {
            $this->payload = $payload;
        }

        if (empty($msg)) {
            $msg = "Task completed";
        }

        $this->status = self::COMPLETED;
        $this->addLog(LogLevel::INFO, $msg, $context);
        $this->save();

        return $this;
    }

    /**
     * Register the task as failed. Optionally override the task
     * payload, or provide additional context the message.
     *
     * @param string $msg The progress message
     * @param object $context Additional information as part of this step
     * @param object $payload Override the task payload
     * @return $this
     */
    public function failed($msg, $context = null)
    {
        $this->status = self::FAILED;
        $this->addLog(LogLevel::ERROR, $msg, $context);
        $this->save();

        return $this;
    }

    public function onEntry(TaskLogListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Add a general log message. Normally the methods: progress(), completed(),
     * failed() should suffice.
     *
     * @param string $severity A log severity from the LogLevel constants
     * @param string $message The message to be added to the log
     * @param object $context Json Payload that helps provide more clarity
     */
    public function addLog($severity, $message, $context = null)
    {
        $entry = new TaskLogEntry([
            'entry_number' => $this->entries()->count() + 1,
            'severity' => $severity,
            'message' => $message,
            'context' => $context,
        ]);

        $this->entries()->save($entry);

        foreach ($this->listeners as $listener) {
            $listener->onEntry($entry);
        }
    }

    public function entryMessages()
    {
        $messages = [];
        $this->entries()
            ->orderBy('entry_number', 'asc')
            ->each(function ($entry) use (&$messages) {
                $messages[] = $entry->message;
            });
        return $messages;
    }

    public function messagesForPrint()
    {
        $entries = $this->entries()
            ->orderBy('entry_number', 'asc')
            ->get();

        $end = sizeof($entries) - 1;
        $state = $this->status;

        $messages = [];
        foreach ($entries as $index => $entry) {
            $isLast = ($end !== 0) && ($index === $end);
            $messages[] = [
                "message" => $entry->message,
                "type" => $entry->type,
                "isLast" => $isLast,
                "state" => $state,
                "payload" => $entry->payload
            ];
        }
        return $messages;
    }
}
