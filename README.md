# GymSaaS - Gym Management SaaS Platform

Production-ready Gym Management SaaS built with **Laravel** featuring complete **Razorpay Payment System**, subscription billing, and automated transfers.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)](https://php.net)
[![Razorpay](https://img.shields.io/badge/Razorpay-Subscriptions-02042B?style=flat-square)](https://razorpay.com)

## Overview
Multi-tenant SaaS for gym owners to manage members, subscriptions, payments and invoices. Handles two distinct payment flows with automated 100% transfers to gym owners via Razorpay Linked Accounts.

## Key Features
- **Subscription Management** - Gym Owner → Platform billing via Razorpay Subscriptions API
- **Direct Payments** - Member → Gym Owner with 100% instant transfer (0% commission)
- **Linked Accounts** - Automated Razorpay Linked Account creation & verification for each gym
- **Invoice System** - PDF generation, status tracking (paid/partial/unpaid), payment history
- **Async Transfers** - Queue-based `TransferPaymentJob` for reliable payment transfers
- **Real-time Events** - `PaymentCaptured` broadcasting
- **Webhook Handling** - Secure signature verification for Razorpay events

## Tech Stack
**Backend:** Laravel, PHP 8.2, MySQL, Eloquent ORM
**Payments:** Razorpay API (Subscriptions, Orders, Transfers, Linked Accounts)
**Queue & Events:** Laravel Queues, Jobs, Events/Listeners
**Other:** Blade, Vite

## Architecture
```
Controllers: SubscriptionController, PaymentController, RazorpayAccountController
Models: GymSubscription, RazorpayAccount, Payment, Invoice (with transfer tracking)
Services: RazorpayService (centralized API wrapper)
Jobs: TransferPaymentJob (async transfer processing)
Events: PaymentCaptured
Middleware: Auth, Signature Verification
```

## Payment Flows

### 1. Gym Owner → Platform (Subscription)
Gym subscribes to SaaS platform → `POST /api/subscription/create` → Razorpay Subscription → `subscription.charged` webhook → Activate gym

### 2. Member → Gym Owner (Direct - No Commission)
Member pays gym → `POST /api/payment/create-order` → Payment captured → `TransferPaymentJob` → 100% transferred to gym's Linked Account → `payment.captured` webhook

## Installation

```bash
# 1. Clone & Install
git clone https://github.com/Riyaz7364/gymsaas
cd gymsaas
composer install
npm install

# 2. Env Setup
cp .env.example .env
php artisan key:generate

# Add to .env
RAZORPAY_KEY_ID=your_key_id
RAZORPAY_KEY_SECRET=your_key_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret

# 3. Database & Queue
php artisan migrate
php artisan queue:work
npm run build
```

## API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/subscription/create` | Create gym subscription |
| POST | `/api/payment/create-order` | Create Razorpay order |
| POST | `/api/payment/verify` | Verify payment signature |
| POST | `/api/payment/transfer` | Manual transfer retry |
| POST | `/api/razorpay/account/create` | Create gym Linked Account |
| GET | `/api/razorpay/account/status` | Get Linked Account status |
| POST | `/api/razorpay/webhook` | Razorpay webhook handler |

## Webhook Events Handled
- `subscription.charged` - Renew/activate subscription
- `payment.captured` - Mark payment & trigger transfer
- `payment.failed` - Log & notify

> Webhook route verifies `X-Razorpay-Signature` using `RAZORPAY_WEBHOOK_SECRET`

## Security
- Razorpay signature verification for all payments & webhooks
- CSRF protection & Auth middleware on protected routes
- Comprehensive error logging & failed transfer retry

## Frontend Integration
```html
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="{{ asset('js/razorpay-checkout.js') }}"></script>
```

## License
MIT
