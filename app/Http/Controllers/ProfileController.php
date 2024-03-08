<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
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
            $profiles = Profile::OrderBy("id", "DESC")->paginate(2)->toArray();
        } else {
            $profiles = Profile::where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
        }

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

    public function store(Request $request)
{
    $input = $request->all();

    $validationRules = [
        'first_name' => 'required|min:2',
        'last_name' => 'required|min:2',
        'summary' => 'required|min:10'
    ];

    $validator = Validator::make($input, $validationRules);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $profile = Profile::where('user_id', Auth::user()->id)->first();

    if (!$profile) {
        $profile = new Profile;
        $profile->user_id = Auth::user()->id;
    }

    $profile->first_name = $request->input('first_name');
    $profile->last_name = $request->input('last_name');
    $profile->summary = $request->input('summary');

    if ($request->hasFile('omage')) {
        $firstName = str_replace(' ', '_', $request->input('first_name'));
        $lastName = str_replace(' ', '_', $request->input('last_name'));

        $imageName = Auth::user()->id . '_' . $firstName . '_' . $lastName;
        $request->file('image')->move(storage_path('uploads/iamge_profile'), $imageName);

        $current_image_path = storage_path('avatar') . '/' . $profile->image;
        if (file_exists($current_image_path)) {
            unlink($current_image_path);
        }

        $profile->image = $imageName;
    }

    $profile->save();

    return response()->json($profile, 200);
}


    public function show($id)
    {
        $profiles = Profile::find($id);

        if (!$profiles) {
            abort(404);
        }

        if (Auth::user()->role === 'admin') {
            return response()->json($profiles, 200);
        }

        if (Auth::user()->role === 'editor' && $profiles->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You Are unauthorized'
            ], 403);
        }

        return response()->json($profiles, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $profiles = Profile::find($id);

        if (!$profiles) {
            abort(404);
        }

        if (Gate::denies('update-post', $profiles)) {
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

        $profiles->fill($input);
        $profiles->save();

        return response()->json($profiles, 200);

    }

    public function destroy($id)
    {
        $profiles = Profile::find($id);

        if (!$profiles) {
            abort(404);
        }

        if (Gate::denies('update-post', $profiles)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $profiles->delete();
        $message = ['message' => 'deleted successfully', 'post_id' => $id];
        return response()->json($message, 200);
    }
}

