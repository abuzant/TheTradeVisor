# REST API Endpoints

**Base URL:** `https://api.thetradevisor.com/v1`

## Authentication

All API requests require authentication using your API key in the Authorization header:

```
Authorization: Bearer YOUR_API_KEY
```

Generate your API key from Settings → API Key in your dashboard.

## Rate Limits

Rate limits are based on your subscription tier:

| Tier | Requests/Hour | Window |
|------|---------------|--------|
| Free | 100 | Rolling 60 minutes |
| Pro | 1,000 | Rolling 60 minutes |
| Enterprise | Unlimited | No limits |

## Response Headers

All responses include rate limit information:

- `X-RateLimit-Limit` - Your hourly limit
- `X-RateLimit-Remaining` - Requests remaining
- `X-RateLimit-Reset` - Unix timestamp when limit resets

## Endpoints

### Accounts

#### GET /accounts

Get all trading accounts for authenticated user.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "account_number": "12345678",
      "broker_name": "IC Markets",
      "platform_type": "MT5",
      "account_currency": "USD",
      "balance": 10000.00,
      "equity": 10250.50,
      "is_active": true,
      "created_at": "2025-01-15T10:30:00Z"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

#### GET /accounts/{id}

Get specific trading account by ID.

**Response:**
```json
{
  "data": {
    "id": 1,
    "account_number": "12345678",
    "broker_name": "IC Markets",
    "platform_type": "MT5",
    "account_currency": "USD",
    "balance": 10000.00,
    "equity": 10250.50,
    "margin": 500.00,
    "free_margin": 9750.50,
    "profit": 250.50,
    "is_active": true,
    "last_sync_at": "2025-01-15T14:30:00Z"
  }
}
```

### Trades

#### GET /trades

Get trade history with optional filters.

**Query Parameters:**
- `account_id` (optional) - Filter by account ID
- `symbol` (optional) - Filter by trading symbol
- `from_date` (optional) - Start date (YYYY-MM-DD)
- `to_date` (optional) - End date (YYYY-MM-DD)
- `limit` (optional) - Results per page (default: 50, max: 100)
- `page` (optional) - Page number

**Example:**
```bash
curl -X GET "https://api.thetradevisor.com/v1/trades?symbol=EURUSD&limit=10" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

**Response:**
```json
{
  "data": [
    {
      "id": 12345,
      "account_id": 1,
      "symbol": "EURUSD",
      "type": "buy",
      "volume": 0.10,
      "open_price": 1.0850,
      "profit": 25.00,
      "commission": -2.00,
      "swap": -0.50,
      "time": "2025-01-15T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total_pages": 5,
    "total": 50,
    "per_page": 10
  }
}
```

### Analytics

#### GET /analytics/performance

Get performance analytics for your accounts.

**Query Parameters:**
- `account_id` (optional) - Specific account ID
- `days` (optional) - Time period: 1, 7, 30, 90, 365 (default: 30)

**Response:**
```json
{
  "data": {
    "total_trades": 150,
    "winning_trades": 95,
    "losing_trades": 55,
    "win_rate": 63.3,
    "profit_factor": 1.85,
    "total_profit": 2500.00,
    "total_loss": -1350.00,
    "net_profit": 1150.00,
    "average_win": 26.32,
    "average_loss": -24.55,
    "largest_win": 150.00,
    "largest_loss": -80.00
  }
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Invalid API key provided",
    "details": "The API key you provided is invalid or has been revoked"
  }
}
```

### 403 Forbidden
```json
{
  "error": {
    "code": "FORBIDDEN",
    "message": "Access denied",
    "details": "The specified resource does not belong to you"
  }
}
```

### 404 Not Found
```json
{
  "error": {
    "code": "NOT_FOUND",
    "message": "Resource not found",
    "details": "The requested resource does not exist"
  }
}
```

### 429 Too Many Requests
```json
{
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Rate limit exceeded",
    "limit": 100,
    "retry_after": 3600
  }
}
```

## Best Practices

1. **Monitor Rate Limits** - Check `X-RateLimit-*` headers in responses
2. **Implement Backoff** - Use exponential backoff when approaching limits
3. **Cache Responses** - Cache data when possible to reduce API calls
4. **Handle Errors** - Implement proper error handling for all status codes
5. **Use Pagination** - Use `limit` and `page` parameters for large datasets

## Support

For API support, contact us at [hello@thetradevisor.com](mailto:hello@thetradevisor.com)


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
