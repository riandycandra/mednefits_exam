<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\RoleModel;
use Validator, Hash;

class UserController extends Controller
{
    
    public function __construct()
    {
        //
    }

    public function index($username = null)
    {
        $data = UserModel::with('role')->where(function($query) use ($username) {

            if($username != null)
            {
                $query->where('username', $username);
            }

        })->get(['id', 'username', 'credit', 'role_id']);

        return [
            'status' => 'success',
            'message'=> 'Success retrieve data.',
            'data'   => $data
        ];

    }

    public function assign(Request $request)
    {

        // validate request
        $validate = Validator::make($request->all(), [
            'user'          => 'required|alpha_num',
            'role'          => 'required|numeric',
        ], [
            'user.required' => "Parameter 'user' is required.",
            'user.alpha_num'=> "Parameter 'user' should only contains alpha numeric.",
            'role.required' => "Parameter 'role' is required.",
            'role.numeric'  => "Parameter 'role' should only contains numeric.",
        ]);

        if(!$validate->passes())
        {
            return [
                'status' => 'error',
                'message'=> implode(' ', $validate->errors()->all())
            ];
        }

        // check user is exist by username
        $user   = UserModel::where('username', $request->user)->first();
        if(!isset($user->id))
        {
            return [
                'status' => 'error',
                'message'=> "Given 'user' not exist, choose different username."
            ];
        }

        // check available role by given id
        $role   = RoleModel::where('id', $request->role)->first();
        if(!isset($role->id))
        {
            return [
                'status' => 'error',
                'message'=> "Given 'role' not exist, choose different role ID."
            ];
        }

        try {
            
            $update = [
                'role_id' => $request->role
            ];

            $where  = [
                'id'      => $user->id
            ];

            UserModel::where($where)->update($update);

            return [
                'status' => 'success',
                'message'=> 'Success assign given user.'
            ];

        } catch (\Illuminate\Database\QueryException $e) {

            return [
                'status' => 'error',
                'message'=> 'Server error'
            ];

        }

    }

    public function register(Request $request)
    {
        $validate = $this->validateRequest($request->all());

        if(!$validate->passes)
        {
            return [
                'status' => 'error',
                'message'=> $validate->message,
            ];
        }

        // check for duplicate username
        $user   = UserModel::where('username', $request->username)->first();
        if(isset($user->id))
        {

            return [
                'status' => 'error',
                'message'=> 'Username exist, choose different username.'
            ];

        }

        // check for role_id
        $role    = RoleModel::where('id', $request->role_id)->first();
        if(!isset($role->id))
        {

            return [
                'status' => 'error',
                'message'=> "Parameter 'role_id' doesn't exist in database."
            ];

        }

        $create = [
            'username' => $request->username,
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
            'role_id'  => $request->role_id,
            'credit'   => ($request->role_id == '3' ? '1000' : '99999999'),
        ];

        try {

            UserModel::create($create);

            return [
                'status' => 'success',
                'message'=> 'Success register new user.'
            ];

        } catch (\Illuminate\Database\QueryException $e) {
            
            return [
                'status' => 'error',
                'message'=> 'Server error'
            ];

        }
    }

    public static function validateRequest($request = null)
    {
        $validator = Validator::make($request, [
            'username' => 'required|alpha_num',
            'password' => 'required',
            'role_id'  => 'required|numeric',
        ], [
            'username.required'     => "Parameter 'username' is required.",
            'username.alpha_num'    => "Parameter 'username' should only contains alpha numeric.",
            'password.required'     => "Parameter 'password' is required.",
            'role_id.required'      => "Parameter 'role_id' is required.",
            'role_id.numeric'       => "Parameter 'role_id' not valid.",
        ]);

        if($validator->fails())
        {
            return (object) [
                'passes' => false,
                'message'=> implode(' ', $validator->errors()->all())
            ];
        }

        return (object) [
            'passes' => true,
            'message'=> 'Validation success'
        ];
    }
}
