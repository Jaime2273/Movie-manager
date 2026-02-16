<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class ControlPanelController extends Controller
{
    public function index()
{
    $users = User::all(); 
    return view('controlPanel', compact('users'));
}
}
