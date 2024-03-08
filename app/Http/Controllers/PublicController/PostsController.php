<?php

namespace App\Http\Controllers\PublicController;

use App\Models\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->OrderBy("id", "DESC")->paginate(10)->toArray();

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
        $post = Post::with(['user' => function($query){
            $query->select('id', 'name');
        }])->find($id);

        if (!$post) {
            abort(404);
        }

        return response()->json($post, 200);
    }



}

