<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories=Category::get();
        return response()->json($categories);
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'description'=>'required'
        ]);
        $obj=new Category();
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj,201);
    }
}
