# LiteCard Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shakewellagency/litecard-laravel.svg?style=flat-square)](https://packagist.org/packages/shakewellagency/litecard-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/shakewellagency/litecard-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/shakewellagency/litecard-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/shakewellagency/litecard-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/shakewellagency/litecard-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/shakewellagency/litecard-laravel.svg?style=flat-square)](https://packagist.org/packages/shakewellagency/litecard-laravel)

A comprehensive Laravel package for seamless integration with LiteCard digital membership card services. This package provides a complete solution for managing digital membership cards, templates, user authentication, and card lifecycle management.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Basic Card Operations](#basic-card-operations)
  - [Authentication](#authentication)
  - [Template Management](#template-management)
  - [User Integration](#user-integration)
  - [API Controllers](#api-controllers)
- [Advanced Features](#advanced-features)
- [Configuration Reference](#configuration-reference)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)
- [Contributing](#contributing)
- [Support](#support)

## Features

### 🎯 Core Functionality
- **Digital Card Management**: Create, retrieve, update, and delete digital membership cards
- **Template System**: Access and manage card templates with customizable layouts
- **Smart Authentication**: Automatic token management with intelligent caching
- **Status Management**: Control card lifecycle with ACTIVE, INACTIVE, and DELETED states

### 🚀 Advanced Features  
- **User Integration**: Optional Eloquent model for tracking user-card relationships
- **RESTful API**: Built-in controllers and routes for API integration
- **Caching System**: Intelligent token caching for optimal performance
- **Activity Logging**: Comprehensive logging with configurable channels
- **Error Handling**: Robust exception handling with detailed error messages
- **Validation**: Request validation for all API endpoints
- **Database Integration**: Optional database tracking with migrations

### 🛡️ Enterprise Ready
- **Security**: Token-based authentication with secure credential management
- **Performance**: Optimized caching and efficient API calls
- **Scalability**: Designed for high-volume card operations
- **Monitoring**: Built-in logging and activity tracking
- **Testing**: Comprehensive test suite with PHPUnit/Pest

## Requirements

- **PHP**: ^8.2
- **Laravel**: ^10.0 | ^11.0
- **Dependencies**:
  - `illuminate/support`: ^10.0 | ^11.0
  - `illuminate/http`: ^10.0 | ^11.0
  - `illuminate/database`: ^10.0 | ^11.0
  - `illuminate/cache`: ^10.0 | ^11.0
  - `illuminate/log`: ^10.0 | ^11.0
  - `guzzlehttp/guzzle`: ^7.0

## Installation

### Step 1: Install Package

```bash
composer require shakewellagency/litecard-laravel
```

The package will automatically register its service provider thanks to Laravel's package auto-discovery.

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=litecard-config
```

### Step 3: Configure Environment

Add your LiteCard credentials to your `.env` file:

```env
# Required Configuration
LITECARD_BASE_URL=https://api.litecard.com
LITECARD_USERNAME=your_username
LITECARD_PASSWORD=your_password

# Optional Configuration
LITECARD_TOKEN_CACHE_TTL=86300
LITECARD_TIMEOUT=15
LITECARD_LOGGING_ENABLED=true
LITECARD_LOGGING_CHANNEL=stack
```

### Step 4: Database Setup (Optional)

If you want to track user-card relationships in your database:

```bash
php artisan vendor:publish --tag=litecard-migrations
php artisan migrate
```

This creates a `user_lite_cards` table for tracking card assignments to users.

## Configuration

### Environment Variables

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `LITECARD_BASE_URL` | Yes | `https://bff-api.demo.litecard.io` | LiteCard API base URL |
| `LITECARD_USERNAME` | Yes | - | Your LiteCard API username |
| `LITECARD_PASSWORD` | Yes | - | Your LiteCard API password |
| `LITECARD_TOKEN_CACHE_TTL` | No | `86300` | Token cache TTL (24h - 5min) |
| `LITECARD_TIMEOUT` | No | `15` | HTTP request timeout in seconds |
| `LITECARD_LOGGING_ENABLED` | No | `true` | Enable/disable operation logging |
| `LITECARD_LOGGING_CHANNEL` | No | `stack` | Laravel logging channel to use |

### Configuration File

The published config file (`config/litecard.php`) provides detailed configuration options:

```php
<?php

return [
    // API Configuration
    'base_url' => env('LITECARD_BASE_URL', 'https://bff-api.demo.litecard.io'),
    'username' => env('LITECARD_USERNAME'),
    'password' => env('LITECARD_PASSWORD'),
    'timeout' => env('LITECARD_TIMEOUT', 15),

    // Caching Configuration  
    'token_cache_ttl' => env('LITECARD_TOKEN_CACHE_TTL', 86300),

    // Logging Configuration
    'logging' => [
        'enabled' => env('LITECARD_LOGGING_ENABLED', true),
        'channel' => env('LITECARD_LOGGING_CHANNEL', 'stack'),
    ],

    // User Model (for database integration)
    'user_model' => App\Models\User::class,
];
```

## Usage

### Basic Card Operations

#### Creating Cards

```php
use Shakewell\Litecard\Card;

$card = new Card();

// Method 1: Flexible array-based creation
$response = $card->create([
    'templateId' => 'template-123',
    'cardPayload' => [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'memberNumber' => 'M123456',
        'expiry' => '2024-12-31',
        'phone' => '+1234567890',
        'dateOfBirth' => '1990-01-15',
        'membershipType' => 'Premium',
    ],
    'options' => [
        'emailInvitationEnabled' => true,
        'smsNotification' => false,
    ]
]);

// Method 2: Backward-compatible method
$response = $card->createCard(
    templateId: 'template-123',
    firstName: 'John',
    lastName: 'Doe', 
    email: 'john.doe@example.com',
    expiry: '2024-12-31',
    emailInvitation: true
);

// Handle response
if ($response->successful()) {
    $cardData = $response->json();
    $cardId = $cardData['cardId'] ?? $cardData['id'];
    
    echo "Card created successfully: {$cardId}";
} else {
    echo "Error: " . $response->body();
}
```

#### Retrieving Cards

```php
use Shakewell\Litecard\Card;
use Shakewell\Litecard\LiteCardException;

$card = new Card();

try {
    // Method 1: Get Response object
    $response = $card->get('card-id-123');
    
    if ($response->successful()) {
        $cardData = $response->json();
        echo "Card Status: " . $cardData['status'];
    }
    
    // Method 2: Get array directly (throws exception on error)
    $cardData = $card->getCard('card-id-123');
    echo "Member: {$cardData['firstName']} {$cardData['lastName']}";
    
} catch (LiteCardException $e) {
    Log::error('Failed to retrieve card', [
        'card_id' => 'card-id-123',
        'error' => $e->getMessage()
    ]);
}
```

#### Updating Card Status

```php
use Shakewell\Litecard\Card;
use Shakewell\Litecard\Enums\CardStatusEnum;

$card = new Card();

// Activate a card
$response = $card->status('card-id-123', CardStatusEnum::ACTIVE->value);

// Deactivate a card  
$response = $card->status('card-id-123', CardStatusEnum::INACTIVE->value);

// Delete a card
$response = $card->status('card-id-123', CardStatusEnum::DELETED->value);

if ($response->successful()) {
    echo "Card status updated successfully";
} else {
    echo "Failed to update status: " . $response->body();
}
```

### Authentication

The package handles authentication automatically, but you can also manage tokens manually:

```php
use Shakewell\Litecard\Authenticator;

// Get cached token (recommended - handles caching automatically)
$token = Authenticator::token();

// Manual authentication (for advanced use cases)
$authenticator = new Authenticator();
$authenticator->integrate();

if ($authenticator->getResponse()->successful()) {
    $token = $authenticator->getToken();
    echo "Authentication successful";
} else {
    echo "Authentication failed";
}
```

### Template Management

```php
use Shakewell\Litecard\Services\TemplateService;

$templateService = new TemplateService();

// Get all available templates
$templates = $templateService->getTemplates();

foreach ($templates as $template) {
    echo "Template: {$template['name']} (ID: {$template['id']})\n";
}

// Get specific template details
$templateId = 'template-123';
$template = $templateService->getTemplate($templateId);

echo "Template Name: {$template['name']}";
echo "Template Description: {$template['description']}";
```

### User Integration

When using the optional database integration:

```php
use Shakewell\Litecard\Models\UserLiteCard;

// Track card assignment to user
$userCard = UserLiteCard::create([
    'user_id' => auth()->id(),
    'email' => 'user@example.com', 
    'card_id' => 'card-123',
    'template_id' => 'template-456',
    'status' => 'active',
]);

// Query user cards
$activeCards = UserLiteCard::active()
    ->byUserId(auth()->id())
    ->get();

// Find cards by email
$userCards = UserLiteCard::byEmail('user@example.com')->get();

// Access user relationship
foreach ($userCards as $userCard) {
    $user = $userCard->user;
    echo "Card belongs to: {$user->name}";
}
```

### API Controllers

The package includes ready-to-use API controllers with built-in routes:

#### Available Endpoints

```bash
# Authentication
POST   /api/v1/litecard/auth/login

# Templates
GET    /api/v1/litecard/templates
GET    /api/v1/litecard/templates/{templateId}

# Cards
POST   /api/v1/litecard/cards
GET    /api/v1/litecard/cards/{cardId}
POST   /api/v1/litecard/cards/{cardId}/status
```

#### Using the API

**Authentication:**
```bash
curl -X POST http://your-app.com/api/v1/litecard/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

**Create Card:**
```bash
curl -X POST http://your-app.com/api/v1/litecard/cards \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "templateId": "template-123",
    "cardPayload": {
      "firstName": "John",
      "lastName": "Doe",
      "email": "john@example.com",
      "expiry": "2024-12-31"
    },
    "options": {
      "emailInvitationEnabled": true
    }
  }'
```

## Advanced Features

### Custom Facades

Create a facade for easier access:

```php
// In AppServiceProvider.php
use Shakewell\Litecard\Card;

public function register()
{
    $this->app->bind('litecard', function () {
        return new Card();
    });
}

// Create facade class
use Illuminate\Support\Facades\Facade;

class LiteCard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'litecard';
    }
}

// Usage
$response = LiteCard::create([...]);
```

### Custom User Models

Configure a custom user model:

```php
// config/litecard.php
'user_model' => App\Models\CustomUser::class,
```

### Event Listeners

Listen for card creation events:

```php
// In EventServiceProvider.php
use Shakewell\Litecard\Events\CardCreated;

protected $listen = [
    CardCreated::class => [
        App\Listeners\SendWelcomeEmail::class,
    ],
];
```

### Custom HTTP Client

Customize the HTTP client behavior:

```php
use Illuminate\Support\Facades\Http;
use Shakewell\Litecard\Authenticator;

// Custom timeout and retry logic
$token = Authenticator::token();

$response = Http::timeout(30)
    ->retry(3, 1000)
    ->withToken($token)
    ->post(config('litecard.base_url') . '/api/v1/card', $data);
```

### Logging Integration

The package integrates with Laravel's logging system:

```php
// Logs are automatically created for:
// - Authentication attempts
// - Card operations
// - API errors
// - Status changes

// Custom logging
use Illuminate\Support\Facades\Log;

Log::channel(config('litecard.logging.channel'))->info('Custom card operation', [
    'card_id' => $cardId,
    'operation' => 'custom_action',
    'user_id' => auth()->id(),
]);
```

## Configuration Reference

### Complete Configuration Options

```php
<?php

return [
    // API Configuration
    'base_url' => env('LITECARD_BASE_URL', 'https://bff-api.demo.litecard.io'),
    'username' => env('LITECARD_USERNAME'),
    'password' => env('LITECARD_PASSWORD'),
    'timeout' => env('LITECARD_TIMEOUT', 15),

    // Caching Configuration
    'token_cache_ttl' => env('LITECARD_TOKEN_CACHE_TTL', 86300), // 24 hours - 5 minutes

    // Logging Configuration
    'logging' => [
        'enabled' => env('LITECARD_LOGGING_ENABLED', true),
        'channel' => env('LITECARD_LOGGING_CHANNEL', 'stack'),
        'level' => env('LITECARD_LOGGING_LEVEL', 'info'),
    ],

    // User Integration
    'user_model' => env('LITECARD_USER_MODEL', \App\Models\User::class),
    
    // Database Configuration
    'database' => [
        'track_user_cards' => env('LITECARD_TRACK_USER_CARDS', true),
        'table_name' => env('LITECARD_TABLE_NAME', 'user_lite_cards'),
    ],

    // API Rate Limiting
    'rate_limiting' => [
        'enabled' => env('LITECARD_RATE_LIMITING', false),
        'max_attempts' => env('LITECARD_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('LITECARD_DECAY_MINUTES', 1),
    ],

    // Error Handling
    'error_handling' => [
        'throw_exceptions' => env('LITECARD_THROW_EXCEPTIONS', true),
        'log_errors' => env('LITECARD_LOG_ERRORS', true),
    ],
];
```

## API Reference

### Card Class Methods

| Method | Parameters | Return Type | Description |
|--------|------------|-------------|-------------|
| `create(array $data)` | `$data` - Card creation data | `Response` | Create a new card with flexible data |
| `createCard(...)` | Individual parameters | `Response` | Backward-compatible creation method |
| `get(string $cardId)` | `$cardId` - Card identifier | `Response` | Get card details as Response |
| `getCard(string $cardId)` | `$cardId` - Card identifier | `array` | Get card details as array |
| `status(string $cardId, string $status)` | `$cardId`, `$status` | `Response` | Update card status |

### Authenticator Class Methods

| Method | Parameters | Return Type | Description |
|--------|------------|-------------|-------------|
| `token()` | None | `string` | Get cached authentication token (static) |
| `integrate()` | None | `static` | Perform authentication |
| `getToken()` | None | `string` | Get current token |
| `getResponse()` | None | `Response` | Get authentication response |
| `getAccessToken()` | None | `string` | Backward-compatible token method |

### TemplateService Class Methods  

| Method | Parameters | Return Type | Description |
|--------|------------|-------------|-------------|
| `getTemplates()` | None | `array` | Get all available templates |
| `getTemplate(string $id)` | `$id` - Template identifier | `array` | Get specific template details |

### UserLiteCard Model Scopes

| Scope | Parameters | Description |
|-------|------------|-------------|
| `active()` | None | Filter active cards |
| `byEmail(string $email)` | `$email` | Filter by email address |
| `byUserId(int $userId)` | `$userId` | Filter by user ID |

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test suite
./vendor/bin/pest tests/Unit/

# Run with verbose output
./vendor/bin/pest --verbose
```

### Testing with HTTP Mocking

```php
use Illuminate\Support\Facades\Http;
use Shakewell\Litecard\Card;

// Mock successful authentication
Http::fake([
    '*/api/v1/token' => Http::response(['access_token' => 'fake-token'], 200),
    '*/api/v1/card' => Http::response([
        'cardId' => 'fake-card-123',
        'status' => 'active'
    ], 201),
]);

$card = new Card();
$response = $card->create([
    'templateId' => 'template-123',
    'cardPayload' => ['firstName' => 'Test']
]);

$this->assertTrue($response->successful());
```

### Custom Test Cases

```php
use Tests\TestCase;
use Shakewell\Litecard\Card;
use Shakewell\Litecard\LiteCardException;

class CardTest extends TestCase
{
    public function test_card_creation_success()
    {
        Http::fake([
            '*/api/v1/token' => Http::response(['access_token' => 'test-token']),
            '*/api/v1/card' => Http::response(['cardId' => '123'], 201),
        ]);

        $card = new Card();
        $response = $card->create(['templateId' => 'test']);

        $this->assertTrue($response->successful());
    }

    public function test_invalid_status_throws_exception()
    {
        $this->expectException(LiteCardException::class);
        
        $card = new Card();
        $card->status('123', 'INVALID_STATUS');
    }
}
```

## Troubleshooting

### Common Issues

#### 1. Authentication Failures

**Problem**: Getting 401 Unauthorized errors

**Solution**:
```bash
# Check your credentials
php artisan config:clear
php artisan cache:clear

# Verify environment variables
echo $LITECARD_USERNAME
echo $LITECARD_PASSWORD
```

**Debug authentication**:
```php
use Shakewell\Litecard\Authenticator;

$auth = new Authenticator();
$auth->integrate();
$response = $auth->getResponse();

if ($response->failed()) {
    dd($response->json(), $response->status());
}
```

#### 2. Token Cache Issues

**Problem**: Getting stale token errors

**Solution**:
```php
// Clear token cache manually
Cache::forget('lite_card_token');

// Or disable caching temporarily
config(['litecard.token_cache_ttl' => 0]);
```

#### 3. Card Status Validation

**Problem**: Invalid status errors

**Solution**:
```php
use Shakewell\Litecard\Enums\CardStatusEnum;

// Always use enum values
$validStatuses = CardStatusEnum::values();
// Returns: ['ACTIVE', 'INACTIVE', 'DELETED']

// Correct usage
$card->status($cardId, CardStatusEnum::ACTIVE->value);
```

#### 4. Database Migration Issues

**Problem**: Migration fails or table conflicts

**Solution**:
```bash
# Check if table already exists
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback --step=1

# Re-publish and migrate
php artisan vendor:publish --tag=litecard-migrations --force
php artisan migrate
```

#### 5. HTTP Timeout Issues

**Problem**: Requests timing out

**Solution**:
```php
// Increase timeout in config
'timeout' => env('LITECARD_TIMEOUT', 30),

// Or per-request
use Illuminate\Support\Facades\Http;

Http::timeout(60)->withToken($token)->post($url, $data);
```

### Debugging Tips

#### Enable Debug Logging

```php
// config/litecard.php
'logging' => [
    'enabled' => true,
    'channel' => 'daily',
    'level' => 'debug',
],
```

#### Inspect API Responses

```php
$response = $card->create($data);

// Log full response for debugging
Log::debug('LiteCard API Response', [
    'status' => $response->status(),
    'headers' => $response->headers(),
    'body' => $response->body(),
]);
```

#### Check Package Installation

```bash
# Verify package is installed
composer show shakewellagency/litecard-laravel

# Check service provider registration
php artisan config:cache
php artisan route:list | grep litecard
```

## Best Practices

### Security Best Practices

1. **Environment Variables**: Always store credentials in `.env` files
```env
# ✅ Good
LITECARD_USERNAME=your_username
LITECARD_PASSWORD=your_secure_password

# ❌ Never do this
$username = 'hardcoded_username';
```

2. **Token Security**: Never log or expose authentication tokens
```php
// ✅ Good
Log::info('Card created', ['card_id' => $cardId]);

// ❌ Never log tokens
Log::info('Request made', ['token' => $token]); // DON'T DO THIS
```

3. **Input Validation**: Always validate input data
```php
$validator = Validator::make($request->all(), [
    'firstName' => 'required|string|max:255',
    'lastName' => 'required|string|max:255',
    'email' => 'required|email',
    'expiry' => 'required|date|after:today',
]);
```

### Performance Best Practices

1. **Use Caching**: Let the package handle token caching
```php
// ✅ Good - uses automatic caching
$token = Authenticator::token();

// ❌ Inefficient - bypasses caching  
$auth = new Authenticator();
$token = $auth->integrate()->getToken();
```

2. **Batch Operations**: Group related operations
```php
// ✅ Good - batch creation
$cards = collect($userData)->map(function ($data) use ($card) {
    return $card->create($data);
});

// ❌ Inefficient - individual operations in loop
foreach ($userData as $data) {
    $card->create($data); // Creates new instance each time
}
```

3. **Error Handling**: Use appropriate exception handling
```php
try {
    $response = $card->create($data);
    
    if ($response->successful()) {
        // Handle success
    } else {
        // Handle API errors
        Log::warning('Card creation failed', ['response' => $response->body()]);
    }
} catch (LiteCardException $e) {
    // Handle package-specific errors
    Log::error('LiteCard error', ['error' => $e->getMessage()]);
} catch (Exception $e) {
    // Handle unexpected errors
    Log::error('Unexpected error', ['error' => $e->getMessage()]);
}
```

### Code Organization

1. **Service Classes**: Create dedicated service classes for complex operations
```php
class MembershipCardService
{
    public function createMemberCard(User $user, string $templateId): array
    {
        $card = new Card();
        
        $response = $card->create([
            'templateId' => $templateId,
            'cardPayload' => [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'memberNumber' => $this->generateMemberNumber(),
                'expiry' => $this->calculateExpiry(),
            ],
        ]);

        if ($response->successful()) {
            $this->trackUserCard($user, $response->json());
        }

        return $response->json();
    }
}
```

2. **Configuration Management**: Use configuration classes
```php
class LiteCardConfig
{
    public static function getDefaultCardPayload(User $user): array
    {
        return [
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'email' => $user->email,
            'memberNumber' => self::generateMemberNumber(),
            'expiry' => self::getDefaultExpiry(),
        ];
    }
}
```

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/shakewellagency/litecard-laravel.git

# Install dependencies
composer install

# Run tests
composer test

# Fix code style
composer format

# Run static analysis
composer analyse
```

### Contribution Guidelines

1. **Code Style**: Follow PSR-12 standards
2. **Testing**: Write tests for new features
3. **Documentation**: Update documentation for changes
4. **Commit Messages**: Use conventional commits

## Support

### Getting Help

- **Email**: developers@shakewell.agency
- **Issues**: [GitHub Issues](https://github.com/shakewellagency/litecard-laravel/issues)
- **Documentation**: [Full Documentation](https://github.com/shakewellagency/litecard-laravel/wiki)

### Reporting Issues

When reporting issues, please include:

1. **Laravel Version**: `php artisan --version`
2. **PHP Version**: `php --version`  
3. **Package Version**: `composer show shakewellagency/litecard-laravel`
4. **Error Messages**: Full error messages and stack traces
5. **Code Examples**: Minimal reproduction code

### Commercial Support

For enterprise support and custom development:
- **Website**: [shakewellagency.com](https://shakewellagency.com)
- **Email**: developers@shakewell.agency

## Security

If you discover security vulnerabilities, please email developers@shakewell.agency instead of using the issue tracker. All security vulnerabilities will be promptly addressed.

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).

## Credits

- **[Shakewell Agency](https://shakewellagency.com)** - Package development and maintenance
- **[All Contributors](../../contributors)** - Community contributions

## Changelog

### Version 1.0.0
- Initial release with comprehensive LiteCard integration
- Card creation, retrieval, and status management
- Template service with full API access
- User-card relationship tracking
- Built-in API controllers and routes
- Authentication with intelligent token caching
- Comprehensive logging and error handling
- Database migrations for user tracking
- Full test suite with PHPUnit/Pest

---

**Made with ❤️ by [Shakewell Agency](https://shakewellagency.com)**