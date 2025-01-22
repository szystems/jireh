<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Config;
use DB;
use PDF;

class AdminController extends Controller
{
    public function index()
    {
        $config = Config::first();

        $today = date("Y-m-d");

        return view('admin.index', compact('config'));
    }
}
