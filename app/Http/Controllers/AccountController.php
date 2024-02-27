<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::OrderBy("id", "DESC")->paginate(10);

        $outPut = [
            "message" => "posts",
            "results" => $accounts

        ];

        return response()->json($accounts, 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $account = Account::create($input);

        return response()->json($account, 200);
    }

    public function show($id)
    {
        $account = Account::find($id);

        if (!$account) {
            abort(404);
        }
        return response()->json($account, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $account = Account::find($id);

        if (!$account) {
            abort(404);
        }

        $account->fill($input);
        $account->save();

        return response()->json($account, 200);

    }

    public function destroy($id)
    {
        $account = Account::find($id);

        if (!$account) {
            abort(404);
        }

        $account->delete();
        $message = ['message' => 'deleted successfully', 'id' => $id];
        return response()->json($message, 200);
    }
}

