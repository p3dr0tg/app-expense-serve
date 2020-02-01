<?php

namespace App\Http\Controllers;

use App\Category;
use App\SavingAccount;
use Illuminate\Http\Request;

class SavingAccountController extends Controller
{
    public function index()
    {
        $saving_accounts=SavingAccount::get();
        return response()->json($saving_accounts);
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'description'=>'required'
        ]);
        $obj=new SavingAccount();
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj,201);
    }
}
