<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Gym SaaS Payment System

This application includes a production-ready payment system using Razorpay with the following features:

### Payment Flows

1. **Gym Owner → Platform (Subscription)**: Gym owners subscribe to the platform using Razorpay Subscriptions API
2. **Member → Gym Owner (NO Commission)**: Members pay gyms directly with 100% transfer to gym owner's linked account

### Key Components

- **Controllers**: SubscriptionController, PaymentController, RazorpayAccountController
- **Models**: GymSubscription, RazorpayAccount, Payment (with transfer tracking), Invoice
- **Services**: RazorpayService for all API interactions
- **Jobs**: TransferPaymentJob for asynchronous payment transfers
- **Events**: PaymentCaptured for real-time notifications
- **Routes**: API endpoints for payments, subscriptions, webhooks, and invoice management

### Setup Instructions

1. **Install Dependencies**:

    ```bash
    composer install
    npm install
    ```

2. **Environment Configuration**:
   Add these to your `.env` file:

    ```env
    RAZORPAY_KEY_ID=your_razorpay_key_id
    RAZORPAY_KEY_SECRET=your_razorpay_key_secret
    RAZORPAY_WEBHOOK_SECRET=your_webhook_secret
    ```

3. **Database Setup**:

    ```bash
    php artisan migrate
    ```

4. **Queue Configuration**:

    ```bash
    php artisan queue:work
    ```

5. **Frontend Integration**:
   Include the Razorpay checkout script in your payment pages:
    ```html
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('js/razorpay-checkout.js') }}"></script>
    ```

### API Endpoints

- `POST /api/subscription/create` - Create gym subscription
- `POST /api/payment/create-order` - Create payment order
- `POST /api/payment/verify` - Verify payment
- `POST /api/payment/transfer` - Manual transfer (if needed)
- `POST /api/razorpay/account/create` - Create linked account
- `GET /api/razorpay/account/status` - Get account status
- `POST /api/razorpay/webhook` - Webhook handler

### Invoice & Payment History Features

When the `online_payments` module is enabled, additional features become available:

#### Invoice Management

- **PDF Generation**: Download invoices as PDF with professional formatting
- **Payment Tracking**: View all payments associated with an invoice
- **Status Management**: Track paid, partial, and unpaid invoice statuses

#### Payment History

- **Comprehensive History**: View all payment transactions with filtering
- **Transfer Status**: Monitor Razorpay transfer status to gym accounts
- **Retry Transfers**: Manually retry failed transfers
- **Advanced Filtering**: Filter by status, method, member, and date range

#### Navigation

- Payment History appears in Finance menu when `online_payments` module is active
- PDF download button available on invoice detail pages

### Webhook Events

The system handles these Razorpay webhook events:

- `subscription.charged`
- `payment.captured`
- `payment.failed`

### Security Features

- Signature verification for payments and webhooks
- CSRF protection on API endpoints
- Authentication middleware on protected routes
- Comprehensive error logging

### Testing

Run the test suite:

```bash
php artisan test
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
