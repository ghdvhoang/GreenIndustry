<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
        public function dashboard()
    {
        $page_data['view_path'] = 'dashboard';
        return view('backend.index', $page_data);
    }
}
