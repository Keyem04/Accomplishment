<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    protected $connection = 'mysql';
    protected $table = 'accomplishment_db.model_has_roles';
}
