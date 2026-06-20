<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->when($request->category, fn ($q, $slug) =>
                $q->whereHas('category', fn ($q) => $q->where('slug', $slug))
            )
            ->when($request->tag, fn ($q, $slug) =>
                $q->whereHas('tags', fn ($q) => $q->where('slug', $slug))
            )
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($q) => $q->where('title', 'ilike', "%{$s}%")
                                       ->orWhere('excerpt', 'ilike', "%{$s}%"))
            )
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = BlogCategory::where('is_active', true)
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $tags = BlogTag::withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get()
            ->filter(fn ($tag) => $tag->posts_count > 0)
            ->take(30)
            ->values();

        return Inertia::render('Blog/Index', [
            'posts'      => $posts,
            'categories' => $categories,
            'tags'       => $tags,
            'filters'    => $request->only(['category', 'tag', 'search']),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('views');

        $related = BlogPost::published()
            ->with(['author', 'category'])
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) =>
                $q->where('category_id', $post->category_id)
            )
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post'    => $post,
            'related' => $related,
        ]);
    }
}
