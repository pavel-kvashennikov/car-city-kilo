# AutoMarket Portal

Единый B2B/B2C портал городского авторынка (Laravel 13 + Vue 3 + Inertia).

## Быстрый старт через Docker

Требования: [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Compose v2).

```powershell
# из корня проекта
docker compose up -d --build
```

Первый запуск занимает несколько минут: сборка образа PHP, `composer install`, миграции и сиды.

### Адреса сервисов

| Сервис | URL |
|--------|-----|
| Приложение | http://localhost:8080 |
| Vite HMR | http://localhost:5173 |
| PostgreSQL | localhost:5432 |
| Meilisearch | http://localhost:7700 |

### Тестовые аккаунты

| Email | Пароль | Роль |
|-------|--------|------|
| admin@automarket.ru | password | SuperAdmin |
| buyer@automarket.ru | password | Покупатель |

### Полезные команды

```powershell
# Логи всех сервисов
docker compose logs -f

# Логи только приложения
docker compose logs -f app

# Artisan внутри контейнера
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed

# Остановить
docker compose down

# Полный сброс (БД + Meilisearch)
docker compose down -v
```

### Пересоздать начальные данные

```powershell
docker compose exec app rm -f storage/.docker-initialized
docker compose restart app
```

### Конфигурация окружения

- `.env.docker` — шаблон для Docker (копируется в `.env` при первом запуске, если `.env` отсутствует)
- Переменные в `docker-compose.yml` переопределяют `.env` внутри контейнеров

### Сервисы Compose

- **app** — PHP 8.3-FPM, Laravel
- **nginx** — веб-сервер (порт 8080)
- **vite** — фронтенд dev-сервер с hot reload
- **queue** — обработчик очередей Laravel
- **postgres** — PostgreSQL 16
- **meilisearch** — полнотекстовый поиск

## Локальная разработка без Docker

PHP 8.3+, PostgreSQL, Node.js 22+, Composer.

```powershell
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

Или одной командой: `composer dev`
