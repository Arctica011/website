<?php

namespace Jdlx\Task\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
  * @OA\Schema(
  *   schema="TaskLogEntry",
  *   description="TaskLogEntry model",
  *   title="TaskLogEntry model",
  *   readOnly=true,
  *
  *   @OA\Property(
  *     property="id",
  *     description="",
  *     readOnly="true",
  *     type="string"
  *   ),
  *   @OA\Property(
  *     property="task_id",
  *     description="",
  *     writeOnly="true",
  *     type="string"
  *   ),
  *   @OA\Property(
  *     property="severity",
  *     description="",
  *     type="string"
  *   ),
  *   @OA\Property(
  *     property="message",
  *     description="",
  *     type="string"
  *   ),
  *   @OA\Property(
  *     property="context",
  *     description="",
  *     type="object"
  *   ),
  *   @OA\Property(
  *     property="created_at",
  *     description="",
  *     readOnly="true",
  *     type="string"
  *   ),
  *   @OA\Property(
  *     property="updated_at",
  *     description="",
  *     readOnly="true",
  *     type="string"
  *   )
  * )
  */

class TaskLogEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            //'task' => new TaskResource($this->task),
            'severity' => $this->severity,
            'message' => $this->message,
            'context' => is_array($this->context) ? $this->context : json_decode($this->context),
            'created_at' => $this->created_at->toRfc3339String(),
            'updated_at' => $this->updated_at->toRfc3339String()
        ];
    }
}
