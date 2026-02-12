<?php

namespace App\Models;

use App\Models\AccomplishmentDetail;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccomplishmentHeader extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    // protected $fillable = [
    //     'department_id',
    //     'reporting_month',
    //     'reporting_year',
    // ];
    protected $guarded = [];
   
    public function department()
    {
        return $this->belongsTo(Office::class, 'department_id');
    }
    
    /**
     * One header has many accomplishmentdetails
     */
    public function accomplishmentdetails()
    {
        return $this->hasMany(AccomplishmentDetail::class, 'header_id');
    }

    public function ppa()
    {
        return $this->belongsTo(\App\Models\ProgramAndProject::class, 'ppa_id');
    }

}
