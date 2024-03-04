<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::OrderBy("id", "DESC")->paginate(2)->toArray();

        $response = [
            "total_count" => $posts["total"],
            "limit" => $posts["per_page"],
            "pagination" => [
                "next_page" => $posts["next_page_url"],
                "current_page" => $posts["current_page"]
            ],
            "data" => $posts["data"]
        ];

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }
        return response()->json($post, 200);
    }

}

