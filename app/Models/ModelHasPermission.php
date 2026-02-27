<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasPermission extends Model
{
    protected $table = 'model_has_permissions';
    protected $connection = 'mysql';
    public $timestamps = false;
    protected $guarded = [];
}
