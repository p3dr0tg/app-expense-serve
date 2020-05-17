<?php

namespace App\Http\Controllers;

use App\Category;
use App\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () use ($request){
            $amount=$request->amount;
            $category=Category::find($request->category_id);
            if($category->type=='Egresos'){
                $amount=$amount*-1;
            }
            $obj=Movement::find($request->input('id'));
            if(is_null($obj)){
                $obj=new Movement();
                $obj->user_id=$request->user()->id;
            }
            $obj->fill($request->all());
            $obj->amount=$amount;
            $obj->save();
            return response()->json($obj,201);
        });

    }
    public function show($id,Request $request)
    {
        $result=Movement::withCategory()->findOrFail($id);
        if($result->user_id!=$request->user()->id){
            return response()->json('no autorizado',403);
        }
        return response()->json($result);
    }
    public function summary(Request $request)
    {
        $summary=Movement::getSummary($request)->get();
        $total= Movement::getTotal($request)->first();
        return response()->json(['rows'=>$summary,'total'=>$total]);
    }
    public function categories(Request $request)
    {
        $result=(new Movement())->getByCategories($request);
        $totals=[
            'expenses'=>collect($result)->where('type','Egresos')->sum('amount'),
            'income'=>collect($result)->where('type','Ingresos')->sum('amount'),
            'balance'=>$result->sum('amount')
        ];
        return response()->json(['rows'=>$result,'totals'=>$totals]);
    }

}
