# SMS Gateway API

A Laravel-based SMS Gateway that routes messages from multiple client projects to different SMS providers through a single, unified API endpoint.

---



## Architecture Overview



---

## Installation

### 1. Clone & Install

```bash
git clone git@github.com:Jamshid-Mamatov/sms-gateway-api.git
cd sms-gateway
composer install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sms_gateway
DB_USERNAME=root
DB_PASSWORD=secret

QUEUE_CONNECTION=database
```

### 3. Database

```bash
php artisan migrate
php artisan db:seed        # Creates demo providers + projects
```

### 4. Register Middleware

In `app/Http/Kernel.php`, add to `$middlewareAliases`:

```php
'api.key' => \App\Http\Middleware\AuthenticateApiKey::class,
```

### 5. Run the Application

```bash
# Terminal 1 — web server
php artisan serve

# Terminal 2 — queue worker (required for SMS processing)
php artisan queue:work --tries=3

# Optional: create the jobs table first if using database queue
php artisan queue:table && php artisan migrate
```

---

## API Reference

### Base URL
```
http://localhost:8000/api
```

---

### Admin: Providers

> Manage SMS provider configurations. Protect these routes with admin auth in production.

#### List Providers
```http
GET /api/providers
```

#### Create Provider
```http
POST /api/providers
Content-Type: application/json

{
  "name": "Eskiz Production",
  "driver": "eskiz",
  "config": {
    "login": "email@example.com",
    "password": "secret",
    "from": "4546"
  },
  "is_active": true
}
```
**Supported drivers:** `fake` · `eskiz` · `playmobile`

#### Update Provider
```http
PUT /api/providers/{id}
```

#### Delete Provider
```http
DELETE /api/providers/{id}
```
> Fails if the provider has attached projects.

---

### Admin: Projects

#### List Projects
```http
GET /api/projects
```

#### Create Project
```http
POST /api/projects
Content-Type: application/json

{
  "name": "E-Commerce",
  "description": "Order notifications",
  "provider_id": 1
}
```
**Response includes the generated `api_key` — save it!**

#### Update Project (change provider, name, etc.)
```http
PUT /api/projects/{id}
Content-Type: application/json

{
  "provider_id": 2
}
```

#### Regenerate API Key
```http
POST /api/projects/{id}/regenerate-key
```

---

### Client SMS API

All endpoints require the project API key:
```
X-Api-Key: sk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

#### Send SMS
```http
POST /api/sms/send
X-Api-Key: sk_...
Content-Type: application/json

{
  "phones": ["+998901234567", "+998901234568"],
  "message": "Your order #1234 has been confirmed!"
}
```

**Response `202 Accepted`:**
```json
{
  "success": true,
  "message": "2 SMS message(s) queued for delivery.",
  "data": [
    { "id": 1, "phone": "+998901234567", "status": "pending" },
    { "id": 2, "phone": "+998901234568", "status": "pending" }
  ]
}
```

#### SMS History
```http
GET /api/sms/history?status=sent&phone=998901&from=2024-01-01&to=2024-12-31&per_page=20
X-Api-Key: sk_...
```

**Filter params:**

| Param | Type | Description |
|---|---|---|
| `status` | string | `pending` · `sent` · `delivered` · `failed` |
| `phone` | string | Partial phone number match |
| `from` | date | Start date `Y-m-d` |
| `to` | date | End date `Y-m-d` |
| `per_page` | int | Items per page (max 100, default 15) |

#### Get Single SMS
```http
GET /api/sms/{id}
X-Api-Key: sk_...
```

---

## SMS Status Flow

```
pending → sent → delivered
        ↘ failed
```

| Status | Meaning |
|---|---|
| `pending` | Created, waiting in queue |
| `sent` | Successfully delivered to provider |
| `delivered` | Provider confirmed delivery (webhook) |
| `failed` | All retry attempts exhausted |

---

## Adding a New Provider

1. Create `app/Services/Providers/TwilioProvider.php` implementing `SmsProviderInterface`
2. Register the driver in `ProviderFactory::make()`:
   ```php
   'twilio' => new TwilioProvider($config),
   ```
3. Add `'twilio'` to the allowed values in `StoreProviderRequest`

---
