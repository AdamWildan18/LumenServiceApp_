<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;

use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::where(['user_id' => Auth::user()->id])->orderBy("id", "DESC")->paginate(2)->toArray();
        $response = [
            "total_count" => $barangs["total"],
            "limit" => $barangs["per_page"],
            "pagination" => [
                "next_page" => $barangs["next_page_url"],
                "current_page" => $barangs["current_page"]
            ],
            "data" => $barangs["data"]
        ];

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            abort(404);
        }

        return response()->json($barang, 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $barang = Barang::create($input);

        return response()->json($barang, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $barang = Barang::find($id);

        if (!$barang) {
            abort(404);
        }

        $barang->fill($input);
        $barang->save();

        return response()->json($barang, 200);
    }

    public function destroy($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            abort(404);
        }

        $barang->delete();
        $message = ['message' => 'deleted successfully', 'id' => $id];
        return response()->json($message, 200);
    }
}
