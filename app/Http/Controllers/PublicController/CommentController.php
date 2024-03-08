<?php
namespace App\Http\Controllers\PublicController;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index()
    {
        $comment = Comment::with('user', 'post')->OrderBy("id", "DESC")->paginate(10)->toArray();

        $response = [
            "total_count" => $comment["total"],
            "limit" => $comment["per_page"],
            "pagination" => [
                "next_page" => $comment["next_page_url"],
                "current_page" => $comment["current_page"]
            ],
            "data" => $comment["data"]
        ];

        return response()->json($response, 200);

    }

    public function store(Request $request)
    {
        $input = [
            'post_id' => $request->input('post_id'),
            'user_id' => Auth::user()->id,
            'comment' => $request->input('comment'),
        ];

        $post = Comment::create($input);



        return response()->json($post, 200);
    }
}
