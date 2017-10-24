<?php

namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JqgridClass
{
  private $request;
  private $ops;

  public function __construct($request)
  {
    $this->request = $request;
    $this->ops     = array(
      'eq' => '=', //igual
      'ne' => '<>', //no igual a
      'bw' => 'ILIKE', //empieza por
      'bn' => 'NOT LIKE', //no empieza por
      'ew' => 'LIKE', //termina por
      'en' => 'NOT LIKE', //no termina por
      'cn' => 'ILIKE', // contiene
      'nc' => 'NOT LIKE', //no contiene
      'in' => 'LIKE', //esta en
      'ni' => 'NOT LIKE', //no esta en
      'nu' => 'IS NULL', //es NULL
      'nn' => 'IS NOT NULL', //no es NULL
      'lt' => '<', //menor a
      'le' => '<=', //menor igual a
      'gt' => '>', //mayor a
      'ge' => '>=', //mayor igual a
    );
  }

  public function getLimitOffset($count)
  {
    $page  = $this->request->input('page');
    $limit = $this->request->input('rows');
    $sord  = $this->request->input('sord');

    if($this->request->has('sidx'))
    {
      $sidx  = $this->request->input('sidx');
    }
    else
    {
      $sidx = 1;
    }

    if($count > 0 && $limit > 0)
    {
      $total_pages = ceil($count / $limit);
    }
    else
    {
      $total_pages = 0;
    }

    if($page > $total_pages)
    {
      $page = $total_pages;
    }

    $start = $limit * $page - $limit;

    if($start < 0)
    {
      $start = 0;
    }

    $respuesta = [
      'page'        => $page,
      'limit'       => $limit,
      'sidx'        => $sidx,
      'sord'        => $sord,
      'total_pages' => $total_pages,
      'start'       => $start
    ];

    return $respuesta;
  }

  public function getWhere()
  {
    if($this->request->input('_search') == 'true')
    {
      $array_where = $this->arrayWhere();
    }
    else
    {
      $array_where = [];
    }
    return $array_where;
  }

  private function arrayWhere()
  {
    $array_where = [];
    if($this->request->has('filters'))
    {
      $filters = json_decode($this->request->input('filters'), TRUE);

      foreach($filters as $llave => $valor)
      {
        if($llave == 'rules')
        {
          foreach($valor as $llave1 => $valor1)
          {
            if($valor1['op'] == 'cn')
            {
              $array_where_a = [
                [
                  $valor1['field'],
                  $this->ops[$valor1['op']],
                  '%' . $valor1['data'] . '%'
                ]
              ];
            }
            else
            {
              $array_where_a = [
                [
                  $valor1['field'],
                  $this->ops[$valor1['op']],
                  $valor1['data']
                ]
              ];
            }

            $array_where = array_merge($array_where, $array_where_a);
          }
        }
      }
    }
    return $array_where;
  }
}
