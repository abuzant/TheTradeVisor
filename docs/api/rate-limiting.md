# ⏱️ API Rate Limiting

Comprehensive guide to TheTradeVisor's flexible rate limiting system.

## 📊 Overview

TheTradeVisor implements a sophisticated rate limiting system to:
- **Prevent abuse** - Protect API from excessive requests
- **Ensure fair usage** - Distribute resources fairly among users
- **Maintain performance** - Keep the system responsive
- **Support tiers** - Different limits for free, premium, and enterprise users

## 🎯 Default Limits

### Global Limits

| Type | Default Limit | Description |
|------|--------------|-------------|
| **IP-based** | 60 requests/min | Per IP address (all requests) |
| **API Key** | 120 requests/min | Per authenticated API key |
| **Burst** | 200 requests/min | Maximum burst allowance |
| **Premium** | 300 requests/min | For premium users |

### How Limits Work

Rate limits are applied **per minute** and reset every 60 seconds.

```
Minute 1: 0-60 requests ✅
Minute 2: Counter resets, 0-60 requests ✅
```

## 🔑 Limit Types

### 1. IP-Based Limiting

Applied to **all requests** from a single IP address.

**Use case**: Prevent single IP from overwhelming the API

```bash
# Example: 65 requests from same IP
for i in {1..65}; do
  curl https://yourdomain.com/api/v1/data/collect
done

# After 60 requests: 429 Too Many Requests
```

### 2. API Key-Based Limiting

Applied to **authenticated requests** per API key.

**Use case**: Fair usage per user account

```bash
curl -H "Authorization: Bearer tvsr_your_key" \
  https://yourdomain.com/api/v1/data/collect
```

**Limits checked**:
1. ✅ IP limit (60/min)
2. ✅ API key limit (120/min)

### 3. Custom User Limits

Admins can set **custom limits** for specific users.

**Use case**: VIP users, enterprise clients, testing

```php
// Admin sets custom limit for user
$user->rate_limit = 500;
$user->save();
```

### 4. Premium User Limits

Users marked as premium get higher limits automatically.

```php
$user->is_premium = true;
$user->save();
// Now gets 300 requests/min instead of 120
```

## 📡 Rate Limit Headers

Every API response includes rate limit information:

```http
HTTP/1.1 200 OK
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1699999999
```

### Header Descriptions

| Header | Description |
|--------|-------------|
| `X-RateLimit-Limit` | Maximum requests allowed per minute |
| `X-RateLimit-Remaining` | Requests remaining in current window |
| `X-RateLimit-Reset` | Unix timestamp when limit resets |

## 🚫 Rate Limit Exceeded Response

When limit is exceeded, you receive a `429 Too Many Requests` response:

```json
{
  "success": false,
  "error": "Rate limit exceeded",
  "message": "Too many requests. Please try again in 45 seconds.",
  "limit_type": "API Key",
  "limit": 120,
  "retry_after": 45
}
```

### Response Headers

```http
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1699999999
Retry-After: 45
```

## 🎛️ Admin Management

### Access Admin Panel

1. Log in as admin
2. Navigate to **Admin** → **Rate Limits**
3. URL: `/admin/rate-limits`

### Managing Limits

#### View All Limits

See all configured rate limits with:
- Current values
- Type (IP, API Key, Global, User)
- Status (Active/Inactive)
- Description

#### Update Limit

1. Click **Edit** on any limit
2. Change the value (1-10,000)
3. Update description if needed
4. Toggle active status
5. Click **Update**

#### Create Custom Limit

1. Click **Add New Limit**
2. Enter unique key (e.g., `enterprise_api_key_limit`)
3. Select type (Global, IP, API Key, User)
4. Set limit value
5. Add description
6. Click **Create**

#### Delete Limit

Click **Delete** on custom limits (core limits cannot be deleted)

#### Toggle Status

Click the status badge to activate/deactivate a limit

#### Clear Cache

Click **Clear Cache** to apply changes immediately

## 🔧 Configuration

### Database Settings

All limits are stored in the `rate_limit_settings` table:

```sql
SELECT * FROM rate_limit_settings;
```

| key | value | type | is_active |
|-----|-------|------|-----------|
| global_ip_limit | 60 | ip | true |
| global_api_key_limit | 120 | api_key | true |
| premium_api_key_limit | 300 | api_key | true |

### Programmatic Access

```php
use App\Models\RateLimitSetting;

// Get a limit
$ipLimit = RateLimitSetting::get('global_ip_limit', 60);

// Set a limit
RateLimitSetting::set('custom_limit', 500, 'Custom description');

// Get all active limits
$limits = RateLimitSetting::getAllActive();
```

## 🎯 Best Practices

### For API Consumers

1. **Monitor headers** - Check `X-RateLimit-Remaining`
2. **Implement backoff** - Wait when limit is close
3. **Cache responses** - Reduce unnecessary requests
4. **Batch requests** - Combine multiple operations
5. **Handle 429s** - Respect `Retry-After` header

### Example: Respectful Client

```php
function makeApiRequest($url) {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey
    ])->get($url);
    
    $remaining = $response->header('X-RateLimit-Remaining');
    
    // Slow down when approaching limit
    if ($remaining < 10) {
        sleep(5); // Wait 5 seconds
    }
    
    // Handle rate limit
    if ($response->status() === 429) {
        $retryAfter = $response->header('Retry-After');
        sleep($retryAfter);
        return makeApiRequest($url); // Retry
    }
    
    return $response;
}
```

### For Administrators

1. **Monitor usage** - Check logs for rate limit hits
2. **Adjust limits** - Based on server capacity
3. **Set custom limits** - For VIP users
4. **Use whitelist** - For trusted IPs/keys
5. **Clear cache** - After changing limits

## 🔍 Monitoring

### Check Rate Limit Hits

```bash
# View rate limit logs
tail -f storage/logs/laravel.log | grep "Rate limit exceeded"
```

### Log Format

```
[2025-11-07 20:54:52] warning: Rate limit exceeded
{
  "identifier": "192.168.1.100",
  "type": "ip",
  "limit": 60,
  "timestamp": "2025-11-07 20:54:52"
}
```

### Statistics (Coming Soon)

Admin panel will include:
- Total requests per hour/day
- Rate limit hits
- Top IPs hitting limits
- Top API keys hitting limits

## 🛡️ Whitelist

### Add to Whitelist

Create a whitelist setting:

```php
RateLimitSetting::create([
    'key' => 'rate_limit_whitelist_ip',
    'value' => 0,
    'description' => '192.168.1.1, 10.0.0.1, 172.16.0.1',
    'type' => 'ip',
]);
```

Whitelisted IPs/keys bypass rate limits entirely.

## 🔄 Cache Behavior

### Redis-Backed

Rate limits use Redis for fast lookups:

```
Key format: rate_limit:{type}:{identifier}
TTL: 60 seconds
```

### Clear Individual Limit

```php
use App\Services\RateLimiterService;

$rateLimiter = app(RateLimiterService::class);
$rateLimiter->clear('192.168.1.100', 'ip');
```

### Clear All Limits

```bash
# Via admin panel: Click "Clear Cache"

# Via command line:
php artisan cache:clear
```

## 🧪 Testing

### Test Rate Limiting

```bash
# Test IP limit (60 requests)
for i in {1..65}; do
  echo "Request $i"
  curl -s https://yourdomain.com/api/v1/data/collect | jq .
done

# Test API key limit (120 requests)
for i in {1..125}; do
  echo "Request $i"
  curl -s -H "Authorization: Bearer tvsr_your_key" \
    https://yourdomain.com/api/v1/data/collect | jq .
done
```

### Expected Behavior

- Requests 1-60: ✅ Success (200 OK)
- Request 61+: ❌ Rate limited (429)
- After 60 seconds: ✅ Limit resets

## 🐛 Troubleshooting

### Rate Limit Not Working

**Problem**: Requests not being rate limited

**Solutions**:
1. Check middleware is registered in `bootstrap/app.php`
2. Verify Redis is running: `redis-cli ping`
3. Check rate limit is active in database
4. Clear cache: `php artisan cache:clear`

### Too Strict Limits

**Problem**: Legitimate users hitting limits

**Solutions**:
1. Increase global limits in admin panel
2. Set custom limits for affected users
3. Add users to whitelist
4. Upgrade users to premium

### Rate Limit Not Resetting

**Problem**: Limit doesn't reset after 60 seconds

**Solutions**:
1. Check Redis TTL: `redis-cli TTL rate_limit:ip:192.168.1.1`
2. Restart Redis: `sudo systemctl restart redis`
3. Clear all caches: `php artisan cache:clear`

## 📊 Performance Impact

### Overhead

- **Per request**: ~1-5ms
- **Cache hit**: <1ms
- **Cache miss**: ~3-5ms
- **Database query**: Only on cache miss

### Optimization

Rate limiting is highly optimized:
- ✅ Redis-backed (in-memory)
- ✅ Minimal database queries
- ✅ Efficient caching
- ✅ No impact on successful requests

## 🔮 Future Enhancements

Planned features:
- 📊 Real-time statistics dashboard
- 📧 Email alerts for rate limit abuse
- 🌍 Geographic rate limiting
- ⏰ Time-based limits (hourly, daily)
- 📈 Dynamic limits based on server load
- 🎯 Endpoint-specific limits
- 📱 Mobile app integration

## 📚 Related Documentation

- [API Overview](overview.md)
- [Authentication](authentication.md)
- [Error Codes](../troubleshooting/error-codes.md)
- [Admin Dashboard](../admin/dashboard.md)

## 🆘 Support

### Getting Help

- **Email**: hello@thetradevisor.com
- **Documentation**: https://thetradevisor.com/docs
- **GitHub**: https://github.com/abuzant/TheTradeVisor/issues

### Reporting Issues

Include:
1. Rate limit error response
2. Request headers
3. Expected vs actual behavior
4. Your API key (last 4 characters only)

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
**Feature Version**: 1.0  
**Status**: Production Ready ✅
