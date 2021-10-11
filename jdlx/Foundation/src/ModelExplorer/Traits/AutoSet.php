<?php

namespace Jdlx\ModelExplorer\Traits;

use Illuminate\Support\Facades\DB;

trait AutoSet
{
    public $table = null;

    /**
     * For a simple CRUD Panel, there should be no need to add/define the fields.
     * The public columns in the database will be converted to be fields.
     *
     * @return array
     */
    public function getFields()
    {
        if (! $this->driverIsMongoDb()) {
            $this->setDoctrineTypesMapping();
            $this->getDbColumnTypes();
        }

        return array_map(function ($field) {
            $new_field = [
                'name'       => $field,
                'label'      => $this->makeLabel($field),
                'default'    => isset($this->autoset['db_column_types'][$field]['default']) ? $this->autoset['db_column_types'][$field]['default'] : null,
                'type'       => $this->inferFieldTypeFromDbColumnType($field),
                'attributes' => [],
            ];
            return $new_field;
        }, $this->getDbColumnsNames());
    }

    /**
     * Get all columns from the database for that table.
     *
     * @return array
     */
    public function getDbColumnTypes()
    {
        $dbColumnTypes = [];

        foreach ($this->getDbColumns() as $column) {
            $column_type = $column->DATA_TYPE;
            $name = $column->COLUMN_NAME;
            $def = $column->COLUMN_DEFAULT;
            $dbColumnTypes[$name]['type'] = trim(preg_replace('/\(\d+\)(.*)/i', '', $column_type));
            $dbColumnTypes[$name]['default'] = $def;
        }
        $this->autoset['db_column_types'] = $dbColumnTypes;

        return $dbColumnTypes;
    }

    /**
     * Get all columns in the database table.
     *
     * @return array
     */
    public function getDbTableColumns()
    {
        if (isset($this->autoset['table_columns']) && $this->autoset['table_columns']) {
            return $this->autoset['table_columns'];
        }

        $conn = $this->getConnection();
        $table = $conn->getTablePrefix(). ($this->model ? $this->model->getTable() : $this->table);
        $columns = $conn->getDoctrineSchemaManager()->listTableColumns($table);

        $this->autoset['table_columns'] = $columns;
        return $this->autoset['table_columns'];
    }

    /**
     * Infer a field type, judging from the database column type.
     *
     * @param string $field Field name.
     *
     * @return string Field type.
     */
    protected function inferFieldTypeFromDbColumnType($fieldName)
    {

        if ($fieldName == 'password') {
            return 'password';
        }

        if ($fieldName == 'email') {
            return 'email';
        }

        if (is_array($fieldName)) {
            return 'text'; // not because it's right, but because we don't know what it is
        }

        $dbColumnTypes = $this->getDbColumnTypes();

        if (! isset($dbColumnTypes[$fieldName])) {
            return 'text';
        }

        switch ($dbColumnTypes[$fieldName]['type']) {
            case 'int':
            case 'integer':
            case 'smallint':
            case 'mediumint':
            case 'longint':
            case 'bigint':
                return 'integer';

            case 'string':
            case 'varchar':
            case 'set':
                return 'text';

            // case 'enum':
            //     return 'enum';
            // break;

            case 'boolean':
                return 'boolean';

            case 'tinyint':
                return 'boolean';
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'textarea';

            case 'date':
                return 'date';

            case 'datetime':
                return 'datetime';
            case 'timestamp':
                return 'timestamp';

            case 'time':
                return 'time';

            case 'json':
                return 'json';

            default:
                return 'text';
        }

        return 'text';
    }

    // Fix for DBAL not supporting enum
    public function setDoctrineTypesMapping()
    {
        $types = ['enum' => 'string'];
        $platform = $this->getSchema()->getConnection()->getDoctrineConnection()->getDatabasePlatform();
        foreach ($types as $type_key => $type_value) {
            if (! $platform->hasDoctrineTypeMappingFor($type_key)) {
                $platform->registerDoctrineTypeMapping($type_key, $type_value);
            }
        }
    }

    /**
     * Turn a database column name or PHP variable into a pretty label to be shown to the user.
     *
     * @param string $value The value.
     *
     * @return string The transformed value.
     */
    public function makeLabel($value)
    {
        if (isset($this->autoset['labeller']) && is_callable($this->autoset['labeller'])) {
            return ($this->autoset['labeller'])($value);
        }

        return trim(ucfirst(str_replace('_', ' ', preg_replace('/(_id|_at|\[\])$/i', '', $value))));
    }

    /**
     * Alias to the makeLabel method.
     */
    public function getLabel($value)
    {
        return $this->makeLabel($value);
    }

    /**
     * Change the way labels are made.
     *
     * @param callable $labeller A function that receives a string and returns the formatted string, after stripping down useless characters.
     *
     * @return self
     */
    public function setLabeller(callable $labeller)
    {
        $this->autoset['labeller'] = $labeller;

        return $this;
    }

    /**
     * Get the database column names, in order to figure out what fields/columns to show in the auto-fields-and-columns functionality.
     *
     * @return array Database column names as an array.
     */
    public function getDbColumnsNames()
    {
        $fillable = null;

        if ($this->driverIsMongoDb()) {
            $columns = $fillable;
        } else {
            // Automatically-set columns should be both in the database, and in the $fillable variable on the Eloquent Model

            $db =  $this->getConnection()->getDatabaseName();
            $table = $this->model ? $this->model->getTable() : $this->table;

            $columns = DB::table('information_schema.columns')
                ->where('table_schema', $db)
                ->where ('table_name', $table)
                ->orderBy("ORDINAL_POSITION")
                ->pluck('COLUMN_NAME')
                ->toArray();

            if (! empty($fillable)) {
               // $columns = array_intersect($columns, $fillable);
            }
        }
        return $columns;
    }

    /**
     * Get the database column names, in order to figure out what fields/columns to show in the auto-fields-and-columns functionality.
     *
     * @return array Database column names as an array.
     */
    public function getDbColumns()
    {
        $fillable = null;

        if ($this->driverIsMongoDb()) {
            $columns = $fillable;
        } else {
            // Automatically-set columns should be both in the database, and in the $fillable variable on the Eloquent Model

            $db =  $this->getConnection()->getDatabaseName();
            $table = $this->model ? $this->model->getTable() : $this->table;

            $columns = DB::table('information_schema.columns')
                ->where('table_schema', $db)
                ->where ('table_name', $table)
                ->orderBy("ORDINAL_POSITION")
                ->get()
                ->toArray();
        }
        return $columns;
    }


}
