<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicModel extends Model
{
    protected $table        = 'clinic';
    protected $fillable     = ['code', 'name'];
    protected $primaryKey   = 'id';
}
