<?php


namespace Jdlx\Commands\Make;


use Jdlx\ModelExplorer\Explorer;
use Illuminate\Support\Str;

trait ModelUtil
{
    /*
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new \InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . "Models\\" . $model;
        }

        return $model;
    }

    protected function getTable($model)
    {
        return (new $model())->getTable();
    }

    protected function getRelationShips($modelClass)
    {
        $fields = $this->getDbFields($modelClass, true);
        $relationships = [];
        foreach ($fields as $field) {
            preg_match_all('/(.*)_id$/m', $field['name'], $matches, PREG_SET_ORDER, 0);
            if (sizeof($matches) > 0) {
                $relationship = $matches[0][1];
                $isMorph = isset($fields[$relationship . "_type"]);
                $method = Str::camel($relationship);
                $class = Str::ucfirst($method);

                $relationships[$field['name']] = [
                    'type' => 'belongsTo',
                    'field' => $field,
                    'method' => $method,
                    'class' => $class,
                    'db_name' => $relationship,
                    'fdn_class' => "\\App\\{$class}"
                ];

                if ($isMorph) {
                    $relationships[$field['name']]['type'] = 'morphTo';
                }

            }
        }
        return $relationships;
    }

    protected function partOfForeignKey($modelClass, $fieldName)
    {
        preg_match_all('/(.*)_(id|type)$/m', $fieldName, $matches, PREG_SET_ORDER, 0);
        if (sizeof($matches) > 0) {
            $relationship = $matches[0][1];
            $type = $matches[0][2];
            $rel = $this->getRelationShips($modelClass);
            if (isset($rel[$relationship . "_id"])) {
                return $type;
            }
        }
        return false;
    }

    protected function getDbFields($modelClass, $withIndex = false)
    {

        $explorer = new Explorer();
        if (class_exists($modelClass)) {
            $explorer->setModel($modelClass);
        } else {
            $table = null;
            if (method_exists($this, "getTableInput")) {
                $table = $this->getTableInput();
            }
            if (empty($table)) {
                $table = (new $modelClass())->getTable();
            }
            $explorer->table = $table;
        }
        $fields = $explorer->getFields();
        if ($withIndex) {
            $indexed = [];
            foreach ($fields as $field) {
                $indexed[$field['name']] = $field;
            }
            return $indexed;
        }
        return $fields;
    }

    protected function getFieldDefinitions($modelClass, $configOnly = false, $withIndex = false)
    {
        $configurations = $modelClass::getFieldConfigurations();
        $sortOrder = array_keys($configurations);
        $fields = $this->getDbFields($modelClass);
        $indexed = [];

        foreach ($fields as $field) {
            $indexed[$field['name']] = $field;
        }
        $sorted = [];
        foreach ($sortOrder as $index) {
            if ($indexed[$index]) {
                $sorted[$index] = array_merge($indexed[$index], $configurations[$index]);
            }
            unset($indexed[$index]);
        }

        if (!$withIndex) {
            $sorted = array_values($sorted);
            $indexed = array_values($indexed);
        }

        if ($configOnly) {
            return $sorted;
        }

        return array_merge($sorted, $indexed);
    }
}
