<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ModelHasRole extends MorphPivot 
{
    protected $connection = 'mysql';
    protected $table = 'accomplishment_db.model_has_roles';
}
