<?php

namespace App\Http\Controllers;

use App\Departments;
use App\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Http\Requests;
use App\User;

use Auth;
use Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return view('admin.addHOD');
    }

    public function addHOD( Request $request){

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $password = $request['password'];
        $department = $request['department'];
        $record = Departments::where('departmentName', $department)->first();
//        $check = User::find($record->id);
//        if (!$check) {
            $newEntry = new User();
            $newEntry->email = $request['email'];
            $newEntry->manNumber = $request['manNumber'];
            $newEntry->lastName = ucfirst($request['lastName']);
            $newEntry->firstName = ucfirst($request['firstName']);
            $newEntry->otherName = ucfirst($request['otherName']);
            $newEntry->password = bcrypt($request['password']);
            $newEntry->access_level_id = 'HD';
            $newEntry->departments_id= $record->id;
            $newEntry->save();
//        }else{
//            Session::flash('flash_message', 'sorry but department of '.$department.',has already been assigned an HOD! ');
//            Return view('admin.addHOD');
//        }

//        //Send mail to new user
//        Mail::send('Mails.new_user', ['password' => $password], function ($m) use ($data) {
//
//            $m->to($data->email, 'Me')->subject('Complete registration');
//        });

        Session::flash('flash_message', 'You have successfully added a user to the system!');
        Return view('admin.index');
    }

    public function getAcc(){
        return view('admin.addAccountant');
    }

    public function addAccountant(Request $request){
        $validator = $this->validateAccountant($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $password = $request['password'];
        $school = $request['school'];
        $record = school::where('schoolName', $school)->first();
//        $check = User::find($record->id)->first();
//        if (!$check) {
            $newEntry = new User();
            $newEntry->email = $request['email'];
            $newEntry->manNumber = $request['manNumber'];
            $newEntry->lastName = ucfirst($request['lastName']);
            $newEntry->firstName = ucfirst($request['firstName']);
            $newEntry->otherName = ucfirst($request['otherName']);
            $newEntry->password = bcrypt($request['password']);
            $newEntry->access_level_id = 'AC';
            $newEntry->schools_id= $record->id;
            $newEntry->save();
//        }else{
            Session::flash('flash_message', 'sorry but department of '.$school.',has already been assigned an HOD! ');
            Return view('admin.addAccountant');
//        }
    }

    public function dos(){
        return view('admin.addDOS');
    }

    public function addDos(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $password = $request['password'];
        $school = $request['school'];
        $record = school::where('schoolName', $school)->first();
//        $check = User::find($record->id)->first();
//        if (!$check) {
        $newEntry = new User();
        $newEntry->email = $request['email'];
        $newEntry->manNumber = $request['manNumber'];
        $newEntry->lastName = ucfirst($request['lastName']);
        $newEntry->firstName = ucfirst($request['firstName']);
        $newEntry->otherName = ucfirst($request['otherName']);
        $newEntry->password = bcrypt($request['password']);
        $newEntry->access_level_id = 'DN';
        $newEntry->schools_id= $record->id;
        $newEntry->save();
//        }else{
        Session::flash('flash_message', 'sorry but department of '.$school.',has already been assigned an HOD! ');
        Return view('admin.addAccountant');
//        }
    }

    /**
     * Get a validator for an incoming profile editing request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateAccountant(array $data)
    {
        return Validator::make($data, [
            'lastName' => 'required|max:40',
            'firstName' => 'required|max:40',
            'otherName' => '|max:40',
            'school' => 'required|max:255',
            'manNumber' => 'required|max:40|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Get a validator for an incoming profile editing request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'lastName' => 'required|max:40',
            'firstName' => 'required|max:40',
            'otherName' => '|max:40',
            'manNumber' => 'required|max:40|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }
}
