<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table        = 'user';
    protected $fillable     = ['role_id', 'username', 'password', 'credit'];
    protected $primaryKey   = 'id';
    protected $hidden 		= ['password'];

    public function role()
    {
    	return $this->belongsTo('App\Models\RoleModel', 'role_id');
    }
}
