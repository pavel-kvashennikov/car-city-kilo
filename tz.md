# Техническое Задание: AutoMarket Portal
### Единый B2B/B2C портал городского авторынка

---

**Версия:** 1.0.0  
**Дата:** 2025  
**Статус:** Draft → Ready for Development  
**Стек:** Laravel 11+ / PHP 8.3+ / PostgreSQL / Meilisearch / Inertia.js + Vue 3  

---

## Содержание

1. [Product Vision & Goals](#1-product-vision--goals)
2. [User Roles & Personas](#2-user-roles--personas)
3. [User Flows & Scenarios](#3-user-flows--scenarios)
4. [Architecture & Design Patterns](#4-architecture--design-patterns)
5. [Database Schema (ERD)](#5-database-schema-erd)
6. [Roles & Permissions (RBAC)](#6-roles--permissions-rbac)
7. [Modules & API Structure](#7-modules--api-structure)
8. [Frontend Architecture](#8-frontend-architecture)
9. [Search & Indexing](#9-search--indexing)
10. [Non-Functional Requirements](#10-non-functional-requirements)
11. [Integrations](#11-integrations)
12. [Step-by-step Implementation Plan](#12-step-by-step-implementation-plan)

---

## 1. Product Vision & Goals

### 1.1 Problem Statement

В городе существует крупная оффлайн-площадка (авторынок), на которой работают несколько десятков резидентов: автодилеры, магазины запчастей, автосервисы. На текущий момент каждый резидент действует независимо: размещает объявления на сторонних классифайдах (Авито, Дром), не имеет единой точки контакта с покупателем, физические локации не привязаны к онлайну. Покупатель вынужден посещать десятки разных сайтов или ехать на рынок вслепую.

### 1.2 Solution

Создать **единый B2B/B2C портал** — цифровой двойник физического авторынка, который:

- Предоставляет каждому резиденту (Tenant) персонализированную витрину с нужными бизнес-профилями.
- Объединяет покупателя с продавцом, магазином запчастей и сервисом в едином интерфейсе.
- Связывает онлайн-присутствие с физическим расположением на карте рынка.
- Позволяет одному резиденту вести несколько видов деятельности (мультипрофильность).

### 1.3 Key Value Propositions

| Для кого | Ценность |
|---|---|
| Резидент (Tenant) | Единая онлайн-витрина, CRM-lite, аналитика, снижение стоимости привлечения клиентов |
| Покупатель (Buyer) | Одна точка входа для поиска авто/запчастей/сервиса, возможность видеть физическое расположение |
| Администрация рынка | Контроль резидентов, биллинг, управление картой площадки |

### 1.4 Business Profiles — Критическое Разграничение

Один `Tenant` (компания-резидент) может одновременно активировать один или несколько бизнес-профилей:

```
Company (Tenant)
├── DealerProfile    → продажа автомобилей (единичные объекты)
├── PartsProfile     → продажа запчастей (SKU с остатками)
└── ServiceProfile   → автосервис (услуги + онлайн-запись)
```

Каждый профиль — это независимый домен со своей сущностной моделью, своими правами доступа для сотрудников и своим UI-шаблоном.

### 1.5 Success Metrics

- Онбординг нового резидента < 30 минут.
- Время ответа страниц каталога < 300ms (p95).
- Поиск по OEM-номеру запчасти < 150ms.
- Конверсия заявки на тест-драйв/запись в сервис ≥ 8%.

---

## 2. User Roles & Personas

### 2.1 Иерархия ролей

```
SuperAdmin (Администрация площадки)
└── Company (Tenant) — резидент рынка
    ├── TenantOwner — владелец компании
    └── TenantStaff — сотрудники (привязаны к профилю)
        ├── SalesManager    → только DealerProfile
        ├── Storekeeper     → только PartsProfile
        └── ServiceAdvisor  → только ServiceProfile

Buyer — независимый покупатель (авторизованный или гость)
```

### 2.2 Описание ролей

#### SuperAdmin
- Полный доступ к системе.
- Управление компаниями (одобрение, блокировка).
- Биллинг: подписки, тарифные планы.
- Управление физической картой рынка (павильоны, боксы).
- Глобальная модерация объявлений.
- Системная аналитика и отчётность.

#### TenantOwner
- Регистрирует компанию и проходит верификацию.
- Активирует нужные бизнес-профили (платная подписка).
- Управляет сотрудниками: приглашает, назначает роль и профиль.
- Видит агрегированную аналитику по всем своим профилям.
- Управляет физическим расположением на карте рынка.

#### SalesManager (DealerProfile)
- Создаёт/редактирует объявления о продаже автомобилей.
- Загружает фото/видео.
- Обрабатывает заявки на тест-драйв, trade-in, кредит.
- Видит только данные DealerProfile своей компании.

#### Storekeeper (PartsProfile)
- Управляет каталогом товаров (запчасти/SKU).
- Обновляет остатки и цены.
- Обрабатывает заказы покупателей.
- Управляет кросс-номерами и применяемостью.

#### ServiceAdvisor (ServiceProfile)
- Управляет прайс-листом услуг.
- Ведёт расписание (слоты), назначает мастеров.
- Принимает и обрабатывает заявки на сервис.
- Ведёт историю обслуживания автомобилей клиентов.

#### Buyer
- Просматривает каталог (гость).
- Регистрируется для расширенных функций.
- Ищет авто, запчасти, услуги.
- Добавляет запчасти в корзину.
- Записывается на сервис.
- Единый чекаут (запчасти + установка).
- Отслеживает историю заказов/записей.
- Избранное и уведомления.

---

## 3. User Flows & Scenarios

### 3.1 Flow: Онбординг Резидента

```
[TenantOwner]
  1. Открывает /register/company
  2. Заполняет анкету компании: название, ИНН, контакты, описание
  3. Выбирает физическое расположение на интерактивной карте рынка
  4. Выбирает тарифный план (Basic / Pro / Enterprise)
  5. Выбирает бизнес-профили для активации
  6. Оплачивает подписку (или запрашивает счёт)
  7. Статус: "На модерации" → SuperAdmin проверяет данные
  8. SuperAdmin одобряет → компании создаются профили в БД
  9. TenantOwner получает email с доступом в личный кабинет
  10. Настраивает профили: логотип, описание, рабочее время
```

### 3.2 Flow: Управление автомобилями (DealerProfile)

```
[SalesManager]
  1. Вход в личный кабинет → раздел "Автомобили"
  2. "Добавить объявление"
  3. Заполняет форму:
     - Выбор марки → модели → поколения (из справочника)
     - Год, VIN, пробег, цвет, двигатель, КПП, привод
     - Комплектация: галочки опций
     - Состояние: Новый / С пробегом / Битый/Восстановленный
     - Юридический статус: Чистый / Кредитный / Арест
     - Цена (розница, trade-in, кредит от X руб./мес.)
  4. Загружает фото (до 30 штук, drag-and-drop, авто-сжатие)
  5. Опционально загружает видео (YouTube URL или файл)
  6. Указывает доступность для тест-драйва и кредита
  7. Публикует объявление (статус: pending → approved)
  8. SuperAdmin модерирует (или автоодобрение на Pro-тарифе)
```

```
[SalesManager] — Обработка заявок
  1. Dashboard показывает входящие заявки:
     - Запрос тест-драйва: имя, телефон, удобное время
     - Trade-in: описание и фото авто клиента
     - Запрос на кредит: ФИО, примерный доход
  2. Менеджер звонит клиенту, обновляет статус заявки
  3. Завершённые заявки архивируются
```

### 3.3 Flow: Управление запчастями (PartsProfile)

```
[Storekeeper] — Добавление товара
  1. Раздел "Каталог запчастей"
  2. "Добавить товар" или "Импорт из файла" (Excel/CSV)
  3. Форма товара:
     - Название, артикул поставщика, OEM-номер
     - Кросс-номера (аналоги) — множественное поле
     - Применяемость: список "Марка → Модель → Год от/до"
     - Категория (из дерева категорий)
     - Цена розничная / оптовая (от N штук)
     - Остаток на складе
     - Фото (до 5 штук)
     - Состояние: Оригинал / Аналог / Б/У
  4. Товар проходит индексацию в Meilisearch
  5. После сохранения — доступен в каталоге

[Storekeeper] — Обработка заказа
  1. Входящие заказы в разделе "Заказы"
  2. Статусы: Новый → Подтверждён → Готов к выдаче → Выдан
  3. Уведомление покупателю по каждому изменению статуса
  4. Возможность частичной выдачи (если заказ из нескольких товаров)
```

### 3.4 Flow: Управление сервисом (ServiceProfile)

```
[ServiceAdvisor] — Настройка прайс-листа
  1. Раздел "Услуги"
  2. Создание категорий работ: ТО, Двигатель, Ходовая, Кузов, ...
  3. Добавление услуги:
     - Название, описание
     - Нормо-час (стоимость часа работы мастера)
     - Фиксированная стоимость (если есть)
     - Диапазон стоимости (мин/макс)
     - Предполагаемое время выполнения
     - Тип авто: Легковой / Грузовой / Мотоцикл
  4. Настройка мастеров: ФИО, специализация, расписание

[ServiceAdvisor] — Управление расписанием
  1. Раздел "Расписание"
  2. Настройка рабочих дней и часов
  3. Создание слотов (например, каждые 30/60 минут)
  4. Блокировка слотов (праздники, обед)
  5. Просмотр записей в виде календаря (day/week/month view)

[ServiceAdvisor] — Обработка записи
  1. Входящая запись: клиент, авто, услуга, желаемое время
  2. Подтверждение или перенос записи
  3. Назначение мастера
  4. После выполнения: отметка о завершении, запрос отзыва
```

### 3.5 Flow: Покупатель — Поиск и Покупка

```
[Buyer] — Поиск автомобиля
  1. Главная страница / раздел "Автомобили"
  2. Фильтры: марка, модель, год (от/до), цена, пробег, КПП, привод, состояние, дилер
  3. Результаты поиска: карточки авто (фото, ключевые параметры, цена)
  4. Открывает карточку: полная галерея, описание, характеристики
  5. Действия:
     a. "Записаться на тест-драйв" → форма (имя, телефон, дата)
     b. "Рассчитать кредит" → калькулятор
     c. "Trade-in" → форма описания своего авто
     d. "Позвонить" → показать номер телефона
  6. Может сохранить в "Избранное"

[Buyer] — Поиск запчастей
  1. Раздел "Запчасти"
  2. Поиск: строка поиска (OEM, артикул, название)
  3. Фильтры: категория, марка авто, состояние (оригинал/аналог/б.у.), магазин
  4. Выбор товара → карточка: фото, применяемость, наличие, цена
  5. "В корзину" (указать количество)
  6. Корзина: список товаров, итого, кнопка "Оформить заказ"
  7. Оформление: имя, телефон, способ получения (самовывоз / доставка)
  8. Опционально: добавить услугу по установке из сервиса (Единый чекаут)

[Buyer] — Запись в сервис
  1. Раздел "Сервисы" или карточка сервиса
  2. Выбор услуги / калькулятор стоимости
  3. Выбор даты и свободного слота
  4. Выбор мастера (опционально)
  5. Форма: имя, телефон, марка/модель/год авто, VIN (опционально)
  6. Подтверждение → SMS/email уведомление

[Buyer] — Единый чекаут (запчасти + установка)
  1. В корзине есть запчасти
  2. Система предлагает: "Хотите записаться на установку?"
  3. Показывает сервисы, которые устанавливают данный тип запчастей
  4. Покупатель выбирает сервис, дату, слот
  5. В одном заказе: позиции товаров + позиция записи в сервис
  6. Единое подтверждение → уведомления и в магазин, и в сервис
```

### 3.6 Flow: SuperAdmin — Управление платформой

```
[SuperAdmin]
  1. Dashboard: общая статистика, новые заявки резидентов, финансы
  2. Резиденты:
     - Список компаний с фильтрами (статус, тип профиля)
     - Модерация: одобрить/отклонить с комментарием
     - Блокировка/разблокировка
     - Просмотр всех данных компании
  3. Карта рынка:
     - Drag-and-drop редактор физических мест
     - Привязка мест к компаниям
     - Статусы мест: Свободно / Занято / В аренде
  4. Биллинг:
     - Тарифные планы (CRUD)
     - История платежей
     - Генерация актов/счётов
  5. Модерация контента:
     - Очередь объявлений на проверку
     - Жалобы от пользователей
  6. Настройки платформы: справочники (марки, категории), SEO, баннеры
```

---

## 4. Architecture & Design Patterns

### 4.1 Общая Архитектура

Проект строится как **Modular Monolith** с принципами **Clean Architecture**. Это означает:

- Код организован в независимые модули по бизнес-доменам.
- Каждый модуль имеет чёткое разделение на слои.
- Модули взаимодействуют только через публичные интерфейсы (ServiceContracts) или доменные события (Domain Events).
- **Нет микросервисов** на старте — монолит легко мигрировать позже при необходимости.

### 4.2 Структура директорий

```
project_root/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   └── Http/
│       ├── Middleware/
│       └── Kernel.php
│
├── src/                            ← Всё бизнес-логика здесь
│   ├── Shared/                     ← Общие компоненты (не зависят от модулей)
│   │   ├── Domain/
│   │   │   ├── ValueObjects/       ← Money, PhoneNumber, Address, VIN, OemNumber
│   │   │   └── Events/             ← BaseEvent
│   │   ├── Infrastructure/
│   │   │   ├── Persistence/        ← BaseRepository, QueryBuilder helpers
│   │   │   ├── Media/              ← MediaService (Spatie)
│   │   │   └── Search/             ← MeilisearchService
│   │   └── Support/
│   │       ├── Enums/              ← Статусы, типы (общие)
│   │       └── Traits/             ← HasUuid, HasSlug, SoftDeletes helpers
│   │
│   ├── Company/                    ← Tenant Management
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── Company.php
│   │   │   │   ├── CompanyLocation.php
│   │   │   │   └── BusinessProfile.php
│   │   │   ├── Contracts/
│   │   │   │   └── CompanyRepositoryInterface.php
│   │   │   └── Events/
│   │   │       ├── CompanyRegistered.php
│   │   │       └── CompanyApproved.php
│   │   ├── Application/
│   │   │   ├── UseCases/
│   │   │   │   ├── RegisterCompany/
│   │   │   │   │   ├── RegisterCompanyCommand.php
│   │   │   │   │   └── RegisterCompanyHandler.php
│   │   │   │   ├── ApproveCompany/
│   │   │   │   └── AttachBusinessProfile/
│   │   │   └── DTO/
│   │   │       └── CompanyDTO.php
│   │   ├── Infrastructure/
│   │   │   ├── Persistence/
│   │   │   │   ├── CompanyRepository.php
│   │   │   │   └── Migrations/
│   │   │   └── Http/
│   │   │       ├── Controllers/
│   │   │       └── Requests/
│   │   └── Providers/
│   │       └── CompanyServiceProvider.php
│   │
│   ├── Dealer/                     ← Бизнес-профиль: Автодилер
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── DealerProfile.php
│   │   │   │   ├── Vehicle.php
│   │   │   │   ├── VehiclePhoto.php
│   │   │   │   └── DealerLead.php  ← Заявки (тест-драйв, кредит, trade-in)
│   │   │   ├── Contracts/
│   │   │   │   └── VehicleRepositoryInterface.php
│   │   │   └── Events/
│   │   │       ├── VehiclePublished.php
│   │   │       └── LeadCreated.php
│   │   ├── Application/
│   │   │   ├── UseCases/
│   │   │   │   ├── CreateVehicle/
│   │   │   │   ├── PublishVehicle/
│   │   │   │   ├── CreateTestDriveRequest/
│   │   │   │   └── CalculateTradeIn/
│   │   │   └── DTO/
│   │   ├── Infrastructure/
│   │   │   ├── Persistence/
│   │   │   ├── Search/             ← VehicleSearchIndexer.php
│   │   │   └── Http/
│   │   │       ├── Controllers/
│   │   │       │   ├── VehicleController.php
│   │   │       │   └── LeadController.php
│   │   │       └── Requests/
│   │   └── Providers/
│   │       └── DealerServiceProvider.php
│   │
│   ├── Parts/                      ← Бизнес-профиль: Магазин запчастей
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── PartsProfile.php
│   │   │   │   ├── Product.php
│   │   │   │   ├── ProductApplicability.php ← Применяемость
│   │   │   │   ├── CrossNumber.php          ← Кросс-номера
│   │   │   │   └── StockItem.php            ← Остатки на складе
│   │   │   ├── Contracts/
│   │   │   └── Events/
│   │   │       ├── ProductCreated.php
│   │   │       └── StockUpdated.php
│   │   ├── Application/
│   │   │   ├── UseCases/
│   │   │   │   ├── CreateProduct/
│   │   │   │   ├── ImportProductsFromFile/
│   │   │   │   ├── UpdateStock/
│   │   │   │   └── SearchByOemNumber/
│   │   │   └── DTO/
│   │   ├── Infrastructure/
│   │   │   ├── Persistence/
│   │   │   ├── Search/             ← ProductSearchIndexer.php
│   │   │   ├── Import/             ← ExcelImporter, MoySkladSync
│   │   │   └── Http/
│   │   └── Providers/
│   │       └── PartsServiceProvider.php
│   │
│   ├── Service/                    ← Бизнес-профиль: Автосервис
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── ServiceProfile.php
│   │   │   │   ├── ServiceItem.php      ← Услуга/позиция прайс-листа
│   │   │   │   ├── ServiceMaster.php    ← Мастер
│   │   │   │   ├── TimeSlot.php         ← Слот времени
│   │   │   │   └── Appointment.php      ← Запись клиента
│   │   │   ├── Contracts/
│   │   │   └── Events/
│   │   │       ├── AppointmentCreated.php
│   │   │       └── AppointmentConfirmed.php
│   │   ├── Application/
│   │   │   ├── UseCases/
│   │   │   │   ├── CreateAppointment/
│   │   │   │   ├── ConfirmAppointment/
│   │   │   │   ├── GetAvailableSlots/
│   │   │   │   └── CalculateRepairCost/
│   │   │   └── DTO/
│   │   ├── Infrastructure/
│   │   │   ├── Persistence/
│   │   │   └── Http/
│   │   └── Providers/
│   │       └── ServiceServiceProvider.php
│   │
│   ├── Order/                      ← Корзина и чекаут (Shared для Parts + Service)
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── Cart.php
│   │   │   │   ├── CartItem.php         ← Polymorphic: product / appointment
│   │   │   │   ├── Order.php
│   │   │   │   └── OrderItem.php
│   │   │   └── Events/
│   │   │       └── OrderPlaced.php
│   │   ├── Application/
│   │   │   └── UseCases/
│   │   │       ├── AddToCart/
│   │   │       ├── PlaceOrder/
│   │   │       └── ProcessCheckout/
│   │   └── Infrastructure/
│   │
│   ├── Catalog/                    ← Справочники (марки, модели, категории)
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── CarBrand.php
│   │   │   │   ├── CarModel.php
│   │   │   │   ├── CarGeneration.php
│   │   │   │   └── PartCategory.php
│   │   └── ...
│   │
│   ├── MarketMap/                  ← Карта физической площадки
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── MarketZone.php       ← Зона рынка
│   │   │   │   ├── MarketLocation.php   ← Конкретное место (павильон, бокс)
│   │   │   │   └── LocationType.php     ← Enum: pavilion, box, outdoor
│   │   └── ...
│   │
│   ├── Billing/                    ← Подписки и платежи
│   │   ├── Domain/
│   │   │   ├── Models/
│   │   │   │   ├── Plan.php
│   │   │   │   ├── Subscription.php
│   │   │   │   └── Payment.php
│   │   └── ...
│   │
│   └── Notification/               ← Уведомления (SMS, Email, Push)
│       ├── Channels/
│       │   ├── SmsChannel.php
│       │   └── PushChannel.php
│       └── Notifications/
│           ├── OrderPlacedNotification.php
│           └── AppointmentConfirmedNotification.php
│
├── config/
├── database/
│   └── migrations/                 ← Все миграции (можно группировать по модулям)
├── resources/
│   └── js/                         ← Vue 3 + Inertia.js
│       ├── Pages/
│       │   ├── Dealer/
│       │   ├── Parts/
│       │   ├── Service/
│       │   └── Admin/
│       └── Components/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── modules/                    ← Файлы маршрутов по модулям
│       ├── dealer.php
│       ├── parts.php
│       └── service.php
└── tests/
    ├── Unit/
    │   └── src/
    └── Feature/
        └── src/
```

### 4.3 Паттерны внутри модулей

#### Clean Architecture слои:

```
Domain Layer (Ядро, нет зависимостей от фреймворка)
  ├── Eloquent Models (используем Eloquent как Domain Models для прагматичности)
  ├── Value Objects (Money, VIN, OemNumber)
  ├── Domain Events
  └── Repository Contracts (интерфейсы)

Application Layer (Бизнес-логика, Use Cases)
  ├── Command / Query объекты (DTO)
  ├── Handlers (Use Case классы)
  └── Services (оркестрация нескольких репозиториев)

Infrastructure Layer (Конкретные реализации)
  ├── Eloquent Repositories (реализация контрактов)
  ├── HTTP Controllers (тонкие, делегируют в Use Cases)
  ├── Form Requests (валидация)
  ├── Search Indexers (Meilisearch)
  └── External Integrations (1С, МойСклад)
```

#### Паттерн Use Case (Command Handler):

```php
// Application/UseCases/CreateVehicle/CreateVehicleCommand.php
final class CreateVehicleCommand
{
    public function __construct(
        public readonly int    $dealerProfileId,
        public readonly string $brandId,
        public readonly string $modelId,
        public readonly int    $year,
        public readonly string $vin,
        public readonly int    $mileage,
        public readonly Money  $price,
        public readonly array  $attributes,  // JSONB flexible attrs
    ) {}
}

// Application/UseCases/CreateVehicle/CreateVehicleHandler.php
final class CreateVehicleHandler
{
    public function __construct(
        private VehicleRepositoryInterface $vehicles,
        private EventDispatcherInterface   $events,
        private MeilisearchService         $search,
    ) {}

    public function handle(CreateVehicleCommand $cmd): Vehicle
    {
        $vehicle = Vehicle::create([...]);
        $this->vehicles->save($vehicle);
        $this->events->dispatch(new VehicleCreated($vehicle));
        // Индексация асинхронно через Event Listener → Queue
        return $vehicle;
    }
}
```

#### Тонкий контроллер:

```php
// Infrastructure/Http/Controllers/VehicleController.php
class VehicleController extends Controller
{
    public function store(CreateVehicleRequest $request, CreateVehicleHandler $handler): Response
    {
        $vehicle = $handler->handle(
            CreateVehicleCommand::fromRequest($request)
        );
        return Inertia::render('Dealer/Vehicle/Show', ['vehicle' => $vehicle]);
    }
}
```

### 4.4 Взаимодействие между модулями

Модули **не импортируют** классы друг друга напрямую (кроме Shared). Взаимодействие через:

1. **Domain Events** (предпочтительно): `OrderPlaced` → `PartsModule` резервирует товар, `ServiceModule` блокирует слот.
2. **Service Contracts**: `Order` вызывает `StockServiceInterface` (определён в `Parts/Domain/Contracts`), реализован в `Parts/Infrastructure`.
3. **Shared DTOs**: передача данных между модулями через DTO в `Shared/`.

```php
// Пример: Order модуль создаёт заказ, не зная о деталях Parts и Service
class ProcessCheckoutHandler
{
    public function __construct(
        private StockReservationInterface $stockReservation,    // из Parts
        private SlotBookingInterface      $slotBooking,         // из Service
        private OrderRepositoryInterface  $orders,
    ) {}
}
```

---

## 5. Database Schema (ERD)

### 5.1 Стратегия для Business Profiles

**Выбранный паттерн: Class Table Inheritance + Полиморфная ссылка**

Причина: у каждого профиля совершенно разные атрибуты и связи. Попытка объединить их в одну таблицу привела бы к "разреженной матрице" (sparse table). Вместо этого:

```
companies (1) ──── (N) business_profiles ──┬── (1) dealer_profiles
                                            ├── (1) parts_profiles
                                            └── (1) service_profiles
```

`business_profiles` — это "якорная" таблица с общими полями и полиморфной ссылкой на конкретный профиль.

### 5.2 Основные таблицы

#### `users`
```sql
CREATE TABLE users (
    id           BIGSERIAL PRIMARY KEY,
    uuid         UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,
    name         VARCHAR(255) NOT NULL,
    email        VARCHAR(255) NOT NULL UNIQUE,
    phone        VARCHAR(20),
    password     VARCHAR(255) NOT NULL,
    avatar_path  VARCHAR(500),
    is_active    BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at   TIMESTAMP,
    updated_at   TIMESTAMP,
    deleted_at   TIMESTAMP  -- SoftDeletes
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_phone ON users(phone);
```

#### `companies` (Tenants)
```sql
CREATE TABLE companies (
    id                BIGSERIAL PRIMARY KEY,
    uuid              UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,
    slug              VARCHAR(255) NOT NULL UNIQUE,  -- для URL: /companies/auto-prestige
    name              VARCHAR(255) NOT NULL,
    legal_name        VARCHAR(255),     -- юридическое название
    inn               VARCHAR(12),      -- ИНН
    description       TEXT,
    logo_path         VARCHAR(500),
    website           VARCHAR(255),
    phone             VARCHAR(20),
    email             VARCHAR(255),
    status            VARCHAR(50) NOT NULL DEFAULT 'pending',
    -- Enum: pending, active, suspended, rejected
    rejection_reason  TEXT,
    approved_at       TIMESTAMP,
    approved_by       BIGINT REFERENCES users(id),
    settings          JSONB DEFAULT '{}',
    -- Например: {"moderation_auto": true, "notifications": {...}}
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP,
    deleted_at        TIMESTAMP
);

CREATE INDEX idx_companies_status ON companies(status);
CREATE INDEX idx_companies_settings ON companies USING GIN(settings);
```

#### `company_users` (Связь сотрудников с компанией)
```sql
CREATE TABLE company_users (
    id                   BIGSERIAL PRIMARY KEY,
    company_id           BIGINT NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    user_id              BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role                 VARCHAR(50) NOT NULL,
    -- Enum: owner, sales_manager, storekeeper, service_advisor
    business_profile_id  BIGINT REFERENCES business_profiles(id),
    -- NULL = доступ ко всем профилям компании (для owner)
    invited_at           TIMESTAMP,
    joined_at            TIMESTAMP,
    is_active            BOOLEAN DEFAULT TRUE,
    UNIQUE(company_id, user_id)
);
```

#### `business_profiles` (Полиморфный якорь)
```sql
CREATE TABLE business_profiles (
    id                BIGSERIAL PRIMARY KEY,
    uuid              UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,
    company_id        BIGINT NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    profile_type      VARCHAR(50) NOT NULL,
    -- Enum: dealer, parts, service
    profileable_id    BIGINT NOT NULL,
    -- ID записи в dealer_profiles / parts_profiles / service_profiles
    profileable_type  VARCHAR(100) NOT NULL,
    -- Значения: 'DealerProfile', 'PartsProfile', 'ServiceProfile'
    name              VARCHAR(255),     -- Кастомное имя профиля (если нужно)
    is_active         BOOLEAN DEFAULT TRUE,
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP,
    UNIQUE(company_id, profile_type)   -- Один тип профиля на компанию
);

CREATE INDEX idx_business_profiles_company ON business_profiles(company_id);
CREATE INDEX idx_business_profiles_polymorphic 
    ON business_profiles(profileable_type, profileable_id);
```

#### `market_locations` (Физические места на рынке)
```sql
CREATE TABLE market_zones (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,  -- "Павильон А", "Открытая площадка"
    description TEXT,
    color       VARCHAR(7),             -- HEX цвет для карты
    sort_order  INTEGER DEFAULT 0
);

CREATE TABLE market_locations (
    id                BIGSERIAL PRIMARY KEY,
    zone_id           BIGINT REFERENCES market_zones(id),
    company_id        BIGINT REFERENCES companies(id),  -- NULL если свободно
    business_profile_id BIGINT REFERENCES business_profiles(id), -- к какому профилю
    identifier        VARCHAR(50) NOT NULL,   -- "Павильон 15", "Бокс 42"
    location_type     VARCHAR(20) NOT NULL,   -- pavilion, box, outdoor_spot
    floor             SMALLINT DEFAULT 1,
    coordinates       JSONB,
    -- {"x": 120, "y": 340, "width": 80, "height": 60} для SVG-карты
    geo_coordinates   JSONB,
    -- {"lat": 55.12345, "lng": 37.12345} для Google Maps
    status            VARCHAR(20) DEFAULT 'available',
    -- available, occupied, reserved, inactive
    area_sqm          DECIMAL(8,2),
    notes             TEXT,
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP
);
```

### 5.3 Dealer Profile Tables

#### `dealer_profiles`
```sql
CREATE TABLE dealer_profiles (
    id                    BIGSERIAL PRIMARY KEY,
    company_id            BIGINT NOT NULL REFERENCES companies(id),
    description           TEXT,
    working_hours         JSONB DEFAULT '{}',
    -- {"mon": {"open": "09:00", "close": "19:00"}, ...}
    allows_test_drive     BOOLEAN DEFAULT TRUE,
    allows_credit         BOOLEAN DEFAULT TRUE,
    allows_trade_in       BOOLEAN DEFAULT TRUE,
    credit_partners       JSONB DEFAULT '[]',
    -- [{"name": "Сбербанк", "rate_from": 3.5}, ...]
    meta                  JSONB DEFAULT '{}',
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);
```

#### `vehicles`
```sql
CREATE TABLE vehicles (
    id                    BIGSERIAL PRIMARY KEY,
    uuid                  UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,
    slug                  VARCHAR(255) UNIQUE,
    dealer_profile_id     BIGINT NOT NULL REFERENCES dealer_profiles(id),
    
    -- Идентификация
    vin                   VARCHAR(17) UNIQUE,
    
    -- Справочные данные (из catalog)
    car_brand_id          BIGINT REFERENCES car_brands(id),
    car_model_id          BIGINT REFERENCES car_models(id),
    car_generation_id     BIGINT REFERENCES car_generations(id),
    
    -- Основные характеристики
    year                  SMALLINT NOT NULL,
    mileage               INTEGER NOT NULL DEFAULT 0,  -- в км
    color                 VARCHAR(50),
    color_type            VARCHAR(20),   -- solid, metallic, pearl
    
    -- Двигатель
    engine_type           VARCHAR(20),   -- petrol, diesel, hybrid, electric, gas
    engine_volume         DECIMAL(3,1),  -- литры
    engine_power          SMALLINT,      -- л.с.
    
    -- Трансмиссия и привод
    transmission          VARCHAR(20),   -- manual, automatic, robot, cvt
    drive_type            VARCHAR(10),   -- fwd, rwd, awd, 4wd
    
    -- Кузов
    body_type             VARCHAR(20),   -- sedan, suv, hatchback, ...
    
    -- Ценообразование
    price                 BIGINT NOT NULL,       -- в копейках (целое для точности)
    price_currency        CHAR(3) DEFAULT 'RUB',
    credit_price_from     BIGINT,                -- минимальный платёж в месяц
    trade_in_discount     BIGINT,                -- скидка при trade-in
    
    -- Состояние и юридический статус
    condition             VARCHAR(20) NOT NULL DEFAULT 'used',
    -- new, used, damaged
    legal_status          VARCHAR(20) DEFAULT 'clean',
    -- clean, credit, arrest, duplicate
    owners_count          SMALLINT DEFAULT 1,
    has_accidents         BOOLEAN DEFAULT FALSE,
    
    -- Комплектация и опции (JSONB)
    equipment             JSONB DEFAULT '{}',
    -- {
    --   "comfort": ["климат-контроль", "подогрев сидений"],
    --   "safety": ["ABS", "ESP", "6 подушек"],
    --   "multimedia": ["Android Auto", "Apple CarPlay"]
    -- }
    
    -- Дополнительная информация
    description           TEXT,
    
    -- Модерация и публикация
    status                VARCHAR(20) DEFAULT 'draft',
    -- draft, pending, active, sold, archived, rejected
    rejection_reason      TEXT,
    published_at          TIMESTAMP,
    sold_at               TIMESTAMP,
    
    -- SEO
    seo_title             VARCHAR(255),
    seo_description       VARCHAR(500),
    
    -- Просмотры
    views_count           INTEGER DEFAULT 0,
    favorites_count       INTEGER DEFAULT 0,
    
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP,
    deleted_at            TIMESTAMP
);

-- Индексы для фильтрации каталога
CREATE INDEX idx_vehicles_dealer ON vehicles(dealer_profile_id);
CREATE INDEX idx_vehicles_status ON vehicles(status);
CREATE INDEX idx_vehicles_brand_model ON vehicles(car_brand_id, car_model_id);
CREATE INDEX idx_vehicles_year ON vehicles(year);
CREATE INDEX idx_vehicles_price ON vehicles(price);
CREATE INDEX idx_vehicles_mileage ON vehicles(mileage);
CREATE INDEX idx_vehicles_condition ON vehicles(condition);
CREATE INDEX idx_vehicles_equipment ON vehicles USING GIN(equipment);
-- Полнотекстовый поиск по описанию
CREATE INDEX idx_vehicles_fts ON vehicles 
    USING GIN(to_tsvector('russian', coalesce(description, '')));
```

#### `vehicle_photos`
```sql
CREATE TABLE vehicle_photos (
    id            BIGSERIAL PRIMARY KEY,
    vehicle_id    BIGINT NOT NULL REFERENCES vehicles(id) ON DELETE CASCADE,
    path          VARCHAR(500) NOT NULL,
    thumb_path    VARCHAR(500),
    sort_order    SMALLINT DEFAULT 0,
    is_cover      BOOLEAN DEFAULT FALSE,
    created_at    TIMESTAMP
);

CREATE INDEX idx_vehicle_photos_vehicle ON vehicle_photos(vehicle_id);
```

#### `dealer_leads` (Заявки: тест-драйв, кредит, trade-in)
```sql
CREATE TABLE dealer_leads (
    id                    BIGSERIAL PRIMARY KEY,
    uuid                  UUID DEFAULT gen_random_uuid() UNIQUE,
    dealer_profile_id     BIGINT NOT NULL REFERENCES dealer_profiles(id),
    vehicle_id            BIGINT REFERENCES vehicles(id),
    user_id               BIGINT REFERENCES users(id),  -- NULL если гость
    
    lead_type             VARCHAR(20) NOT NULL,
    -- test_drive, credit, trade_in, callback
    
    -- Контактные данные (дублируем на случай гостя)
    contact_name          VARCHAR(255) NOT NULL,
    contact_phone         VARCHAR(20) NOT NULL,
    contact_email         VARCHAR(255),
    
    -- Специфичные поля
    preferred_datetime     TIMESTAMP,              -- для тест-драйва
    trade_in_data         JSONB DEFAULT '{}',
    -- {"brand": "BMW", "model": "X5", "year": 2018, "mileage": 80000, "description": "..."}
    credit_data           JSONB DEFAULT '{}',
    -- {"monthly_income": 150000, "initial_payment": 500000}
    
    message               TEXT,
    
    status                VARCHAR(20) DEFAULT 'new',
    -- new, in_progress, completed, cancelled
    
    notes                 TEXT,          -- внутренние заметки менеджера
    assigned_to           BIGINT REFERENCES users(id),
    
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);

CREATE INDEX idx_dealer_leads_dealer ON dealer_leads(dealer_profile_id);
CREATE INDEX idx_dealer_leads_status ON dealer_leads(status);
CREATE INDEX idx_dealer_leads_type ON dealer_leads(lead_type);
```

### 5.4 Parts Profile Tables

#### `parts_profiles`
```sql
CREATE TABLE parts_profiles (
    id                    BIGSERIAL PRIMARY KEY,
    company_id            BIGINT NOT NULL REFERENCES companies(id),
    description           TEXT,
    working_hours         JSONB DEFAULT '{}',
    min_order_amount      BIGINT DEFAULT 0,       -- минимальная сумма заказа
    delivery_info         JSONB DEFAULT '{}',
    -- {"available": true, "free_from": 5000, "cost": 300}
    payment_methods       JSONB DEFAULT '[]',
    -- ["cash", "card", "bank_transfer"]
    meta                  JSONB DEFAULT '{}',
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);
```

#### `part_categories`
```sql
CREATE TABLE part_categories (
    id          BIGSERIAL PRIMARY KEY,
    parent_id   BIGINT REFERENCES part_categories(id),
    name        VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon_path   VARCHAR(500),
    sort_order  INTEGER DEFAULT 0,
    is_active   BOOLEAN DEFAULT TRUE,
    path        LTREE  -- для иерархических запросов (расширение ltree)
);

CREATE INDEX idx_part_categories_parent ON part_categories(parent_id);
CREATE INDEX idx_part_categories_path ON part_categories USING GIST(path);
```

#### `products`
```sql
CREATE TABLE products (
    id                    BIGSERIAL PRIMARY KEY,
    uuid                  UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,
    slug                  VARCHAR(255),
    parts_profile_id      BIGINT NOT NULL REFERENCES parts_profiles(id),
    category_id           BIGINT REFERENCES part_categories(id),
    
    -- Идентификация
    name                  VARCHAR(500) NOT NULL,
    article_number        VARCHAR(100),            -- артикул поставщика
    oem_number            VARCHAR(100),            -- OEM номер производителя
    barcode               VARCHAR(50),
    
    -- Характеристики
    brand                 VARCHAR(100),            -- бренд запчасти (не авто)
    condition             VARCHAR(20) DEFAULT 'new',
    -- new, used, refurbished
    part_type             VARCHAR(20) DEFAULT 'aftermarket',
    -- original, oem, aftermarket
    
    -- Цены (в копейках)
    price_retail          BIGINT NOT NULL,
    price_wholesale       BIGINT,
    wholesale_min_qty     SMALLINT DEFAULT 1,      -- от скольки штук оптовая цена
    
    -- Описание
    description           TEXT,
    
    -- Гибкие атрибуты (JSONB) — специфичные для категории
    attributes            JSONB DEFAULT '{}',
    -- {"weight_kg": 1.5, "warranty_months": 12, "country_origin": "Германия"}
    
    -- Публикация
    status                VARCHAR(20) DEFAULT 'active',
    -- active, inactive, out_of_stock, archived
    
    -- SEO
    seo_title             VARCHAR(255),
    seo_description       VARCHAR(500),
    
    views_count           INTEGER DEFAULT 0,
    
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP,
    deleted_at            TIMESTAMP
);

CREATE INDEX idx_products_parts_profile ON products(parts_profile_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_oem ON products(oem_number);
CREATE INDEX idx_products_article ON products(article_number);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_attributes ON products USING GIN(attributes);
-- Полнотекстовый поиск
CREATE INDEX idx_products_fts ON products 
    USING GIN(to_tsvector('russian', 
        coalesce(name,'') || ' ' || 
        coalesce(article_number,'') || ' ' || 
        coalesce(oem_number,'')
    ));
```

#### `cross_numbers` (Кросс-номера / аналоги)
```sql
CREATE TABLE cross_numbers (
    id          BIGSERIAL PRIMARY KEY,
    product_id  BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    brand       VARCHAR(100),               -- бренд (производитель аналога)
    number      VARCHAR(100) NOT NULL,      -- номер аналога
    is_oem      BOOLEAN DEFAULT FALSE,      -- это OEM номер (от производителя авто)
    created_at  TIMESTAMP
);

CREATE INDEX idx_cross_numbers_product ON cross_numbers(product_id);
CREATE INDEX idx_cross_numbers_number ON cross_numbers(number);
-- Важный индекс для поиска по кросс-номерам
CREATE INDEX idx_cross_numbers_search ON cross_numbers 
    USING GIN(to_tsvector('english', number));
```

#### `product_applicabilities` (Применяемость запчасти к автомобилям)
```sql
CREATE TABLE product_applicabilities (
    id                    BIGSERIAL PRIMARY KEY,
    product_id            BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    car_brand_id          BIGINT REFERENCES car_brands(id),
    car_model_id          BIGINT REFERENCES car_models(id),
    year_from             SMALLINT,
    year_to               SMALLINT,
    engine_codes          VARCHAR(255),    -- "2JZ-GE, 2JZ-GTE"
    notes                 VARCHAR(255),    -- дополнительные примечания
    created_at            TIMESTAMP
);

CREATE INDEX idx_product_applicabilities_product ON product_applicabilities(product_id);
CREATE INDEX idx_product_applicabilities_brand_model 
    ON product_applicabilities(car_brand_id, car_model_id);
```

#### `stock_items` (Остатки на складе)
```sql
CREATE TABLE stock_items (
    id              BIGSERIAL PRIMARY KEY,
    product_id      BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity        INTEGER NOT NULL DEFAULT 0,
    quantity_reserved INTEGER NOT NULL DEFAULT 0,  -- зарезервировано в заказах
    -- quantity_available = quantity - quantity_reserved
    location_code   VARCHAR(50),    -- место на складе: "A-12-3"
    updated_at      TIMESTAMP,
    UNIQUE(product_id)
);

-- Лог изменений остатков
CREATE TABLE stock_movements (
    id              BIGSERIAL PRIMARY KEY,
    product_id      BIGINT NOT NULL REFERENCES products(id),
    order_item_id   BIGINT,               -- если движение связано с заказом
    user_id         BIGINT REFERENCES users(id),
    movement_type   VARCHAR(20) NOT NULL,
    -- purchase, sale, adjustment, return, reservation, reservation_release
    quantity_before INTEGER NOT NULL,
    quantity_delta  INTEGER NOT NULL,     -- положительный = приход, отрицательный = расход
    quantity_after  INTEGER NOT NULL,
    notes           TEXT,
    created_at      TIMESTAMP
);

CREATE INDEX idx_stock_movements_product ON stock_movements(product_id);
```

#### `product_photos`
```sql
CREATE TABLE product_photos (
    id          BIGSERIAL PRIMARY KEY,
    product_id  BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    path        VARCHAR(500) NOT NULL,
    thumb_path  VARCHAR(500),
    sort_order  SMALLINT DEFAULT 0,
    is_cover    BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP
);
```

### 5.5 Service Profile Tables

#### `service_profiles`
```sql
CREATE TABLE service_profiles (
    id                    BIGSERIAL PRIMARY KEY,
    company_id            BIGINT NOT NULL REFERENCES companies(id),
    description           TEXT,
    specializations       JSONB DEFAULT '[]',
    -- ["ТО", "Кузовной ремонт", "Диагностика", "Шиномонтаж"]
    car_types             JSONB DEFAULT '["passenger"]',
    -- ["passenger", "truck", "motorcycle", "suv"]
    working_hours         JSONB DEFAULT '{}',
    -- {"mon": {"open": "09:00", "close": "20:00", "is_day_off": false}, ...}
    slot_duration_minutes SMALLINT DEFAULT 60,
    advance_booking_days  SMALLINT DEFAULT 30,  -- запись за сколько дней вперёд
    meta                  JSONB DEFAULT '{}',
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);
```

#### `service_categories`
```sql
CREATE TABLE service_categories (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    icon        VARCHAR(50),
    sort_order  INTEGER DEFAULT 0
);
```

#### `service_items` (Прайс-лист)
```sql
CREATE TABLE service_items (
    id                    BIGSERIAL PRIMARY KEY,
    uuid                  UUID DEFAULT gen_random_uuid() UNIQUE,
    service_profile_id    BIGINT NOT NULL REFERENCES service_profiles(id),
    category_id           BIGINT REFERENCES service_categories(id),
    
    name                  VARCHAR(255) NOT NULL,
    description           TEXT,
    
    -- Ценообразование
    price_type            VARCHAR(20) DEFAULT 'fixed',
    -- fixed, hourly, range, by_agreement
    price_fixed           BIGINT,          -- фиксированная цена (копейки)
    price_min             BIGINT,          -- минимальная цена диапазона
    price_max             BIGINT,          -- максимальная цена диапазона
    labor_rate            BIGINT,          -- стоимость нормо-часа
    labor_hours           DECIMAL(5,2),    -- количество нормо-часов
    
    -- Время выполнения
    duration_minutes      INTEGER,         -- оценочное время работы в минутах
    
    -- Применяемость (к каким авто)
    applicable_car_types  JSONB DEFAULT '["passenger"]',
    applicable_brands     JSONB DEFAULT '[]',  -- [] = все марки
    
    is_active             BOOLEAN DEFAULT TRUE,
    sort_order            INTEGER DEFAULT 0,
    
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);

CREATE INDEX idx_service_items_profile ON service_items(service_profile_id);
CREATE INDEX idx_service_items_category ON service_items(category_id);
```

#### `service_masters` (Мастера)
```sql
CREATE TABLE service_masters (
    id                    BIGSERIAL PRIMARY KEY,
    service_profile_id    BIGINT NOT NULL REFERENCES service_profiles(id),
    user_id               BIGINT REFERENCES users(id),  -- если есть аккаунт
    name                  VARCHAR(255) NOT NULL,
    specialization        VARCHAR(255),
    photo_path            VARCHAR(500),
    bio                   TEXT,
    rating                DECIMAL(3,2),
    is_active             BOOLEAN DEFAULT TRUE,
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);
```

#### `time_slots`
```sql
CREATE TABLE time_slots (
    id                    BIGSERIAL PRIMARY KEY,
    service_profile_id    BIGINT NOT NULL REFERENCES service_profiles(id),
    master_id             BIGINT REFERENCES service_masters(id),
    date                  DATE NOT NULL,
    start_time            TIME NOT NULL,
    end_time              TIME NOT NULL,
    status                VARCHAR(20) DEFAULT 'available',
    -- available, booked, blocked
    appointment_id        BIGINT,   -- FK добавим после создания таблицы appointments
    notes                 VARCHAR(255),
    created_at            TIMESTAMP
);

CREATE INDEX idx_time_slots_profile_date ON time_slots(service_profile_id, date);
CREATE INDEX idx_time_slots_master_date ON time_slots(master_id, date);
CREATE INDEX idx_time_slots_status ON time_slots(status);
-- Уникальность: один мастер не может иметь два слота в одно время
CREATE UNIQUE INDEX idx_time_slots_unique 
    ON time_slots(service_profile_id, master_id, date, start_time) 
    WHERE status != 'blocked';
```

#### `appointments` (Записи на сервис)
```sql
CREATE TABLE appointments (
    id                    BIGSERIAL PRIMARY KEY,
    uuid                  UUID DEFAULT gen_random_uuid() UNIQUE,
    service_profile_id    BIGINT NOT NULL REFERENCES service_profiles(id),
    time_slot_id          BIGINT NOT NULL REFERENCES time_slots(id),
    master_id             BIGINT REFERENCES service_masters(id),
    user_id               BIGINT REFERENCES users(id),   -- NULL если гость
    
    -- Данные авто клиента
    car_brand             VARCHAR(100),
    car_model             VARCHAR(100),
    car_year              SMALLINT,
    car_vin               VARCHAR(17),
    car_mileage           INTEGER,
    
    -- Выбранные услуги
    service_items         JSONB DEFAULT '[]',
    -- [{"id": 1, "name": "Замена масла", "price": 150000, "duration_min": 30}]
    
    -- Контакты (дублируем)
    contact_name          VARCHAR(255) NOT NULL,
    contact_phone         VARCHAR(20) NOT NULL,
    
    -- Стоимость
    estimated_cost        BIGINT,          -- оценочная стоимость (копейки)
    actual_cost           BIGINT,          -- фактическая после выполнения
    
    -- Статус
    status                VARCHAR(20) DEFAULT 'pending',
    -- pending, confirmed, in_progress, completed, cancelled, no_show
    
    -- Связь с заказом
    order_id              BIGINT,          -- если пришёл через единый чекаут
    
    notes                 TEXT,            -- пожелания клиента
    internal_notes        TEXT,            -- внутренние заметки мастера
    
    confirmed_at          TIMESTAMP,
    completed_at          TIMESTAMP,
    cancelled_at          TIMESTAMP,
    cancellation_reason   TEXT,
    
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);

-- Обратный FK для time_slots
ALTER TABLE time_slots ADD CONSTRAINT fk_time_slots_appointment 
    FOREIGN KEY (appointment_id) REFERENCES appointments(id);

CREATE INDEX idx_appointments_profile ON appointments(service_profile_id);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_appointments_user ON appointments(user_id);
```

### 5.6 Order & Cart Tables

#### `carts`
```sql
CREATE TABLE carts (
    id              BIGSERIAL PRIMARY KEY,
    uuid            UUID DEFAULT gen_random_uuid() UNIQUE,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    session_id      VARCHAR(100),   -- для гостевой корзины
    expires_at      TIMESTAMP,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

CREATE INDEX idx_carts_user ON carts(user_id);
CREATE INDEX idx_carts_session ON carts(session_id);
```

#### `cart_items`
```sql
CREATE TABLE cart_items (
    id                  BIGSERIAL PRIMARY KEY,
    cart_id             BIGINT NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
    itemable_id         BIGINT NOT NULL,
    itemable_type       VARCHAR(100) NOT NULL,
    -- 'Product' или 'Appointment' (polymorphic)
    quantity            INTEGER NOT NULL DEFAULT 1,
    unit_price          BIGINT NOT NULL,    -- цена на момент добавления (копейки)
    snapshot            JSONB NOT NULL DEFAULT '{}',
    -- Снимок данных: {"name": "...", "article": "...", "image": "..."}
    -- Важно: храним snapshot чтобы не ломать корзину при изменении товара
    meta                JSONB DEFAULT '{}',
    -- Для appointment: {"service_profile_id": 1, "time_slot_id": 5, "date": "2024-03-15"}
    created_at          TIMESTAMP,
    updated_at          TIMESTAMP
);
```

#### `orders`
```sql
CREATE TABLE orders (
    id              BIGSERIAL PRIMARY KEY,
    uuid            UUID DEFAULT gen_random_uuid() UNIQUE,
    order_number    VARCHAR(50) NOT NULL UNIQUE,  -- ORD-2024-00001
    user_id         BIGINT REFERENCES users(id),
    
    -- Получатель
    contact_name    VARCHAR(255) NOT NULL,
    contact_phone   VARCHAR(20) NOT NULL,
    contact_email   VARCHAR(255),
    
    -- Итоги
    items_total     BIGINT NOT NULL,    -- сумма позиций (копейки)
    discount_total  BIGINT DEFAULT 0,
    total_amount    BIGINT NOT NULL,
    
    status          VARCHAR(20) DEFAULT 'pending',
    -- pending, confirmed, processing, ready, completed, cancelled
    
    notes           TEXT,
    
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_number ON orders(order_number);
```

#### `order_items`
```sql
CREATE TABLE order_items (
    id                  BIGSERIAL PRIMARY KEY,
    order_id            BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    
    -- Привязка к конкретному магазину/сервису
    parts_profile_id    BIGINT REFERENCES parts_profiles(id),
    service_profile_id  BIGINT REFERENCES service_profiles(id),
    
    itemable_id         BIGINT NOT NULL,
    itemable_type       VARCHAR(100) NOT NULL,  -- 'Product' или 'Appointment'
    
    product_id          BIGINT REFERENCES products(id),
    appointment_id      BIGINT REFERENCES appointments(id),
    
    quantity            INTEGER NOT NULL DEFAULT 1,
    unit_price          BIGINT NOT NULL,
    total_price         BIGINT NOT NULL,
    
    snapshot            JSONB NOT NULL DEFAULT '{}',
    status              VARCHAR(20) DEFAULT 'pending',
    
    created_at          TIMESTAMP
);
```

### 5.7 Catalog Reference Tables

#### `car_brands`
```sql
CREATE TABLE car_brands (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    logo_path   VARCHAR(500),
    country     VARCHAR(100),
    is_popular  BOOLEAN DEFAULT FALSE,
    sort_order  INTEGER DEFAULT 0,
    is_active   BOOLEAN DEFAULT TRUE
);
```

#### `car_models`
```sql
CREATE TABLE car_models (
    id          BIGSERIAL PRIMARY KEY,
    brand_id    BIGINT NOT NULL REFERENCES car_brands(id),
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100),
    is_active   BOOLEAN DEFAULT TRUE,
    UNIQUE(brand_id, name)
);

CREATE INDEX idx_car_models_brand ON car_models(brand_id);
```

#### `car_generations`
```sql
CREATE TABLE car_generations (
    id          BIGSERIAL PRIMARY KEY,
    model_id    BIGINT NOT NULL REFERENCES car_models(id),
    name        VARCHAR(100),   -- "I поколение", "Restyling 2020"
    year_from   SMALLINT,
    year_to     SMALLINT,
    body_type   VARCHAR(20)
);
```

### 5.8 Billing Tables

#### `plans`
```sql
CREATE TABLE plans (
    id                    BIGSERIAL PRIMARY KEY,
    name                  VARCHAR(100) NOT NULL,
    slug                  VARCHAR(50) NOT NULL UNIQUE,
    -- basic, pro, enterprise
    price_monthly         BIGINT NOT NULL,       -- в копейках
    price_yearly          BIGINT,
    
    features              JSONB NOT NULL DEFAULT '{}',
    -- {
    --   "max_vehicles": 20,         -- null = unlimited
    --   "max_products": 500,
    --   "max_photos_per_item": 10,
    --   "auto_moderation": false,
    --   "analytics": "basic",
    --   "api_access": false
    -- }
    
    allowed_profiles      JSONB DEFAULT '["dealer"]',
    -- ["dealer", "parts", "service"]
    
    is_active             BOOLEAN DEFAULT TRUE,
    sort_order            INTEGER DEFAULT 0
);
```

#### `subscriptions`
```sql
CREATE TABLE subscriptions (
    id              BIGSERIAL PRIMARY KEY,
    company_id      BIGINT NOT NULL REFERENCES companies(id),
    plan_id         BIGINT NOT NULL REFERENCES plans(id),
    status          VARCHAR(20) DEFAULT 'active',
    -- active, past_due, cancelled, trial
    trial_ends_at   TIMESTAMP,
    current_period_start TIMESTAMP NOT NULL,
    current_period_end   TIMESTAMP NOT NULL,
    cancelled_at    TIMESTAMP,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);
```

#### `payments`
```sql
CREATE TABLE payments (
    id                  BIGSERIAL PRIMARY KEY,
    uuid                UUID DEFAULT gen_random_uuid() UNIQUE,
    company_id          BIGINT NOT NULL REFERENCES companies(id),
    subscription_id     BIGINT REFERENCES subscriptions(id),
    amount              BIGINT NOT NULL,
    currency            CHAR(3) DEFAULT 'RUB',
    status              VARCHAR(20) DEFAULT 'pending',
    -- pending, paid, failed, refunded
    gateway             VARCHAR(50),    -- yookassa, tinkoff, etc.
    gateway_payment_id  VARCHAR(255),
    paid_at             TIMESTAMP,
    created_at          TIMESTAMP,
    updated_at          TIMESTAMP
);
```

### 5.9 Additional Tables

#### `favorites`
```sql
CREATE TABLE favorites (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    favoriteable_id   BIGINT NOT NULL,
    favoriteable_type VARCHAR(100) NOT NULL,  -- Vehicle, Product
    created_at      TIMESTAMP,
    UNIQUE(user_id, favoriteable_id, favoriteable_type)
);
```

#### `reviews`
```sql
CREATE TABLE reviews (
    id                    BIGSERIAL PRIMARY KEY,
    user_id               BIGINT NOT NULL REFERENCES users(id),
    reviewable_id         BIGINT NOT NULL,
    reviewable_type       VARCHAR(100) NOT NULL,  -- Company, ServiceMaster, Vehicle
    rating                SMALLINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title                 VARCHAR(255),
    body                  TEXT,
    status                VARCHAR(20) DEFAULT 'pending',
    -- pending, approved, rejected
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);

CREATE INDEX idx_reviews_reviewable ON reviews(reviewable_type, reviewable_id);
```

#### `notifications` (хранение in-app уведомлений)
```sql
-- Используем стандартную таблицу Laravel Notifications
-- notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at)
-- Уже создаётся Laravel migration: php artisan notifications:table
```

---

## 6. Roles & Permissions (RBAC)

### 6.1 Используемый пакет

**`spatie/laravel-permission`** — стандарт для Laravel RBAC.

Концепция: права **скопированы на уровень компании и профиля**. Нельзя просто выдать роль `sales_manager` без привязки к конкретной компании и её DealerProfile.

### 6.2 Реализация Tenant-Scoped Permissions

```php
// Используем team_id из spatie/laravel-permission для изоляции
// config/permission.php: 'teams' => true

// При добавлении сотрудника:
setPermissionsTeamId($companyId);
$user->assignRole('sales_manager');

// Проверка:
setPermissionsTeamId($companyId);
$user->hasRole('sales_manager');  // true только для этой компании
```

### 6.3 Список ролей

| Роль | Scope | Описание |
|---|---|---|
| `super_admin` | Global | Полный доступ к платформе |
| `tenant_owner` | Company | Владелец компании, все профили |
| `sales_manager` | Company + DealerProfile | Только работа с автомобилями |
| `storekeeper` | Company + PartsProfile | Только работа с запчастями |
| `service_advisor` | Company + ServiceProfile | Только работа с сервисом |
| `buyer` | Global | Покупатель |

### 6.4 Матрица прав (Permissions)

#### Super Admin
| Permission | Описание |
|---|---|
| `companies.view_any` | Просмотр всех компаний |
| `companies.update_any` | Редактирование любой компании |
| `companies.approve` | Одобрение регистрации |
| `companies.suspend` | Блокировка компании |
| `billing.manage` | Управление тарифами и платежами |
| `market_map.manage` | Управление картой рынка |
| `catalog.manage` | Управление справочниками |
| `moderation.manage` | Модерация любого контента |
| `users.manage_any` | Управление любыми пользователями |
| `analytics.view_global` | Глобальная аналитика |

#### Tenant Owner
| Permission | Scope | Описание |
|---|---|---|
| `company.edit` | Own | Редактирование своей компании |
| `company.staff.manage` | Own | Управление сотрудниками |
| `business_profiles.manage` | Own | Подключение/отключение профилей |
| `dealer.*` | Own Dealer | Все права дилерского профиля |
| `parts.*` | Own Parts | Все права профиля запчастей |
| `service.*` | Own Service | Все права профиля сервиса |
| `analytics.view_own` | Own | Аналитика по своей компании |

#### Sales Manager (DealerProfile)
| Permission | Описание |
|---|---|
| `dealer.vehicles.create` | Создание объявления |
| `dealer.vehicles.edit` | Редактирование объявления |
| `dealer.vehicles.delete` | Удаление объявления (мягкое) |
| `dealer.vehicles.publish` | Публикация (если разрешено в тарифе) |
| `dealer.vehicles.mark_sold` | Отметить как проданный |
| `dealer.leads.view` | Просмотр заявок |
| `dealer.leads.manage` | Управление заявками (смена статуса, заметки) |
| `dealer.photos.manage` | Загрузка/удаление фото |

#### Storekeeper (PartsProfile)
| Permission | Описание |
|---|---|
| `parts.products.create` | Создание товара |
| `parts.products.edit` | Редактирование товара |
| `parts.products.delete` | Удаление товара |
| `parts.stock.update` | Обновление остатков |
| `parts.orders.view` | Просмотр заказов |
| `parts.orders.manage` | Управление заказами |
| `parts.import` | Импорт товаров из файла |
| `parts.prices.manage` | Управление ценами |

#### Service Advisor (ServiceProfile)
| Permission | Описание |
|---|---|
| `service.items.manage` | Управление прайс-листом |
| `service.masters.manage` | Управление мастерами |
| `service.slots.manage` | Управление расписанием |
| `service.appointments.view` | Просмотр записей |
| `service.appointments.manage` | Управление записями (подтверждение, отмена) |
| `service.appointments.assign` | Назначение мастера |

#### Buyer
| Permission | Описание |
|---|---|
| `favorites.manage` | Управление избранным |
| `reviews.create` | Создание отзывов |
| `orders.create` | Оформление заказов |
| `orders.view_own` | Просмотр своих заказов |
| `appointments.create` | Создание записей в сервис |
| `appointments.view_own` | Просмотр своих записей |
| `leads.create` | Отправка заявок (тест-драйв и т.д.) |

### 6.5 Реализация проверки в коде

```php
// Policy для Vehicle
class VehiclePolicy
{
    public function create(User $user, DealerProfile $profile): bool
    {
        return $user->hasPermissionTo('dealer.vehicles.create', $profile->company_id)
            && $user->isStaffOf($profile);
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->hasPermissionTo('dealer.vehicles.edit', $vehicle->dealerProfile->company_id)
            && $vehicle->dealerProfile->company_id === $user->currentCompanyId();
    }
}

// Gate check в контроллере
public function store(Request $request, DealerProfile $profile): Response
{
    $this->authorize('create', [Vehicle::class, $profile]);
    // ...
}
```

### 6.6 Middleware для Tenant-контекста

```php
// app/Http/Middleware/SetTenantContext.php
class SetTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        if ($company = $request->user()?->currentCompany()) {
            setPermissionsTeamId($company->id);
            app()->instance('current_company', $company);
        }
        return $next($request);
    }
}
```

---

## 7. Modules & API Structure

### 7.1 Регистрация модулей

Каждый модуль регистрирует свой `ServiceProvider`:

```php
// config/app.php или bootstrap/providers.php
'providers' => [
    // ...
    Src\Company\Providers\CompanyServiceProvider::class,
    Src\Dealer\Providers\DealerServiceProvider::class,
    Src\Parts\Providers\PartsServiceProvider::class,
    Src\Service\Providers\ServiceServiceProvider::class,
    Src\Order\Providers\OrderServiceProvider::class,
    Src\Catalog\Providers\CatalogServiceProvider::class,
    Src\Billing\Providers\BillingServiceProvider::class,
    Src\MarketMap\Providers\MarketMapServiceProvider::class,
],
```

```php
// Пример: src/Dealer/Providers/DealerServiceProvider.php
class DealerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            VehicleRepositoryInterface::class,
            EloquentVehicleRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Infrastructure/Persistence/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Infrastructure/Http/routes.php');

        // Регистрация Policies
        Gate::policy(Vehicle::class, VehiclePolicy::class);

        // Регистрация Event Listeners
        Event::listen(VehiclePublished::class, IndexVehicleInSearch::class);
        Event::listen(VehiclePublished::class, SendVehiclePublishedNotification::class);
    }
}
```

### 7.2 Маршруты (Routes)

#### Публичный каталог (без авторизации)
```php
// routes/web.php

Route::get('/', [HomeController::class, 'index'])->name('home');

// Каталог авто
Route::prefix('cars')->name('cars.')->group(function () {
    Route::get('/', [VehicleCatalogController::class, 'index'])->name('index');
    Route::get('/{slug}', [VehicleCatalogController::class, 'show'])->name('show');
});

// Каталог запчастей
Route::prefix('parts')->name('parts.')->group(function () {
    Route::get('/', [PartsCatalogController::class, 'index'])->name('index');
    Route::get('/{slug}', [PartsCatalogController::class, 'show'])->name('show');
    Route::get('/search', [PartsCatalogController::class, 'search'])->name('search');
});

// Каталог сервисов
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceCatalogController::class, 'index'])->name('index');
    Route::get('/{slug}', [ServiceCatalogController::class, 'show'])->name('show');
    Route::get('/{slug}/slots', [ServiceCatalogController::class, 'slots'])->name('slots');
});

// Компании
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyCatalogController::class, 'index'])->name('index');
    Route::get('/{slug}', [CompanyCatalogController::class, 'show'])->name('show');
});

// Карта рынка
Route::get('/map', [MarketMapController::class, 'index'])->name('market-map');
```

#### Покупатель (авторизованный)
```php
Route::middleware(['auth', 'verified'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');

    // Корзина
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/items', [CartController::class, 'addItem'])->name('items.add');
        Route::patch('/items/{item}', [CartController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [CartController::class, 'removeItem'])->name('items.remove');
        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    });

    // Заказы
    Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{uuid}', [BuyerOrderController::class, 'show'])->name('orders.show');

    // Записи
    Route::get('/appointments', [BuyerAppointmentController::class, 'index'])->name('appointments.index');

    // Избранное
    Route::apiResource('/favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);

    // Профиль
    Route::get('/profile', [BuyerProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [BuyerProfileController::class, 'update'])->name('profile.update');
});

// Заявки (можно от гостя)
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
```

#### Tenant Кабинет
```php
Route::middleware(['auth', 'tenant.active', 'set.tenant.context'])
    ->prefix('cabinet')
    ->name('cabinet.')
    ->group(function () {

    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');

    // Компания
    Route::get('/company', [CompanySettingsController::class, 'show'])->name('company');
    Route::patch('/company', [CompanySettingsController::class, 'update']);

    // Сотрудники
    Route::apiResource('/staff', StaffController::class);

    // === DealerProfile ===
    Route::prefix('/dealer')
        ->name('dealer.')
        ->middleware('profile:dealer')
        ->group(function () {
            Route::get('/dashboard', [DealerDashboardController::class, 'index'])->name('dashboard');
            Route::apiResource('/vehicles', CabinetVehicleController::class);
            Route::patch('/vehicles/{vehicle}/publish', [CabinetVehicleController::class, 'publish'])->name('vehicles.publish');
            Route::patch('/vehicles/{vehicle}/sold', [CabinetVehicleController::class, 'markSold'])->name('vehicles.sold');
            Route::post('/vehicles/{vehicle}/photos', [VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
            Route::delete('/vehicles/photos/{photo}', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');
            Route::get('/leads', [CabinetLeadController::class, 'index'])->name('leads.index');
            Route::patch('/leads/{lead}', [CabinetLeadController::class, 'update'])->name('leads.update');
        });

    // === PartsProfile ===
    Route::prefix('/parts')
        ->name('parts.')
        ->middleware('profile:parts')
        ->group(function () {
            Route::get('/dashboard', [PartsDashboardController::class, 'index'])->name('dashboard');
            Route::apiResource('/products', CabinetProductController::class);
            Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import');
            Route::patch('/products/{product}/stock', [StockController::class, 'update'])->name('products.stock');
            Route::get('/orders', [CabinetPartsOrderController::class, 'index'])->name('orders.index');
            Route::patch('/orders/{order}/items/{item}', [CabinetPartsOrderController::class, 'updateItem'])->name('orders.items.update');
        });

    // === ServiceProfile ===
    Route::prefix('/service')
        ->name('service.')
        ->middleware('profile:service')
        ->group(function () {
            Route::get('/dashboard', [ServiceDashboardController::class, 'index'])->name('dashboard');
            Route::apiResource('/service-items', CabinetServiceItemController::class);
            Route::apiResource('/masters', CabinetMasterController::class);
            Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
            Route::post('/schedule/slots', [ScheduleController::class, 'generateSlots'])->name('schedule.generate');
            Route::patch('/schedule/slots/{slot}', [ScheduleController::class, 'updateSlot'])->name('schedule.slots.update');
            Route::get('/appointments', [CabinetAppointmentController::class, 'index'])->name('appointments.index');
            Route::patch('/appointments/{appointment}', [CabinetAppointmentController::class, 'update'])->name('appointments.update');
        });

    // Биллинг
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');

    // Аналитика
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});
```

#### Super Admin Panel
```php
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::apiResource('/companies', AdminCompanyController::class);
    Route::patch('/companies/{company}/approve', [AdminCompanyController::class, 'approve'])->name('companies.approve');
    Route::patch('/companies/{company}/suspend', [AdminCompanyController::class, 'suspend'])->name('companies.suspend');

    // Модерация
    Route::get('/moderation/vehicles', [ModerationController::class, 'vehicles'])->name('moderation.vehicles');
    Route::patch('/moderation/vehicles/{vehicle}', [ModerationController::class, 'moderateVehicle']);
    Route::get('/moderation/products', [ModerationController::class, 'products'])->name('moderation.products');

    // Карта рынка
    Route::apiResource('/market-zones', AdminMarketZoneController::class);
    Route::apiResource('/market-locations', AdminMarketLocationController::class);

    // Биллинг
    Route::apiResource('/plans', AdminPlanController::class);
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

    // Справочники
    Route::apiResource('/car-brands', AdminCarBrandController::class);
    Route::apiResource('/car-models', AdminCarModelController::class);
    Route::apiResource('/part-categories', AdminPartCategoryController::class);
    Route::apiResource('/service-categories', AdminServiceCategoryController::class);

    // Пользователи
    Route::apiResource('/users', AdminUserController::class);

    // Аналитика
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
});
```

#### REST API (для мобильного приложения в будущем)
```php
Route::prefix('api/v1')->middleware('api')->name('api.v1.')->group(function () {
    // Публичные
    Route::get('/cars', [Api\VehicleController::class, 'index']);
    Route::get('/cars/{uuid}', [Api\VehicleController::class, 'show']);
    Route::get('/parts', [Api\ProductController::class, 'index']);
    Route::get('/services', [Api\ServiceController::class, 'index']);
    Route::get('/companies', [Api\CompanyController::class, 'index']);

    // Авторизованные
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/cart/items', [Api\CartController::class, 'addItem']);
        Route::post('/orders', [Api\OrderController::class, 'store']);
        Route::post('/appointments', [Api\AppointmentController::class, 'store']);
        Route::post('/leads', [Api\LeadController::class, 'store']);
    });
});
```

### 7.3 Структура Middleware

```php
// Кастомные Middleware
'tenant.active'       → CheckTenantSubscription   // компания активна и подписка не истекла
'set.tenant.context'  → SetTenantContext           // устанавливает team_id для RBAC
'profile:dealer'      → CheckProfileAccess         // пользователь имеет доступ к нужному профилю
'profile:parts'       → CheckProfileAccess
'profile:service'     → CheckProfileAccess
```

---

## 8. Frontend Architecture

### 8.1 Выбранный стек: Inertia.js + Vue 3 + SSR

**Обоснование выбора:**

| Критерий | Inertia.js+Vue3 | Livewire | Next.js/Nuxt |
|---|---|---|---|
| SEO | ✅ (с SSR) | ✅ | ✅ |
| Опыт команды Laravel | ✅ Максимальный | ✅ | ⚠️ |
| SPA-опыт | ✅ | ⚠️ | ✅ |
| Сложность | Средняя | Низкая | Высокая |
| Реалтайм | ✅ (Echo) | ✅ | ✅ |
| **Вывод** | **✅ Оптимально** | Для MVP | Оверинжиниринг |

**Выбор: Inertia.js + Vue 3 + Vite + SSR через `@inertiajs/vue3` + `@inertiajs/server`**

SSR обеспечивает SEO для страниц каталога. SPA-навигация — для кабинета.

### 8.2 Frontend структура

```
resources/js/
├── app.js                          ← Точка входа клиента
├── ssr.js                          ← Точка входа SSR
├── bootstrap.js
│
├── Pages/                          ← Inertia Pages (соответствуют маршрутам)
│   ├── Home.vue                    ← Главная
│   │
│   ├── Catalog/                    ← Публичный каталог
│   │   ├── Cars/
│   │   │   ├── Index.vue           ← Список авто с фильтрами
│   │   │   └── Show.vue            ← Карточка авто
│   │   ├── Parts/
│   │   │   ├── Index.vue
│   │   │   └── Show.vue
│   │   └── Services/
│   │       ├── Index.vue
│   │       └── Show.vue
│   │
│   ├── Companies/
│   │   ├── Index.vue               ← Список компаний
│   │   └── Show.vue                ← Витрина компании
│   │
│   ├── MarketMap/
│   │   └── Index.vue               ← Интерактивная карта рынка
│   │
│   ├── Buyer/                      ← Личный кабинет покупателя
│   │   ├── Dashboard.vue
│   │   ├── Orders/
│   │   ├── Appointments/
│   │   ├── Favorites.vue
│   │   └── Profile.vue
│   │
│   ├── Cabinet/                    ← Кабинет резидента
│   │   ├── Dashboard.vue
│   │   ├── Company/
│   │   ├── Staff/
│   │   ├── Billing/
│   │   ├── Analytics/
│   │   ├── Dealer/
│   │   │   ├── Dashboard.vue
│   │   │   ├── Vehicles/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   ├── Edit.vue
│   │   │   │   └── Show.vue
│   │   │   └── Leads/
│   │   │       └── Index.vue
│   │   ├── Parts/
│   │   │   ├── Dashboard.vue
│   │   │   ├── Products/
│   │   │   ├── Orders/
│   │   │   └── Import.vue
│   │   └── Service/
│   │       ├── Dashboard.vue
│   │       ├── ServiceItems/
│   │       ├── Masters/
│   │       ├── Schedule.vue
│   │       └── Appointments/
│   │
│   └── Admin/                      ← Панель суперадмина
│       ├── Dashboard.vue
│       ├── Companies/
│       ├── Moderation/
│       ├── MarketMap/
│       ├── Billing/
│       ├── Catalog/
│       └── Analytics/
│
├── Components/                     ← Переиспользуемые компоненты
│   ├── UI/                         ← Базовые UI компоненты
│   │   ├── Button.vue
│   │   ├── Input.vue
│   │   ├── Modal.vue
│   │   ├── Dropdown.vue
│   │   ├── Badge.vue
│   │   ├── Pagination.vue
│   │   └── DataTable.vue
│   ├── Catalog/
│   │   ├── VehicleCard.vue         ← Карточка авто в списке
│   │   ├── ProductCard.vue         ← Карточка запчасти
│   │   ├── ServiceCard.vue
│   │   ├── VehicleFilters.vue      ← Боковая панель фильтров
│   │   ├── PartsFilters.vue
│   │   └── SearchBar.vue
│   ├── Vehicle/
│   │   ├── PhotoGallery.vue        ← Галерея с zoom
│   │   ├── TestDriveForm.vue
│   │   ├── CreditCalculator.vue
│   │   └── TradeInForm.vue
│   ├── Cart/
│   │   ├── CartDrawer.vue          ← Выдвижная корзина
│   │   ├── CartItem.vue
│   │   └── CheckoutForm.vue
│   ├── Service/
│   │   ├── BookingCalendar.vue     ← Выбор даты и слота
│   │   ├── RepairCalculator.vue
│   │   └── AppointmentCard.vue
│   ├── Map/
│   │   ├── MarketMap.vue           ← SVG-карта рынка
│   │   └── LocationPin.vue
│   └── Shared/
│       ├── AppLayout.vue
│       ├── CabinetLayout.vue
│       ├── AdminLayout.vue
│       └── Notifications.vue
│
├── Composables/                    ← Vue Composables
│   ├── useCart.js                  ← Логика корзины
│   ├── useSearch.js                ← Поиск через Meilisearch
│   ├── useFilters.js               ← URL-параметры фильтров
│   ├── useNotifications.js
│   └── useEcho.js                  ← Laravel Echo (WebSockets)
│
└── Stores/                         ← Pinia stores
    ├── cart.js
    ├── auth.js
    └── notifications.js
```

### 8.3 SEO Стратегия

```php
// Для каждой страницы каталога — мета-теги через Inertia Head
// VehicleCatalogController.php
return Inertia::render('Catalog/Cars/Show', [
    'vehicle' => $vehicleData,
    'seo' => [
        'title'       => "{$vehicle->year} {$brand} {$model} — купить за {$price} руб.",
        'description' => "Продажа {$brand} {$model} {$year} года, пробег {$mileage} км. {$dealer}",
        'og_image'    => $vehicle->cover_photo_url,
        'canonical'   => route('cars.show', $vehicle->slug),
        'schema_org'  => $this->buildVehicleSchema($vehicle),
    ],
]);
```

Генерация Schema.org разметки:
- `Car` — для объявлений авто
- `Product` — для запчастей
- `LocalBusiness` + `AutoRepair` — для сервисов
- `Offer` — для прайсов

### 8.4 Интерактивная Карта Рынка

Карта реализована как SVG-изображение с кликабельными элементами:
- Создаётся в Adobe Illustrator / Inkscape по планировке рынка.
- Каждый павильон/бокс — SVG `<g id="location-{id}">`.
- Vue-компонент `MarketMap.vue` вешает hover и click события.
- При клике — popup с информацией о компании/профиле.
- Цветовое кодирование: Зелёный = свободно, Синий = дилер, Жёлтый = запчасти, Красный = сервис.

---

## 9. Search & Indexing

### 9.1 Meilisearch Индексы

#### Индекс: `vehicles`
```php
// Атрибуты для поиска
'searchableAttributes' => [
    'brand_name', 'model_name', 'generation_name',
    'description', 'vin', 'dealer_name'
],
// Атрибуты для фильтрации
'filterableAttributes' => [
    'status', 'condition', 'car_brand_id', 'car_model_id',
    'year', 'price', 'mileage', 'engine_type', 'transmission',
    'drive_type', 'body_type', 'dealer_profile_id', 'legal_status'
],
// Сортировка
'sortableAttributes' => ['price', 'mileage', 'year', 'published_at', 'views_count'],

// Типичный документ
[
    'id'               => 1,
    'uuid'             => 'abc-123',
    'slug'             => 'bmw-x5-2021-abc123',
    'brand_name'       => 'BMW',
    'model_name'       => 'X5',
    'generation_name'  => 'G05',
    'year'             => 2021,
    'price'            => 7500000,
    'mileage'          => 35000,
    'condition'        => 'used',
    'engine_type'      => 'petrol',
    'transmission'     => 'automatic',
    'drive_type'       => 'awd',
    'body_type'        => 'suv',
    'cover_photo_url'  => '...',
    'dealer_name'      => 'АвтоПрестиж',
    'dealer_profile_id'=> 1,
    'status'           => 'active',
]
```

#### Индекс: `products` (запчасти) — КРИТИЧЕН
```php
// Поиск по OEM и кросс-номерам — ГЛАВНАЯ ФИЧА
'searchableAttributes' => [
    'name', 'article_number', 'oem_number',
    'cross_numbers',        // Массив всех кросс-номеров
    'brand', 'description'
],
'filterableAttributes' => [
    'status', 'category_id', 'category_path',
    'parts_profile_id', 'condition', 'part_type',
    'applicable_brand_ids', 'applicable_model_ids',
    'price_retail', 'in_stock'
],
'sortableAttributes' => ['price_retail', 'name', 'created_at'],

// Документ
[
    'id'               => 1,
    'name'             => 'Фильтр масляный',
    'article_number'   => 'OC295',
    'oem_number'       => '15400-RTA-003',
    'cross_numbers'    => ['15400RTA003', 'HU610x', 'OX345D', 'W61280'],
    'brand'            => 'MANN-FILTER',
    'category_id'      => 5,
    'category_path'    => 'filters.oil-filters',
    'price_retail'     => 75000,
    'in_stock'         => true,
    'quantity'         => 45,
    'condition'        => 'new',
    'applicable_brand_ids'  => [1, 3, 5],
    'applicable_model_ids'  => [10, 22, 38],
    'parts_profile_id' => 2,
    'store_name'       => 'АвтоДеталь+',
    'cover_photo_url'  => '...',
]
```

#### Индекс: `companies`
```php
'searchableAttributes' => ['name', 'description', 'profile_types'],
'filterableAttributes' => ['profile_types', 'status', 'zone_id'],
```

### 9.2 Индексаторы

```php
// src/Dealer/Infrastructure/Search/VehicleSearchIndexer.php
class VehicleSearchIndexer
{
    public function __construct(
        private Client $meilisearch,
    ) {}

    public function index(Vehicle $vehicle): void
    {
        $this->meilisearch->index('vehicles')
            ->addDocuments([$this->toDocument($vehicle)]);
    }

    public function delete(Vehicle $vehicle): void
    {
        $this->meilisearch->index('vehicles')
            ->deleteDocument($vehicle->id);
    }

    private function toDocument(Vehicle $vehicle): array
    {
        return [
            'id'         => $vehicle->id,
            'brand_name' => $vehicle->carBrand->name,
            // ... маппинг полей
        ];
    }
}
```

```php
// Event Listener — асинхронный (через Queue)
class IndexVehicleInSearch implements ShouldQueue
{
    public function handle(VehiclePublished $event): void
    {
        app(VehicleSearchIndexer::class)->index($event->vehicle);
    }
}
```

### 9.3 Поиск по OEM-номерам (алгоритм)

Поиск запчасти по номеру — ключевая фича. Номера хранятся в нескольких форматах:

```php
// OEM: "15400-RTA-003" → нормализуется в "15400RTA003"
// Кросс: "OC 295" → "OC295", "oc295"

class OemSearchService
{
    // Нормализация числа перед поиском
    public function normalize(string $number): string
    {
        return strtoupper(preg_replace('/[\s\-_.]/', '', $number));
    }

    public function search(string $query): SearchResults
    {
        $normalized = $this->normalize($query);

        // Поиск в Meilisearch — проверит поля oem_number и cross_numbers
        return $this->meilisearch->index('products')
            ->search($normalized, [
                'attributesToSearchOn' => ['oem_number', 'article_number', 'cross_numbers'],
                'filter' => 'status = active AND in_stock = true',
                'limit' => 50,
            ]);
    }
}
```

---

## 10. Non-Functional Requirements

### 10.1 Кэширование (Redis)

```php
// config/cache.php → driver: 'redis'
// Стратегии кэширования:

// 1. Справочники (марки, модели) — долгий TTL, редко меняются
Cache::remember('catalog:brands', 3600, fn() => CarBrand::active()->get());

// 2. Страницы каталога — средний TTL, инвалидируется при новом объявлении
Cache::tags(['vehicles', "dealer:{$dealerProfileId}"])
    ->remember("vehicles:list:{$hash}", 300, fn() => $query->paginate());

// 3. Карточка товара — кэш по UUID
Cache::tags(["vehicle:{$vehicle->uuid}"])
    ->remember("vehicle:{$vehicle->uuid}", 600, fn() => $data);

// 4. Доступные слоты сервиса — короткий TTL (конкурентный доступ!)
Cache::remember("service:{$id}:slots:{$date}", 60, fn() => $slots);

// 5. Сессии — Redis (не файловая система)
// config/session.php → driver: 'redis'
```

### 10.2 Очереди (Laravel Queues)

```php
// config/queue.php → driver: 'redis'

// Очереди по приоритету:
QUEUE_HIGH    = 'high'       // Уведомления о заказах/записях
QUEUE_DEFAULT = 'default'    // Индексация в Meilisearch, email
QUEUE_LOW     = 'low'        // Аналитика, очистка кэша

// Jobs:
IndexVehicleInSearch::class           → queue: 'default'
IndexProductInSearch::class           → queue: 'default'
SendOrderPlacedNotification::class    → queue: 'high'
SendAppointmentConfirmation::class    → queue: 'high'
SyncWithMoySklad::class              → queue: 'low'
GenerateCompanyAnalytics::class       → queue: 'low'
SendDailyDigestEmail::class           → queue: 'low'
CleanExpiredCarts::class              → queue: 'low'

// Запуск воркеров (Supervisor)
php artisan queue:work redis --queue=high,default,low --tries=3 --timeout=60
```

### 10.3 Работа с медиафайлами (Spatie Media Library)

```php
// Модели с медиа
class Vehicle extends Model
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->useDisk('s3')               // или 'public' для локальной разработки
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();     // авто-генерация размеров

        $this->addMediaCollection('videos')
            ->useDisk('s3')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Карточка в списке
        $this->addMediaConversion('thumb')
            ->width(400)->height(300)
            ->format('webp')
            ->quality(80);

        // Галерея
        $this->addMediaConversion('gallery')
            ->width(1200)->height(900)
            ->format('webp')
            ->quality(85);

        // Обложка (og:image)
        $this->addMediaConversion('og')
            ->width(1200)->height(630)
            ->format('jpeg');
    }
}
```

**Хранилище:**
- Локальная разработка: `storage/app/public` (с symlink)
- Production: AWS S3 / Yandex Object Storage / Selectel

**Ограничения:**
- Авто: максимум 30 фото, каждое < 10MB, форматы: jpg/png/webp
- Запчасти: максимум 5 фото, каждое < 5MB
- Логотипы компаний: < 2MB
- Видео: загружается как YouTube-ссылка (iframe) или прямой файл < 500MB

### 10.4 Мониторинг и Логирование

```php
// Стек мониторинга:
// - Laravel Telescope (development) → отключить на production
// - Sentry (production) — трекинг ошибок
// - Laravel Pulse (production) — метрики производительности
// - Grafana + Prometheus (опционально, для серьёзной нагрузки)

// Логирование:
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack_errors'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    'slack_errors' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'level' => 'error',
    ],
],
```

### 10.5 Производительность

| Метрика | Цель | Инструмент |
|---|---|---|
| TTFB страниц каталога | < 200ms | Redis Cache + Мeilisearch |
| LCP | < 2.5s | Responsive Images, CDN |
| Поиск по OEM | < 150ms | Meilisearch |
| API ответы | < 300ms p95 | Query optimization |
| Параллельные пользователи | 500+ | Redis Sessions, Octane |

**Laravel Octane** (опционально, для высоких нагрузок):
```
php artisan octane:install --server=frankenphp
```

### 10.6 Безопасность

```php
// 1. Rate Limiting
Route::middleware('throttle:60,1')->group(...);  // 60 req/min для API

// Специальные лимиты:
RateLimiter::for('leads', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());  // защита от спама заявок
});

RateLimiter::for('search', function (Request $request) {
    return Limit::perMinute(100)->by($request->ip());
});

// 2. CSRF Protection (Inertia автоматически)

// 3. SQL Injection — используем Eloquent/Query Builder (параметризованные запросы)

// 4. XSS — Blade/Vue автоэкранирование

// 5. Mass Assignment — $fillable на всех моделях

// 6. Авторизация — Policy на каждый ресурс

// 7. File Upload — валидация mime-type, размера, антивирус (опционально)

// 8. Sensitive Data
// - Пароли: bcrypt (Laravel default)
// - Телефоны в логах: маскировать
// - VIN в URL: использовать UUID
```

### 10.7 SEO & Performance для Поисковиков

```php
// Sitemap (spatie/laravel-sitemap)
Sitemap::create()
    ->add(Url::create('/cars')
        ->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY)
        ->setPriority(0.9))
    ->add(
        Vehicle::active()->get()->map(fn($v) => 
            Url::create("/cars/{$v->slug}")
                ->setLastModificationDate($v->updated_at)
                ->setPriority(0.8)
        )
    )
    ->writeToFile(public_path('sitemap.xml'));

// Robots.txt — закрыть кабинет и API от индексации
User-agent: *
Disallow: /cabinet/
Disallow: /admin/
Disallow: /api/
Allow: /
```

---

## 11. Integrations

### 11.1 МойСклад / 1С (синхронизация остатков запчастей)

```php
// src/Parts/Infrastructure/Import/MoySkladSyncService.php
class MoySkladSyncService
{
    // Два направления:
    // 1. Pull: импорт товаров и остатков из МойСклад → наша БД
    // 2. Push: уменьшение остатков при оформлении заказа

    public function syncProducts(PartsProfile $profile): void
    {
        // Получить список товаров из API МойСклад
        $products = $this->moySkladClient->getProducts($profile->moysklad_token);

        foreach ($products as $msSku) {
            Product::updateOrCreate(
                ['article_number' => $msSku['article']],
                [/* маппинг полей */]
            );
        }

        // Запускать по расписанию: Schedule::job(SyncMoySkladJob::class)->hourly();
    }
}
```

**Альтернатива**: импорт через Excel/CSV (более простой вариант на старте):
```php
// src/Parts/Infrastructure/Import/ExcelProductImporter.php
// Использует maatwebsite/excel пакет
```

### 11.2 Платёжная система

```php
// Платежи для биллинга резидентов (подписки)
// Интеграция: ЮKassa / Тинькофф Касса

// src/Billing/Infrastructure/Payments/YookassaGateway.php
class YookassaGateway implements PaymentGatewayInterface
{
    public function createPayment(PaymentIntent $intent): PaymentResponse
    {
        // POST /v3/payments
    }

    public function handleWebhook(array $payload): void
    {
        // Обработка callback от ЮKassa
        // Обновление статуса Payment → активация Subscription
    }
}
```

### 11.3 SMS / Email Уведомления

```php
// SMS: смс.ру / SMSC.ru
// Email: Mailtrap (dev), SMTP / Postmark (prod)

// Уведомления:
AppointmentConfirmedNotification::class  // SMS + Email
OrderPlacedNotification::class           // SMS + Email
LeadCreatedNotification::class           // Email → менеджеру + Email → клиенту
VehiclePublishedNotification::class      // Email → владельцу
CompanyApprovedNotification::class       // Email → владельцу компании
```

### 11.4 WebSockets (Realtime)

```php
// Pusher / Laravel Reverb (self-hosted, рекомендуется)
// Использование:
// 1. Уведомления в реальном времени (новая заявка, новый заказ)
// 2. Обновление статуса заказа в реальном времени для покупателя

// Event:
class NewLeadReceived implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("company.{$this->lead->dealer_profile->company_id}"),
        ];
    }
}

// Vue Composable:
// useEcho.js
const echo = new Echo({ broadcaster: 'reverb', ... });
echo.private(`company.${companyId}`)
    .listen('NewLeadReceived', (e) => {
        notificationStore.add(e.lead);
    });
```

---

## 12. Step-by-step Implementation Plan

### Принципы реализации

- **Строгий порядок**: каждая фаза строится на фундаменте предыдущей.
- **Итерации**: каждая фаза заканчивается рабочим, демонстрируемым результатом.
- **Тесты**: Unit и Feature тесты пишутся параллельно с кодом (не после).
- **Не оверинжинирить**: добавлять сложность только когда она нужна.

---

### Фаза 0: Инфраструктура и Каркас (2-3 дня)

#### 0.1 Создание проекта
```bash
# Создать проект
composer create-project laravel/laravel automarket
cd automarket

# Основные пакеты
composer require \
    inertiajs/inertia-laravel \
    tightenco/ziggy \
    spatie/laravel-permission \
    spatie/laravel-media-library \
    spatie/laravel-sluggable \
    spatie/laravel-query-builder \
    laravel/sanctum \
    meilisearch/meilisearch-php \
    maatwebsite/excel \
    league/flysystem-aws-s3-v3

# Dev зависимости
composer require --dev \
    laravel/telescope \
    pestphp/pest \
    pestphp/pest-plugin-laravel \
    laravel/pint

# Frontend
npm install vue@latest @inertiajs/vue3 @vitejs/plugin-vue \
    @inertiajs/server pinia ziggy-js \
    tailwindcss @tailwindcss/vite \
    lucide-vue-next
```

#### 0.2 Конфигурация окружения
```env
# .env
APP_NAME="AutoMarket"
APP_ENV=local
APP_URL=http://automarket.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=automarket

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=your_master_key

FILESYSTEM_DISK=local  # s3 на production
```

#### 0.3 Настройка autoload для src/

```json
// composer.json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Src\\": "src/"
    }
}
```

#### 0.4 Настройка spatie/laravel-permission для teams

```php
// config/permission.php
'teams' => true,
'team_foreign_key' => 'company_id',
```

#### 0.5 Базовые миграции

Порядок создания миграций (соблюдать из-за FK):
```
1. create_users_table          (стандартная Laravel)
2. create_car_brands_table
3. create_car_models_table
4. create_car_generations_table
5. create_part_categories_table
6. create_service_categories_table
7. create_market_zones_table
8. create_companies_table
9. create_business_profiles_table
10. create_market_locations_table
11. create_company_users_table
12. create_dealer_profiles_table
13. create_parts_profiles_table
14. create_service_profiles_table
15. create_vehicles_table
16. create_vehicle_photos_table
17. create_dealer_leads_table
18. create_products_table
19. create_cross_numbers_table
20. create_product_applicabilities_table
21. create_product_photos_table
22. create_stock_items_table
23. create_stock_movements_table
24. create_service_items_table
25. create_service_masters_table
26. create_time_slots_table
27. create_appointments_table (без FK на time_slots.appointment_id)
28. alter_time_slots_add_appointment_fk_table
29. create_carts_table
30. create_cart_items_table
31. create_orders_table
32. create_order_items_table
33. create_plans_table
34. create_subscriptions_table
35. create_payments_table
36. create_favorites_table
37. create_reviews_table
38. create_notifications_table (php artisan notifications:table)
39. create_jobs_table          (php artisan queue:table)
40. create_permission_tables   (spatie)
```

---

### Фаза 1: Shared & Company Module (1 неделя)

#### 1.1 Value Objects
```php
// src/Shared/Domain/ValueObjects/Money.php
final class Money
{
    public function __construct(
        private readonly int $amount,      // в копейках
        private readonly string $currency = 'RUB'
    ) {
        if ($amount < 0) throw new \InvalidArgumentException('Amount cannot be negative');
    }

    public static function fromRubles(float $rubles): self
    {
        return new self((int) round($rubles * 100));
    }

    public function toRubles(): float { return $this->amount / 100; }
    public function format(): string { return number_format($this->toRubles(), 0, '.', ' ') . ' ₽'; }
    public function add(Money $other): self { return new self($this->amount + $other->amount); }
}

// src/Shared/Domain/ValueObjects/VIN.php
final class VIN
{
    public function __construct(private readonly string $value)
    {
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/i', $value)) {
            throw new \InvalidArgumentException("Invalid VIN: {$value}");
        }
    }
    public function getValue(): string { return strtoupper($this->value); }
}

// src/Shared/Domain/ValueObjects/OemNumber.php
final class OemNumber
{
    public function __construct(private readonly string $value) {}

    public function normalize(): string
    {
        return strtoupper(preg_replace('/[\s\-_.]/', '', $this->value));
    }
}
```

#### 1.2 Company Module
- Создать модель `Company` с отношениями `businessProfiles()`, `users()`, `locations()`
- Создать модель `BusinessProfile` с полиморфным отношением `profileable()`
- `CompanyRepository` с методами: `findBySlug`, `findByStatus`, `paginate`
- `RegisterCompanyHandler` — создаёт компанию, отправляет email "на модерации"
- `ApproveCompanyHandler` — одобряет, создаёт профили в БД, уведомляет
- Контроллеры: `RegisterCompanyController`, `CompanySettingsController`
- Inertia-страницы: `Pages/Companies/Register.vue`, `Pages/Companies/Show.vue`

#### 1.3 Тесты Company модуля

```php
// tests/Feature/Company/RegisterCompanyTest.php
it('creates company in pending status', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/companies/register', [
            'name' => 'Тест Авто',
            'inn'  => '1234567890',
            // ...
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('companies', [
        'name'   => 'Тест Авто',
        'status' => 'pending',
    ]);
});
```

---

### Фаза 2: Authentication & RBAC (3 дня)

#### 2.1 Auth
```bash
# Базовая auth через Laravel Fortify или вручную
php artisan make:controller Auth/LoginController
php artisan make:controller Auth/RegisterController
# Inertia страницы: Auth/Login.vue, Auth/Register.vue
```

#### 2.2 RBAC конфигурация
- Создать seeder для ролей и прав (из матрицы в разделе 6)
- Middleware `SetTenantContext`, `CheckProfileAccess`
- Policies: `VehiclePolicy`, `ProductPolicy`, `AppointmentPolicy`, `CompanyPolicy`

```php
// database/seeders/RolesAndPermissionsSeeder.php
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Создать роли
        $superAdmin  = Role::create(['name' => 'super_admin',     'guard_name' => 'web']);
        $tenantOwner = Role::create(['name' => 'tenant_owner',    'guard_name' => 'web']);
        $salesManager = Role::create(['name' => 'sales_manager',  'guard_name' => 'web']);
        $storekeeper  = Role::create(['name' => 'storekeeper',    'guard_name' => 'web']);
        $serviceAdvisor = Role::create(['name' => 'service_advisor', 'guard_name' => 'web']);

        // Создать права и назначить ролям...
        $superAdmin->givePermissionTo(Permission::all());
        
        $salesManager->givePermissionTo([
            'dealer.vehicles.create',
            'dealer.vehicles.edit',
            // ...
        ]);
    }
}
```

---

### Фаза 3: Catalog Module (3 дня)

- Миграции и модели: `CarBrand`, `CarModel`, `CarGeneration`, `PartCategory`, `ServiceCategory`
- Seeder с популярными марками (Toyota, BMW, Mercedes, VW, Kia, Hyundai, ...)
- Seeder с категориями запчастей (иерархия: Фильтры → Масляные фильтры, ...)
- API-эндпоинты для каскадных dropdown'ов:
  ```
  GET /api/v1/catalog/brands
  GET /api/v1/catalog/brands/{id}/models
  GET /api/v1/catalog/models/{id}/generations
  GET /api/v1/catalog/part-categories (дерево)
  ```
- Meilisearch: создать индексы `vehicles`, `products`, `companies`

---

### Фаза 4: Dealer Module (1.5 недели)

#### 4.1 Backend
- Модели: `DealerProfile`, `Vehicle`, `VehiclePhoto`, `DealerLead`
- `VehicleRepository` с методами фильтрации (`FilterVehiclesQuery`)
- Use Cases: `CreateVehicle`, `UpdateVehicle`, `PublishVehicle`, `MarkVehicleSold`, `CreateLead`
- `VehicleSearchIndexer` + Event Listener `IndexVehicleInSearch`
- Конвертации медиа в `Vehicle` модели (thumb, gallery, og)
- Фильтрация через `spatie/laravel-query-builder`

#### 4.2 Frontend — Публичный каталог
- `Pages/Catalog/Cars/Index.vue` — список с фильтрами, сортировкой, пагинацией
- `Pages/Catalog/Cars/Show.vue` — карточка авто
- `Components/Catalog/VehicleCard.vue` — компонент карточки
- `Components/Catalog/VehicleFilters.vue` — фильтры (sidebar)
- `Components/Vehicle/PhotoGallery.vue` — галерея с zoom (Swiper.js)
- `Components/Vehicle/CreditCalculator.vue` — калькулятор кредита (Vue, локальный)
- `Components/Vehicle/TestDriveForm.vue` — форма заявки

#### 4.3 Frontend — Кабинет дилера
- `Pages/Cabinet/Dealer/Vehicles/Index.vue` — список с управлением
- `Pages/Cabinet/Dealer/Vehicles/Create.vue` — форма добавления авто
- `Pages/Cabinet/Dealer/Vehicles/Edit.vue`
- `Pages/Cabinet/Dealer/Leads/Index.vue` — заявки

#### 4.4 Тесты
```php
// tests/Feature/Dealer/VehicleTest.php
it('sales manager can create vehicle', ...);
it('storekeeper cannot create vehicle', ...);
it('vehicle is indexed in meilisearch on publish', ...);
it('published vehicle appears in catalog', ...);
```

---

### Фаза 5: Parts Module (2 недели)

#### 5.1 Backend
- Модели: `PartsProfile`, `Product`, `CrossNumber`, `ProductApplicability`, `StockItem`, `StockMovement`
- `ProductRepository` с поиском по OEM/кросс-номерам
- `OemSearchService` — нормализация и поиск
- `StockService` — обновление остатков, резервирование
- Use Cases: `CreateProduct`, `UpdateProduct`, `UpdateStock`, `SearchByOem`, `ImportProducts`
- `ExcelProductImporter` — импорт из Excel файла (maatwebsite/excel)
- `ProductSearchIndexer` — индексация в Meilisearch с кросс-номерами

#### 5.2 Критически важная логика: индексация кросс-номеров
```php
// При сохранении Product → пересобрать документ в Meilisearch
// Документ включает array всех кросс-номеров (нормализованных)
private function toSearchDocument(Product $product): array
{
    $crossNumbers = $product->crossNumbers
        ->map(fn($cn) => (new OemNumber($cn->number))->normalize())
        ->toArray();

    return [
        'id'           => $product->id,
        'oem_number'   => $product->oem_number
            ? (new OemNumber($product->oem_number))->normalize()
            : null,
        'cross_numbers' => $crossNumbers,
        // ...
    ];
}
```

#### 5.3 Frontend
- `Pages/Catalog/Parts/Index.vue` — список с умной строкой поиска
- `Pages/Catalog/Parts/Show.vue` — карточка запчасти с таблицей применяемости
- `Components/Parts/OemSearchBar.vue` — поиск с автодополнением
- `Components/Parts/ApplicabilityTable.vue` — для каких авто подходит
- `Pages/Cabinet/Parts/Products/` — CRUD продуктов в кабинете
- `Pages/Cabinet/Parts/Import.vue` — загрузка Excel-файла

---

### Фаза 6: Service Module (1.5 недели)

#### 6.1 Backend
- Модели: `ServiceProfile`, `ServiceItem`, `ServiceMaster`, `TimeSlot`, `Appointment`
- `SlotGeneratorService` — генерация слотов на N дней вперёд по расписанию
- `AvailabilityService` — получение свободных слотов (учёт конкуренции)
- `AppointmentService` — создание записи + блокировка слота (транзакция + пессимистическая блокировка)
- Use Cases: `CreateAppointment`, `ConfirmAppointment`, `GetAvailableSlots`, `CalculateRepairCost`

#### 6.2 Критически важная логика: конкурентное бронирование

```php
// src/Service/Application/UseCases/CreateAppointment/CreateAppointmentHandler.php
public function handle(CreateAppointmentCommand $cmd): Appointment
{
    return DB::transaction(function () use ($cmd) {
        // Пессимистическая блокировка слота
        $slot = TimeSlot::where('id', $cmd->slotId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($slot->status !== 'available') {
            throw new SlotAlreadyBookedException("Слот уже занят");
        }

        $appointment = Appointment::create([...]);
        
        $slot->update([
            'status'         => 'booked',
            'appointment_id' => $appointment->id,
        ]);

        $this->events->dispatch(new AppointmentCreated($appointment));
        
        return $appointment;
    });
}
```

#### 6.3 Frontend
- `Pages/Catalog/Services/Show.vue` — витрина сервиса + прайс
- `Components/Service/BookingCalendar.vue` — выбор даты (vue-cal или собственный)
- `Components/Service/SlotPicker.vue` — выбор слота времени
- `Components/Service/RepairCalculator.vue` — калькулятор стоимости
- `Pages/Cabinet/Service/Schedule.vue` — недельный календарь (FullCalendar.js)
- `Pages/Cabinet/Service/Appointments/Index.vue` — список записей

---

### Фаза 7: Order & Cart Module (1 неделя)

#### 7.1 Backend
- Модели: `Cart`, `CartItem`, `Order`, `OrderItem`
- `CartService` — добавление (Product + Appointment), merge guest → auth корзина
- `CheckoutService` — валидация корзины, резервирование остатков, создание Order+Appointment
- `OrderNumberGenerator` — генерация номеров ORD-2024-XXXXX

#### 7.2 Единый чекаут (самая сложная часть)

```php
// src/Order/Application/UseCases/ProcessCheckout/ProcessCheckoutHandler.php
public function handle(ProcessCheckoutCommand $cmd): Order
{
    return DB::transaction(function () use ($cmd) {
        $cart = $this->carts->findWithItems($cmd->cartId);
        
        // 1. Разделить items по типам
        $productItems = $cart->items->where('itemable_type', 'Product');
        $appointmentItems = $cart->items->where('itemable_type', 'Appointment');
        
        // 2. Проверить и зарезервировать остатки запчастей
        foreach ($productItems as $item) {
            $this->stockReservation->reserve($item->product_id, $item->quantity);
        }
        
        // 3. Создать/подтвердить записи в сервис
        $appointments = [];
        foreach ($appointmentItems as $item) {
            $appointments[] = $this->slotBooking->book($item->meta['time_slot_id'], $cmd->contactData);
        }
        
        // 4. Создать заказ
        $order = $this->orders->create([...]);
        
        // 5. Создать позиции заказа
        foreach ($productItems as $item) {
            $this->orderItems->createFromCartItem($order, $item);
        }
        foreach ($appointments as $appointment) {
            $this->orderItems->createFromAppointment($order, $appointment);
        }
        
        // 6. Очистить корзину
        $cart->delete();
        
        // 7. Уведомления
        $this->events->dispatch(new OrderPlaced($order));
        
        return $order;
    });
}
```

#### 7.3 Frontend
- `Components/Cart/CartDrawer.vue` — drawer справа, открывается по клику
- `Pages/Buyer/Cart/Checkout.vue` — страница оформления заказа
- Pinia store `useCart` — состояние корзины, синхронизация с сервером

---

### Фаза 8: Admin Panel & Market Map (1 неделя)

#### 8.1 Admin Panel
- `Pages/Admin/Dashboard.vue` — статистика платформы
- `Pages/Admin/Companies/Index.vue` — список с фильтрами, кнопки одобрить/заблокировать
- `Pages/Admin/Moderation/` — очередь объявлений
- `Pages/Admin/Billing/` — тарифы и платежи
- `Pages/Admin/Catalog/` — управление справочниками

#### 8.2 Интерактивная карта рынка
- Создать SVG-схему площадки (в Figma/Illustrator с экспортом в SVG)
- `Components/Map/MarketMap.vue` — Vue компонент на основе SVG
- `Pages/MarketMap/Index.vue` — публичная карта
- `Pages/Admin/MarketMap/Editor.vue` — редактор для SuperAdmin

---

### Фаза 9: Notifications, WebSockets, Finalizing (1 неделя)

#### 9.1 Уведомления
```bash
# Настройка Laravel Reverb
php artisan install:broadcasting
```

- Настроить каналы: Email (Mailtrap → SMTP), SMS (smс.ру)
- Написать все Notification классы (см. раздел 11.3)
- Добавить `Notifications.vue` компонент с badge в шапку
- Real-time уведомления через Laravel Echo + Reverb

#### 9.2 Аналитика для Tenant
Дашборды через простые SQL-агрегаты (без отдельного аналитического движка):
```php
// Для DealerProfile:
- Количество активных объявлений
- Просмотры за период
- Заявки по типам (тест-драйв, кредит, trade-in)
- Конверсия (заявки/просмотры)

// Для PartsProfile:
- Заказы и выручка за период
- ТОП-10 товаров по продажам
- Движение остатков

// Для ServiceProfile:
- Записи за период (выполнено/отменено)
- Выручка по услугам
- Загрузка мастеров
```

#### 9.3 Финальные задачи
- Настроить очереди Supervisor (конфиг файл)
- Настроить расписание (Laravel Schedule): генерация слотов, синхронизация склада, очистка просроченных корзин, генерация Sitemap
- Настроить логирование Sentry
- Написать документацию по деплою
- Performance аудит: N+1 запросы, индексы БД, кэши

---

### Фаза 10: SEO & Launch Preparation (3 дня)

#### 10.1 SEO
- Schema.org разметка для всех типов страниц
- Мета-теги (title, description, og:*) для каждого контроллера
- Sitemap.xml (spatie/laravel-sitemap) + автообновление
- Canonical URLs для фильтров каталога
- Robots.txt
- Open Graph изображения

#### 10.2 Производительность
- Eager loading: аудит всех List-запросов на N+1 (Laravel Telescope → N+1 warnings)
- Добавить Database-индексы по результатам `EXPLAIN ANALYZE`
- Настроить Response Caching для публичных страниц каталога
- Оптимизация изображений: WebP конвертация, lazy loading в браузере

#### 10.3 Деплой
```bash
# Production deploy checklist:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=CatalogSeeder
php artisan scout:import "Src\Dealer\Domain\Models\Vehicle"
php artisan scout:import "Src\Parts\Domain\Models\Product"
```

---

## Приложения

### Приложение А: Enums (PHP 8.1+ Backed Enums)

```php
enum CompanyStatus: string {
    case PENDING   = 'pending';
    case ACTIVE    = 'active';
    case SUSPENDED = 'suspended';
    case REJECTED  = 'rejected';
}

enum VehicleStatus: string {
    case DRAFT   = 'draft';
    case PENDING = 'pending';
    case ACTIVE  = 'active';
    case SOLD    = 'sold';
    case ARCHIVED = 'archived';
    case REJECTED = 'rejected';
}

enum VehicleCondition: string {
    case NEW     = 'new';
    case USED    = 'used';
    case DAMAGED = 'damaged';
}

enum EngineType: string {
    case PETROL   = 'petrol';
    case DIESEL   = 'diesel';
    case HYBRID   = 'hybrid';
    case ELECTRIC = 'electric';
    case GAS      = 'gas';
}

enum TransmissionType: string {
    case MANUAL    = 'manual';
    case AUTOMATIC = 'automatic';
    case ROBOT     = 'robot';
    case CVT       = 'cvt';
}

enum LeadType: string {
    case TEST_DRIVE = 'test_drive';
    case CREDIT     = 'credit';
    case TRADE_IN   = 'trade_in';
    case CALLBACK   = 'callback';
}

enum AppointmentStatus: string {
    case PENDING     = 'pending';
    case CONFIRMED   = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED   = 'completed';
    case CANCELLED   = 'cancelled';
    case NO_SHOW     = 'no_show';
}

enum OrderStatus: string {
    case PENDING    = 'pending';
    case CONFIRMED  = 'confirmed';
    case PROCESSING = 'processing';
    case READY      = 'ready';
    case COMPLETED  = 'completed';
    case CANCELLED  = 'cancelled';
}

enum BusinessProfileType: string {
    case DEALER  = 'dealer';
    case PARTS   = 'parts';
    case SERVICE = 'service';
}

enum SlotStatus: string {
    case AVAILABLE = 'available';
    case BOOKED    = 'booked';
    case BLOCKED   = 'blocked';
}
```

### Приложение Б: Composer пакеты (полный список)

```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^11.0",
        "laravel/sanctum": "^4.0",
        "laravel/reverb": "^1.0",
        "inertiajs/inertia-laravel": "^1.0",
        "tightenco/ziggy": "^2.0",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-media-library": "^11.0",
        "spatie/laravel-sluggable": "^3.5",
        "spatie/laravel-query-builder": "^5.0",
        "spatie/laravel-sitemap": "^7.0",
        "meilisearch/meilisearch-php": "^1.0",
        "laravel/scout": "^10.0",
        "maatwebsite/excel": "^3.1",
        "league/flysystem-aws-s3-v3": "^3.0",
        "intervention/image-laravel": "^1.0"
    },
    "require-dev": {
        "laravel/telescope": "^5.0",
        "pestphp/pest": "^2.0",
        "pestphp/pest-plugin-laravel": "^2.0",
        "laravel/pint": "^1.0",
        "fakerphp/faker": "^1.23"
    }
}
```

### Приложение В: NPM пакеты (package.json)

```json
{
    "dependencies": {
        "vue": "^3.4",
        "@inertiajs/vue3": "^1.0",
        "pinia": "^2.1",
        "ziggy-js": "^2.0",
        "swiper": "^11.0",
        "@fullcalendar/vue3": "^6.0",
        "@fullcalendar/daygrid": "^6.0",
        "@fullcalendar/timegrid": "^6.0",
        "laravel-echo": "^1.15",
        "pusher-js": "^8.0"
    },
    "devDependencies": {
        "@vitejs/plugin-vue": "^5.0",
        "vite": "^5.0",
        "tailwindcss": "^3.4",
        "@tailwindcss/forms": "^0.5",
        "@tailwindcss/typography": "^0.5",
        "autoprefixer": "^10.4"
    }
}
```

### Приложение Г: Переменные окружения (production)

```env
APP_NAME="AutoMarket"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://automarket.ru
APP_KEY=base64:...

DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=automarket_prod
DB_USERNAME=automarket
DB_PASSWORD=strong_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your-redis-host
REDIS_PORT=6379
REDIS_PASSWORD=redis_password

MEILISEARCH_HOST=http://your-meilisearch-host:7700
MEILISEARCH_KEY=your_master_key

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=ru-central1
AWS_BUCKET=automarket-media
AWS_URL=https://storage.yandexcloud.net/automarket-media
AWS_ENDPOINT=https://storage.yandexcloud.net

MAIL_MAILER=smtp
MAIL_HOST=smtp.postmarkapp.com
MAIL_PORT=587
MAIL_USERNAME=your_postmark_token
MAIL_PASSWORD=your_postmark_token

SENTRY_LARAVEL_DSN=https://...@sentry.io/...

REVERB_APP_ID=automarket
REVERB_APP_KEY=your_reverb_key
REVERB_APP_SECRET=your_reverb_secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/...
```

---

*Конец Технического Задания*

*Документ содержит полное описание архитектуры, схемы базы данных, ролевой модели, структуры модулей и пошагового плана реализации платформы AutoMarket Portal. Все решения приняты в пользу прагматичного, масштабируемого и поддерживаемого кода без излишней абстракции.*
