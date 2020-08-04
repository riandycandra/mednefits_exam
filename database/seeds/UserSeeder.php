<?php

use Illuminate\Database\Seeder;

use App\Models\UserModel;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$create = [
    		'username' => 'admin',
    		'password' => password_hash('admin123', PASSWORD_DEFAULT),
    		'role_id'  => '1',
    		'credit'   => '99999999'
    	];
        UserModel::create($create);
    }
}
