<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd(auth()->user()->usuario);
        //dd(session()->all());
        return view('inicio');
    }

}
