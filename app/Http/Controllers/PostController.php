<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;


class PostController extends Controller
{
    use ApiResponse, SoftDeletes;
    public function index()
    {
        $posts = auth()->user()->posts()->with('tags')->orderBy('pinned', 'desc')->orderBy('created_at', 'desc')->get();
        if ($posts->isEmpty()) {
            return $this->error('You did not publish any posts yet!', 404);
        }
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imagePath = $image->store('cover_images', 'public');
            $validated['cover_image'] = $imagePath;
        }

        $post = auth()->user()->posts()->create($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return new PostResource($post);
    }

    public function show(Post $post)
    {

        $post = auth()->user()->posts()->first();

        $this->authorize('view', $post);
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post = auth()->user()->posts()->first();
        $this->authorize('update', $post);

        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $imagePath = $request->file('cover_image')->store('cover_images', 'public');
            $validated['cover_image'] = $imagePath;
        }

        $post->update($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $post = auth()->user()->posts()->first();
        $this->authorize('delete', $post);

        $post->delete();

        return $this->success('Post deleted successfully');
    }

    public function deletedPosts()
    {
        $user = auth()->user();
        $deletedPosts = $user->posts()->onlyTrashed()->get();
        return PostResource::collection($deletedPosts);
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $post);

        $post->restore();

        return new PostResource($post);
    }
}