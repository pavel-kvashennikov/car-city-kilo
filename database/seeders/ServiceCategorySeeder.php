<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Техническое обслуживание', 'slug' => 'to'],
            ['name' => 'Двигатель',                'slug' => 'engine'],
            ['name' => 'Трансмиссия',              'slug' => 'transmission'],
            ['name' => 'Ходовая часть',            'slug' => 'suspension'],
            ['name' => 'Тормозная система',        'slug' => 'brakes'],
            ['name' => 'Рулевое управление',       'slug' => 'steering'],
            ['name' => 'Электрика и электроника',  'slug' => 'electrics'],
            ['name' => 'Кузовной ремонт',          'slug' => 'body'],
            ['name' => 'Покраска',                 'slug' => 'paint'],
            ['name' => 'Шиномонтаж и балансировка','slug' => 'tyres'],
            ['name' => 'Кондиционер',              'slug' => 'ac'],
            ['name' => 'Диагностика',              'slug' => 'diagnostics'],
            ['name' => 'Детейлинг',                'slug' => 'detailing'],
        ];

        foreach ($categories as $i => $cat) {
            DB::table('service_categories')->insertOrIgnore([
                'name'       => $cat['name'],
                'slug'       => $cat['slug'],
                'sort_order' => $i,
                'is_active'  => true,
            ]);
        }
    }
}
