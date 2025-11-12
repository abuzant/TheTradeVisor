# 🏗️ System Architecture

Technical overview of TheTradeVisor's architecture and design patterns.

## 📐 High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   Client Layer                          │
├─────────────────────────────────────────────────────────┤
│  MT4/MT5 EA  │  Web Browser  │  Mobile App (Future)   │
└──────┬────────────────┬────────────────┬───────────────┘
       │                │                │
       ▼                ▼                ▼
┌─────────────────────────────────────────────────────────┐
│                  API Gateway / Load Balancer             │
└──────┬──────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────┐
│              Application Layer (Laravel)                 │
├─────────────────────────────────────────────────────────┤
│  Controllers  │  Services  │  Middleware  │  Jobs       │
└──────┬────────────────┬────────────────┬───────────────┘
       │                │                │
       ▼                ▼                ▼
┌─────────────────────────────────────────────────────────┐
│                   Data Layer                             │
├─────────────────────────────────────────────────────────┤
│  PostgreSQL  │  Redis Cache  │  File Storage  │  GeoIP │
└─────────────────────────────────────────────────────────┘
```

## 🎯 Design Patterns

### MVC Architecture

**Model-View-Controller** pattern for clean separation:

- **Models**: Data representation and business logic
- **Views**: Blade templates for UI rendering
- **Controllers**: Request handling and response generation

### Repository Pattern

Abstraction layer for data access:

```php
interface TradingAccountRepository {
    public function find($id);
    public function all();
    public function create(array $data);
}
```

### Service Layer

Business logic encapsulation:

```php
class TradingAccountService {
    public function syncAccountData($accountId, $data) {
        // Complex business logic here
    }
}
```

### Middleware Pattern

Request/response filtering:

```php
TrackCountryMiddleware
ValidateApiKey
RateLimiting
```

## 📦 Directory Structure

```
/www
├── app/
│   ├── Console/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/  # Request handlers
│   │   ├── Middleware/   # Request filters
│   │   └── Requests/     # Form validation
│   ├── Models/           # Eloquent models
│   ├── Services/         # Business logic
│   └── Helpers/          # Helper functions
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   ├── seeders/          # Data seeders
│   └── factories/        # Model factories
├── public/               # Public assets
├── resources/
│   ├── views/            # Blade templates
│   ├── js/               # JavaScript files
│   └── css/              # Stylesheets
├── routes/
│   ├── web.php           # Web routes
│   ├── api.php           # API routes
│   └── console.php       # Console routes
├── storage/
│   ├── app/              # Application files
│   ├── logs/             # Log files
│   └── framework/        # Framework files
└── tests/
    ├── Feature/          # Feature tests
    └── Unit/             # Unit tests
```

## 🔄 Request Flow

### Web Request Flow

```
1. User Request
   ↓
2. Web Server (Nginx)
   ↓
3. PHP-FPM
   ↓
4. Laravel Bootstrap
   ↓
5. Middleware Stack
   ↓
6. Route Matching
   ↓
7. Controller Action
   ↓
8. Service Layer
   ↓
9. Model/Database
   ↓
10. View Rendering
    ↓
11. Response
```

### API Request Flow

```
1. MT4/MT5 EA Request
   ↓
2. API Gateway
   ↓
3. Authentication Middleware
   ↓
4. Rate Limiting
   ↓
5. Country Tracking
   ↓
6. API Controller
   ↓
7. Service Layer
   ↓
8. Database Transaction
   ↓
9. JSON Response
```

## 🗄️ Database Architecture

### Primary Database (PostgreSQL)

**Tables**:
- `users` - User accounts
- `trading_accounts` - MT4/MT5 accounts
- `positions` - Open positions
- `orders` - Pending orders
- `deals` - Closed trades
- `api_request_logs` - API activity
- `password_reset_tokens` - Password resets

### Caching Layer (Redis)

**Cached Data**:
- User sessions
- GeoIP lookups (24h TTL)
- Analytics queries (1h TTL)
- Rate limiting counters
- Currency exchange rates

### File Storage

**Stored Files**:
- GeoIP database (`storage/app/geoip/`)
- Export files (`storage/app/exports/`)
- Log files (`storage/logs/`)
- User uploads (`storage/app/public/`)

## 🔐 Security Architecture

### Authentication Layers

1. **Web Authentication**: Laravel Breeze
2. **API Authentication**: Laravel Passport (OAuth2)
3. **Admin Authentication**: Middleware-based

### Security Measures

- **CSRF Protection**: Token-based
- **XSS Prevention**: Blade escaping
- **SQL Injection**: Eloquent ORM
- **Rate Limiting**: Per-user throttling
- **Password Hashing**: Bcrypt
- **API Key Encryption**: AES-256

## 📊 Data Flow

### Trading Data Synchronization

```
MT4/MT5 Terminal
    ↓ (Every 5 minutes)
Expert Advisor
    ↓ (HTTPS POST)
API Endpoint (/api/sync)
    ↓
Authentication Middleware
    ↓
Country Tracking Middleware
    ↓
SyncController
    ↓
TradingAccountService
    ↓
Database Transaction
    ├→ Update Account
    ├→ Sync Positions
    ├→ Sync Orders
    └→ Sync Deals
    ↓
Cache Invalidation
    ↓
Response to EA
```

### Analytics Generation

```
User Request (/analytics)
    ↓
Check Cache
    ├→ Hit: Return cached data
    └→ Miss: Generate analytics
        ↓
    Query Database
        ├→ Aggregate trades
        ├→ Calculate metrics
        ├→ Group by country
        └→ Compute statistics
        ↓
    Cache Results (1 hour)
        ↓
    Render View
        ↓
    Response to User
```

## 🚀 Performance Optimizations

### Database Optimizations

- **Indexes**: On frequently queried columns
- **Query Optimization**: Eager loading relationships
- **Connection Pooling**: Persistent connections
- **Read Replicas**: For analytics queries (future)

### Caching Strategy

```php
// Multi-level caching
1. Application Cache (Redis)
2. Query Cache (Database)
3. OPcache (PHP)
4. Browser Cache (Static assets)
```

### Queue System

**Async Jobs**:
- Email notifications
- Report generation
- Data exports
- GeoIP database updates

## 🔄 Scalability

### Horizontal Scaling

```
Load Balancer
    ├→ App Server 1
    ├→ App Server 2
    └→ App Server 3
        ↓
    Shared Database
    Shared Redis
    Shared Storage
```

### Vertical Scaling

- Increase server resources
- Optimize database queries
- Add more cache memory
- Upgrade PHP version

## 📡 External Services

### MaxMind GeoIP

- **Purpose**: Country detection
- **Update**: Every 2 weeks
- **Fallback**: Graceful degradation

### Currency Exchange API

- **Purpose**: Multi-currency support
- **Update**: Hourly
- **Cache**: 1 hour TTL

### Email Service

- **Provider**: Configurable (SMTP, SendGrid, etc.)
- **Queue**: Async sending
- **Retry**: 3 attempts

## 🧪 Testing Architecture

### Test Pyramid

```
        /\
       /  \  E2E Tests (Few)
      /────\
     /      \  Integration Tests (Some)
    /────────\
   /          \  Unit Tests (Many)
  /────────────\
```

### Test Types

1. **Unit Tests**: Individual components
2. **Feature Tests**: HTTP requests/responses
3. **Integration Tests**: Multiple components
4. **Browser Tests**: Selenium/Dusk (future)

## 📈 Monitoring & Logging

### Log Levels

```php
emergency() // System unusable
alert()     // Immediate action required
critical()  // Critical conditions
error()     // Error conditions
warning()   // Warning conditions
notice()    // Normal but significant
info()      // Informational
debug()     // Debug messages
```

### Monitored Metrics

- Request/response times
- Database query performance
- Cache hit/miss ratio
- API error rates
- Queue job failures
- Disk space usage

## 🔮 Future Architecture

### Planned Enhancements

1. **Microservices**: Split into smaller services
2. **Event Sourcing**: Track all state changes
3. **CQRS**: Separate read/write models
4. **GraphQL API**: Alternative to REST
5. **WebSockets**: Real-time updates
6. **Kubernetes**: Container orchestration

## 📚 Related Documentation

- [Database Schema](database-schema.md)
- [Testing Guide](testing.md)
- [API Documentation](../api/overview.md)
- [Deployment Guide](../deployment/production.md)

---

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
