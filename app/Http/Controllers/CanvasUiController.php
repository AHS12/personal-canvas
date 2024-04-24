<?php

namespace App\Http\Controllers;

use App\Models\PinnedPost;
use Canvas\Events\PostViewed;
use Canvas\Models\Post;
use Canvas\Models\Tag;
use Canvas\Models\Topic;
use Canvas\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CanvasUiController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('canvas-ui')->with([
            'config' => [
                'canvasPath' => config('canvas.path'),
                'user' => $request->user('canvas'),
                'timezone' => config('app.timezone'),
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request                    $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPosts(Request $request): LengthAwarePaginator
    {
        return Post::latest()->published()->with('user', 'topic')->paginate();
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPost(Request $request, $slug): JsonResponse
    {
        $post = Post::with('user', 'tags', 'topic')->firstWhere('slug', $slug);

        if ($post) {
            event(new PostViewed($post));

            return response()->json($post, 200);
        } else {
            return response()->json(null, 404);
        }
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    public function getTags(Request $request): string
    {
        return Tag::all()->toJson();
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showTag(Request $request, $slug): JsonResponse
    {
        $tag = Tag::firstWhere('slug', $slug);

        return $tag ? response()->json($tag, 200) : response()->json(null, 404);
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsForTag(Request $request, $slug): JsonResponse
    {
        $tag = Tag::firstWhere('slug', $slug);

        return $tag ? response()->json($tag->posts()->with('topic', 'user')->paginate(), 200) : response()->json(null, 200);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    public function getTopics(Request $request): string
    {
        return Topic::all()->toJson();
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showTopic(Request $request, $slug): JsonResponse
    {
        $topic = Topic::firstWhere('slug', $slug);

        return $topic ? response()->json($topic, 200) : response()->json(null, 404);
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsForTopic(Request $request, $slug): JsonResponse
    {
        $topic = Topic::firstWhere('slug', $slug);

        return $topic ? response()->json($topic->posts()->with('topic', 'user')->paginate(), 200) : response()->json(null, 200);
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUser(Request $request, $id): JsonResponse
    {
        $user = User::with('posts')->find($id);

        return $user ? response()->json($user, 200) : response()->json(null, 404);
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @param                                $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsForUser(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        return $user ? response()->json($user->posts()->published()->with('user', 'topic')->paginate(), 200) : response()->json(null, 200);
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPosts(Request $request): JsonResponse
    {
        $key = 'search-posts';

        $cachedData = Cache::remember($key, 36000, function () {
            $posts = Post::query()
                         ->select('id', 'title', 'slug')
                         ->latest()
                         ->get();

            $posts->map(function ($post) {
                $post['name'] = $post->title;
                $post['slug'] = $post->slug;
                $post['type'] = 'Post';
                $post['route'] = 'show-post';

                return $post;
            });

            return collect($posts)->toArray();
        });

        return response()->json($cachedData, 200);
    }

    /**
     * @param  \Illuminate\Http\Request      $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPinnedPosts(Request $request): JsonResponse
    {
        $pinnedPostIds = PinnedPost::all()->pluck('post_id')->toArray();
        $key = 'pinned-posts';

        $cachedData = Cache::remember($key, 36000, function () use ($pinnedPostIds) {
            $posts = Post::query()
                         ->whereIn('id', $pinnedPostIds)
                         ->with('user', 'topic')
                         ->latest()
                         ->get();

            return collect($posts)->toArray();
        });

        return response()->json($cachedData, 200);
    }


    public function makePinnedPost(Request $request): JsonResponse
    {
        if(!$request->user('canvas')) {
            return response()->json(null, 401);
        }
        $post = Post::whereSlug($request->slug)->first();
        if (!PinnedPost::where('post_id', $post->id)->exists()) {
            PinnedPost::create(['post_id' => $post->id]);
        }
        return response()->json(null, 200);
    }

}
