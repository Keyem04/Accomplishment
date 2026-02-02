<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramAndProject extends Model
{
    use HasFactory;

    protected $connection = 'opcr';  // <--- important! tells Eloquent to use OPCR DB
    protected $table = 'program_and_projects';
    protected $primaryKey = 'id';

    
    //  protected $fillable = [
    //     'paps_desc',
    //     'department_code',
    //     'MOV',
    //     'type',
    //     'sector',
    //     'subsector',
    // ];

    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Office::class, 'department_code', 'department_code');
    }
}
