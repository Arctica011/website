<?php


namespace Jdlx\ModelExplorer;


use Jdlx\ModelExplorer\Traits\AutoSet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Explorer
{
    use AutoSet;

    // --------------
    // CRUD variables
    // --------------
    // These variables are passed to the CRUD views, inside the $crud variable.
    // All variables are public, so they can be modified from your EntityCrudController.
    // All functions and methods are also public, so they can be used in your EntityCrudController to modify these variables.

    public $model; // what's the namespace for your entity's model

    // The following methods are used in CrudController or your EntityCrudController to manipulate the variables above.

    public function __construct($model = null)
    {
        if ($model) {
            $this->setModel($model);
        }
    }



    // ------------------------------------------------------
    // BASICS - model, route, entity_name, entity_name_plural
    // ------------------------------------------------------

    /**
     * This function binds the CRUD to its corresponding Model (which extends Eloquent).
     * All Create-Read-Update-Delete operations are done using that Eloquent Collection.
     *
     * @param string $model_namespace Full model namespace. Ex: App\Models\Article
     *
     * @throws \Exception in case the model does not exist
     */
    public function setModel($model_namespace)
    {
        if (!class_exists($model_namespace)) {
            throw new \Exception('The model does not exist.', 500);
        }

        $this->model = new $model_namespace();
        $this->query = $this->model->select('*');
        $this->entry = null;
    }

    /**
     * Get the corresponding Eloquent Model for the CrudController, as defined with the setModel() function.
     *
     * @return string|\Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the database connection, as specified in the .env file or overwritten by the property on the model.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    private function getSchema()
    {
        return $this->getConnection()->getSchemaBuilder();
    }

    private function getDefaultConfig()
    {
        // Automatically-set columns should be both in the database, and in the $fillable variable on the Eloquent Model
        $defaultConn = config('database.default');
        $connections = config('database.connections');
        return $connections[$defaultConn];
    }

    private function getConnection()
    {
        if ($this->model) {
            return $this->model->getConnection();
        }

        return Model::resolveConnection(config('database.default'));
    }

    /**
     * Check if the database connection driver is using mongodb.
     *
     * @return bool
     */
    private function driverIsMongoDb()
    {
        return $this->getConnection()->getConfig()['driver'] === 'mongodb';
    }



    // ----------------------------------
    // Miscellaneous functions or methods
    // ----------------------------------

    /**
     * Return the first element in an array that has the given 'type' attribute.
     *
     * @param string $type
     * @param array $array
     *
     * @return array
     */
    public function getFirstOfItsTypeInArray($type, $array)
    {
        return Arr::first($array, function ($item) use ($type) {
            return $item['type'] == $type;
        });
    }

    // ------------
    // TONE FUNCTIONS - UNDOCUMENTED, UNTESTED, SOME MAY BE USED IN THIS FILE
    // ------------
    //
    // TODO:
    // - figure out if they are really needed
    // - comments inside the function to explain how they work
    // - write docblock for them
    // - place in the correct section above (CREATE, READ, UPDATE, DELETE, ACCESS, MANIPULATION)

    public function sync($type, $fields, $attributes)
    {
        if (!empty($this->{$type})) {
            $this->{$type} = array_map(function ($field) use ($fields, $attributes) {
                if (in_array($field['name'], (array)$fields)) {
                    $field = array_merge($field, $attributes);
                }

                return $field;
            }, $this->{$type});
        }
    }

    /**
     * Get the Eloquent Model name from the given relation definition string.
     *
     * @param string $relationString Relation string. A dot notation can be used to chain multiple relations.
     * @param int $length Optionally specify the number of relations to omit from the start of the relation string. If
     *                                                            the provided length is negative, then that many relations will be omitted from the end of the relation
     *                                                            string.
     * @param \Illuminate\Database\Eloquent\Model $model Optionally specify a different model than the one in the crud object.
     *
     * @return string Relation model name.
     * @example For a given string 'company' and a relation between App/Models/User and App/Models/Company, defined by a
     *          company() method on the user model, the 'App/Models/Company' string will be returned.
     * @example For a given string 'company.address' and a relation between App/Models/User, App/Models/Company and
     *          App/Models/Address defined by a company() method on the user model and an address() method on the
     *          company model, the 'App/Models/Address' string will be returned.
     *
     */
    public function getRelationModel($relationString, $length = null, $model = null)
    {
        $relationArray = explode('.', $relationString);

        if (!isset($length)) {
            $length = count($relationArray);
        }

        if (!isset($model)) {
            $model = $this->model;
        }

        $result = array_reduce(array_splice($relationArray, 0, $length), function ($obj, $method) {
            try {
                $result = $obj->$method();

                return $result->getRelated();
            } catch (\Exception $e) {
                return $obj;
            }
        }, $model);

        return get_class($result);
    }

    /**
     * Get the given attribute from a model or models resulting from the specified relation string (eg: the list of streets from
     * the many addresses of the company of a given user).
     *
     * @param \Illuminate\Database\Eloquent\Model $model Model (eg: user).
     * @param string $relationString Model relation. Can be a string representing the name of a relation method in the given
     *                                                            Model or one from a different Model through multiple relations. A dot notation can be used to specify
     *                                                            multiple relations (eg: user.company.address).
     * @param string $attribute The attribute from the relation model (eg: the street attribute from the address model).
     *
     * @return array An array containing a list of attributes from the resulting model.
     */
    public function getRelatedEntriesAttributes($model, $relationString, $attribute)
    {
        $endModels = $this->getRelatedEntries($model, $relationString);
        $attributes = [];
        foreach ($endModels as $model => $entries) {
            $model_instance = new $model();
            $modelKey = $model_instance->getKeyName();

            if (is_array($entries)) {
                //if attribute does not exist in main array we have more than one entry OR the attribute
                //is an acessor that is not in $appends property of model.
                if (!isset($entries[$attribute])) {
                    //we first check if we don't have the attribute because it's and acessor that is not in appends.
                    if ($model_instance->hasGetMutator($attribute) && isset($entries[$modelKey])) {
                        $entry_in_database = $model_instance->find($entries[$modelKey]);
                        $attributes[$entry_in_database->{$modelKey}] = $this->parseTranslatableAttributes($model_instance, $attribute, $entry_in_database->{$attribute});
                    } else {
                        //we have multiple entries
                        //for each entry we check if $attribute exists in array or try to check if it's an acessor.
                        foreach ($entries as $entry) {
                            if (isset($entry[$attribute])) {
                                $attributes[$entry[$modelKey]] = $this->parseTranslatableAttributes($model_instance, $attribute, $entry[$attribute]);
                            } else {
                                if ($model_instance->hasGetMutator($attribute)) {
                                    $entry_in_database = $model_instance->find($entry[$modelKey]);
                                    $attributes[$entry_in_database->{$modelKey}] = $this->parseTranslatableAttributes($model_instance, $attribute, $entry_in_database->{$attribute});
                                }
                            }
                        }
                    }
                } else {
                    //if we have the attribute we just return it, does not matter if it is direct attribute or an acessor added in $appends.
                    $attributes[$entries[$modelKey]] = $this->parseTranslatableAttributes($model_instance, $attribute, $entries[$attribute]);
                }
            }
        }

        return $attributes;
    }

    /**
     * Parse translatable attributes from a model or models resulting from the specified relation string.
     *
     * @param \Illuminate\Database\Eloquent\Model $model Model (eg: user).
     * @param string $attribute The attribute from the relation model (eg: the street attribute from the address model).
     * @param string $value Attribute value translatable or not
     *
     * @return string A string containing the translated attributed based on app()->getLocale()
     */
    public function parseTranslatableAttributes($model, $attribute, $value)
    {
        if (!method_exists($model, 'isTranslatableAttribute')) {
            return $value;
        }

        if (!$model->isTranslatableAttribute($attribute)) {
            return $value;
        }

        if (!is_array($value)) {
            $decodedAttribute = json_decode($value, true);
        } else {
            $decodedAttribute = $value;
        }

        if (is_array($decodedAttribute) && !empty($decodedAttribute)) {
            if (isset($decodedAttribute[app()->getLocale()])) {
                return $decodedAttribute[app()->getLocale()];
            } else {
                return Arr::first($decodedAttribute);
            }
        }

        return $value;
    }

    /**
     * Traverse the tree of relations for the given model, defined by the given relation string, and return the ending
     * associated model instance or instances.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The CRUD model.
     * @param string $relationString Relation string. A dot notation can be used to chain multiple relations.
     *
     * @return array An array of the associated model instances defined by the relation string.
     */
    private function getRelatedEntries($model, $relationString)
    {
        $relationArray = explode('.', $relationString);
        $firstRelationName = Arr::first($relationArray);
        $relation = $model->{$firstRelationName};
        $currentResults = [];

        $results = [];
        if (!is_null($relation)) {
            if ($relation instanceof Collection && !$relation->isEmpty()) {
                $currentResults[get_class($relation->first())] = $relation->toArray();
            } elseif (is_array($relation) && !empty($relation)) {
                $currentResults[get_class($relation->first())] = $relation;
            } else {
                //relation must be App\Models\Article or App\Models\Category
                if (!$relation instanceof Collection && !empty($relation)) {
                    $currentResults[get_class($relation)] = $relation->toArray();
                }
            }

            array_shift($relationArray);

            if (!empty($relationArray)) {
                foreach ($currentResults as $model => $currentResult) {
                    $results[$model] = array_merge($results[$model], $this->getRelatedEntries($currentResult, implode('.', $relationArray)));
                }
            } else {
                $results = $currentResults;
            }
        }

        return $results;
    }
}
