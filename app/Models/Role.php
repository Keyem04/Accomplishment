<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole {
    protected $connection = "mysql";
    protected $table = 'roles';
    protected $guarded = ['id'];

    public function users(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(
            \App\Models\User::class,
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key'),
        )->using(ModelHasRole::class);
    }
}
