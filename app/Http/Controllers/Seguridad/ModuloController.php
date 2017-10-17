<?php

namespace App\Http\Controllers\Seguridad;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Libraries\JqgridClass;

class ModuloController extends Controller
{
  private $estado;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');

    $this->estado = [
      '1' => 'HABILITADO',
      '2' => 'INHABILITADO'
    ];
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = [
      'title'        => 'Gestor de módulos',
      'home'         => 'Inicio',
      'sistema'      => 'Seguridad',
      'modulo'       => 'Gestor de módulos',
      'title_table'  => 'Modulos',
      'estado_array' => $this->estado
    ];
    return view('seguridad.modulo.modulo')->with($data);
  }

  public function view_jqgrid(Request $request)
  {
    if( ! $request->ajax())
    {
      $respuesta = [
        'page'    => 0,
        'total'   => 0,
        'records' => 0
      ];
      return json_encode($respuesta);
    }

    $tipo = $request->input('tipo');

    switch($tipo)
    {
      case '1':
        $respuesta = [
          'page'    => 0,
          'total'   => 0,
          'records' => 0
        ];
        return json_encode($respuesta);





      // if(!$this->input->get('flujo_correspondencia_id'))
      //             exit('Opps!!! No se permite acceso directo al script.');
      //
      //         $flujo_correspondencia_id = trim($this->input->get('flujo_correspondencia_id'));
      //
      //         $esquema1 = 'andromeda';
      //         $tabla1   = 'andro_fc_solicitud';
      //         $tabla2   = 'andro_tipos_correspondencia';
      //
      //         $this->load->library('util/jquery_jqgrid');
      //         $jquery_jqgrid = new jquery_jqgrid();
      //
      //         $select = "
      //             SELECT
      //                 a1.id,
      //                 a1.estado,
      //                 a1.flujo_correspondencia_id,
      //                 a1.tipo_correspondencia_id,
      //                 a1.f_correspondencia,
      //                 a1.n_correspondencia,
      //                 a1.hoja_ruta,
      //                 a1.destinatario,
      //                 a1.referencia,
      //                 a1.upload_pdf,
      //                 a1.estado_pdf,
      //
      //                 a2.nombre AS tipo_correspondencia
      //         ";
      //         $from = "
      //         FROM      $esquema1.$tabla1 AS a1
      //         LEFT JOIN $esquema1.$tabla2 AS a2 ON a2.id=a1.tipo_correspondencia_id
      //         ";
      //         $where = "WHERE a1.flujo_correspondencia_id=" . $flujo_correspondencia_id;
      //
      //         $query = $jquery_jqgrid->jqgrid($select, $from, $where);
      //         $responce = array('page' => $jquery_jqgrid->getPage(), 'total' => $jquery_jqgrid->getTotal_pages(), 'records' => $jquery_jqgrid->getCount());
      //
      //         $i = 0;
      //         foreach ($query as $row)
      //         {
      //             $val_array = array(
      //                 'estado'                   => $row["estado"],
      //                 'flujo_correspondencia_id' => $row["flujo_correspondencia_id"],
      //                 'tipo_correspondencia_id'  => $row["tipo_correspondencia_id"],
      //                 'upload_pdf'               => $row["upload_pdf"],
      //                 'estado_pdf'               => $row["estado_pdf"]
      //             );
      //
      //             $responce['rows'][$i]['id'] = $row["id"];
      //             $responce['rows'][$i]['cell'] = array(
      //                 '',
      //                 $this->utilitarios(array('tipo' => '13', 'id' => $row["id"], 'estado_pdf' => $row["estado_pdf"])),
      //                 $row["tipo_correspondencia"],
      //                 $row["f_correspondencia"],
      //                 $row["n_correspondencia"],
      //                 $row["hoja_ruta"],
      //                 $row["destinatario"],
      //                 $row["referencia"],
      //                 //OCULTOS
      //                 json_encode($val_array)
      //             );
      //             $i++;
      //         }
      //         exit(json_encode($responce));
        break;
      default:
        $respuesta = [
          'page'    => 0,
          'total'   => 0,
          'records' => 0
        ];
        return json_encode($respuesta);
        break;
    }
  }

  public function send_ajax(Request $request)
  {
    if( ! $request->ajax())
    {
      $respuesta = [
        'page'    => 0,
        'total'   => 0,
        'records' => 0
      ];
      return json_encode($respuesta);
    }

    $tipo = $request->input('tipo');

    switch($tipo)
    {
      case '1':
        # code...
        break;

      default:
        break;
    }
  }
}
