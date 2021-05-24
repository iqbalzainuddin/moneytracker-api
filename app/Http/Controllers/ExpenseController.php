<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Expense;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Expense::where('user_id', request()->user()->id)
            ->orderBy('id')
            ->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $fields = $request->validate([
            'type' => 'required|string',
            'item' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Guzzle for Internal API request
        $walletUpd = Http::withHeaders([
            'authorization' => $request->header('authorization')
        ])->withOptions([
            'verify' => false
        ])->put('http://expense-tracker.dev/api/wallet/'.request()->user()->id, [
            'type' => $fields['type'],
            'wallet' => $fields['amount'],
        ]);

        // Verify updated wallet
        if (!$walletUpd == true) {
            return response()->json([
                'message' => 'User wallet failed to update'
            ], 400);
        }

        // Create entry for transaction
        $expense = Expense::create([
            'type' => $fields['type'],
            'item' => $fields['item'],
            'amount' => $fields['amount'],
            'user_id' => request()->user()->id,
        ]);

        // Verify data creation and return response accordingly
        if ($expense) {
            return response()->json($expense, 201);
        }
        else {
            return response()->json($expense, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        return response()->json($expense, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'type' => 'required|string',
            'item' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $res = $expense->update([
            'type' => $request->type,
            'item' => $request->item,
            'amount' => $request->amount,
            'user_id' => request()->user()->id
        ]);

        if ($res) {
            return response()->json($res, 200);
        } else {
            return response()->json($res, 500);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->type == "INCOME") {
            // Guzzle for Internal API request
            $walletUpd = Http::withHeaders([
                'authorization' => $request->header('authorization')
            ])->withOptions([
                'verify' => false
            ])->put('http://expense-tracker.dev/api/wallet/'.request()->user()->id, [
                'type' => "EXPENSE",
                'wallet' => $expense->amount,
            ]);
        } else {
            // Guzzle for Internal API request
            $walletUpd = Http::withHeaders([
                'authorization' => $request->header('authorization')
            ])->withOptions([
                'verify' => false
            ])->put('http://expense-tracker.dev/api/wallet/'.request()->user()->id, [
                'type' => "INCOME",
                'wallet' => $expense->amount,
            ]);
        }
        
        if (!$walletUpd == true) {
            return response()->json([
                'status' => 'error'
            ], 500);
        }

        $res = $expense->delete();

        if ($res) {
            return response()->json($res, 200);
        } else {
            return response()->json($res, 500);
        }
    }
}
