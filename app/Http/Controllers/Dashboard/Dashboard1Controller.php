<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Dashboard1Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array(
            'title'       => 'Dashboard 1',
            'title_table' => 'Dashboard 1',
            'modulo'      => 'Dashboard 1',
            'submodulo'   => 'Dashboard 1'
        );
        return view('dashboard.dashboard1.dashboard1')->with($data);
    }
}
