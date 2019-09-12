<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends BaseController
{

    public function store(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data,[
            'type' => 'required|in:DEBIT,CREDIT',
            'amount' => 'required|numeric|min:0|not_in:0',
        ]);

        if($validate->fails()){
            return response()->json(['status'=> 400, 'message' => 'Transaction not created', 'errors' => $validate->errors()->getMessageBag()]);
        }

        $data["user_id"] = Auth::user()->id;
        $transaction = Transaction::create($data);
        return response()->json(['status'=> 200, 'message' => 'Transaction Created Succesfully', 'data' => $transaction]);

    }

    public function getDebitTransactions()
    {
        $users = User::with(['transactions' => function ($query) {
            $query->sum('amount');
        }])->orderByDesc('id')
            ->limit(10)->get();

        $data = [];

        foreach ($users as $key=>$user){
            $data[$key]['id']= $user->id;
            $data[$key]['name']= $user->name;
            $data[$key]['email']= $user->name;
            $data[$key]['total_debit_amount']= $user->transactions->where('type','DEBIT')->sum('amount');
        }

        return response()->json(['status'=>200,'message'=>'Transactions found successfully', 'data'=>$data]);
    }
}
