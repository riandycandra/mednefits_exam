<?php

use Illuminate\Database\Seeder;
use App\Models\RoleModel;
use \Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insert = [
        	['name' => 'Admin', 'created_at' => Carbon::now()],
        	['name' => 'HR', 'created_at' => Carbon::now()],
        	['name' => 'Member', 'created_at' => Carbon::now()],
        ];

        RoleModel::insert($insert);
    }
}
