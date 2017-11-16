<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use TADPHP\TADFactory;
use TADPHP\TAD;

use Exception;

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
          $opciones = array(
              'ip'            => '192.168.30.30', // '192.168.30.30' '200.107.241.111' by default (totally useless!!!).
              'internal_id'   => 1,         // 1 by default.
              'com_key'       => 0,            // 0 by default.
              //'description' => '',              // 'N/A' by default.
              'soap_port'     => 80,              // 80 by default,
              'udp_port'      => 4370,            // 4370 by default.
              'encoding'      => 'utf-8'          // iso8859-1 by default.
          );

          $tad  = (new TADFactory($opciones))->get_instance();
          // $tad_factory = new TADFactory($opciones);
          // $tad         = $tad_factory->get_instance();
          //
          // // $dt = $tad->get_date();
          //
          $logs1 = '';
          try
          {
            $fs_conexion = date("Y-m-d H:i:s");

            echo $fs_conexion;
            // // $logs1 = $tad->set_date(['date' => '2016-11-01','time' => '05:50:49']);
            // echo("<br>");echo("<br>");
            $logs1 = $tad->get_date();

            $all_user_info = $tad->get_all_user_info()->to_array();

            echo("<br>");echo("<br>");

            // TODOS LOS USUARIOS
              // foreach ($all_user_info as $key => $value)
              // {
              //   // echo $key . " =>" . $value . "<br><br>";
              //   echo $key . "<br><br>";

              //   foreach ($value as $key1 => $value1) {
              //     echo $key1 . "<br><br>";

              //     foreach ($value1 as $key2 => $value2) {
              //       if ($key2 == 'Password') {
              //         if(is_array($value2))
              //         {
              //           print_r($value2);

              //           echo("<br>");
              //         }
              //         else
              //         {
              //           echo $key2 . " =>" . $value2 . "<br>";
              //         }
              //       }
              //       else
              //       {
              //         echo $key2 . " =>" . $value2 . "<br>";
              //       }
              //     }

              //     print_r($value1);

              //     echo("<br>");echo("<br>");
              //   }
              //   print_r($value);

              //   echo("<br>");echo("<br>");
              // }

              // print_r($all_user_info);

            // ALGUNOS USUARIOS
              $usuer_log = $tad->get_att_log(['pin' => 1119323])->to_array();

              print_r($usuer_log);

              echo("<br>");echo("<br>");

              $user_info = $tad->get_user_info(['pin' => 1119323])->to_array();
              print_r($user_info);

              echo("<br>");echo("<br>");

              $tad->delete_user(['pin' => 1119323]);

              // echo("<br>");echo("<br>");

              // $res1 = $tad->set_user_info([
              //     'pin'       => 123456789012,
              //     'name'      => "YAÑEZ SASAMOTO JAVIER JESUS",
              //     'privilege' => 0,
              //     'password'  => rand(1000, 9999)
              // ]);

              // echo($res1 . "<br>");echo("<br>");

              $usuer_log = $tad->get_att_log(['pin' => 1119323])->to_array();

              print_r($usuer_log);

              echo("<br>");echo("<br>");

              // if(count($user_info) == 0)
              // {
              //   echo "SIN VALORES";
              // }
              // else
              // {
              //   echo count($user_info);
              // }

              $user_info = $tad->get_user_info(['pin' => 1119323])->to_array();
              print_r($user_info);

            echo("<br>");echo("<br>");

            // $tad->disable();
            // $tad->enable();
              // $logs1 = $tad->get_att_log()->to_array();
              // // $logs1 = $tad->get_user_info(['pin' => '1146351'])->to_json();
              // if(count($logs1) > 0)
              // {
              //   echo("El Array tiene " . count($logs1) . " filas.<br>");
              // }
              // else
              // {
              //   echo("Array vacio<br>");
              // }
              // echo("<br>");
              // print_r($logs1);

              // echo("<br>");
              // echo("<br>");
              // $logs1 = json_encode($logs1);
          }
          catch (Exception $e)
          {
            $logs1 = $e;
          }

          return $logs1;



        // $data = array(
        //     'title'       => 'Dashboard 1',
        //     'title_table' => 'Dashboard 1',
        //     'modulo'      => 'Dashboard 1',
        //     'submodulo'   => 'Dashboard 1'
        // );
        // return view('dashboard.dashboard1.dashboard1')->with($data);
    }
}
