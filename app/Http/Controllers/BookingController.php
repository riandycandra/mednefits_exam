<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingModel;
use App\Models\ClinicModel;
use App\Models\UserModel;
use DB, Validator;

class BookingController extends Controller
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
        $data = BookingModel::with('user.role', 'clinic')->where('status', '1')->get(['user_id', 'clinic_id']);

        return [
            'status' => 'success',
            'message'=> 'Success retrieve data.',
            'data'   => $data
        ];
    }

    public function start(Request $request)
    {
        $validate = $this->validateRequest($request->all());

        if(!$validate->passes)
        {
            return [
                'status' => 'error',
                'message'=> $validate->message,
            ];
        }

        // get user id
        $user   = UserModel::where('username', $request->user)->first();
        if(!isset($user->id))
        {
            return [
                'status' => 'error',
                'message'=> 'User not found, choose different username.'
            ];
            
        }
        $user_id = $user->id;

        // get clinic id
        $clinic  = ClinicModel::where('code', $request->clinic)->first();
        if(!isset($clinic->id))
        {
            return [
                'status' => 'error',
                'message'=> 'Clinic not found, choose different clinic code.'
            ];
            
        }
        $clinic_id= $clinic->id;

        // validation on member can't book when the user still not processing last book
        if(!$this->validateLastBook($user_id))
        {
            return [
                'status' => 'error',
                'message'=> 'Current user has an active booking.'
            ];
        }

        // validation on member can't book when the user still not processing last book
        if(!$this->validateBooking($user_id))
        {
            return [
                'status' => 'error',
                'message'=> 'Current user can only book one clinic a day.'
            ];
        }

        // validation on clinic can't be booked when clinic have an active booking
        if(!$this->validateClinic($clinic_id))
        {
            return [
                'status' => 'error',
                'message'=> 'Current clinic have an active booking.'
            ];
        }

        // validation on member can't booking when credit less than 100 and role is member
        if($user->credit < 100 && $user->role_id == '3')
        {
            return [
                'status' => 'error',
                'message'=> 'Current user out of credits.'
            ];
        }

        // assign member to clinic
        $create = [
            'user_id'    => $user_id,
            'clinic_id'  => $clinic_id,
            'status'     => '1',
        ];

        try {

            BookingModel::create($create);

            return [
                'status' => 'success',
                'message'=> 'Booking created.'
            ];

        } catch (\Illuminate\Database\QueryException $e) {
            
            return [
                'status' => 'error',
                'message'=> 'Server error'
            ];

        }

    }

    public function end(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user'          => 'required|alpha_num'
        ], [
            'user.required' => "Parameter 'user' is required.",
            'user.alpha_num'=> "Parameter 'user' should only contains alpha numeric.",
        ]);

        if(!$validate->passes())
        {
            return [
                'status' => 'error',
                'message'=> implode(' ', $validate->errors()->all())
            ];
        }

        // get user id
        $user = UserModel::where('username', $request->user)->first();
        if(!isset($user->id))
        {
            return [
                'status' => 'error',
                'message'=> 'User not found, choose different username.'
            ];
        }
        $user_id = $user->id;

        // try to find booking with current user_id and active = 1;
        $booking = BookingModel::where('user_id', $user_id)->where('status', '1')->first();
        if(!isset($booking->id))
        {
            return [
                'status' => 'error',
                'message'=> 'There is no active booking with current user',
            ];
        }

        $update_booking = [
            'status' => '0'
        ];

        $where_booking  = [
            'user_id' => $user_id
        ];


        // Update booking table, and then update user table
        try {
            
            BookingModel::where($where_booking)->update($update_booking);

        } catch (\Illuminate\Database\QueryException $e) {

            return [
                'status' => 'error',
                'message'=> 'Server error.'
            ];

        }

        $update_user = [
            'credit' => DB::raw("credit - 100")
        ];

        $where_user  = [
            'id' => $user_id
        ];

        try {
            
            // if not member, don't decrease the credit
            if($user->role_id == '3')
            {
                UserModel::where($where_user)->update($update_user);
            }

            return [
                'status' => 'success',
                'message'=> 'Booking ended successfully.'
            ];

        } catch (\Illuminate\Database\QueryException $e) {
            
            return [
                'status' => 'error',
                'message'=> 'Server error.'
            ];

        }

    }

    // validation on member can't book when the user still not processing last book
    public static function validateLastBook($user_id = null)
    {
        $booking = BookingModel::where(function($query) use ($user_id) {
            $query->where('user_id', $user_id);

            $query->where('status', '1'); // search for active booking
        })->first();

        if(isset($booking->id))
        {
            return false;
        }

        return true;
    }

    // validation on member can't book more than one clinic a day
    public static function validateBooking($user_id = null)
    {
        $booking = BookingModel::where(function($query) use ($user_id) {
            $query->where('user_id', $user_id);

            $query->where(DB::raw('DATE(created_at)'), date('Y-m-d'));
        })->first();

        if(isset($booking->id))
        {
            return false;
        }

        return true;
    }

    // validation on clinic can't be booked when clinic have an active booking
    public static function validateClinic($clinic_id = null)
    {
        $booking = BookingModel::where(function($query) use ($clinic_id) {
            $query->where('clinic_id', $clinic_id);

            $query->where('status', '1');
        })->first();

        if(isset($booking->id))
        {
            return false;
        }

        return true;
    }

    public static function validateRequest($request = null)
    {
        $validator = Validator::make($request, [
            'clinic'    => 'required|alpha_num',
            'user'      => 'required|alpha_num',
        ], [
            'clinic.required'   => "Parameter 'clinic' is required.",
            'clinic.alpha_num'  => "Parameter 'clinic' should only contains alpha numeric.",
            'user.required'     => "Parameter 'user' is required.",
            'user.alpha_num'    => "Parameter 'user' should only contains alpha numeric.",
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
