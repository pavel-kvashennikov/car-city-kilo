<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = BlogPost::with(['author', 'category'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) =>
                $q->where('title', 'ilike', "%{$s}%")
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Blog/Posts/Index', [
            'posts'   => $posts,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Blog/Posts/Create', [
            'categories' => BlogCategory::where('is_active', true)->orderBy('sort_order')->get(),
            'tags'       => BlogTag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                => ['required', 'string', 'max:255'],
            'slug'                 => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'excerpt'              => ['nullable', 'string', 'max:500'],
            'content'              => ['required', 'string'],
            'category_id'          => ['nullable', 'exists:blog_categories,id'],
            'status'               => ['required', 'in:draft,published'],
            'published_at'         => ['nullable', 'date'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1'],
            'tags'                 => ['nullable', 'array'],
            'tags.*'               => ['exists:blog_tags,id'],
            'thumbnail'            => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['title']);
        $validated['author_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog', 'public');
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $post = BlogPost::create($validated);
        $post->tags()->sync($tags);

        return redirect()->route('admin.blog.posts.index')->with('success', 'Статья создана.');
    }

    public function edit(BlogPost $post): Response
    {
        return Inertia::render('Admin/Blog/Posts/Edit', [
            'post'       => $post->load(['tags', 'category']),
            'categories' => BlogCategory::where('is_active', true)->orderBy('sort_order')->get(),
            'tags'       => BlogTag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title'                => ['sometimes', 'string', 'max:255'],
            'slug'                 => ['nullable', 'string', 'max:255', "unique:blog_posts,slug,{$post->id}"],
            'excerpt'              => ['nullable', 'string', 'max:500'],
            'content'              => ['sometimes', 'string'],
            'category_id'          => ['nullable', 'exists:blog_categories,id'],
            'status'               => ['sometimes', 'in:draft,published'],
            'published_at'         => ['nullable', 'date'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1'],
            'tags'                 => ['nullable', 'array'],
            'tags.*'               => ['exists:blog_tags,id'],
            'thumbnail'            => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) Storage::disk('public')->delete($post->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog', 'public');
        }

        if (($validated['status'] ?? $post->status) === 'published' && empty($post->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        $post->update($validated);
        if ($tags !== null) $post->tags()->sync($tags);

        return back()->with('success', 'Статья обновлена.');
    }

    public function destroy(BlogPost $post)
    {
        if ($post->thumbnail) Storage::disk('public')->delete($post->thumbnail);
        $post->delete();

        return redirect()->route('admin.blog.posts.index')->with('success', 'Статья удалена.');
    }
}
