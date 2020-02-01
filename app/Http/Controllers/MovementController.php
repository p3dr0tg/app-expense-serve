<?php

namespace App\Http\Controllers;

use App\Category;
use App\Movement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $movements=Movement::getAll($request)->get();
        $total= Movement::getTotal($request)->first();
        return response()->json(['rows'=>$movements,'total'=>$total]);
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'category_id'=>'required',
            'saving_account_id'=>'required',
            'month'=>'required',
            'amount'=>'required',
            'date'=>'required|date',
        ]);
        $amount=$request->amount;
        $category=Category::find($request->category_id);
        if($category->type=='Egresos'){
            $amount=$amount*-1;
        }
        $obj=new Movement();
        $obj->fill($request->all());
        $obj->amount=$amount;
        $obj->user_id=$request->user()->id;
        $obj->save();
        return response()->json($obj,201);
    }
    public function summary(Request $request)
    {
        $summary=Movement::getSummary($request)->get();
        $total= Movement::getTotal($request)->first();
        return response()->json(['rows'=>$summary,'total'=>$total]);
    }

}
