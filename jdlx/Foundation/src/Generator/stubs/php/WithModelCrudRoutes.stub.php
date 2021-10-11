<?php

namespace App\Generated\__MODEL__;

use App\Models\__MODEL__;
use Illuminate\Http\Request;
use Jdlx\Http\Controllers\Traits\CanFilterSortAndPage;

trait With__MODEL__CrudRoutes
{
    use CanFilterSortAndPage;

    public function index(Request $request)
    {
        $this->authorize('viewAny', __MODEL__::class);

        $items = __MODEL__::with([]);
        $paging = $this->filterSortAndPage($request, $items, __MODEL__::getFilterableFields());
        $data = $paging->jsonSerialize();

        $resource = $this->getResourceClass();
        $data["items"] = call_user_func($resource . '::collection', $paging->items());
        return response()->success($data, 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', [__MODEL__::class]);

        $request->validate($this->getValidationRules("create"));

        $fields = $request->only(__MODEL__::getWritableFields());

        $__CAMEL_MODEL__ = new __MODEL__($fields);
        $__CAMEL_MODEL__->save();

        $resource = $this->getResourceClass();
        return response()->success(new $resource($__CAMEL_MODEL__), 201);
    }


    public function show(__MODEL__ $__CAMEL_MODEL__)
    {
        $this->authorize('view', $__CAMEL_MODEL__);

        $resource = $this->getResourceClass();
        return response()->success(new $resource($__CAMEL_MODEL__));
    }


    public function update(Request $request, __MODEL__ $__CAMEL_MODEL__)
    {
        $this->authorize('update', $__CAMEL_MODEL__);

        $request->validate($request->validate($this->getValidationRules("update")));
        $fields = $request->only(__MODEL__::getEditableFields());
        $__CAMEL_MODEL__->fill($fields)->save();
        $resource = $this->getResourceClass();
        return response()->success(new $resource($__CAMEL_MODEL__));
    }

    public function destroy(__MODEL__ $__CAMEL_MODEL__)
    {
        $this->authorize('delete', $__CAMEL_MODEL__);

        $__CAMEL_MODEL__->delete();
        return response()->success([
            "id" => $__CAMEL_MODEL__->id,
            "deleted" => true
        ], 200);
    }

    protected function getValidationRules($for = "create")
    {
        return __MODEL__Fields::validation($for);
    }

    /**
     * Return the resource class to be used to return responses
     * defaults to the generated resource, but can be overwritten
     * to alter responses
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return __MODEL__Resource::class;
    }
}
