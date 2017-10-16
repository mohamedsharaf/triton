<?php

namespace App\Http\Controllers\Seguridad;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    if($request->ajax())
    {
      $tipo = $request->input('tipo');
      return $tipo;
    }
  }
}
