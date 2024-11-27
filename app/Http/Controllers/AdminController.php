<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Administrateur');
    }

    public function securitySettings()
    {
        return view('admin.security-settings');
    }
}