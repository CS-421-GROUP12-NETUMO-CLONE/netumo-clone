# 🌐 Netumo Clone – Website Monitoring System

A full-stack capstone project for CS 421 that mimics key features of Netumo:

- HTTP/HTTPS uptime checks
- SSL & domain expiry monitoring
- Email/Slack alerts
- REST API with JWT
- Dockerized microservices
- CI/CD with GitHub Actions + EC2
- Vue 3 dashboard

---

## 📦 Tech Stack

- **Backend**: Laravel 12 (API + Scheduler + Queued Workers)
- **Frontend**: Vue 3 + Tailwind CSS
- **Database**: MySQL
- **Queue**: Redis
- **Containerization**: Docker + Docker Compose
- **CI/CD**: GitHub Actions + Docker Hub + AWS EC2
- **Docs**: Swagger/OpenAPI

---

## 🗂 Features

### 🔁 Monitoring
- Pings each target URL every 5 minutes
- Logs status code and latency
- Detects 2x failures to trigger alert

### 🔐 SSL & Domain Checks
- Daily SSL certificate check via OpenSSL
- Domain expiry via WHOIS
- Alerts when ≤ 14 days remaining

### 🔔 Notifications
- Email (via Mailtrap)
- Slack webhook alerts

### 🧩 API Endpoints (JWT-secured)
| Method | Endpoint              | Description            |
|--------|-----------------------|------------------------|
| GET    | `/api/targets`        | List targets           |
| POST   | `/api/targets`        | Add target             |
| GET    | `/api/status/{id}`    | Latest status          |
| GET    | `/api/history/{id}`   | 24h latency history    |
| GET    | `/api/alerts`         | List alerts            |

📖 See: [`/api/documentation`](http://your-api-url/api/documentation)

---

## ⚙️ Setup

### 🔧 Backend (Laravel)

```bash
git clone https://github.com/your-org/netumo-clone.git
cd netumo-clone
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan queue:work
php artisan schedule:work
