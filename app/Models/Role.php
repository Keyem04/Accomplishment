<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole {
    protected $connection = "mysql";
    protected $table = 'roles';
    protected $guarded = ['id'];

     public function users(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return parent::users()->using(ModelHasRole::class);
    }
}
