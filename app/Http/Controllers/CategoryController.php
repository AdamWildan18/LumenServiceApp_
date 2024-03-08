<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        if (Gate::denies('read-post')) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        if (Auth::user()->role === 'admin') {
            $categories = Category::OrderBy("id", "DESC")->paginate(2)->toArray();
        } else {
            $categories = Category::where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
        }

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

    public function store(Request $request)
    {
        $input = [
            'categories' => $request->input('categories'),
            'post_id' => $request->input('post_id'),
            'user_id' => Auth::user()->id
        ];

        if (Gate::denies('store-post')) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        $categories = Category::create($input);

        $newCategoryId = $categories->id;

        $post = Post::find($request->input('post_id'));

        $post->categories()->attach($newCategoryId);

        return response()->json($categories, 200);
    }

    public function show($id)
    {
        $categories = Category::find($id);

        if (!$categories) {
            abort(404);
        }

        if (Gate::denies('read-post', $categories)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        if (Auth::user()->role === 'admin') {
            return response()->json($categories, 200);
        }

        if (Auth::user()->role === 'editor' && $categories->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        return response()->json($categories, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $categories = Category::find($id);

        if (!$categories) {
            abort(404);
        }

        if (Gate::denies('update-post', $categories)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $categories->fill($input);
        $categories->save();

        return response()->json($categories, 200);

    }

    public function destroy($id)
    {
        $categories = Category::find($id);

        if (!$categories) {
            abort(404);
        }

        if (Gate::denies('update-post', $categories)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $categories->delete();
        $message = ['message' => 'deleted successfully', 'post_id' => $id];
        return response()->json($message, 200);
    }
}

