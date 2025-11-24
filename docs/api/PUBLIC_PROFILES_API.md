# Public Profiles API Documentation

**Last Updated:** November 24, 2025  
**API Version:** 2.7.0  
**Base URL:** `https://thetradevisor.com`

---

## 📖 Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Endpoints](#endpoints)
4. [Data Structures](#data-structures)
5. [Caching](#caching)
6. [Rate Limiting](#rate-limiting)
7. [Error Handling](#error-handling)
8. [Examples](#examples)

---

## Overview

The Public Profiles API provides access to trader profiles and leaderboard data. All endpoints are publicly accessible (no authentication required) for viewing public data.

### Base Information
- **Protocol:** HTTPS only
- **Format:** HTML responses (JSON API coming in future release)
- **Cache:** 15-minute cache on profile data
- **Rate Limit:** Standard Laravel rate limiting applies

---

## Authentication

### Public Endpoints (No Auth Required)

The following endpoints are publicly accessible:
- View public profiles
- View leaderboard

### Authenticated Endpoints (Auth Required)

The following endpoints require authentication:
- Manage public profile settings
- Update account visibility
- Configure widget presets

**Authentication Method:**
- Laravel session-based authentication
- CSRF token required for POST requests

---

## Endpoints

### 1. View Public Profile

**Endpoint:** `GET /@{username}/{slug}/{account}`

**Description:** View a trader's public profile for a specific account.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `username` | string | Yes | Public username (alphanumeric + underscore) |
| `slug` | string | Yes | Account slug (lowercase alphanumeric + hyphens) |
| `account` | integer | Yes | Account number (numeric) |

**Example Request:**
```http
GET /@john_trader/main-account/12345678 HTTP/1.1
Host: thetradevisor.com
```

**Response:** HTML page with profile data

**HTTP Status Codes:**
- `200 OK` - Profile found and displayed
- `404 Not Found` - Profile not found or not public
- `500 Internal Server Error` - Server error

**Caching:**
- Cache Duration: 15 minutes
- Cache Key: `public_profile_{profile_id}`
- Cache Driver: Redis (production)

---

### 2. View Leaderboard

**Endpoint:** `GET /top-traders`

**Description:** View the top traders leaderboard with filtering options.

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `rank_by` | string | No | `total_profit` | Ranking criteria |

**Valid `rank_by` Values:**
- `total_profit` - Sort by total profit (USD)
- `roi` - Sort by return on investment (%)
- `win_rate` - Sort by win rate (%)
- `profit_factor` - Sort by profit factor ratio

**Example Requests:**
```http
GET /top-traders HTTP/1.1
Host: thetradevisor.com

GET /top-traders?rank_by=roi HTTP/1.1
Host: thetradevisor.com

GET /top-traders?rank_by=win_rate HTTP/1.1
Host: thetradevisor.com
```

**Response:** HTML page with leaderboard

**HTTP Status Codes:**
- `200 OK` - Leaderboard displayed
- `500 Internal Server Error` - Server error

**Data Specifications:**
- Time Window: Last 30 days
- Limit: Top 50 traders
- Sorting: Descending by selected criteria

---

### 3. Manage Public Profiles (Authenticated)

**Endpoint:** `GET /accounts/public-profiles`

**Description:** View and manage public profile settings for all accounts.

**Authentication:** Required (session-based)

**Example Request:**
```http
GET /accounts/public-profiles HTTP/1.1
Host: thetradevisor.com
Cookie: laravel_session=...
```

**Response:** HTML page with management interface

**HTTP Status Codes:**
- `200 OK` - Page displayed
- `401 Unauthorized` - Not authenticated
- `500 Internal Server Error` - Server error

---

### 4. Update Account Profile (Authenticated)

**Endpoint:** `POST /accounts/{account}/public-profile`

**Description:** Update public profile settings for a specific account.

**Authentication:** Required (session-based)

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `account` | integer | Yes | Account ID (route parameter) |
| `is_public` | boolean | Yes | Public visibility (0 or 1) |
| `slug` | string | Yes | URL-friendly account name |
| `widget_preset` | string | Yes | Widget preset type |
| `show_symbols` | boolean | No | Show symbol performance |
| `show_recent_trades` | boolean | No | Show recent trades |

**Valid `widget_preset` Values:**
- `minimal` - Basic stats only
- `balanced` - Stats + charts + symbols
- `maximum` - Full transparency

**Example Request:**
```http
POST /accounts/12345/public-profile HTTP/1.1
Host: thetradevisor.com
Cookie: laravel_session=...
Content-Type: application/x-www-form-urlencoded

_token=csrf_token_here&
is_public=1&
slug=main-account&
widget_preset=balanced&
show_symbols=1&
show_recent_trades=0
```

**Response:** Redirect to management page with success message

**HTTP Status Codes:**
- `302 Found` - Redirect after successful update
- `401 Unauthorized` - Not authenticated
- `403 Forbidden` - Not account owner
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

---

## Data Structures

### Profile Data Structure

When viewing a public profile, the following data is available:

```php
[
    'user' => [
        'id' => 123,
        'public_username' => 'john_trader',
        'public_display_mode' => 'username', // or 'display_name' or 'anonymous'
        'display_name' => 'John Trading',
        'show_on_leaderboard' => true
    ],
    
    'account' => [
        'id' => 456,
        'account_number' => '12345678',
        'broker' => 'IC Markets',
        'platform_type' => 'MT5',
        'currency' => 'USD',
        'initial_balance' => 10000.00
    ],
    
    'profile' => [
        'id' => 789,
        'slug' => 'main-account',
        'is_public' => true,
        'widget_preset' => 'balanced',
        'show_symbols' => true,
        'show_recent_trades' => false
    ],
    
    'badges' => [
        'top_performer' => true,
        'transparent' => false,
        'verified' => false
    ],
    
    'stats' => [
        'total_profit' => 2500.50,
        'total_trades' => 150,
        'winning_trades' => 95,
        'losing_trades' => 55,
        'win_rate' => 63.33,
        'roi' => 25.01,
        'profit_factor' => 1.85,
        'currency' => 'USD',
        'period_days' => 30
    ],
    
    'equity_curve' => [
        ['date' => '2025-10-25', 'equity' => 10000.00],
        ['date' => '2025-10-26', 'equity' => 10150.25],
        // ... 30 data points (daily)
        ['date' => '2025-11-23', 'equity' => 12500.50]
    ],
    
    'symbol_performance' => [
        [
            'normalized_symbol' => 'XAUUSD',
            'raw_symbol' => 'XAUUSD.sd',
            'trades' => 45,
            'profit' => 1250.00,
            'win_rate' => 68.89
        ],
        // ... up to 10 symbols
    ],
    
    'recent_trades' => [ // if show_recent_trades = true
        [
            'symbol' => 'XAUUSD',
            'type' => 'buy',
            'volume' => 0.1,
            'profit' => 125.50,
            'open_time' => '2025-11-23 10:30:00',
            'close_time' => '2025-11-23 14:45:00'
        ],
        // ... up to 10 trades
    ]
]
```

---

### Leaderboard Data Structure

When viewing the leaderboard, the following data is available:

```php
[
    [
        'user' => [
            'id' => 123,
            'public_username' => 'john_trader',
            'public_display_mode' => 'username',
            'display_name' => 'John Trading'
        ],
        
        'stats' => [
            'total_profit' => 5250.75,      // aggregated across all public accounts
            'total_trades' => 320,          // aggregated
            'win_rate' => 64.50,            // weighted average
            'roi' => 26.25,                 // calculated from total initial balance
            'profit_factor' => 1.92,        // aggregated
            'currency' => 'USD'             // normalized for comparison
        ],
        
        'accounts' => [
            [
                'profile' => [
                    'id' => 789,
                    'slug' => 'main-account',
                    'account_number' => '12345678'
                ],
                'account' => [
                    'broker' => 'IC Markets',
                    'platform_type' => 'MT5',
                    'currency' => 'USD'
                ],
                'stats' => [
                    'total_profit' => 2500.50,
                    'total_trades' => 150,
                    'win_rate' => 63.33,
                    'roi' => 25.01,
                    'profit_factor' => 1.85
                ]
            ],
            [
                'profile' => [
                    'id' => 790,
                    'slug' => 'scalping-account',
                    'account_number' => '87654321'
                ],
                'account' => [
                    'broker' => 'Pepperstone',
                    'platform_type' => 'MT4',
                    'currency' => 'EUR'
                ],
                'stats' => [
                    'total_profit' => 2750.25,
                    'total_trades' => 170,
                    'win_rate' => 65.29,
                    'roi' => 27.50,
                    'profit_factor' => 2.01
                ]
            ]
        ],
        
        'account_count' => 2,
        
        'badges' => [
            'top_performer' => true,
            'transparent' => false,
            'verified' => false
        ]
    ],
    // ... up to 50 traders
]
```

---

## Caching

### Cache Strategy

Public profile data is cached to improve performance and reduce database load.

**Cache Configuration:**

| Aspect | Value |
|--------|-------|
| Driver | Redis (production), File (development) |
| TTL | 900 seconds (15 minutes) |
| Key Format | `public_profile_{profile_id}` |
| Invalidation | Automatic after TTL, manual via artisan |

**Cache Keys:**
```
public_profile_789
public_profile_790
```

### Manual Cache Clearing

**Clear Specific Profile:**
```bash
php artisan cache:forget public_profile_789
```

**Clear All Caches:**
```bash
php artisan optimize:clear
```

**Clear View Cache:**
```bash
php artisan view:clear
```

### Cache Behavior

- **First Request:** Data fetched from database, cached for 15 minutes
- **Subsequent Requests:** Served from cache (fast)
- **After 15 Minutes:** Cache expires, next request refreshes data
- **Profile Updates:** Cache automatically cleared for that profile

---

## Rate Limiting

### Default Limits

Public endpoints use Laravel's standard rate limiting:

| Endpoint Type | Limit | Window |
|--------------|-------|--------|
| Public Views | 60 requests | per minute |
| Authenticated | 60 requests | per minute |

### Rate Limit Headers

Responses include rate limit information:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1700000000
```

### Rate Limit Exceeded

When rate limit is exceeded:

**HTTP Status:** `429 Too Many Requests`

**Response:**
```html
<html>
<body>
    <h1>Too Many Requests</h1>
    <p>Please try again later.</p>
</body>
</html>
```

### Cloudflare Protection

Additional DDoS protection provided by Cloudflare:
- Challenge pages for suspicious traffic
- IP-based rate limiting
- Geographic restrictions (if configured)

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 302 | Found | Redirect after POST |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Not authorized for this resource |
| 404 | Not Found | Profile or account not found |
| 422 | Unprocessable Entity | Validation error |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Error Responses

#### 404 Not Found

**Possible Causes:**
- Profile doesn't exist
- Account is set to private
- Invalid username/slug/account combination
- Account deleted

**Example Response:**
```html
<html>
<body>
    <h1>404 - Profile Not Found</h1>
    <p>This profile is either private or does not exist.</p>
</body>
</html>
```

#### 422 Validation Error

**Possible Causes:**
- Invalid slug format (must be lowercase, alphanumeric, hyphens only)
- Missing required fields
- Invalid widget preset value
- Slug already in use by another account

**Example Response:**
```html
<html>
<body>
    <div class="alert alert-danger">
        <ul>
            <li>The slug format is invalid.</li>
            <li>The widget preset must be one of: minimal, balanced, maximum.</li>
        </ul>
    </div>
</body>
</html>
```

---

## Examples

### Example 1: View Public Profile

**Request:**
```bash
curl -X GET "https://thetradevisor.com/@john_trader/main-account/12345678"
```

**Response:**
```html
HTTP/2 200 
content-type: text/html; charset=UTF-8
cache-control: max-age=900

<!DOCTYPE html>
<html>
<head>
    <title>@john_trader - Main Account | TheTradeVisor</title>
    <meta property="og:title" content="@john_trader's Trading Performance">
    <meta property="og:description" content="30-day ROI: 25.01% | Win Rate: 63.33%">
    ...
</head>
<body>
    <!-- Profile content -->
</body>
</html>
```

---

### Example 2: View Leaderboard (Default)

**Request:**
```bash
curl -X GET "https://thetradevisor.com/top-traders"
```

**Response:**
```html
HTTP/2 200 
content-type: text/html; charset=UTF-8

<!DOCTYPE html>
<html>
<head>
    <title>Top Traders Leaderboard | TheTradeVisor</title>
</head>
<body>
    <!-- Leaderboard sorted by total profit -->
</body>
</html>
```

---

### Example 3: View Leaderboard (By ROI)

**Request:**
```bash
curl -X GET "https://thetradevisor.com/top-traders?rank_by=roi"
```

**Response:**
```html
HTTP/2 200 
content-type: text/html; charset=UTF-8

<!DOCTYPE html>
<html>
<head>
    <title>Top Traders by ROI | TheTradeVisor</title>
</head>
<body>
    <!-- Leaderboard sorted by ROI -->
</body>
</html>
```

---

### Example 4: Update Account Profile (Authenticated)

**Request:**
```bash
curl -X POST "https://thetradevisor.com/accounts/12345/public-profile" \
  -H "Cookie: laravel_session=your_session_cookie" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=csrf_token" \
  -d "is_public=1" \
  -d "slug=main-account" \
  -d "widget_preset=balanced" \
  -d "show_symbols=1" \
  -d "show_recent_trades=0"
```

**Response:**
```html
HTTP/2 302 
location: /accounts/public-profiles

<!-- Redirect with success message -->
```

---

## Future API Enhancements

### Planned Features

1. **JSON API Endpoints**
   - `GET /api/v1/profiles/@{username}/{slug}/{account}`
   - `GET /api/v1/leaderboard`
   - JSON responses for programmatic access

2. **Webhooks**
   - Profile update notifications
   - Leaderboard position changes
   - Badge earned notifications

3. **Embeddable Widgets**
   - JavaScript widget for external sites
   - iframe embed option
   - Customizable styling

4. **Historical Data**
   - Access to historical leaderboard snapshots
   - Performance over time API
   - Comparison endpoints

---

## Related Documentation

- [User Guide](../guides/PUBLIC_PROFILES_USER_GUIDE.md) - How to use public profiles
- [Technical Architecture](../technical/PUBLIC_PROFILES_ARCHITECTURE.md) - System design
- [Implementation Details](../features/PUBLIC_PROFILES_IMPLEMENTATION.md) - Development guide

---

## Support

For API support and questions:
- **Email:** hello@thetradevisor.com
- **Documentation:** https://thetradevisor.com/docs
- **Status Page:** https://status.thetradevisor.com (coming soon)

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
