# 🧪 Testing Guide

Complete guide to running and writing tests for TheTradeVisor.

## 🎯 Overview

TheTradeVisor uses **PHPUnit** for testing with Laravel's testing framework. Tests are isolated in a separate database to prevent production data loss.

## ✅ Test Database Setup

### Separate Test Database

Tests run on a **separate PostgreSQL database** (`thetradevisor_test`) to ensure:
- ✅ Production data is never affected
- ✅ Tests can run safely anytime
- ✅ No risk of data loss
- ✅ Clean state for each test run

### Configuration

The test database is configured in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="pgsql"/>
<env name="DB_DATABASE" value="thetradevisor_test"/>
```

### Initial Setup

If you haven't set up the test database yet:

```bash
# Create test database
sudo -u postgres psql -c "CREATE DATABASE thetradevisor_test;"

# Grant permissions
sudo -u postgres psql -d thetradevisor_test -c "
  GRANT ALL PRIVILEGES ON DATABASE thetradevisor_test TO tradevisor_user;
  GRANT ALL PRIVILEGES ON SCHEMA public TO tradevisor_user;
  ALTER DATABASE thetradevisor_test OWNER TO tradevisor_user;
"
```

**Note**: Replace `tradevisor_user` with your database username from `.env`

## 🚀 Running Tests

### Run All Tests

```bash
php artisan test
```

Expected output:
```
Tests:    25 passed (59 assertions)
Duration: 2.55s
```

### Run Specific Test Suite

```bash
# Run only feature tests
php artisan test --testsuite=Feature

# Run only unit tests
php artisan test --testsuite=Unit
```

### Run Specific Test File

```bash
php artisan test tests/Feature/Auth/PasswordResetTest.php
```

### Run Specific Test Method

```bash
php artisan test --filter=test_password_can_be_reset_with_valid_token
```

### Run with Coverage

```bash
php artisan test --coverage
```

### Run in Parallel

```bash
php artisan test --parallel
```

## 📁 Test Structure

```
tests/
├── Feature/              # Feature/integration tests
│   ├── Auth/            # Authentication tests
│   │   ├── AuthenticationTest.php
│   │   ├── PasswordResetTest.php
│   │   ├── RegistrationTest.php
│   │   └── ...
│   ├── ExampleTest.php
│   └── ProfileTest.php
└── Unit/                 # Unit tests
    └── ExampleTest.php
```

## ✍️ Writing Tests

### Feature Test Example

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_dashboard(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/dashboard');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }
}
```

### Unit Test Example

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GeoIPService;

class GeoIPServiceTest extends TestCase
{
    public function test_can_get_country_from_ip(): void
    {
        $service = new GeoIPService();
        
        $result = $service->getCountryFromIP('8.8.8.8');
        
        $this->assertEquals('US', $result['country_code']);
        $this->assertEquals('United States', $result['country_name']);
    }
}
```

## 🔄 Database Refresh

### RefreshDatabase Trait

Use `RefreshDatabase` to reset the database between tests:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
    
    // Database will be migrated and reset for each test
}
```

### DatabaseTransactions Trait

Use `DatabaseTransactions` for faster tests (rollback instead of migrate):

```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MyTest extends TestCase
{
    use DatabaseTransactions;
    
    // Changes will be rolled back after each test
}
```

## 🏭 Factories

### Using Factories

```php
// Create a single user
$user = User::factory()->create();

// Create multiple users
$users = User::factory()->count(10)->create();

// Create with specific attributes
$admin = User::factory()->create([
    'is_admin' => true,
    'email' => 'admin@example.com',
]);

// Create without persisting
$user = User::factory()->make();
```

### Defining Factories

```php
// database/factories/UserFactory.php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ];
}

// Add factory states
public function admin(): static
{
    return $this->state(fn (array $attributes) => [
        'is_admin' => true,
    ]);
}
```

## 🎭 Mocking & Faking

### Notification Fake

```php
use Illuminate\Support\Facades\Notification;

Notification::fake();

// Perform action that sends notification

Notification::assertSentTo($user, ResetPassword::class);
```

### Mail Fake

```php
use Illuminate\Support\Facades\Mail;

Mail::fake();

// Send email

Mail::assertSent(WelcomeEmail::class);
```

### Queue Fake

```php
use Illuminate\Support\Facades\Queue;

Queue::fake();

// Dispatch job

Queue::assertPushed(ProcessTrade::class);
```

### Storage Fake

```php
use Illuminate\Support\Facades\Storage;

Storage::fake('public');

// Upload file

Storage::disk('public')->assertExists('file.jpg');
```

## 🔍 Assertions

### Response Assertions

```php
$response->assertStatus(200);
$response->assertOk();
$response->assertRedirect('/dashboard');
$response->assertJson(['success' => true]);
$response->assertSee('Welcome');
$response->assertDontSee('Error');
$response->assertViewHas('user');
```

### Database Assertions

```php
$this->assertDatabaseHas('users', [
    'email' => 'test@example.com',
]);

$this->assertDatabaseMissing('users', [
    'email' => 'deleted@example.com',
]);

$this->assertDatabaseCount('users', 5);
```

### Model Assertions

```php
$this->assertTrue($user->is_admin);
$this->assertFalse($user->is_banned);
$this->assertEquals('John', $user->name);
$this->assertNull($user->deleted_at);
```

## 🐛 Debugging Tests

### Dump Response

```php
$response = $this->get('/dashboard');
$response->dump();  // Dump response content
$response->dd();    // Dump and die
```

### View Queries

```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

// Perform action

dd(DB::getQueryLog());
```

### Stop on Failure

```bash
php artisan test --stop-on-failure
```

## 📊 Test Coverage

### Generate Coverage Report

```bash
php artisan test --coverage --min=80
```

### HTML Coverage Report

```bash
php artisan test --coverage-html coverage-report
```

Then open `coverage-report/index.html` in browser.

## ⚡ Performance

### Speed Up Tests

1. **Use DatabaseTransactions** instead of RefreshDatabase when possible
2. **Run tests in parallel**: `php artisan test --parallel`
3. **Reduce bcrypt rounds** (already configured in phpunit.xml)
4. **Use in-memory cache**: `CACHE_STORE=array` (already configured)

### Current Performance

- **Total Tests**: 25
- **Assertions**: 59
- **Duration**: ~2.5 seconds
- **Database**: Separate test database

## 🔒 Security Testing

### Test Authentication

```php
public function test_guest_cannot_access_dashboard(): void
{
    $response = $this->get('/dashboard');
    
    $response->assertRedirect('/login');
}

public function test_user_can_access_dashboard(): void
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertOk();
}
```

### Test Authorization

```php
public function test_non_admin_cannot_access_admin_panel(): void
{
    $user = User::factory()->create(['is_admin' => false]);
    
    $response = $this->actingAs($user)->get('/admin');
    
    $response->assertForbidden();
}
```

### Test CSRF Protection

```php
public function test_csrf_protection_on_forms(): void
{
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    $response->assertStatus(419); // CSRF token mismatch
}
```

## 🚨 Common Issues

### Tests Affecting Production

**Problem**: Tests cleared production database

**Solution**: ✅ **FIXED!** Tests now use separate `thetradevisor_test` database

### Slow Tests

**Problem**: Tests take too long

**Solutions**:
- Use `DatabaseTransactions` instead of `RefreshDatabase`
- Run tests in parallel
- Mock external services
- Use factories efficiently

### Failed Migrations

**Problem**: Migration errors during tests

**Solutions**:
```bash
# Clear test database
php artisan db:wipe --database=pgsql --env=testing

# Run tests (migrations will run automatically)
php artisan test
```

## 📚 Best Practices

### ✅ Do's

- ✅ Use descriptive test names
- ✅ Follow Arrange-Act-Assert pattern
- ✅ Test one thing per test
- ✅ Use factories for test data
- ✅ Mock external services
- ✅ Test edge cases
- ✅ Keep tests fast

### ❌ Don'ts

- ❌ Don't test framework code
- ❌ Don't use production database
- ❌ Don't make tests dependent on each other
- ❌ Don't test implementation details
- ❌ Don't skip assertions
- ❌ Don't ignore failing tests

## 🎯 Test Coverage Goals

| Component | Target Coverage |
|-----------|----------------|
| Controllers | 80%+ |
| Services | 90%+ |
| Models | 70%+ |
| Middleware | 85%+ |
| Overall | 80%+ |

## 📖 Related Documentation

- [Architecture Overview](architecture.md)
- [Database Schema](database-schema.md)
- [Contributing Guide](../contributing/CONTRIBUTING.md)

## 🆘 Getting Help

### Resources

- **Laravel Testing Docs**: https://laravel.com/docs/testing
- **PHPUnit Docs**: https://phpunit.de/documentation.html
- **Email**: hello@thetradevisor.com

### Reporting Test Issues

Include:
1. Test command used
2. Full error output
3. PHP version
4. Database version
5. Steps to reproduce

---

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)
❤️ From Palestine to the world with Love

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)

---

**Last Updated**: November 7, 2025  
**Test Suite Version**: 1.0  
**Status**: All Tests Passing ✅
