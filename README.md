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

## Продакшн (Docker)

Требования: Docker + Compose v2, reverse proxy с SSL (nginx/Caddy), свободный порт `127.0.0.1:8080`.

Схема: `Интернет → reverse proxy (443, SSL) → http://127.0.0.1:8080 → Docker nginx`.

Файлы: `docker-compose.prod.yml`, `.env.prod.example`.

### Первый деплой

```bash
cd /path/to/car-city-kilo

# 1. Создать конфиг
cp .env.prod.example .env.prod
nano .env.prod   # APP_URL, APP_KEY, пароли, почта

# 2. Сгенерировать APP_KEY (обязательно до запуска!)
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm app php artisan key:generate --force --show
# Скопировать вывод в .env.prod:
# APP_KEY=base64:xxxxxxxx...

# 3. Собрать фронтенд
docker compose --env-file .env.prod -f docker-compose.prod.yml --profile build run --rm frontend

# 4. Запустить
docker compose --env-file .env.prod -f docker-compose.prod.yml up -d --build

# 5. Проверить
docker compose --env-file .env.prod -f docker-compose.prod.yml ps
curl -I http://127.0.0.1:8080
```

### Важные переменные `.env.prod`

| Переменная | Описание |
|------------|----------|
| `APP_URL` | `https://ваш-домен.ru` |
| `APP_KEY` | **Обязателен**, формат `base64:...`. Пустой `APP_KEY=` вызывает 500 |
| `DB_PASSWORD` | Сильный пароль PostgreSQL |
| `MEILISEARCH_KEY` | Сильный ключ Meilisearch |
| `RUN_DB_SEED` | `true` — сиды при первом старте, потом `false` |

### Сервисы prod Compose

- **app** — PHP 8.3-FPM, Laravel (production)
- **nginx** — только `127.0.0.1:8080` (для reverse proxy)
- **queue** — `queue:work`
- **scheduler** — `schedule:work`
- **postgres** — без внешнего порта
- **meilisearch** — без внешнего порта
- **frontend** — одноразовая сборка (`--profile build`), dev-сервер Vite не запускается

### Reverse proxy (nginx)

```nginx
location / {
    proxy_pass http://127.0.0.1:8080;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

### Алиас `dc-prod`

`dc-prod` — **не системная команда**, а короткий алиас для длинной строки:

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml
```

Создайте один раз (из корня проекта или с любого места):

```bash
# Временно — только в текущей сессии терминала
alias dc-prod='docker compose --env-file .env.prod -f docker-compose.prod.yml'

# Постоянно — добавить в ~/.bashrc
echo "alias dc-prod='docker compose --env-file .env.prod -f docker-compose.prod.yml'" >> ~/.bashrc
source ~/.bashrc
```

Проверка:

```bash
dc-prod ps
```

Если `command not found` — алиас не создан. Используйте полную команду:

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml ps
```

Ниже в примерах используется `dc-prod` — при необходимости заменяйте на полную форму.

### Полезные команды

```bash
# Статус и логи
dc-prod ps
dc-prod logs -f
dc-prod logs -f app

# Artisan
dc-prod exec app php artisan migrate --force
dc-prod exec app php artisan db:seed --force

# Один конкретный сидер
dc-prod exec app php artisan db:seed --class=DemoSeeder --force
dc-prod exec app php artisan db:seed --class=CatalogSeeder --force

# Полный сброс БД + все сиды (удалит все данные!)
dc-prod exec app php artisan migrate:fresh --seed --force

# Кэширование (после смены .env.prod)
dc-prod exec app php artisan config:clear
dc-prod exec app php artisan config:cache
dc-prod exec app php artisan route:cache
dc-prod exec app php artisan view:cache

# Остановить / перезапустить
dc-prod down
dc-prod restart app queue scheduler

# Полный сброс (БД + Meilisearch)
dc-prod down -v
```

### Сиды

`php artisan db:seed` запускает `DatabaseSeeder`, который включает:

1. `RolePermissionSeeder` — роли и права
2. `CatalogSeeder` — каталог
3. `PlanSeeder` — тарифы
4. `ServiceCategorySeeder` — категории услуг
5. Пользователей `admin@automarket.ru` / `buyer@automarket.ru`
6. `DemoSeeder` — демо-данные

Автоматически при первом деплое (если `RUN_DB_SEED=true`):

```bash
dc-prod exec app rm -f storage/.docker-initialized
dc-prod restart app
```

### Обновление кода

```bash
git pull
dc-prod --profile build run --rm frontend
dc-prod up -d --build
dc-prod exec app php artisan migrate --force
dc-prod exec app php artisan config:cache
```

### Диагностика 500

```bash
# Проверка напрямую (минуя SSL-прокси)
curl -I http://127.0.0.1:8080
curl http://127.0.0.1:8080/up

# Laravel-лог
dc-prod exec app tail -50 storage/logs/laravel.log

# Логи контейнеров
dc-prod logs --tail=100 app
dc-prod logs --tail=50 nginx
```

**Частая ошибка: `No application encryption key has been specified`**

```bash
# 1. Сгенерировать ключ
dc-prod exec app php artisan key:generate --force --show

# 2. Прописать в .env.prod на хосте:
# APP_KEY=base64:xxxxxxxx...

# 3. Сбросить кэш и перезапустить
dc-prod exec app php artisan config:clear
dc-prod exec app php artisan config:cache
dc-prod restart app queue scheduler
```

Временно для отладки в `.env.prod`: `APP_DEBUG=true` (после — вернуть `false`).

### Безопасность после деплоя

- Сменить пароль `admin@automarket.ru` (по умолчанию `password`)
- Поставить `RUN_DB_SEED=false`
- Закрыть firewall: снаружи только 80/443, не открывать 5432/7700/8080

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
