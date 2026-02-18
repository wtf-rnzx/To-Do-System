<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


class UserManagementController extends Controller
{
    public function index()
    {
        return view('admin.userManagement');
    }
}
