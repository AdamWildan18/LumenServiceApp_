<?php

namespace App\Http\Controllers\PublicController;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::with('user')->OrderBy("id", "DESC")->paginate(10)->toArray();

        $response = [
            "total_count" => $profiles["total"],
            "limit" => $profiles["per_page"],
            "pagination" => [
                "next_page" => $profiles["next_page_url"],
                "current_page" => $profiles["current_page"]
            ],
            "data" => $profiles["data"]
        ];

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $profiles = Profile::with(['post' => function($query){
            $query->select('id', 'name');
        }])->find($id);

        if (!$profiles) {
            abort(404);
        }

        return response()->json($profiles, 200);
    }
}

