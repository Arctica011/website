<?php

namespace Jdlx\Task\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskLogEntryResource;
use App\TaskLogEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskLogEntryController extends Controller
{
    /**
     * @OA\Get(
     *   path="/task-log-entry",
     *   tags={"TaskLogEntry"},
     *   summary="Retrieve page of taskLogEntrys",
     *   operationId="list",
     *
     *   @OA\Parameter(ref="#/components/parameters/pageParameter"),
     *   @OA\Parameter(ref="#/components/parameters/limitParameter"),
     *   @OA\Parameter(ref="#/components/parameters/sortParameter"),
     *   @OA\Parameter(ref="#/components/parameters/filterParameter"),
     *
     *   @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *   @OA\Response(
     *      response="200",
     *      description="Page of taskLogEntrys",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              allOf={
     *                   @OA\Schema(ref="#/components/schemas/Paginator"),
     *                   @OA\Schema(
     *                       @OA\Property( property="items", type="array",  @OA\Items(ref="#/components/schemas/TaskLogEntry"))
     *                  ),
     *               }
     *          )
     *       )
     *    ),
     *    security={
     *      {
     *          "bearer_auth": {},
     *          "cookie_auth": {},
     *      }
     *    },
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $items = TaskLogEntry::with([ "task" ]);
        $paging = $this->filterSortAndPage($request, $items, TaskLogEntry::getFilterableFields());
        $data = $paging->jsonSerialize();

        $data["items"] = TaskLogEntryResource::collection($paging->items());
        return response()->success($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/task-log-entry",
     *     tags={"TaskLogEntry"},
     *     summary="Create a new taskLogEntry",
     *     operationId="create",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/TaskLogEntry"
     *          )
     *     ),
     *     @OA\Response(
     *        response="201",
     *        description="TaskLogEntry created",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/TaskLogEntry" )
     *        )
     *    ),
     *    @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *    @OA\Response(response="422", ref="#/components/responses/Invalid"),
     *
     *    security={
     *      {
     *          "bearer_auth": {},
     *          "cookie_auth": {},
     *      }
     *    },
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // Add validation rules
        ]);

        $fields = $request->only(TaskLogEntry::getWritableFields());

        $taskLogEntry = new TaskLogEntry($fields);
        $taskLogEntry->save();

        return response()->success(new TaskLogEntryResource($taskLogEntry), 201);
    }

    /**
     * @OA\Get(
     *     path="/task-log-entry/{id}",
     *     tags={"TaskLogEntry"},
     *     summary="Retrieve a taskLogEntry",
     *     operationId="get",
     *   @OA\Parameter(ref="#/components/parameters/idParameter"),
     *   @OA\Response(
     *      response="200",
     *      description="Retrieving a taskLogEntry",
     *      @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/TaskLogEntry" )
     *       )
     *    ),
     *    @OA\Response(response="404", ref="#/components/responses/NotFound"),
     *    @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     security={
     *      {
     *          "bearer_auth": {},
     *          "cookie_auth": {},
     *      }
     *    },
     * )
     *
     *
     * @param  \App\TaskLogEntry  $taskLogEntry
     * @return \Illuminate\Http\Response
     */
    public function show(TaskLogEntry $taskLogEntry)
    {

        return response()->success(new TaskLogEntryResource($taskLogEntry));
    }

    /**
     * @OA\Put(
     *     path="/task-log-entry/{id}",
     *     tags={"TaskLogEntry"},
     *     summary="Update a taskLogEntry",
     *     operationId="update",
     *     @OA\Parameter(ref="#/components/parameters/idParameter"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/TaskLogEntry"
     *          )
     *     ),
     *     @OA\Response(
     *        response="200",
     *        description="Updated taskLogEntry",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/TaskLogEntry" )
     *        )
     *    ),
     *    @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *    @OA\Response(response="422", ref="#/components/responses/Invalid"),
     *     security={
     *      {
     *          "bearer_auth": {},
     *          "cookie_auth": {},
     *      }
     *    },
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaskLogEntry  $taskLogEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaskLogEntry $taskLogEntry)
    {
        $request->validate([
            // Write validation rules
        ]);

        $fields = $request->only(TaskLogEntry::getEditableFields());
        $taskLogEntry->fill($fields);
        $taskLogEntry->save();
        return response()->success(new TaskLogEntryResource($taskLogEntry));
    }

    /**
     * @OA\Delete(
     *     path="/task-log-entry/{id}",
     *     tags={"TaskLogEntry"},
     *     summary="Delete a taskLogEntry",
     *     operationId="delete",
     *     @OA\Parameter(ref="#/components/parameters/idParameter"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/TaskLogEntry"
     *          )
     *     ),
     *     @OA\Response(
     *        response="200",
     *        description="Confirmation taskLogEntry",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/TaskLogEntry" )
     *        )
     *    ),
     *
     *    @OA\Response(response="404", ref="#/components/responses/NotFound"),
     *     security={
     *      {
     *          "bearer_auth": {},
     *          "cookie_auth": {},
     *      }
     *    },
     * )
     *
     * @throws \Exception
     * @param  \App\TaskLogEntry  $taskLogEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskLogEntry $taskLogEntry)
    {
         $taskLogEntry->delete();
         return response()->success([
             "id" => $taskLogEntry->id,
             "deleted" => true
         ], 200);
     }
}
