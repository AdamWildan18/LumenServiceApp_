<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
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
            $posts = Post::OrderBy("id", "DESC")->paginate(2)->toArray();
        } else {
            $posts = Post::where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
        }

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

    public function store(Request $request)
    {
        if (Gate::denies('store-post')) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        $input = $request->all();

        $validationRules = [
            'title' => 'required|min:2',
            'content' => 'required|min:2',
            'status' => 'required|min:2',
            'user_id' => 'required'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }



        $post = Post::where('user_id', Auth::user()->id)->first();

        if (!$post) {
            $post = new Post;
            $post->user_id = Auth::user()->id;
        }
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->status = $request->input('status');
        $post->user_id = $request->input('user_id');

        if ($request->hasFile('image')) {
            $titleReplace = str_replace(' ', '_', $request->input('title'));
            // $lastName = str_replace(' ', '_', $request->input('last_name'));

            $imageName = Auth::user()->id . '_' . $titleReplace;
            $request->file('image')->move(storage_path('uploads/image_profile'), $imageName);

            $current_image_path = storage_path('avatar') . '/' . $post->image;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $post->image = $imageName;
        }

        if ($request->hasFile('video')) {
            $titleReplace = str_replace(' ', '_', $request->input('title'));
            // $lastName = str_replace(' ', '_', $request->input('last_name'));

            $videoName = Auth::user()->id . '_' . $titleReplace;
            $request->file('video')->move(storage_path('uploads/post_video'), $videoName);

            if (!empty($post->video)) {
                $current_video_path = storage_path('uploads/post_video') . '/' . $post->video;
                if (file_exists($current_video_path)) {
                    unlink($current_video_path);
                }
            }
            $post->video = $videoName;
        }
        $post->save();

        return response()->json($post, 200);
    }

    public function image($imageName)
    {
        $imagePath = storage_path('uploads/image_profile'). '/' . $imageName;
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        return response()->json(array(
            "message" => "Image not found"
        ), 401);
    }

    public function video($videoName)
    {
        $videoPath = storage_path('uploads/post_video'). '/' . $videoName;
        if (file_exists($videoPath)) {
            $file = file_get_contents($videoPath);
            return response($file, 200)->header('Content-Type', 'video/mp4');
        }
        return response()->json(array(
            "message" => "Video not found"
        ), 401);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        if (Gate::denies('read-post', $post)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        if (Auth::user()->role === 'admin') {
            return response()->json($post, 200);
        }

        if (Auth::user()->role === 'editor' && $post->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        return response()->json($post, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        if (Gate::denies('update-post', $post)) {
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

        $post->fill($input);
        $post->save();

        return response()->json($post, 200);

    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        if (Gate::denies('update-post', $post)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $post->delete();
        $message = ['message' => 'deleted successfully', 'post_id' => $id];
        return response()->json($message, 200);
    }
}

