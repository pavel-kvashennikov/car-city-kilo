<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\CarModel;
use Src\Catalog\Domain\Models\PartCategory;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Toyota', 'slug' => 'toyota', 'is_popular' => true, 'models' => ['Camry', 'Corolla', 'RAV4', 'Land Cruiser', 'Prado']],
            ['name' => 'Hyundai', 'slug' => 'hyundai', 'is_popular' => true, 'models' => ['Solaris', 'Creta', 'Tucson', 'Santa Fe', 'Sonata']],
            ['name' => 'Kia', 'slug' => 'kia', 'is_popular' => true, 'models' => ['Rio', 'Sportage', 'Ceed', 'Seltos', 'K5']],
            ['name' => 'Volkswagen', 'slug' => 'volkswagen', 'is_popular' => true, 'models' => ['Polo', 'Tiguan', 'Passat', 'Golf', 'Touareg']],
            ['name' => 'BMW', 'slug' => 'bmw', 'is_popular' => true, 'models' => ['3 Series', '5 Series', 'X3', 'X5', 'X7']],
            ['name' => 'Mercedes-Benz', 'slug' => 'mercedes-benz', 'is_popular' => true, 'models' => ['C-Class', 'E-Class', 'GLC', 'GLE', 'S-Class']],
            ['name' => 'Audi', 'slug' => 'audi', 'is_popular' => true, 'models' => ['A4', 'A6', 'Q3', 'Q5', 'Q7']],
            ['name' => 'Nissan', 'slug' => 'nissan', 'is_popular' => true, 'models' => ['Qashqai', 'X-Trail', 'Juke', 'Murano', 'Pathfinder']],
            ['name' => 'Mazda', 'slug' => 'mazda', 'is_popular' => false, 'models' => ['3', '6', 'CX-5', 'CX-9', 'CX-30']],
            ['name' => 'Honda', 'slug' => 'honda', 'is_popular' => false, 'models' => ['CR-V', 'Civic', 'Accord', 'HR-V', 'Pilot']],
            ['name' => 'Mitsubishi', 'slug' => 'mitsubishi', 'is_popular' => false, 'models' => ['Outlander', 'Pajero', 'ASX', 'Eclipse Cross', 'L200']],
            ['name' => 'Skoda', 'slug' => 'skoda', 'is_popular' => false, 'models' => ['Octavia', 'Rapid', 'Kodiaq', 'Karoq', 'Superb']],
            ['name' => 'Lada', 'slug' => 'lada', 'is_popular' => true, 'models' => ['Granta', 'Vesta', 'XRAY', 'Niva', 'Largus']],
            ['name' => 'Renault', 'slug' => 'renault', 'is_popular' => false, 'models' => ['Duster', 'Logan', 'Sandero', 'Kaptur', 'Arkana']],
            ['name' => 'Chery', 'slug' => 'chery', 'is_popular' => false, 'models' => ['Tiggo 4', 'Tiggo 7 Pro', 'Tiggo 8 Pro', 'Arrizo 8', 'Omoda C5']],
            ['name' => 'Haval', 'slug' => 'haval', 'is_popular' => false, 'models' => ['Jolion', 'F7', 'H9', 'Dargo', 'M6']],
            ['name' => 'Geely', 'slug' => 'geely', 'is_popular' => false, 'models' => ['Coolray', 'Atlas Pro', 'Tugella', 'Monjaro', 'Emgrand']],
        ];

        foreach ($brands as $brandData) {
            $models = $brandData['models'];
            unset($brandData['models']);
            $brand = CarBrand::firstOrCreate(['slug' => $brandData['slug']], $brandData);

            foreach ($models as $modelName) {
                CarModel::firstOrCreate(
                    ['slug' => $brand->slug.'-'.str($modelName)->slug()],
                    [
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                        'slug' => $brand->slug.'-'.str($modelName)->slug(),
                    ]
                );
            }
        }

        // Part Categories
        $categories = [
            ['name' => 'Двигатель и компоненты', 'slug' => 'engine', 'children' => ['Масла и фильтры', 'Свечи зажигания', 'Ремни и цепи', 'Прокладки', 'Турбины']],
            ['name' => 'Подвеска и рулевое', 'slug' => 'suspension', 'children' => ['Амортизаторы', 'Пружины', 'Рычаги', 'Сайлентблоки', 'Рулевые наконечники']],
            ['name' => 'Тормозная система', 'slug' => 'brakes', 'children' => ['Тормозные колодки', 'Тормозные диски', 'Суппорты', 'Тормозные шланги']],
            ['name' => 'Кузовные детали', 'slug' => 'body', 'children' => ['Бамперы', 'Капоты', 'Двери', 'Крылья', 'Зеркала']],
            ['name' => 'Электрика', 'slug' => 'electric', 'children' => ['Стартеры', 'Генераторы', 'Аккумуляторы', 'Датчики', 'Лампы']],
            ['name' => 'Трансмиссия', 'slug' => 'transmission', 'children' => ['Сцепление', 'КПП', 'ШРУС', 'Карданы']],
            ['name' => 'Система охлаждения', 'slug' => 'cooling', 'children' => ['Радиаторы', 'Помпы', 'Термостаты', 'Патрубки']],
            ['name' => 'Салон', 'slug' => 'interior', 'children' => ['Сиденья', 'Панели', 'Коврики', 'Чехлы']],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'];
            unset($categoryData['children']);
            $parent = PartCategory::firstOrCreate(['slug' => $categoryData['slug']], $categoryData);

            foreach ($children as $index => $childName) {
                PartCategory::firstOrCreate(
                    ['slug' => $parent->slug.'-'.str($childName)->slug()],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'slug' => $parent->slug.'-'.str($childName)->slug(),
                        'sort_order' => $index,
                    ]
                );
            }
        }
    }
}
