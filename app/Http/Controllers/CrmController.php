<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmController extends Controller
{
    public function dashboard(): View
    {
        return view('crm.dashboard');
    }
}