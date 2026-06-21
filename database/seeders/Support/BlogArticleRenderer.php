<?php

namespace Database\Seeders\Support;

class BlogArticleRenderer
{
    private static array $images = [
        'car'         => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1100&q=80',
        'interior'    => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1100&q=80',
        'suv'         => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0aed?w=1100&q=80',
        'maintenance' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1100&q=80',
        'tires'       => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1100&q=80',
        'road'        => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1100&q=80',
        'winter'      => 'https://images.unsplash.com/photo-1517713487977-8c42db1e5e59?w=1100&q=80',
        'engine'      => 'https://images.unsplash.com/photo-1625047509168-a7024178532e?w=1100&q=80',
        'parts'       => 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=1100&q=80',
        'electric'    => 'https://images.unsplash.com/photo-1593941707882-a5bba14938ca?w=1100&q=80',
        'garage'      => 'https://images.unsplash.com/photo-1558618047-3f44c5a2d615?w=1100&q=80',
        'safety'      => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1100&q=80',
        'highway'     => 'https://images.unsplash.com/photo-1519003300449-424ad0405076?w=1100&q=80',
        'tools'       => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=1100&q=80',
        'family'      => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=1100&q=80',
        'wash'        => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=1100&q=80',
        'night'       => 'https://images.unsplash.com/photo-1520209759809-a9bcb6cb3241?w=1100&q=80',
        'parking'     => 'https://images.unsplash.com/photo-1506521781263-d8422e82f27a?w=1100&q=80',
        'spring'      => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1100&q=80',
        'map'         => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?w=1100&q=80',
    ];

    public static function image(string $key): string
    {
        return self::$images[$key] ?? self::$images['car'];
    }

    /** @param array<int, array<string, mixed>> $sections */
    public static function render(array $sections): string
    {
        $html = '';
        $h2Count = 0;

        foreach ($sections as $section) {
            if ($section['type'] === 'h2') {
                $html .= self::renderH2($section['text'], $h2Count > 0);
                $h2Count++;
                continue;
            }

            $html .= match ($section['type']) {
                'lead'    => self::renderLead($section),
                'h3'      => self::renderH3($section['text']),
                'p'       => '<p>'.e($section['text']).'</p>',
                'ul'      => self::renderList('ul', $section['items'] ?? []),
                'ol'      => self::renderList('ol', $section['items'] ?? []),
                'quote'   => self::renderQuote($section),
                'tip'     => self::renderCallout($section, 'tip'),
                'warning' => self::renderCallout($section, 'warning'),
                'fact'    => self::renderCallout($section, 'fact'),
                'divider' => '<hr />',
                'image'   => self::renderImage($section),
                default   => '',
            };
        }

        return $html;
    }

    private static function renderH2(string $text, bool $hasPrev = false): string
    {
        $topClass = $hasPrev ? 'mt-10' : 'mt-2';
        return '<div class="not-prose '.$topClass.' mb-3">'
            . '<h2 class="m-0 text-[1.3rem] sm:text-[1.45rem] font-extrabold text-on-surface leading-snug tracking-tight">'
            . e($text)
            . '</h2></div>';
    }

    private static function renderH3(string $text): string
    {
        return '<div class="not-prose mt-7 mb-2">'
            . '<h3 class="m-0 text-base sm:text-lg font-bold text-on-surface">'
            . e($text)
            . '</h3></div>';
    }

    private static function renderLead(array $section): string
    {
        return '<div class="not-prose my-6 pl-5 py-1 border-l-[3px] border-primary">'
            . '<p class="text-lg leading-[1.8] text-on-surface-muted italic m-0">'
            . e($section['text'])
            . '</p></div>';
    }

    private static function renderQuote(array $section): string
    {
        $author = isset($section['author'])
            ? '<footer class="mt-2 text-sm text-on-surface-muted not-italic">— '.e($section['author']).'</footer>'
            : '';
        return '<blockquote><p>'.e($section['text']).'</p>'.$author.'</blockquote>';
    }

    private static function renderCallout(array $section, string $type): string
    {
        [$bg, $border, $labelColor, $icon, $label] = match ($type) {
            'tip'     => ['bg-primary/5',   'border-primary/30',   'text-primary',      '💡', $section['title'] ?? 'Совет'],
            'warning' => ['bg-danger/5',    'border-danger/30',    'text-danger',       '⚠️', $section['title'] ?? 'Важно'],
            'fact'    => ['bg-surface-muted', 'border-outline/40', 'text-on-surface',   '📌', $section['title'] ?? 'Факт'],
            default   => ['bg-primary/5',   'border-primary/30',   'text-primary',      '💡', 'Совет'],
        };

        return '<div class="not-prose my-6 rounded-xl '.$bg.' border '.$border.' p-5 space-y-1">'
            . '<p class="font-semibold '.$labelColor.' flex items-center gap-2 m-0 text-sm uppercase tracking-wide">'
            . $icon.' '.e($label).'</p>'
            . '<p class="text-on-surface leading-relaxed m-0">'.e($section['text']).'</p>'
            . '</div>';
    }

    /** @param array<int, string> $items */
    private static function renderList(string $tag, array $items): string
    {
        if ($items === []) return '';

        $markerClass = $tag === 'ul'
            ? 'list-disc marker:text-primary'
            : 'list-decimal marker:text-primary';

        $html = '<div class="not-prose my-5">'
            . "<{$tag} class=\"{$markerClass} pl-6 space-y-2\">";

        foreach ($items as $item) {
            $html .= '<li class="text-on-surface text-base leading-relaxed pl-1">'
                . e($item)
                . '</li>';
        }

        return $html . "</{$tag}></div>";
    }

    /** @param array<string, mixed> $section */
    private static function renderImage(array $section): string
    {
        $src = $section['src'] ?? self::image($section['key'] ?? 'car');
        $alt = e($section['alt'] ?? '');
        $caption = $section['caption'] ?? null;

        $html = '<figure class="my-8">'
            . "<img src=\"{$src}\" alt=\"{$alt}\" loading=\"lazy\" />";
        if ($caption) {
            $html .= '<figcaption class="text-center text-sm text-on-surface-muted mt-2">'.e($caption).'</figcaption>';
        }
        return $html.'</figure>';
    }
}
