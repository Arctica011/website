<?php

namespace App\Policies;

use App\Generated\__MODEL__\Use__MODEL__PolicyGlobal;
use App\Generated\__MODEL__\Use__MODEL__PolicyOwned;

use Illuminate\Auth\Access\HandlesAuthorization;

class __MODEL__Policy
{

    use HandlesAuthorization;

    // Pick the UserPolicy scaffold that best fits
    // Global = App wide ownership of records
    // Owned =  Determine per owner namespacing of models Model

    use Use__MODEL__PolicyGlobal;
    // use Use__MODEL__PolicyOwned;

}
