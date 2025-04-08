<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(){
        $json = array(
            "detail"=>"web no encontrada"
        );

        echo json_encode($json,true);
    }
}
