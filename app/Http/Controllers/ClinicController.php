<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicModel;
use Validator, Hash;

class ClinicController extends Controller
{
    
    public function __construct()
    {
        //
    }

    public function index()
    {
        $data = ClinicModel::get(['id', 'code', 'name']);

        return [
            'status' => 'success',
            'message'=> 'Success retrieve data.',
            'data'   => $data
        ];
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

        // check for duplicate name
        $clinic   = ClinicModel::where('name', $request->name)->first();
        if(isset($clinic->id))
        {

            return [
                'status' => 'error',
                'message'=> 'Clinic name exist, choose different name.'
            ];

        }

        // get latest code
        $clinic   = ClinicModel::orderBy('id', 'DESC')->first();

        // check row is exist or not
        // if exist, continue, else start from C1
        if(isset($clinic->id))
        {
            $code  = (int) filter_var($clinic->code, FILTER_SANITIZE_NUMBER_INT) + 1;
            $code  = "C" . $code;
        } else {
            $code  = "C1";
        }

        $create = [
            'code' => $code,
            'name' => $request->name,
        ];

        try {

            ClinicModel::create($create);

            return [
                'status' => 'success',
                'message'=> 'Success register new clinic.'
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
            'name' => 'required|regex:/^[\pL\s\d\-\/]+$/u', // alpha numeric spaces dash slashes',
        ], [
            'name.required' => "Parameter 'name' is required.",
            'name.regex'    => "Parameter 'name' should only contains alpha numeric spaces dash slashes.",
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
