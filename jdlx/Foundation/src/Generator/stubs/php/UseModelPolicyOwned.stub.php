<?php

namespace App\Generated\__MODEL__;

use App\Models\User;
use App\Models\__MODEL__;

trait Use__MODEL__PolicyOwned
{
    /**
     * Determine whether the user can take action on the model
     *
     * @param \App\Models\User $userWithRequest
     * @return bool
     */
    public function before(User $userWithRequest): bool
    {
        return $userWithRequest->hasRole("Super Admin");
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $userWithRequest
     * @return bool
     */
    public function viewAny(User $userWithRequest): bool
    {
        return $userWithRequest->hasPermissionTo("*.__CAMEL_MODEL__.view.*");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\__MODEL__ $__CAMEL_MODEL__
     * @return bool
     */
    public function view(User $userWithRequest, __MODEL__ $__CAMEL_MODEL__): bool
    {
        $name = $this->getOwnerName($__CAMEL_MODEL__);
        return $userWithRequest->hasPermissionTo("{$name}.__CAMEL_MODEL__.view.{$__CAMEL_MODEL__->id}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $userWithRequest
     * @return bool
     */
    public function create(User $userWithRequest, $name): bool
    {
        return $userWithRequest->hasPermissionTo("{$name}.__CAMEL_MODEL__.create");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\__MODEL__ $__CAMEL_MODEL__
     * @return bool
     */
    public function update(User $userWithRequest, __MODEL__ $__CAMEL_MODEL__): bool
    {
        $name = $this->getOwnerName($__CAMEL_MODEL__);
        return $userWithRequest->hasPermissionTo("{$name}..__CAMEL_MODEL__.update.{$__CAMEL_MODEL__->id}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\__MODEL__ $__CAMEL_MODEL__
     * @return bool
     */
    public function delete(User $userWithRequest, __MODEL__ $__CAMEL_MODEL__): bool
    {
        $name = $this->getOwnerName($__CAMEL_MODEL__);
        return $userWithRequest->hasPermissionTo("{$name}..__CAMEL_MODEL__.delete.{$__CAMEL_MODEL__->id}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\__MODEL__ $__CAMEL_MODEL__
     * @return bool
     */
    public function restore(User $userWithRequest, __MODEL__ $__CAMEL_MODEL__): bool
    {
        $name = $this->getOwnerName($__CAMEL_MODEL__);
        return $userWithRequest->hasPermissionTo("{$name}..__CAMEL_MODEL__.restore.{$__CAMEL_MODEL__->id}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\__MODEL__ $__CAMEL_MODEL__
     * @return bool
     */
    public function forceDelete(User $userWithRequest, __MODEL__ $__CAMEL_MODEL__): bool
    {
        $name = $this->getOwnerName($__CAMEL_MODEL__);
        return $userWithRequest->hasPermissionTo("{$name}..__CAMEL_MODEL__.delete.{$__CAMEL_MODEL__->id}");
    }

    abstract public function getOwnerName(__MODEL__ $model);
}
