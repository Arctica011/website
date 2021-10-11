<?php

namespace Jdlx\Task\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *   path="/task",
     *   tags={"Task"},
     *   summary="Retrieve page of tasks",
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
     *      description="Page of tasks",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              allOf={
     *                   @OA\Schema(ref="#/components/schemas/Paginator"),
     *                   @OA\Schema(
     *                       @OA\Property( property="items", type="array",  @OA\Items(ref="#/components/schemas/Task"))
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
        $items = Task::with([  ]);
        $paging = $this->filterSortAndPage($request, $items, Task::getFilterableFields());
        $data = $paging->jsonSerialize();

        $data["items"] = TaskResource::collection($paging->items());
        return response()->success($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/task",
     *     tags={"Task"},
     *     summary="Create a new task",
     *     operationId="create",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Task"
     *          )
     *     ),
     *     @OA\Response(
     *        response="201",
     *        description="Task created",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/Task" )
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

        $fields = $request->only(Task::getWritableFields());

        $task = new Task($fields);
        $task->save();

        return response()->success(new TaskResource($task), 201);
    }

    /**
     * @OA\Get(
     *     path="/task/{id}",
     *     tags={"Task"},
     *     summary="Retrieve a task",
     *     operationId="get",
     *   @OA\Parameter(ref="#/components/parameters/idParameter"),
     *   @OA\Response(
     *      response="200",
     *      description="Retrieving a task",
     *      @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/Task" )
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
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {

        return response()->success(new TaskResource($task));
    }

    /**
     * @OA\Put(
     *     path="/task/{id}",
     *     tags={"Task"},
     *     summary="Update a task",
     *     operationId="update",
     *     @OA\Parameter(ref="#/components/parameters/idParameter"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Task"
     *          )
     *     ),
     *     @OA\Response(
     *        response="200",
     *        description="Updated task",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/Task" )
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
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            // Write validation rules
        ]);

        $fields = $request->only(Task::getEditableFields());
        $task->fill($fields);
        $task->save();
        return response()->success(new TaskResource($task));
    }

    /**
     * @OA\Delete(
     *     path="/task/{id}",
     *     tags={"Task"},
     *     summary="Delete a task",
     *     operationId="delete",
     *     @OA\Parameter(ref="#/components/parameters/idParameter"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Task"
     *          )
     *     ),
     *     @OA\Response(
     *        response="200",
     *        description="Confirmation task",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/Task" )
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
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
         $task->delete();
         return response()->success([
             "id" => $task->id,
             "deleted" => true
         ], 200);
     }

    /**
     * @OA\Post(
     *     path="/task/{id}/restart",
     *     tags={"Task"},
     *     summary="Restart the task",
     *     operationId="restart",
     *     @OA\Parameter(ref="#/components/parameters/idParameter"),
     *     @OA\RequestBody(
     *          required=false
     *     ),
     *     @OA\Response(
     *        response="200",
     *        description="Confirmation",
     *        @OA\JsonContent(
     *          @OA\Property( property="data", ref="#/components/schemas/Task" )
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
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function restart(Task $task)
    {
        $task->restart();
        return response()->success([
            "id" => $task->id,
            "restarted" => true
        ], 200);
    }
}
