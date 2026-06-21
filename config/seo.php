<?php

return [

    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'Город машин')),

    'tagline' => env('SEO_TAGLINE', 'Единый портал городского авторынка'),

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Автомобили, запчасти и автосервис от проверенных резидентов городского авторынка. Покупка, продажа, Trade-in и онлайн-запись.'
    ),

    'default_image' => env('SEO_DEFAULT_IMAGE'),

    'twitter_handle' => env('SEO_TWITTER_HANDLE'),

    'google_site_verification' => env('SEO_GOOGLE_SITE_VERIFICATION'),

    'yandex_verification' => env('SEO_YANDEX_VERIFICATION'),

    'yandex_metrika_id' => env('YANDEX_METRIKA_ID'),

    'title_separator' => ' — ',

    'noindex_route_prefixes' => [
        'admin.',
        'cabinet.',
        'buyer.',
        'login',
        'register',
        'company.register',
        'cart.',
        'logout',
    ],

    'noindex_paths' => [
        'login',
        'register/company',
        'register',
        'cart',
        'buyer',
        'cabinet',
        'admin',
    ],

    'static_pages' => [
        '/' => ['priority' => 1.0, 'frequency' => 'daily'],
        '/catalog/cars' => ['priority' => 0.9, 'frequency' => 'hourly'],
        '/catalog/parts' => ['priority' => 0.9, 'frequency' => 'hourly'],
        '/catalog/services' => ['priority' => 0.9, 'frequency' => 'daily'],
        '/companies' => ['priority' => 0.8, 'frequency' => 'daily'],
        '/market-map' => ['priority' => 0.7, 'frequency' => 'weekly'],
        '/sell-car' => ['priority' => 0.8, 'frequency' => 'weekly'],
        '/blog' => ['priority' => 0.8, 'frequency' => 'daily'],
    ],

];
