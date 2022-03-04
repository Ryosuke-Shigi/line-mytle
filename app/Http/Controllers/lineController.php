<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use \Symfony\Component\HttpFoundation\Response;//webステータスコード

class lineController extends Controller
{
    //
    public function reMessage(REQUEST $request){
        return response()->json(['test'=>'testMessage'],200);
    }

    public function mytles(REQUEST $request){

        return response()->json(['mytle'=>'mytle'],200);
    }
}
