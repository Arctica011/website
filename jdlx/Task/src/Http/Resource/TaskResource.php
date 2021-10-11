<?php

namespace Jdlx\Task\Http\Resources;

use App\TaskLogEntry;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Task",
 *   description="Task model",
 *   title="Task model",
 *   readOnly=true,
 *
 *   @OA\Property(
 *     property="id",
 *     description="",
 *     readOnly="true",
 *     type="string"
 *   ),
 *   @OA\Property(
 *     property="type",
 *     description="",
 *     type="string"
 *   ),
 *   @OA\Property(
 *     property="step",
 *     description="",
 *     type="string"
 *   ),
 *   @OA\Property(
 *     property="status",
 *     description="",
 *     type="string"
 *   ),
 *   @OA\Property(
 *     property="payload",
 *     description="",
 *     type="string"
 *   ),
 *   @OA\Property(
 *     property="log",
 *     type="array",
 *     @OA\items(
 *      ref="#/components/schemas/TaskLogEntry"
 *     )
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
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'step' => $this->step,
            'status' => $this->status,
            'payload' => $this->payload,
            'log' => TaskLogEntryResource::collection($this->entries),
            'created_at' => $this->created_at->toRfc3339String(),
            'updated_at' => $this->updated_at->toRfc3339String()
        ];
    }
}
