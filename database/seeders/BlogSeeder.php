<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Database\Seeders\Support\BlogArticleRenderer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /** @var array<string, int> */
    private array $categoryIds = [];

    /** @var array<string, int> */
    private array $tagIds = [];

    public function run(): void
    {
        $author = User::where('email', 'admin@automarket.ru')->first()
            ?? User::first();

        if (! $author) {
            $this->command?->warn('BlogSeeder: пользователь-автор не найден, пропуск.');

            return;
        }

        $this->seedCategories();
        $this->seedTags();

        $articles = array_merge(
            require __DIR__.'/data/blog_articles_part1.php',
            require __DIR__.'/data/blog_articles_part2.php',
            require __DIR__.'/data/blog_articles_part3.php',
        );

        $dates = $this->spreadDates(count($articles));

        foreach ($articles as $index => $article) {
            $this->seedPost($article, $author->id, $dates[$index]);
        }

        $this->command?->info('BlogSeeder: создано/обновлено '.count($articles).' статей.');
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Покупка и выбор', 'slug' => 'pokupka-vybor', 'description' => 'Гиды по выбору автомобиля, оценке и сделкам', 'sort_order' => 1],
            ['name' => 'Обслуживание и уход', 'slug' => 'obsluzhivanie', 'description' => 'ТО, сезонная подготовка и уход за автомобилем', 'sort_order' => 2],
            ['name' => 'Запчасти и ремонт', 'slug' => 'zapchasti-remont', 'description' => 'Подбор деталей, ремонт и экономия без потери качества', 'sort_order' => 3],
            ['name' => 'Советы водителю', 'slug' => 'sovety-voditelyu', 'description' => 'Практика вождения, безопасность и комфорт', 'sort_order' => 4],
            ['name' => 'Автолайф', 'slug' => 'avtolajf', 'description' => 'Путешествия, lifestyle и повседневная жизнь с автомобилем', 'sort_order' => 5],
            ['name' => 'Техника и устройство', 'slug' => 'tehnika-ustrojstvo', 'description' => 'Как устроен автомобиль: двигатель, трансмиссия, системы', 'sort_order' => 6],
        ];

        foreach ($categories as $data) {
            $category = BlogCategory::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true]),
            );
            $this->categoryIds[$data['slug']] = $category->id;
        }
    }

    private function seedTags(): void
    {
        $tags = [
            'probeg' => 'Пробег', 'novyy-avtomobil' => 'Новый автомобиль', 'byu' => 'Б/У',
            'trade-in' => 'Trade-in', 'kredit' => 'Кредит', 'zima' => 'Зима', 'leto' => 'Лето',
            'to' => 'ТО', 'maslo' => 'Масло', 'shiny' => 'Шины', 'tormoza' => 'Тормоза',
            'vin' => 'VIN', 'oem' => 'OEM', 'analog' => 'Аналог', 'gorod' => 'Город',
            'trassa' => 'Трасса', 'ekonomiya' => 'Экономия', 'bezopasnost' => 'Безопасность',
            'semeynyy-avto' => 'Семейный авто', 'krossover' => 'Кроссовер', 'sedan' => 'Седан',
            'elektromobil' => 'Электромобиль', 'diy' => 'DIY', 'chek-list' => 'Чек-лист',
            'byudzhet' => 'Бюджет', 'podveska' => 'Подвеска', 'akkumulyator' => 'Аккумулятор',
            'kondicioner' => 'Кондиционер', 'remont' => 'Ремонт', 'dvigatel' => 'Двигатель',
            'zapchasti' => 'Запчасти', 'sovety' => 'Советы', 'puteshestvie' => 'Путешествие',
            'deti' => 'Дети', 'fary' => 'Фары', 'gibrid' => 'Гибрид', 'kpp' => 'КПП',
            'pdd' => 'ПДД', 'salon' => 'Салон', 'kuzov' => 'Кузов', 'uhod' => 'Уход',
            'tehnika' => 'Техника', 'upravlyaemost' => 'Управляемость', 'parkovka' => 'Парковка',
            'avtolajf' => 'Автолайф', 'dilery' => 'Дилеры', 'ekologiya' => 'Экология',
        ];

        foreach ($tags as $slug => $name) {
            $tag = BlogTag::firstOrCreate(['slug' => $slug], ['name' => $name]);
            $this->tagIds[$slug] = $tag->id;
        }
    }

    /** @return array<int, Carbon> */
    private function spreadDates(int $count): array
    {
        $start = Carbon::parse('2025-02-03')->startOfDay();
        $end = Carbon::parse('2026-06-15')->startOfDay();
        $dates = [];

        for ($i = 0; $i < $count; $i++) {
            if ($count === 1) {
                $dates[] = $start->copy();

                continue;
            }

            $offsetDays = (int) round($start->diffInDays($end) * $i / ($count - 1));
            $date = $start->copy()->addDays($offsetDays);

            // Небольшой разброс по времени публикации внутри дня
            $date->setTime(9 + ($i % 8), ($i * 13) % 60, 0);
            $dates[] = $date;
        }

        return $dates;
    }

    /** @param  array<string, mixed>  $article */
    private function seedPost(array $article, int $authorId, Carbon $publishedAt): void
    {
        $slug = $article['slug'];
        $content = BlogArticleRenderer::render($article['sections']);

        $thumbnail = $this->downloadThumbnail(
            $article['thumbnail_key'] ?? $slug,
            $slug,
        );

        $post = BlogPost::updateOrCreate(
            ['slug' => $slug],
            [
                'author_id' => $authorId,
                'category_id' => $this->categoryIds[$article['category']] ?? null,
                'title' => $article['title'],
                'excerpt' => $article['excerpt'] ?? null,
                'content' => $content,
                'thumbnail' => $thumbnail,
                'status' => 'published',
                'reading_time_minutes' => $article['reading_time_minutes'] ?? null,
                'views' => rand(120, 4800),
                'published_at' => $publishedAt,
            ],
        );

        $tagIds = collect($article['tags'] ?? [])
            ->map(fn (string $tagSlug) => $this->tagIds[$tagSlug] ?? null)
            ->filter()
            ->values()
            ->all();

        $post->tags()->sync($tagIds);
    }

    private function downloadThumbnail(string $seed, string $slug): ?string
    {
        $path = "blog/{$slug}.jpg";

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {
            $response = Http::timeout(20)
                ->withOptions(['allow_redirects' => true])
                ->get('https://picsum.photos/seed/'.urlencode($seed).'/1200/630');

            if ($response->successful() && strlen($response->body()) > 1000) {
                Storage::disk('public')->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable) {
            // Без превью статья всё равно будет опубликована
        }

        return null;
    }
}
