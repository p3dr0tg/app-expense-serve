<?php

namespace App\Http\Controllers;

use App\Category;
use App\SavingAccount;
use Illuminate\Http\Request;

class SavingAccountController extends Controller
{
    public function index(Request $request)
    {
        $saving_accounts=SavingAccount::select('id','description')
            ->whereIn('user_id',[0,$request->user()->id])
            ->orderBy('id','asc')->get();
        return response()->json($saving_accounts);
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'description'=>'required'
        ]);
        $obj=new SavingAccount();
        $obj->user_id=$request->user()->id;
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj,201);
    }
}
