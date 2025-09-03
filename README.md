# LiteCard Laravel Package

A comprehensive Laravel package for integrating with LiteCard digital membership card service.

## Features

- **Card Management**: Create, update, and retrieve digital membership cards
- **Template Management**: Access and manage card templates
- **Authentication**: Secure token-based authentication with LiteCard API
- **User Integration**: Optional user-card relationship management
- **Caching**: Automatic token caching for performance
- **Logging**: Configurable activity logging
- **Database**: Optional database integration with migrations

## Installation

```bash
composer require shakewellagency/litecard-laravel
```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=litecard-config
```

Add your LiteCard credentials to your `.env` file:

```env
LITECARD_BASE_URL=https://api.litecard.com
LITECARD_USERNAME=your_username
LITECARD_PASSWORD=your_password

# Optional configuration
LITECARD_TOKEN_CACHE_TTL=86300
LITECARD_TIMEOUT=15
LITECARD_LOGGING_ENABLED=true
LITECARD_LOGGING_CHANNEL=stack
```

## Database Migration (Optional)

If you want to use the optional user-card relationship tracking:

```bash
php artisan vendor:publish --tag=litecard-migrations
php artisan migrate
```

## Usage

### Basic Card Operations

```php
use Shakewell\Litecard\Card;

// Create a new card
$card = new Card();

$response = $card->create([
    'templateId' => 'template-123',
    'cardPayload' => [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'memberNumber' => 'M123456',
        'expiry' => '2024-12-31',
    ],
    'options' => [
        'emailInvitationEnabled' => true,
    ]
]);

// Update card status
$response = $card->status('card-id', 'active');

// Get card details
$response = $card->get('card-id');
```

### Using Template Service

```php
use Shakewell\Litecard\Services\TemplateService;

$templateService = new TemplateService();

// Get all templates
$templates = $templateService->getTemplates();

// Get specific template
$template = $templateService->getTemplate('template-id');
```

### Using Controllers (API Routes)

The package includes optional API routes. If you want to use them, the routes will be automatically loaded:

- `POST /api/v1/litecard/auth/login` - Authentication
- `GET /api/v1/litecard/templates` - Get templates  
- `POST /api/v1/litecard/cards` - Create card
- `GET /api/v1/litecard/cards/{card}` - Get card
- `POST /api/v1/litecard/cards/{card}/status` - Update card status

### Authentication

```php
use Shakewell\Litecard\Authenticator;

// Get cached token
$token = Authenticator::token();

// Manual authentication
$auth = new Authenticator();
$auth->integrate();
$token = $auth->getToken();
```

## Advanced Usage

### Using Facades (Optional)

You can create facades if needed:

```php
// In your AppServiceProvider
$this->app->bind('litecard', function () {
    return new \Shakewell\Litecard\Card();
});

// Create facade
class LiteCard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'litecard';
    }
}
```

### Custom User Model

Configure a custom user model in the config:

```php
// config/litecard.php
'user_model' => App\Models\CustomUser::class,
```

### Error Handling

```php
use Shakewell\Litecard\LiteCardException;

try {
    $response = $card->create($data);
} catch (LiteCardException $e) {
    // Handle LiteCard specific errors
    Log::error('LiteCard error: ' . $e->getMessage());
}
```

## Testing

```bash
composer test
```

## Configuration Options

| Option | Default | Description |
|--------|---------|-------------|
| `base_url` | `https://bff-api.demo.litecard.io` | LiteCard API base URL |
| `username` | `null` | LiteCard API username |
| `password` | `null` | LiteCard API password |
| `token_cache_ttl` | `86300` | Token cache TTL in seconds |
| `timeout` | `15` | HTTP request timeout |
| `logging.enabled` | `true` | Enable activity logging |
| `logging.channel` | `stack` | Log channel to use |

## License

MIT License. See [LICENSE.md](LICENSE.md) for details.

## Contributing

Please see [CONTRIBUTING.md] for details on how to contribute.

## Security

If you discover any security-related issues, please email security@shakewellagency.com instead of using the issue tracker.

## Credits

- [Shakewell Agency](https://shakewellagency.com)
- [Ehsan Tavakoli](https://github.com/ehsantavakoli)
- [All Contributors](../../contributors)
