<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Blog/Categories/Index', [
            'categories' => BlogCategory::withCount('posts')->orderBy('sort_order')->get(),
            'tags'       => BlogTag::withCount('posts')->orderByDesc('posts_count')->get(),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['nullable', 'string', 'max:120', 'unique:blog_categories,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer'],
        ]);
        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['name']);
        BlogCategory::create($validated);
        return back()->with('success', 'Категория добавлена.');
    }

    public function updateCategory(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:100'],
            'slug'        => ['nullable', 'string', 'max:120', "unique:blog_categories,slug,{$category->id}"],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer'],
            'is_active'   => ['boolean'],
        ]);
        if (!empty($validated['slug'])) $validated['slug'] = Str::slug($validated['slug']);
        $category->update($validated);
        return back()->with('success', 'Категория обновлена.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Категория удалена.');
    }

    public function storeTag(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:80', 'unique:blog_tags,slug'],
        ]);
        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['name']);
        BlogTag::create($validated);
        return back()->with('success', 'Тег добавлен.');
    }

    public function destroyTag(BlogTag $tag)
    {
        $tag->delete();
        return back()->with('success', 'Тег удалён.');
    }
}
