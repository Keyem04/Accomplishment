<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccomplishmentDetail extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    // protected $fillable = [
    //     'header_id',
    //     'date',
    //     'title_of_accomplishment',
    //     'brief_description',
    //     'scope',
    //     'results',
    //     'mov',
    //     'ppa_id',
    // ];

    protected $guarded = [];

    protected $casts = [
        'mov' => 'array',
    ];

    /**
     * Each detail belongs to a header
     */
    public function header()
    {
        return $this->belongsTo(AccomplishmentHeader::class, 'header_id');
    }

     public function ppa()
    {
        return $this->belongsTo(ProgramAndProject::class, 'ppa_id');
    }

    /**
     * Detail gets department THROUGH header (FMS DB)
     */
    // public function department()
    // {
    //     return $this->hasOneThrough(
    //         Office::class,
    //         AccomplishmentHeader::class,
    //         'id',              // FK on headers table
    //         'id',              // FK on offices table
    //         'header_id',       // Local key on details
    //         'department_id'    // Local key on headers
    //     );
    // }
}
