<?php

namespace App\Http\Controllers\PublicController;

use App\Models\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('posts')->OrderBy("id", "DESC")->paginate(10)->toArray();

        $response = [
            "total_count" => $categories["total"],
            "limit" => $categories["per_page"],
            "pagination" => [
                "next_page" => $categories["next_page_url"],
                "current_page" => $categories["current_page"]
            ],
            "data" => $categories["data"]
        ];

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $categories = Category::with(['post' => function($query){
            $query->select('id', 'name');
        }])->find($id);

        if (!$categories) {
            abort(404);
        }

        return response()->json($categories, 200);
    }

}

