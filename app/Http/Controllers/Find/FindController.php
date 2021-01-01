<?php

namespace App\Http\Controllers\Find;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FindController extends Controller
{
    public function index() {
        return view('find.index');
    }
}
