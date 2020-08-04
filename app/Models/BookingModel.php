<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    protected $table        = 'booking';
    protected $fillable     = ['user_id', 'clinic_id', 'status'];
    protected $primaryKey   = 'id';

    public function clinic()
    {
    	return $this->belongsTo('App\Models\ClinicModel', 'clinic_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\UserModel', 'user_id');
    }
}
