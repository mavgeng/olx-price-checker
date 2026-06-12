# OLX Price Checker

Сервіс для стеження за зміною ціни оголошень на [OLX.ua](https://www.olx.ua). Користувач підписується на оголошення за посиланням, підтверджує email, а сервіс у фоновому режимі періодично перевіряє ціну і надсилає лист, якщо вона змінилася.

## Схема роботи

```
                ┌──────────────────┐
  POST /api/subscriptions │  SubscriptionController│
                └─────────┬─────────┘
                          │
                          ▼
                ┌──────────────────┐
                │ CreateSubscription │  - нормалізує URL оголошення
                │      (Action)      │  - дістає external_id (ad-id) зі сторінки
                └─────────┬─────────┘
                          │  знаходить/створює
            ┌─────────────┼──────────────┐
            ▼             ▼              ▼
        Advert       Subscriber    AdvertSubscription
     (зовнішнє ID,   (email,         (звʼязка
      ціна, валюта)   email_verified)  advert <-> subscriber)
                          │
                          │ якщо subscriber новий
                          ▼
              SendSubscriberVerificationEmail (Job)
                          │
                          ▼
              Mail: SubscriberVerificationMail
                          │
              GET /api/subscribers/{id}/verify/{hash}
                          │
                          ▼
                  email_verified_at = now()

──────────────────────────────────────────────────────────

   Scheduler (every 5 minutes)
            │
            ▼
   adverts:check (Console Command)
            │ вибирає активні Advert,
            │ що мають хоча б одного
            │ підтвердженого підписника
            ▼
   CheckAdvertData (Job, queue=checks)
            │
            ▼
   AdvertFetcher -> OLX offers API
            │
   ціна не змінилась? -> оновити last_checked_at
   ціна змінилась?     -> оновити Advert + dispatch
            │
            ▼
   NotifyPriceChange (Job, queue=notifications)
            │
            ▼
   Mail: AdvertPriceChangedMail -> усім підтвердженим підписникам
```

## Технології

- **PHP 8.4 / Laravel 13** - основний фреймворк
- **MySQL 8** - основне зберігання даних (адверти, підписники, підписки)
- **Redis** - драйвер кешу та черг
- **Laravel Horizon** - моніторинг і управління фоновими черг(`checks`, `notifications`, `default`)
- **schedule:work** (окремий контейнер `scheduler`) - запускає `adverts:check` щохвилини
- **Mailpit** - перехоплення вихідних листів у dev-середовищі
- **Laravel Pint** - лінтер/форматер коду (`make pint`)
- **PHPUnit** - тести (Feature + Unit), покриття бізнес-логіки

Все запускається в Docker (`nginx`, `php-fpm`, `php-cli`, `horizon`, `scheduler`, `mysql`, `redis`, `phpmyadmin`, `mailpit`, `composer`).

## Як отримуємо ціну з OLX

Ціна береться через офіційний (мобільний) API OLX, а не через парсинг HTML сторінки оголошення:

```
GET {OLX_OFFERS_API_URL}{external_id}/
```

де `OLX_OFFERS_API_URL` за замовчуванням `https://www.olx.ua/api/v1/offers/` (`src/config/olx.php`, перевизначається через `.env`).

`external_id` (ID оголошення на OLX) ми не вимагаємо від користувача - він автоматично дістається невеликим парсингом HTML-сторінки оголошення (`AdvertUrlParser`): сторінка завантажується один раз під час підписки, з витягується ID.

Далі вся подальша перевірка ціни йде вже через API OLX за цим `external_id` - без повторного парсингу сторінки.

## Підтвердження email

Після створення підписки, якщо підписник новий (раніше не підтверджував email), йому надсилається лист (`SubscriberVerificationMail`) з підписаним посиланням, дійсним 24 години:

```
GET /api/subscribers/{subscriber}/verify/{hash}
```

Після переходу за посиланням у `subscribers.email_verified_at` записується поточний час. Лише підтверджені підписники отримують сповіщення про зміну ціни, і лише оголошення з підтвердженими підписниками перевіряються на зміну ціни.

## API

| Метод | Маршрут | Опис |
|---|---|---|
| `POST` | `/api/subscriptions` | Підписка на оголошення (`url`, `email`) |
| `GET` | `/api/subscribers/{subscriber}/verify/{hash}` | Підтвердження email підписника |

### `POST /api/subscriptions`

```json
{
  "url": "https://www.olx.ua/d/uk/obyavlenie/...",
  "email": "user@example.com"
}
```

Валідація (`StoreSubscriptionRequest`):
- `url` - обовʼязковий, валідний URL, лише `https`, лише хост `www.olx.ua`
- `email` - обовʼязковий, валідний email

Відповідь `201 Created`:

```json
{
  "id": "...ulid...",
  "message": "..."
}
```

(текст повідомлення залежить від того, чи потрібне підтвердження email)

### Модель даних

- **Advert** - `external_id`, `url`, `title`, `last_price` (в копійках), `currency`, `is_active`, `last_checked_at`
- **Subscriber** - `email` (унікальний), `email_verified_at`
- **AdvertSubscription** - звʼязка `subscriber_id` ↔ `advert_id` (унікальна пара, cascade-видалення)

## Запуск проєкту

Потрібен Docker та Docker Compose.

```bash
make init
```

Після запуску:
- застосунок: http://localhost:8000
- phpMyAdmin: http://localhost:8080
- Mailpit (перегляд листів): http://localhost:8025
- Horizon (моніторинг черг): http://localhost:8000/horizon

Інші корисні команди:

```bash
make php     # консоль контейнера php-cli
make pint    # перевірка/форматування коду (Laravel Pint)
make down    # зупинити контейнери
make logs    # логи всіх сервісів
```

## Тести

Написані Feature- та Unit-тести (PHPUnit), що покривають:
- API підписки та підтвердження email (`tests/Feature/Api/*`)
- бізнес-логіку створення підписки (`tests/Unit/Actions/Subscription/CreateSubscriptionTest.php`)
- джоби перевірки ціни та розсилки сповіщень (`tests/Unit/Jobs/*`)
- сервіси роботи з OLX - парсинг URL та запит до API (`tests/Unit/Services/Olx/*`)

Запуск тестів:

```bash
make php
php artisan test tests/
```

## Альтернативи та компроміси

- **Парсинг HTML сторінки vs OLX API.** Повний парсинг HTML для кожної перевірки ціни - повільний, нестабільний (залежить від розмітки сторінки) і важче масштабується. Натомість використовується API OLX для отримання ціни, а HTML-парсинг застосовується лише одноразово при підписці - щоб дістати `external_id` (`ad-id`) з URL оголошення.
- **Черги Redis + Horizon vs синхронне виконання.** Перевірка цін і відправка листів винесені в окремі черги (`checks`, `notifications`), щоб не блокувати HTTP-запити та дати retry/backoff (3 спроби, 10/30/60с) при тимчасових збоях OLX API чи поштового сервера. Horizon дає зручний моніторинг та керування цими черг.
- **Дедублікація оголошень.** Замість підписки "користувач → URL" окремо для кожного, оголошення зберігаються в єдиній таблиці `adverts` за `external_id`, а звʼязок з підписниками - через окрему таблицю `advert_subscriptions` (many-to-many). Це гарантує, що одне оголошення перевіряється і обробляється лише один раз, незалежно від кількості підписників.