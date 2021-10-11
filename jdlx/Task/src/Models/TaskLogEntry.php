<?php

namespace Jdlx\Task\Models;

use App\Traits\FieldDescriptor;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string id
 * @property string task_id
 * @property string severity
 * @property integer entry_number
 * @property string message
 * @property object context
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * Class TaskLogEntry
 * @package DalPraS\OAuth2\Client\Resources
 */
class TaskLogEntry extends Model
{
    use UsesUuid;
    use FieldDescriptor;

   /**
     * Meta data about the model use for code generation
     * and other automation.
     *
     * @var array
     */
    protected static $fieldSetup = [
            'id' => ['readOnly', 'sortable', 'filterable'],
            'task_log_id' => ['editable', 'sortable', 'filterable'],
            'entry_number' => ['editable', 'sortable', 'filterable'],
            'severity' => ['editable', 'sortable', 'filterable'],
            'message' => ['editable', 'sortable', 'filterable'],
            'context' => ['editable', 'sortable', 'filterable'],
            'created_at' => ['readOnly', 'sortable', 'filterable'],
            'updated_at' => ['readOnly', 'sortable', 'filterable']
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_number', 'task_log_id', 'severity', 'message', 'context'
    ];

    /**
     * Cast attributes
     *
     * @var array
     */
    protected $casts = [
        'context' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo('\App\Task');
    }

}
