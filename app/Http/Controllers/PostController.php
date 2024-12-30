<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->has('filter') && $request->has('value')) {
            $query->where($request->input('filter'), 'like', '%' . $request->input('value') . '%');
        }

        if ($request->has('order_by') && $request->has('order')) {
            $query->orderBy($request->input('order_by'), $request->input('order'));
        }

        $posts = $query->get();

        return response()->json(["ok" => true, "data" => $posts], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title"=> "required|string|max:255",
            "author"=> "required|string|max:255",
            "excerpt"=> "required|min:50",
            "text"=> "required|min:150",
        ]);
        $post = Post::create($validated);
        return response()->json(["ok"=> true, "post" => $post],201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(["ok" => false, "message" => "Post not found"], 404);
        }

        return response()->json(["ok" => true, "post" => $post], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post, $id)
    {
        // Validate only the fields that are present in the request
        $validated = $request->validate([
            'title'   => 'sometimes|required|string|max:255',
            'author'  => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|min:50',
            'text'    => 'sometimes|required|min:150',
        ]);

        // Update the post with the validated data
        // $post->update($validated);
        $post = Post::findOrFail(intval($id));
        $post->update($validated);

        // Return the updated post
        return response()->json(["ok" => true, "post" => $post], 200);
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Post $post, $id)
    {
        $post = Post::findOrFail(intval($id));
        $post->delete();
        return response()->json(["ok" => true, "message" => "Post deleted successfully"], 200);
    }
}
