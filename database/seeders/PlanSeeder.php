<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Billing\Domain\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price_monthly' => 499000,
                'price_yearly' => 4990000,
                'features' => ['1 бизнес-профиль', 'До 10 объявлений', 'Базовая аналитика'],
                'allowed_profiles' => ['dealer'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_monthly' => 999000,
                'price_yearly' => 9990000,
                'features' => ['3 бизнес-профиля', 'Безлимит объявлений', 'Автоодобрение', 'Приоритет в поиске'],
                'allowed_profiles' => ['dealer', 'parts', 'service'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price_monthly' => 1999000,
                'price_yearly' => 19990000,
                'features' => ['Все профили', 'API доступ', 'Персональный менеджер', 'Кастомная интеграция'],
                'allowed_profiles' => ['dealer', 'parts', 'service'],
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
