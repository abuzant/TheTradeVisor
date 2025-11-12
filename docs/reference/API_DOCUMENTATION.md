# TheTradeVisor API Documentation

## Overview

TheTradeVisor provides a REST API for MetaTrader 5 Expert Advisors to upload trading data for analysis and visualization.

## Base URL

```
https://api.thetradevisor.com
```

**Important:** Use `api.thetradevisor.com` subdomain (not `thetradevisor.com`) to bypass Cloudflare and ensure direct server communication.

## Authentication

All API requests require authentication using an API key in the Authorization header.

### Getting Your API Key

1. Log into your TheTradeVisor account
2. Navigate to Settings → API
3. Your API key will be displayed (format: `tvsr_...`)

### Using the API Key

Include the API key in the `Authorization` header:

```
Authorization: Bearer tvsr_YOUR_API_KEY_HERE
```

Or without the "Bearer" prefix:

```
Authorization: tvsr_YOUR_API_KEY_HERE
```

Both formats are supported.

## Endpoints

### Health Check

Check API availability.

**Endpoint:** `GET /api/health`

**Authentication:** Not required

**Response:**
```json
{
  "status": "ok",
  "service": "TheTradeVisor API",
  "version": "1.0",
  "timestamp": "2025-11-07T22:00:00+00:00"
}
```

### Data Collection

Upload trading data from MT5 EA.

**Endpoint:** `POST /api/v1/data/collect`

**Authentication:** Required

**Headers:**
```
Authorization: Bearer tvsr_YOUR_API_KEY_HERE
Content-Type: application/json
```

**Request Body:**
```json
{
  "meta": {
    "is_historical": false,
    "is_first_run": false,
    "history_date": "2025-11-07",
    "history_day_number": 1
  },
  "account": {
    "account_number": "12345678",
    "account_hash": "sha256_hash",
    "broker": "Broker Name",
    "server": "Server-Live",
    "trade_mode": 0,
    "balance": 10000.00,
    "equity": 10000.00,
    "margin": 0.00,
    "free_margin": 10000.00,
    "leverage": 100,
    "currency": "USD"
  },
  "positions": [],
  "orders": [],
  "deals": []
}
```

**Field Descriptions:**

- `meta.is_historical`: Boolean - true for historical data upload, false for current data
- `meta.history_date`: String - Date of historical data (YYYY-MM-DD)
- `meta.history_day_number`: Integer - Day number in historical upload sequence
- `account.trade_mode`: Integer - 0 = Real, 1 = Demo, 2 = Contest (Demo/Contest rejected)
- `account.account_hash`: String - SHA256 hash of account number + server

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Data received successfully",
  "data_type": "current",
  "timestamp": "2025-11-07T22:00:00+00:00",
  "queued": true
}
```

**Error Responses:**

**401 Unauthorized:**
```json
{
  "success": false,
  "error": "Invalid API key",
  "message": "The provided API key is invalid or inactive"
}
```

**403 Forbidden (Demo Account):**
```json
{
  "success": false,
  "error": "Demo account not allowed",
  "message": "Demo and contest accounts are not supported. Please connect a real trading account to use TheTradeVisor.",
  "account_type": "demo"
}
```

**403 Forbidden (Account Paused):**
```json
{
  "success": false,
  "error": "Account paused",
  "message": "This trading account has been paused. Reason provided.",
  "paused_at": "2025-11-07T20:00:00+00:00"
}
```

**429 Too Many Requests:**
```json
{
  "success": false,
  "error": "Rate limit exceeded",
  "message": "Too many requests. Please try again in 60 seconds.",
  "limit_type": "API Key",
  "limit": 100,
  "retry_after": 60
}
```

## Rate Limiting

API requests are rate-limited based on:
- IP address
- API key

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1699392000
```

## Testing

### Using cURL

```bash
curl -X POST https://api.thetradevisor.com/api/v1/data/collect \
  -H "Authorization: Bearer tvsr_YOUR_API_KEY_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "meta": {"is_historical": false},
    "account": {
      "account_number": "test",
      "trade_mode": 0,
      "broker": "Test Broker"
    }
  }'
```

### Using Artisan Commands

**Check API Key Status:**
```bash
php artisan api:check tvsr_YOUR_API_KEY_HERE
```

**Test API Endpoint:**
```bash
php artisan api:test tvsr_YOUR_API_KEY_HERE
```

## Troubleshooting

### 401 Unauthorized

**Causes:**
- Invalid API key
- User account is inactive
- API key not sent in Authorization header
- Typo in API key

**Solutions:**
1. Verify API key in your account settings
2. Check that Authorization header is being sent
3. Ensure no extra spaces in the API key
4. Use `api.thetradevisor.com` not `thetradevisor.com`

### 403 Demo Account Error

**Cause:** Attempting to connect a demo or contest account

**Solution:** Only real trading accounts are supported. Connect a live account.

### 429 Rate Limit

**Cause:** Too many requests in a short time period

**Solution:** 
- Reduce request frequency
- Wait for the retry_after duration
- Contact support to increase rate limits

### Connection Issues

**Symptoms:** Timeouts, connection refused, 521 errors

**Solutions:**
1. Ensure using `https://api.thetradevisor.com` (not `thetradevisor.com`)
2. Check firewall settings
3. Verify SSL/TLS support in your client
4. Check server connectivity: `ping api.thetradevisor.com`

## Best Practices

1. **Use the correct endpoint:** Always use `api.thetradevisor.com` subdomain
2. **Handle errors gracefully:** Implement retry logic with exponential backoff
3. **Respect rate limits:** Monitor rate limit headers and adjust request frequency
4. **Secure your API key:** Never commit API keys to version control
5. **Use HTTPS:** Always use HTTPS, never HTTP
6. **Set appropriate timeouts:** Use 30-60 second timeouts for requests
7. **Log errors:** Log failed requests for debugging

## Support

For API support:
- Check application logs: `storage/logs/laravel.log`
- Use diagnostic commands: `php artisan api:check` and `php artisan api:test`
- Contact support with error details and timestamps

## Changelog

### Version 1.0 (2025-11-07)
- Initial API release
- Data collection endpoint
- API key authentication
- Rate limiting
- Demo account rejection
- Account pause functionality

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
