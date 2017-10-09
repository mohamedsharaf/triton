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
        try
        {
          $opciones = array(
              'ip'            => '200.107.241.111', // '192.168.30.19' by default (totally useless!!!).
              'internal_id'   => 1146351,         // 1 by default.
              'com_key'       => 5587,            // 0 by default.
              //'description' => '',              // 'N/A' by default.
              'soap_port'     => 80,              // 80 by default,
              'udp_port'      => 4370,            // 4370 by default.
              'encoding'      => 'utf-8'          // iso8859-1 by default.
          );

          $tad_factory = new TADFactory($opciones);
          $tad         = $tad_factory->get_instance();
          //
          // // $dt = $tad->get_date();
          //
          // $logs1 = $tad->get_att_log()->to_json();
          //
          // throw new Exception("sera");
          //
          // return $logs1;
        }
        catch (Exception $e)
        {
          return $e;
        }



        // $data = array(
        //     'title'       => 'Dashboard 1',
        //     'title_table' => 'Dashboard 1',
        //     'modulo'      => 'Dashboard 1',
        //     'submodulo'   => 'Dashboard 1'
        // );
        // return view('dashboard.dashboard1.dashboard1')->with($data);
    }
}
