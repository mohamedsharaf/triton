<?php

namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JqgridClass
{

  function __construct()
  {

  }

  public function getQuery($data)
  {
    if($data['_search'] == 'true')
    {

    }

    if($data['where'] == '')
    {
        $data['where'] = '';
    }

    if( ! $data['sidx'])
    {
        $data['sidx'] = 1;
    }






    $page = $data;
    return $page;
  }

  public function getData()
  {
    // return DB::table('clasificador_presupuestarios')->get();
  }
}
