<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index(){
        if (Auth::id()){
            $usertype = Auth()->user()->usertype;

            if($usertype == 'user'){
                return view('dashboard');
            }

            else if($usertype == 'admin'){
                return view('admin.adminHome');
            }
            
            else{
                return redirect()->back();
            }
        }
    }

    
}
