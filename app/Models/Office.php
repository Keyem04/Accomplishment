<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $connection = 'fms';
    protected $table = 'offices';
    protected $primaryKey = 'id';

    protected $guarded = [];
    
    public function accomplishments()
    {
        return $this->hasMany(AccomplishmentHeader::class, 'department_id');
    }

    public function ppas()
    {
        return $this->hasMany(ProgramAndProject::class, 'department_code', 'department_code');
    }
}
