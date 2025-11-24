# Enterprise API Documentation

**Version:** 1.0  
**Base URL:** `https://thetradevisor.com/api/enterprise/v1/`  
**Authentication:** Bearer Token (API Key)

---

## Authentication

All Enterprise API requests require authentication using your API key in the `Authorization` header:

```bash
Authorization: Bearer ent_your_64_character_api_key_here
```

### Getting Your API Key

1. Log in to the Enterprise Portal at `https://enterprise.thetradevisor.com`
2. Navigate to **Settings**
3. Find your API key in the **API Access** section
4. Copy the key (it starts with `ent_`)

### Security Notes

- ⚠️ **Never share your API key** - treat it like a password
- ⚠️ **Never commit API keys to version control**
- ⚠️ Use environment variables to store keys in your applications
- 🔄 Regenerate your key immediately if it's compromised

---

## Rate Limiting

- **Rate Limit:** 1000 requests per hour per API key
- **Headers:** Rate limit info is returned in response headers:
  - `X-RateLimit-Limit`: Maximum requests per hour
  - `X-RateLimit-Remaining`: Remaining requests in current window
  - `X-RateLimit-Reset`: Unix timestamp when the limit resets

---

## Endpoints

### 1. Get All Accounts

**Endpoint:** `GET /api/enterprise/v1/accounts`

**Description:** Retrieve all trading accounts associated with your broker.

**Query Parameters:**
- `platform` (optional): Filter by platform (`MT4` or `MT5`)
- `country` (optional): Filter by country code (e.g., `US`, `GB`, `AE`)
- `status` (optional): Filter by status (`active`, `inactive`, `dormant`)
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Results per page (default: 50, max: 100)

**Example Request:**
```bash
curl -H "Authorization: Bearer ent_your_key_here" \
     "https://thetradevisor.com/api/enterprise/v1/accounts?platform=MT5&status=active"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "accounts": [
      {
        "id": 123,
        "account_number": "1012306793",
        "platform_type": "MT5",
        "country_code": "AE",
        "balance": 197144.63,
        "equity": 126986.43,
        "leverage": 500,
        "is_active": true,
        "last_trade_at": "2025-11-24T10:30:00Z",
        "created_at": "2025-01-15T08:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "per_page": 50,
      "total_accounts": 234
    }
  }
}
```

---

### 2. Get Aggregated Metrics

**Endpoint:** `GET /api/enterprise/v1/metrics`

**Description:** Get aggregated performance metrics across all accounts.

**Query Parameters:**
- `days` (optional): Time period in days (default: 30, options: 7, 30, 90, 180)
- `platform` (optional): Filter by platform (`MT4` or `MT5`)
- `country` (optional): Filter by country code

**Example Request:**
```bash
curl -H "Authorization: Bearer ent_your_key_here" \
     "https://thetradevisor.com/api/enterprise/v1/metrics?days=30"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "days": 30,
      "start_date": "2025-10-25",
      "end_date": "2025-11-24"
    },
    "accounts": {
      "total": 234,
      "active": 189,
      "inactive": 45
    },
    "trading": {
      "total_trades": 12543,
      "winning_trades": 7986,
      "losing_trades": 4557,
      "win_rate": 63.6,
      "total_volume": 15678.45,
      "avg_volume_per_trade": 1.25
    },
    "performance": {
      "total_profit": 125678.90,
      "total_loss": -45234.12,
      "net_profit": 80444.78,
      "profit_factor": 2.78,
      "avg_win": 15.74,
      "avg_loss": -9.93,
      "best_trade": 2500.00,
      "worst_trade": -450.00
    },
    "balances": {
      "total_balance": 5678234.56,
      "total_equity": 5234567.89,
      "avg_balance": 24265.53,
      "avg_equity": 22372.51
    },
    "fees": {
      "total_commission": 12345.67,
      "total_swap": 3456.78,
      "total_fees": 15802.45
    }
  }
}
```

---

### 3. Get Performance Data

**Endpoint:** `GET /api/enterprise/v1/performance`

**Description:** Get detailed performance breakdown by symbol, country, and platform.

**Query Parameters:**
- `days` (optional): Time period in days (default: 30)
- `group_by` (optional): Group results by `symbol`, `country`, or `platform` (default: `symbol`)

**Example Request:**
```bash
curl -H "Authorization: Bearer ent_your_key_here" \
     "https://thetradevisor.com/api/enterprise/v1/performance?days=30&group_by=symbol"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "days": 30,
      "start_date": "2025-10-25",
      "end_date": "2025-11-24"
    },
    "performance_by_symbol": [
      {
        "symbol": "XAUUSD",
        "trades": 3456,
        "volume": 4567.89,
        "profit": 45678.90,
        "win_rate": 65.4,
        "avg_profit_per_trade": 13.21
      },
      {
        "symbol": "EURUSD",
        "trades": 2345,
        "volume": 3456.78,
        "profit": 23456.78,
        "win_rate": 58.2,
        "avg_profit_per_trade": 10.00
      }
    ]
  }
}
```

---

### 4. Get Top Performers

**Endpoint:** `GET /api/enterprise/v1/top-performers`

**Description:** Get the top performing accounts ranked by profit.

**Query Parameters:**
- `days` (optional): Time period in days (default: 30)
- `limit` (optional): Number of accounts to return (default: 10, max: 50)
- `metric` (optional): Ranking metric (`profit`, `win_rate`, `volume`) (default: `profit`)

**Example Request:**
```bash
curl -H "Authorization: Bearer ent_your_key_here" \
     "https://thetradevisor.com/api/enterprise/v1/top-performers?days=30&limit=10"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "days": 30,
      "start_date": "2025-10-25",
      "end_date": "2025-11-24"
    },
    "top_performers": [
      {
        "rank": 1,
        "account_number": "1012306793",
        "platform_type": "MT5",
        "country_code": "AE",
        "total_profit": 12345.67,
        "win_rate": 72.5,
        "total_trades": 234,
        "total_volume": 567.89
      }
    ]
  }
}
```

---

### 5. Get Trading Hours Analysis

**Endpoint:** `GET /api/enterprise/v1/trading-hours`

**Description:** Analyze trading activity by hour of day and day of week.

**Query Parameters:**
- `days` (optional): Time period in days (default: 30)
- `timezone` (optional): Timezone for hour grouping (default: `UTC`)

**Example Request:**
```bash
curl -H "Authorization: Bearer ent_your_key_here" \
     "https://thetradevisor.com/api/enterprise/v1/trading-hours?days=30"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "days": 30,
      "start_date": "2025-10-25",
      "end_date": "2025-11-24"
    },
    "by_hour": [
      {
        "hour": 0,
        "trades": 234,
        "volume": 345.67,
        "profit": 1234.56,
        "win_rate": 62.3
      }
    ],
    "by_day": [
      {
        "day": "Monday",
        "trades": 2345,
        "volume": 3456.78,
        "profit": 12345.67,
        "win_rate": 64.2
      }
    ]
  }
}
```

---

### 6. Export Data

**Endpoint:** `GET /api/enterprise/v1/export`

**Description:** Export aggregated data in CSV or JSON format.

**Query Parameters:**
- `format` (required): Export format (`csv` or `json`)
- `type` (required): Data type (`accounts`, `trades`, `metrics`)
- `days` (optional): Time period in days (default: 30)
- `platform` (optional): Filter by platform
- `country` (optional): Filter by country

**Example Request:**
```bash
curl -H "Authorization: Bearer ent_your_key_here" \
     "https://thetradevisor.com/api/enterprise/v1/export?format=csv&type=accounts" \
     -o accounts.csv
```

**Response:** File download (CSV or JSON)

---

## Error Responses

All errors follow this format:

```json
{
  "success": false,
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Invalid or expired API key"
  }
}
```

### Common Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `UNAUTHORIZED` | 401 | Invalid or missing API key |
| `FORBIDDEN` | 403 | API key valid but broker inactive |
| `NOT_FOUND` | 404 | Endpoint not found |
| `VALIDATION_ERROR` | 422 | Invalid query parameters |
| `RATE_LIMIT_EXCEEDED` | 429 | Too many requests |
| `INTERNAL_ERROR` | 500 | Server error |

---

## Code Examples

### PHP (cURL)

```php
<?php
$apiKey = getenv('ENTERPRISE_API_KEY'); // ent_...
$url = 'https://thetradevisor.com/api/enterprise/v1/metrics?days=30';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    print_r($data);
} else {
    echo "Error: HTTP $httpCode\n";
}
```

### Python (requests)

```python
import os
import requests

api_key = os.getenv('ENTERPRISE_API_KEY')  # ent_...
url = 'https://thetradevisor.com/api/enterprise/v1/metrics'

headers = {
    'Authorization': f'Bearer {api_key}',
    'Accept': 'application/json'
}

params = {'days': 30}

response = requests.get(url, headers=headers, params=params)

if response.status_code == 200:
    data = response.json()
    print(data)
else:
    print(f"Error: {response.status_code}")
    print(response.text)
```

### JavaScript (Node.js)

```javascript
const axios = require('axios');

const apiKey = process.env.ENTERPRISE_API_KEY; // ent_...
const url = 'https://thetradevisor.com/api/enterprise/v1/metrics';

axios.get(url, {
  headers: {
    'Authorization': `Bearer ${apiKey}`,
    'Accept': 'application/json'
  },
  params: {
    days: 30
  }
})
.then(response => {
  console.log(response.data);
})
.catch(error => {
  console.error('Error:', error.response?.status, error.response?.data);
});
```

---

## Best Practices

1. **Store API Keys Securely**
   - Use environment variables
   - Never hardcode keys in source code
   - Use secret management systems in production

2. **Handle Rate Limits**
   - Check `X-RateLimit-Remaining` header
   - Implement exponential backoff for 429 errors
   - Cache responses when possible

3. **Error Handling**
   - Always check HTTP status codes
   - Parse error responses for details
   - Log errors for debugging

4. **Pagination**
   - Use `per_page` parameter to control response size
   - Implement pagination for large datasets
   - Don't request more data than needed

5. **Filtering**
   - Use query parameters to filter data
   - Reduces response size and improves performance
   - Combine filters for precise queries

---

## Support

For API support, technical questions, or feature requests:

- **Email:** enterprise@thetradevisor.com
- **Subject:** Enterprise API Support
- **Include:** Your broker name, API key prefix (first 10 chars), and detailed description

---

## Changelog

### Version 1.0 (November 2025)
- Initial release
- 6 core endpoints
- Bearer token authentication
- Rate limiting (1000 req/hour)
- CSV/JSON export support
