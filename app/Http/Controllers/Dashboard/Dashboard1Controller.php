<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use TADPHP\TADFactory;
use TADPHP\TAD;

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

        // $opciones = array(
        //     'ip'            => '192.168.30.30', // '192.168.30.19' by default (totally useless!!!).
        //     'internal_id'   => 1146351,         // 1 by default.
        //     'com_key'       => 5587,            // 0 by default.
        //     //'description' => '',              // 'N/A' by default.
        //     'soap_port'     => 80,              // 80 by default,
        //     'udp_port'      => 4370,            // 4370 by default.
        //     'encoding'      => 'utf-8'          // iso8859-1 by default.
        // );

        // $tad_factory = new TADFactory($opciones);
        // $tad         = $tad_factory->get_instance();

        // $dt = $tad->get_date();

        // $logs1 = $tad->get_att_log(array('pin' => '1146351'))->to_json();

        // return $logs1;

        $data = array(
            'title'       => 'Dashboard 1',
            'title_table' => 'Dashboard 1',
            'modulo'      => 'Dashboard 1',
            'submodulo'   => 'Dashboard 1'
        );
        return view('dashboard.dashboard1.dashboard1')->with($data);
    }
}
