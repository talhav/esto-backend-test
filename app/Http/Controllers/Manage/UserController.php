<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\UserCreateRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{

    public function store(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data,[
            'name' => 'required|unique:users,name',
            'email' => 'required|email',
        ]);

        if($validate->fails()){

            return response()->json(['status'=> 400, 'message' => 'User not created', 'errors' => $validate->errors()->getMessageBag()]);

        }

        $user = User::create($data);
        return response()->json(['status'=> 200, 'message' => 'User Created Succesfully', 'data' => $user]);

    }

    public function users(){
        dd("here");
    }

}
