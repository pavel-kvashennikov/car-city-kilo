<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Src\Catalog\Domain\Models\CarBrand;
use Src\Catalog\Domain\Models\PartCategory;
use Src\Company\Domain\Models\BusinessProfile;
use Src\Company\Domain\Models\Company;
use Src\Dealer\Domain\Models\DealerLead;
use Src\Dealer\Domain\Models\DealerProfile;
use Src\Dealer\Domain\Models\Vehicle;
use Src\Dealer\Domain\Models\VehiclePhoto;
use Src\MarketMap\Domain\Models\MarketLocation;
use Src\MarketMap\Domain\Models\MarketZone;
use Src\Order\Domain\Models\Order;
use Src\Order\Domain\Models\OrderItem;
use Src\Parts\Domain\Models\CrossNumber;
use Src\Parts\Domain\Models\PartsProfile;
use Src\Parts\Domain\Models\Product;
use Src\Parts\Domain\Models\ProductApplicability;
use Src\Service\Domain\Models\Appointment;
use Src\Service\Domain\Models\ServiceItem;
use Src\Service\Domain\Models\ServiceMaster;
use Src\Service\Domain\Models\ServiceProfile;
use Src\Service\Domain\Models\TimeSlot;

class DemoSeeder extends Seeder
{
    /** @var array<string,int> */
    private array $brandIdByName = [];

    /** @var array<int,array<int,int>> brandId => [modelIds] */
    private array $modelsByBrand = [];

    /** @var array<int,int> */
    private array $partCategoryIds = [];

    /** @var array<string, array<int, string>>|null */
    private ?array $demoImages = null;

    private function demoImages(string $folder): array
    {
        if ($this->demoImages === null) {
            $this->demoImages = [];
            foreach (['cars', 'interior', 'parts'] as $dir) {
                $path = public_path('images/demo/'.$dir);
                $files = [];
                if (is_dir($path)) {
                    foreach (scandir($path) as $file) {
                        if ($file === '.' || $file === '..') {
                            continue;
                        }
                        if (preg_match('/\.(jpe?g|png|webp|gif)$/i', $file)) {
                            $files[] = '/images/demo/'.$dir.'/'.$file;
                        }
                    }
                }
                sort($files);
                $this->demoImages[$dir] = $files;
            }
        }

        return $this->demoImages[$folder] ?? [];
    }

    public function run(): void
    {
        $this->bootCatalogCache();

        $zones = $this->seedZones();

        setPermissionsTeamId(0);
        $tenantOwnerRole = \Spatie\Permission\Models\Role::where('name', 'tenant_owner')
            ->where('company_id', 0)
            ->first();

        $companies = [
            [
                'name' => 'АвтоПрестиж', 'inn' => '7701234567', 'legal' => 'ООО «АвтоПрестиж»',
                'desc' => 'Официальный дилер премиальных автомобилей. Новые и проверенные авто с пробегом, trade-in и кредитные программы от банков-партнёров.',
                'phone' => '+7 (495) 120-45-67', 'profiles' => ['dealer'],
                'specials' => ['Премиум', 'Trade-in', 'Кредит'],
            ],
            [
                'name' => 'Восток Моторс', 'inn' => '7702345678', 'legal' => 'ООО «Восток Моторс»',
                'desc' => 'Большой выбор автомобилей массовых брендов и собственный склад запчастей. Поможем подобрать авто и обслужить его.',
                'phone' => '+7 (495) 230-11-22', 'profiles' => ['dealer', 'parts'],
                'specials' => ['Авто с пробегом', 'Запчасти в наличии'],
            ],
            [
                'name' => 'ДеталиПро', 'inn' => '7703456789', 'legal' => 'ООО «ДеталиПро»',
                'desc' => 'Магазин автозапчастей: оригинал и качественные аналоги. Точный подбор по VIN и OEM-номеру, доставка по городу.',
                'phone' => '+7 (495) 340-55-88', 'profiles' => ['parts'],
                'specials' => ['Подбор по VIN', 'Оригинал и аналоги'],
            ],
            [
                'name' => 'ЗапчастьСити', 'inn' => '7704567890', 'legal' => 'ООО «ЗапчастьСити»',
                'desc' => 'Запчасти и установка в одном месте. Купите деталь и сразу запишитесь на установку к нашим мастерам.',
                'phone' => '+7 (495) 450-77-10', 'profiles' => ['parts', 'service'],
                'specials' => ['Запчасти + установка', 'Гарантия'],
            ],
            [
                'name' => 'ТехСервис Плюс', 'inn' => '7705678901', 'legal' => 'ООО «ТехСервис Плюс»',
                'desc' => 'Многопрофильный автосервис: ТО, ремонт двигателя, ходовой и электрики. Онлайн-запись и прозрачные цены.',
                'phone' => '+7 (495) 560-33-44', 'profiles' => ['service'],
                'specials' => ['ТО', 'Ремонт двигателя', 'Диагностика'],
            ],
            [
                'name' => 'Гранд Авто', 'inn' => '7706789012', 'legal' => 'ООО «Гранд Авто»',
                'desc' => 'Автосалон с широким модельным рядом. Честная диагностика авто с пробегом и помощь в оформлении.',
                'phone' => '+7 (495) 670-90-01', 'profiles' => ['dealer'],
                'specials' => ['Кредит 0%', 'Гарантия юр. чистоты'],
            ],
            [
                'name' => 'МастерКласс Авто', 'inn' => '7707890123', 'legal' => 'ООО «МастерКласс Авто»',
                'desc' => 'Кузовной ремонт, покраска и слесарные работы. Опытные мастера и современное оборудование.',
                'phone' => '+7 (495) 780-12-34', 'profiles' => ['service'],
                'specials' => ['Кузовной ремонт', 'Покраска', 'Полировка'],
            ],
            [
                'name' => 'АвтоМир', 'inn' => '7708901234', 'legal' => 'ООО «АвтоМир»',
                'desc' => 'Полный цикл: продажа авто, запчасти и собственный сервис. Всё для вашего автомобиля под одной крышей.',
                'phone' => '+7 (495) 890-23-45', 'profiles' => ['dealer', 'parts', 'service'],
                'specials' => ['Авто', 'Запчасти', 'Сервис'],
            ],
        ];

        $freeZoneCursor = 0;
        foreach ($companies as $index => $data) {
            $owner = User::firstOrCreate(
                ['email' => 'owner'.($index + 1).'@automarket.ru'],
                ['name' => 'Владелец «'.$data['name'].'»', 'password' => bcrypt('password'), 'phone' => $data['phone']]
            );

            $company = Company::firstOrCreate(
                ['inn' => $data['inn']],
                [
                    'owner_id' => $owner->id,
                    'name' => $data['name'],
                    'legal_name' => $data['legal'],
                    'description' => $data['desc'],
                    'phone' => $data['phone'],
                    'email' => 'info@'.str($data['name'])->ascii()->slug().'.ru',
                    'website' => 'https://'.str($data['name'])->ascii()->slug().'.ru',
                    'working_hours' => $this->workingHours(),
                    'social_links' => ['telegram' => 'https://t.me/automarket', 'vk' => 'https://vk.com/automarket'],
                    'status' => 'active',
                    'verified_at' => now()->subDays(rand(10, 200)),
                ]
            );

            DB::table('company_user')->updateOrInsert(
                ['company_id' => $company->id, 'user_id' => $owner->id],
                ['position' => 'Владелец', 'created_at' => now(), 'updated_at' => now()]
            );

            setPermissionsTeamId($company->id);
            if ($tenantOwnerRole && ! $owner->hasRole($tenantOwnerRole)) {
                $owner->assignRole($tenantOwnerRole);
            }

            foreach ($data['profiles'] as $type) {
                BusinessProfile::firstOrCreate(
                    ['company_id' => $company->id, 'type' => $type],
                    ['is_active' => true]
                );
            }

            if (in_array('dealer', $data['profiles'], true)) {
                $this->seedDealer($company, $data['specials']);
            }
            if (in_array('parts', $data['profiles'], true)) {
                $this->seedParts($company);
            }
            if (in_array('service', $data['profiles'], true)) {
                $this->seedService($company, $data['specials']);
            }

            // Market location (occupied)
            $zone = $zones[$index % count($zones)];
            MarketLocation::firstOrCreate(
                ['zone_id' => $zone->id, 'code' => $zone->code.'-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)],
                [
                    'company_id' => $company->id,
                    'type' => $index % 3 === 0 ? 'pavilion' : 'box',
                    'status' => 'occupied',
                    'coordinates' => $this->slotCoordinates($index % count($zones), intdiv($index, count($zones))),
                    'description' => $data['name'],
                ]
            );
            $freeZoneCursor = $index;
        }

        // A few free locations to make the map look alive
        for ($i = 0; $i < 10; $i++) {
            $zone = $zones[$i % count($zones)];
            $row = 2 + intdiv($i, count($zones));
            MarketLocation::firstOrCreate(
                ['zone_id' => $zone->id, 'code' => $zone->code.'-F'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)],
                [
                    'type' => 'box',
                    'status' => $i % 4 === 0 ? 'reserved' : 'available',
                    'coordinates' => $this->slotCoordinates($i % count($zones), $row),
                    'description' => 'Свободное место',
                ]
            );
        }

        $this->seedBuyerActivity();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function bootCatalogCache(): void
    {
        foreach (CarBrand::with('models')->get() as $brand) {
            $this->brandIdByName[$brand->name] = $brand->id;
            $this->modelsByBrand[$brand->id] = $brand->models->pluck('id')->all();
        }
        $this->partCategoryIds = PartCategory::whereNotNull('parent_id')->pluck('id')->all();
    }

    private function seedZones(): array
    {
        $defs = [
            ['name' => 'Павильон А — Автосалоны', 'code' => 'A', 'color' => '#2563eb'],
            ['name' => 'Павильон Б — Запчасти', 'code' => 'B', 'color' => '#0d9488'],
            ['name' => 'Сервисная зона', 'code' => 'C', 'color' => '#d97706'],
            ['name' => 'Открытая площадка', 'code' => 'D', 'color' => '#7c3aed'],
        ];
        $zones = [];
        foreach ($defs as $i => $d) {
            $zones[] = MarketZone::firstOrCreate(
                ['code' => $d['code']],
                [
                    'name' => $d['name'],
                    'description' => 'Зона размещения резидентов рынка',
                    'svg_path' => ['color' => $d['color']],
                    'sort_order' => $i,
                ]
            );
        }

        return $zones;
    }

    private function slotCoordinates(int $col, int $row): array
    {
        $cellW = 150;
        $cellH = 90;
        $gapX = 30;
        $gapY = 28;
        $marginX = 40;
        $marginY = 40;

        return [
            'x' => $marginX + $col * ($cellW + $gapX),
            'y' => $marginY + $row * ($cellH + $gapY),
            'w' => $cellW,
            'h' => $cellH,
        ];
    }

    private function workingHours(): array
    {
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $hours = [];
        foreach ($days as $d) {
            $hours[$d] = $d === 'sun'
                ? ['open' => null, 'close' => null, 'closed' => true]
                : ['open' => '09:00', 'close' => $d === 'sat' ? '17:00' : '20:00', 'closed' => false];
        }

        return $hours;
    }

    private function seedDealer(Company $company, array $specials): void
    {
        $profile = DealerProfile::firstOrCreate(
            ['company_id' => $company->id],
            [
                'description' => $company->description,
                'working_hours' => $this->workingHours(),
                'specializations' => $specials,
                'offers_credit' => true,
                'offers_trade_in' => true,
                'offers_test_drive' => true,
            ]
        );

        if ($profile->vehicles()->exists()) {
            return;
        }

        $carImages = $this->demoImages('cars');
        $interiorImages = $this->demoImages('interior');

        if (empty($carImages)) {
            return;
        }

        $colors = ['Чёрный', 'Белый', 'Серебристый', 'Серый', 'Синий', 'Красный'];
        $bodies = ['Седан', 'Внедорожник', 'Хэтчбек', 'Кроссовер', 'Универсал'];
        $engines = ['petrol', 'diesel', 'hybrid', 'petrol', 'petrol'];
        $transmissions = ['automatic', 'manual', 'robot', 'cvt'];
        $drives = ['fwd', 'rwd', 'awd'];
        $featurePool = ['Климат-контроль', 'Кожаный салон', 'Камера 360', 'Подогрев сидений', 'Адаптивный круиз', 'Панорамная крыша', 'Парктроник', 'Apple CarPlay', 'Запуск с кнопки', 'Светодиодные фары'];

        $brandIds = array_values($this->brandIdByName);
        $count = rand(6, 9);
        for ($i = 0; $i < $count; $i++) {
            $brandId = $brandIds[array_rand($brandIds)];
            $models = $this->modelsByBrand[$brandId] ?? [];
            if (empty($models)) {
                continue;
            }
            $modelId = $models[array_rand($models)];
            $year = rand(2015, 2024);
            $isNew = $year >= 2024;
            $price = rand(90, 650) * 10000;

            $vehicle = Vehicle::create([
                'dealer_profile_id' => $profile->id,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'year' => $year,
                'vin' => strtoupper(str()->random(17)),
                'mileage' => $isNew ? rand(0, 50) : rand(15000, 180000),
                'color' => $colors[array_rand($colors)],
                'engine_type' => $engines[array_rand($engines)],
                'engine_volume' => [1.6, 2.0, 2.5, 3.0][array_rand([1.6, 2.0, 2.5, 3.0])],
                'engine_power' => rand(110, 340),
                'transmission' => $transmissions[array_rand($transmissions)],
                'drive_type' => $drives[array_rand($drives)],
                'body_type' => $bodies[array_rand($bodies)],
                'condition' => $isNew ? 'new' : 'used',
                'legal_status' => 'clean',
                'price' => $price,
                'price_trade_in' => (int) ($price * 0.92),
                'credit_monthly' => (int) round($price / 60, -2),
                'description' => 'Автомобиль в отличном состоянии, обслуживался у официального дилера. Полный пакет документов, юридическая чистота гарантирована. Возможен trade-in и кредит.',
                'features' => collect($featurePool)->shuffle()->take(rand(4, 7))->values()->all(),
                'attributes' => ['owners' => rand(1, 3), 'pts' => 'Оригинал'],
                'status' => 'active',
                'is_featured' => $i < 2,
            ]);

            $photoCount = rand(4, 6);
            for ($p = 0; $p < $photoCount; $p++) {
                $useInterior = $p >= 3 && ! empty($interiorImages);
                $src = $useInterior
                    ? $interiorImages[($i + $p) % count($interiorImages)]
                    : $carImages[($i * 2 + $p) % count($carImages)];
                VehiclePhoto::create([
                    'vehicle_id' => $vehicle->id,
                    'path' => $src,
                    'thumb_path' => $src,
                    'sort_order' => $p,
                    'is_main' => $p === 0,
                ]);
            }

            if ($i < 3) {
                $this->seedLead($profile->id, $vehicle->id);
            }
        }
    }

    private function seedLead(int $dealerProfileId, int $vehicleId): void
    {
        $types = ['test_drive', 'credit', 'trade_in', 'callback'];
        $type = $types[array_rand($types)];
        $names = ['Иван Петров', 'Алексей Смирнов', 'Мария Кузнецова', 'Дмитрий Соколов', 'Елена Попова'];
        DealerLead::create([
            'dealer_profile_id' => $dealerProfileId,
            'vehicle_id' => $vehicleId,
            'lead_type' => $type,
            'client_name' => $names[array_rand($names)],
            'client_phone' => '+7 (9'.rand(10, 99).') '.rand(100, 999).'-'.rand(10, 99).'-'.rand(10, 99),
            'client_email' => 'client'.rand(1, 999).'@mail.ru',
            'preferred_datetime' => now()->addDays(rand(1, 7))->setTime(rand(10, 18), 0),
            'trade_in_data' => $type === 'trade_in' ? ['brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2014, 'mileage' => 120000] : null,
            'credit_data' => $type === 'credit' ? ['income' => rand(60, 200) * 1000, 'term' => 60] : null,
            'message' => 'Здравствуйте, интересует этот автомобиль. Перезвоните, пожалуйста.',
            'status' => ['new', 'in_progress', 'done'][array_rand(['new', 'in_progress', 'done'])],
        ]);
    }

    private function seedParts(Company $company): void
    {
        $profile = PartsProfile::firstOrCreate(
            ['company_id' => $company->id],
            [
                'description' => $company->description,
                'working_hours' => $this->workingHours(),
                'min_order_amount' => 1000,
                'delivery_info' => ['courier' => true, 'pickup' => true, 'days' => '1-2'],
                'payment_methods' => ['Наличные', 'Карта', 'Безнал'],
            ]
        );

        if ($profile->products()->exists()) {
            return;
        }

        $partsImages = $this->demoImages('parts');

        if (empty($partsImages)) {
            return;
        }

        $catalog = [
            ['Тормозные колодки передние', 'BOSCH', 'brakes'],
            ['Тормозные диски вентилируемые', 'TRW', 'brakes'],
            ['Масляный фильтр', 'MANN', 'engine'],
            ['Воздушный фильтр', 'MAHLE', 'engine'],
            ['Свеча зажигания (комплект)', 'NGK', 'engine'],
            ['Ремень ГРМ', 'GATES', 'engine'],
            ['Амортизатор передний', 'KYB', 'suspension'],
            ['Стойка стабилизатора', 'LEMFORDER', 'suspension'],
            ['Рычаг передней подвески', 'FEBI', 'suspension'],
            ['Аккумулятор 60 Ач', 'VARTA', 'electric'],
            ['Генератор', 'VALEO', 'electric'],
            ['Радиатор охлаждения', 'NISSENS', 'cooling'],
            ['Помпа водяная', 'SKF', 'cooling'],
            ['Комплект сцепления', 'LUK', 'transmission'],
            ['ШРУС наружный', 'GKN', 'transmission'],
            ['Фара передняя левая', 'DEPO', 'body'],
        ];

        $brandIds = array_values($this->brandIdByName);
        $count = rand(12, 16);
        for ($i = 0; $i < $count; $i++) {
            $item = $catalog[$i % count($catalog)];
            $retail = rand(8, 350) * 100; // рубли
            $img = $partsImages[$i % count($partsImages)];

            $product = Product::create([
                'parts_profile_id' => $profile->id,
                'category_id' => $this->partCategoryIds[array_rand($this->partCategoryIds)] ?? null,
                'name' => $item[0],
                'article_number' => strtoupper($item[1][0]).rand(1000, 9999).'-'.rand(10, 99),
                'oem_number' => strtoupper(str()->random(2)).rand(100000, 999999),
                'barcode' => (string) rand(4600000000000, 4699999999999),
                'brand' => $item[1],
                'condition' => 'new',
                'part_type' => $i % 3 === 0 ? 'original' : 'aftermarket',
                'price_retail' => $retail,
                'price_wholesale' => (int) ($retail * 0.85),
                'wholesale_min_qty' => 5,
                'description' => 'Качественная деталь от проверенного производителя. Гарантия соответствия оригинальным характеристикам. Подбор по VIN — бесплатно.',
                'attributes' => [
                    'images' => [$img, $partsImages[($i + 2) % count($partsImages)]],
                    'warranty' => '12 мес.',
                    'country' => ['Германия', 'Япония', 'Корея'][array_rand(['Германия', 'Япония', 'Корея'])],
                ],
                'status' => 'active',
                'stock_quantity' => rand(0, 40),
            ]);

            // Cross numbers
            for ($c = 0; $c < rand(1, 3); $c++) {
                CrossNumber::create([
                    'product_id' => $product->id,
                    'brand' => ['FEBI', 'SWAG', 'TRW', 'BOSCH'][array_rand(['FEBI', 'SWAG', 'TRW', 'BOSCH'])],
                    'number' => strtoupper(str()->random(3)).rand(1000, 99999),
                    'is_oem' => $c === 0,
                ]);
            }

            // Applicability
            for ($a = 0; $a < rand(1, 3); $a++) {
                $brandId = $brandIds[array_rand($brandIds)];
                $models = $this->modelsByBrand[$brandId] ?? [];
                ProductApplicability::create([
                    'product_id' => $product->id,
                    'brand_id' => $brandId,
                    'model_id' => ! empty($models) ? $models[array_rand($models)] : null,
                    'year_from' => rand(2008, 2015),
                    'year_to' => rand(2016, 2024),
                ]);
            }
        }
    }

    private function seedService(Company $company, array $specials): void
    {
        $profile = ServiceProfile::firstOrCreate(
            ['company_id' => $company->id],
            [
                'slug' => str($company->name)->ascii()->slug().'-service',
                'description' => $company->description,
                'working_hours' => $this->workingHours(),
                'specializations' => $specials,
                'vehicle_types' => ['passenger', 'suv'],
                'slot_duration_minutes' => 60,
            ]
        );

        if ($profile->serviceItems()->exists()) {
            return;
        }

        $items = [
            ['Замена масла и фильтра', 'Замена моторного масла и масляного фильтра с проверкой уровней.', 1500, 60],
            ['Диагностика двигателя', 'Компьютерная диагностика, считывание ошибок, рекомендации.', 2000, 45],
            ['Замена тормозных колодок', 'Замена передних или задних колодок с проверкой дисков.', 2500, 90],
            ['Развал-схождение 3D', 'Регулировка углов установки колёс на 3D-стенде.', 3500, 90],
            ['Замена ремня ГРМ', 'Замена ремня ГРМ с роликами и помпой.', 8000, 240],
            ['Шиномонтаж (4 колеса)', 'Снятие, монтаж, балансировка четырёх колёс.', 2200, 60],
            ['ТО полное', 'Плановое техническое обслуживание по регламенту.', 6500, 180],
            ['Заправка кондиционера', 'Диагностика и заправка системы кондиционирования.', 2800, 60],
        ];

        $createdItems = [];
        foreach ($items as $idx => $it) {
            $createdItems[] = ServiceItem::create([
                'service_profile_id' => $profile->id,
                'name' => $it[0],
                'description' => $it[1],
                'price_from' => $it[2],
                'price_to' => (int) ($it[2] * 1.6),
                'price_per_hour' => 1200,
                'duration_minutes' => $it[3],
                'vehicle_types' => ['passenger', 'suv'],
                'is_active' => true,
                'sort_order' => $idx,
            ]);
        }

        $masters = [];
        $masterDefs = [
            ['Сергей Иванов', 'Моторист', 'men/32'],
            ['Андрей Волков', 'Ходовая часть', 'men/45'],
            ['Михаил Орлов', 'Диагност-электрик', 'men/12'],
            ['Павел Кузьмин', 'Шиномонтаж', 'men/67'],
        ];
        foreach (array_slice($masterDefs, 0, rand(3, 4)) as $m) {
            $masters[] = ServiceMaster::create([
                'service_profile_id' => $profile->id,
                'name' => $m[0],
                'specialization' => $m[1],
                'photo_path' => "https://randomuser.me/api/portraits/{$m[2]}.jpg",
                'schedule' => ['mon' => '09:00-20:00', 'tue' => '09:00-20:00', 'wed' => '09:00-20:00', 'thu' => '09:00-20:00', 'fri' => '09:00-20:00'],
                'is_active' => true,
            ]);
        }

        // Slots for next 14 days
        $slots = [];
        for ($d = 0; $d < 14; $d++) {
            $date = now()->addDays($d);
            if ($date->isSunday()) {
                continue;
            }
            for ($h = 9; $h <= 18; $h++) {
                $master = $masters[array_rand($masters)];
                $status = 'available';
                $slots[] = TimeSlot::create([
                    'service_profile_id' => $profile->id,
                    'master_id' => $master->id,
                    'date' => $date->toDateString(),
                    'start_time' => sprintf('%02d:00', $h),
                    'end_time' => sprintf('%02d:00', $h + 1),
                    'status' => $status,
                ]);
            }
        }

        // A few booked appointments
        $names = ['Олег Маркин', 'Наталья Седова', 'Игорь Белов', 'Юлия Зайцева'];
        for ($i = 0; $i < rand(2, 4); $i++) {
            $slot = $slots[array_rand($slots)];
            $item = $createdItems[array_rand($createdItems)];
            $appt = Appointment::create([
                'service_profile_id' => $profile->id,
                'service_item_id' => $item->id,
                'master_id' => $slot->master_id,
                'time_slot_id' => $slot->id,
                'client_name' => $names[array_rand($names)],
                'client_phone' => '+7 (9'.rand(10, 99).') '.rand(100, 999).'-'.rand(10, 99).'-'.rand(10, 99),
                'vehicle_brand' => 'Toyota',
                'vehicle_model' => 'Camry',
                'vehicle_year' => rand(2015, 2022),
                'comment' => 'Прошу подтвердить запись.',
                'status' => ['pending', 'confirmed'][array_rand(['pending', 'confirmed'])],
                'estimated_cost' => $item->price_from,
            ]);
            $slot->update(['status' => 'booked', 'appointment_id' => $appt->id]);
        }
    }

    private function seedBuyerActivity(): void
    {
        setPermissionsTeamId(0);
        $buyer = User::where('email', 'buyer@automarket.ru')->first();
        if (! $buyer) {
            return;
        }

        // Favorites
        Vehicle::query()->inRandomOrder()->take(4)->get()->each(function ($v) use ($buyer) {
            Favorite::firstOrCreate([
                'user_id' => $buyer->id,
                'favoriteable_type' => Vehicle::class,
                'favoriteable_id' => $v->id,
            ]);
        });
        Product::query()->inRandomOrder()->take(3)->get()->each(function ($p) use ($buyer) {
            Favorite::firstOrCreate([
                'user_id' => $buyer->id,
                'favoriteable_type' => Product::class,
                'favoriteable_id' => $p->id,
            ]);
        });

        // Orders
        if (Order::where('user_id', $buyer->id)->exists()) {
            return;
        }
        $products = Product::query()->inRandomOrder()->take(6)->get();
        if ($products->isEmpty()) {
            return;
        }
        foreach (['completed', 'processing', 'pending'] as $k => $status) {
            $picks = $products->slice($k * 2, 2);
            if ($picks->isEmpty()) {
                continue;
            }
            $total = 0;
            $order = Order::create([
                'order_number' => 'AM-'.now()->format('Ymd').'-'.str_pad((string) ($k + 1), 4, '0', STR_PAD_LEFT),
                'user_id' => $buyer->id,
                'client_name' => $buyer->name,
                'client_phone' => '+7 (999) 123-45-67',
                'client_email' => $buyer->email,
                'status' => $status,
                'total_amount' => 0,
                'delivery_method' => $k % 2 === 0 ? 'pickup' : 'courier',
                'delivery_address' => $k % 2 === 0 ? null : ['city' => 'Москва', 'street' => 'ул. Автозаводская, 12'],
                'comment' => null,
            ]);
            foreach ($picks as $product) {
                $qty = rand(1, 3);
                $line = $product->price_retail * $qty;
                $total += $line;
                OrderItem::create([
                    'order_id' => $order->id,
                    'itemable_type' => Product::class,
                    'itemable_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $qty,
                    'price' => $product->price_retail,
                    'total' => $line,
                    'meta' => ['brand' => $product->brand],
                ]);
            }
            $order->update(['total_amount' => $total, 'created_at' => now()->subDays(rand(1, 40))]);
        }
    }
}
