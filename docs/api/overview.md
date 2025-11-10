# 🔌 API Overview

Complete guide to TheTradeVisor REST API for MT4/MT5 integration.

## 📋 Introduction

The TheTradeVisor API allows you to:
- Send trading data from MT4/MT5 terminals
- Retrieve account information
- Access analytics and statistics
- Export trading data
- Manage trading accounts

## 🚀 Quick Start

### 1. Get Your API Key

1. Log in to TheTradeVisor
2. Navigate to **Settings** → **API Key**
3. Copy your API key (format: `tvsr_...`)

### 2. Make Your First Request

```bash
curl -X GET "https://yourdomain.com/api/accounts" \
  -H "Authorization: Bearer tvsr_your_api_key_here"
```

### 3. Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "account_number": "12345678",
      "broker_name": "IC Markets",
      "balance": 10000.00,
      "equity": 10500.00,
      "profit": 500.00
    }
  ]
}
```

## 🔑 Authentication

All API requests require authentication using an API key.

### Header Authentication

```http
Authorization: Bearer tvsr_your_api_key_here
```

### Query Parameter (Alternative)

```http
GET /api/accounts?api_key=tvsr_your_api_key_here
```

**Note**: Header authentication is recommended for security.

## 📡 Base URL

```
https://yourdomain.com/api
```

Replace `yourdomain.com` with your TheTradeVisor installation URL.

## 📊 Response Format

All responses follow this structure:

### Success Response

```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}
```

### Error Response

```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

## 🛣️ Available Endpoints

### Account Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/health` | Public health check |
| POST | `/api/v1/data/collect` | Submit trading data from MT4/MT5 |

### Data Collection Endpoint

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/data/collect` | Send trading data (positions, orders, deals) |

**Request Body:**
```json
{
  "account": {
    "account_number": "12345678",
    "broker_name": "IC Markets",
    "balance": 10000.00,
    "equity": 10500.00,
    "profit": 500.00
  },
  "positions": [...],
  "orders": [...],
  "deals": [...]
}
```

### Future Planned Endpoints

The following endpoints are planned for future releases:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/accounts` | List all accounts |
| GET | `/api/accounts/{id}` | Get account details |
| GET | `/api/analytics/overview` | Get overview stats |
| GET | `/api/analytics/performance` | Performance metrics |
| GET | `/api/export/trades/csv` | Export trades to CSV |
| GET | `/api/export/trades/pdf` | Export trades to PDF |

## 📝 Request Examples

### Health Check

```http
GET /api/health
```

**Response:**
```json
{
  "status": "ok",
  "service": "TheTradeVisor API",
  "version": "1.0",
  "timestamp": "2025-11-10T10:30:00.000Z"
}
```

### Submit Trading Data

```http
POST /api/v1/data/collect
Content-Type: application/json
Authorization: Bearer tvsr_your_api_key_here

{
  "account": {
    "account_number": "12345678",
    "broker_name": "IC Markets",
    "balance": 10000.00,
    "equity": 10500.00,
    "profit": 500.00,
    "margin": 200.00,
    "free_margin": 9800.00,
    "margin_level": 525.00
  },
  "positions": [
    {
      "symbol": "EURUSD",
      "type": "buy",
      "volume": 0.1,
      "open_price": 1.08567,
      "current_price": 1.08723,
      "profit": 15.60,
      "open_time": "2025-11-10T09:15:00.000Z"
    }
  ],
  "orders": [],
  "deals": [
    {
      "symbol": "EURUSD",
      "type": "buy",
      "volume": 0.1,
      "price": 1.08567,
      "profit": 15.60,
      "time": "2025-11-10T09:15:00.000Z"
    }
  ]
}
```

## ⚡ Rate Limiting

- **Default**: 60 requests per minute
- **Burst**: 100 requests per minute
- **Headers**: Rate limit info in response headers

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1699999999
```

## 🔒 Security

### Best Practices

1. **Never share your API key**
2. **Use HTTPS only**
3. **Rotate keys regularly**
4. **Monitor API usage**
5. **Implement error handling**

### IP Whitelisting (Optional)

Configure allowed IPs in settings:
```
Settings → API Key → IP Whitelist
```

## 📚 SDKs & Libraries

### MQL4/MQL5 (MetaTrader)

Download our official Expert Advisor:
- Automatic data synchronization
- Error handling
- Retry logic
- Connection monitoring

### PHP

```php
$client = new TheTradeVisorClient('tvsr_your_api_key');
$accounts = $client->getAccounts();
```

### Python

```python
from thetradevisor import Client

client = Client('tvsr_your_api_key')
accounts = client.get_accounts()
```

## 🐛 Error Handling

### Common Error Codes

| Code | Description | Solution |
|------|-------------|----------|
| 401 | Unauthorized | Check API key |
| 403 | Forbidden | Check permissions |
| 404 | Not Found | Verify endpoint URL |
| 422 | Validation Error | Check request data |
| 429 | Rate Limit | Slow down requests |
| 500 | Server Error | Contact support |

### Example Error Response

```json
{
  "success": false,
  "error": "Invalid API key",
  "code": "INVALID_API_KEY",
  "details": {
    "provided_key": "tvsr_invalid..."
  }
}
```

## 📊 Pagination

Large result sets are paginated:

```http
GET /api/deals?page=2&per_page=50
```

Response includes pagination metadata:

```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 2,
    "per_page": 50,
    "total": 500,
    "last_page": 10
  }
}
```

## 🔄 Webhooks (Coming Soon)

Subscribe to events:
- New trade executed
- Account balance changed
- Position opened/closed
- Risk threshold exceeded

## 📖 Next Steps

- [Authentication Guide](authentication.md)
- [Complete Endpoint Reference](endpoints.md)
- [Rate Limiting Details](rate-limiting.md)
- [Error Code Reference](../troubleshooting/error-codes.md)

## 🆘 Support

- **Documentation**: https://thetradevisor.com/docs
- **Email**: hello@thetradevisor.com
- **GitHub**: https://github.com/abuzant/TheTradeVisor/issues

---

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)

---

**API Version**: 1.0  
**Last Updated**: November 7, 2025
