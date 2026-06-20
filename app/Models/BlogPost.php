<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class BlogPost extends Model
{
    protected $fillable = [
        'author_id', 'category_id', 'title', 'slug', 'excerpt',
        'content', 'thumbnail', 'status', 'reading_time_minutes',
        'views', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function getReadingTimeAttribute(): int
    {
        if ($this->reading_time_minutes) return $this->reading_time_minutes;
        $words = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($words / 200));
    }
}
