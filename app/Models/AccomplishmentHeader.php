<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

}
