<?php

namespace App\Http\Controllers;

use App\Departments;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $logged_in_user = Auth::user();
         $access_level_id = $logged_in_user->access_level_id;

         if ($access_level_id =='SA'){
             return view('admin.index');
         }elseif ($access_level_id =='AC'){
             return view('acc.index');
         }elseif ($access_level_id == 'HD' || $access_level_id =='OT'){
             $id = $logged_in_user->department_id;
             $record = Departments::find($id);
             if (isset($record)){
//                 $record = $record->departmentName;
                 return view('hod.index', ['record',$record]);
             }else{
                 return view('hod.index');
             }

         }elseif ($access_level_id == 'DN'){
             return view('dos.index');
         }elseif ($access_level_id =='HU'){
             return view('hou.index');
         }elseif ($access_level_id =='OT'){
             return view('staff.index');
         }else {

         }
    }
}
