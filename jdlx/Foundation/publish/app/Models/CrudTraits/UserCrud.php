<?php

namespace App\Models\CrudTraits;

use Carbon\Carbon;
use Jdlx\Traits\FieldDescriptor;
use Jdlx\Traits\UsesUuid;

/**
 * @property integer id
 * @property string name
 * @property string email
 * @property Carbon email_verified_at
 * @property string password
 * @property string remember_token
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @package DalPraS\OAuth2\Client\Resources
 */
trait UserCrud
{
    use FieldDescriptor;

   /**
     * Meta data about the model use for code generation
     * and other automation.
     *
     * @var array
     */
    protected static $fieldSetup = [
            'id' => ['readOnly', 'sortable', 'filterable'],
            'name' => ['editable', 'sortable', 'filterable'],
            'email' => ['editable', 'sortable', 'filterable'],
            'email_verified_at' => ['editable', 'sortable', 'filterable'],
            'password' => ['editable', 'writeOnly', 'sortable', 'filterable'],
            'remember_token' => ['editable', 'sortable', 'filterable'],
            'created_at' => ['readOnly', 'sortable', 'filterable'],
            'updated_at' => ['readOnly', 'sortable', 'filterable']
    ];


}
