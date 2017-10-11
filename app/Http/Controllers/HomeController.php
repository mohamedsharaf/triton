<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array(
            'title'       => 'Inicio',
            'title_table' => 'Inicio',
            'modulo'      => 'Inicio',
            'submodulo'   => 'Inicio'
        );
        // return view('dashboard.dashboard1.dashboard1')->with($data);
        return view('home')->with($data);
    }
}
