<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use \Symfony\Component\HttpFoundation\Response;//webステータスコード


//events
use App\Events\mytleRepeat;

class lineController extends Controller
{
    //
    public function reMessage(REQUEST $request){
        return response()->json(['test'=>'testMessage'],200);
    }

    public function mytles(REQUEST $request){

        $values=json_decode(json_encode($request->all()),true);
        event(new mytleRepeat($values));
        return 0;
    }




}
