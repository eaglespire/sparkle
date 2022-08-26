<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModalTestController extends Controller
{
    public function index()
    {
        return view('modal');
    }
}
