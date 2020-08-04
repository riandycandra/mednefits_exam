<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $data = RoleModel::get(['id', 'name']);

        return [
            'status' => 'success',
            'message'=> 'Success retrieve data.',
            'data'   => $data
        ];
    }
}
