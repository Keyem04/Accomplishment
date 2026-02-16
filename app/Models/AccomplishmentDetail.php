<?php

namespace App\Models;

use App\Models\AccomplishmentHeader;
use App\Models\ProgramAndProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'recid');
    }

}
