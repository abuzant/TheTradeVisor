# Affiliate API Documentation

## Base URL
```
https://thetradevisor.com/api/v1/affiliate
```

## Authentication
All affiliate API endpoints require authentication using Laravel Sanctum or session-based authentication.

### Session Authentication
Login via the affiliate portal and use the session cookie.

### Token Authentication (Coming Soon)
```bash
curl -H "Authorization: Bearer YOUR_API_TOKEN" \
  https://thetradevisor.com/api/v1/affiliate/profile
```

## Endpoints

### 1. Get Affiliate Profile
Retrieve the authenticated affiliate's profile information.

**Endpoint:** `GET /api/v1/affiliate/profile`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "slug": "johndoe",
    "referral_url": "https://join.thetradevisor.com/offers/johndoe",
    "is_active": true,
    "is_verified": true,
    "total_clicks": 1250,
    "total_signups": 45,
    "paid_signups": 12,
    "pending_earnings": 15.88,
    "approved_earnings": 23.88,
    "total_paid": 100.00,
    "total_earnings": 139.76,
    "usdt_wallet_address": "TXYZabc123...",
    "wallet_type": "TRC20",
    "created_at": "2025-01-15T10:30:00Z"
  }
}
```

---

### 2. Get Statistics
Get aggregate statistics for a specified time period.

**Endpoint:** `GET /api/v1/affiliate/stats`

**Query Parameters:**
- `days` (optional): Number of days to include (default: 30)

**Example:**
```bash
GET /api/v1/affiliate/stats?days=7
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_clicks": 350,
    "total_unique_clicks": 280,
    "total_signups": 15,
    "total_paid_signups": 4,
    "total_earnings": 7.96,
    "conversion_rate": 4.29,
    "paid_conversion_rate": 26.67
  }
}
```

---

### 3. Get Performance Data
Retrieve daily performance metrics.

**Endpoint:** `GET /api/v1/affiliate/performance`

**Query Parameters:**
- `days` (optional): Number of days to include (default: 30)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "date": "2025-01-15",
      "clicks": 45,
      "signups": 2,
      "paid_signups": 1,
      "earnings": 1.99
    },
    {
      "date": "2025-01-14",
      "clicks": 38,
      "signups": 1,
      "paid_signups": 0,
      "earnings": 0.00
    }
  ]
}
```

---

### 4. Get Top Campaigns
Retrieve top performing campaigns.

**Endpoint:** `GET /api/v1/affiliate/campaigns`

**Query Parameters:**
- `limit` (optional): Number of campaigns to return (default: 10)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "utm_campaign": "summer_promo",
      "utm_source": "facebook",
      "utm_medium": "cpc",
      "clicks": 150,
      "conversions": 8
    },
    {
      "utm_campaign": "email_blast",
      "utm_source": "email",
      "utm_medium": "email",
      "clicks": 95,
      "conversions": 5
    }
  ]
}
```

---

### 5. Get Geographic Distribution
Get click and conversion data by country.

**Endpoint:** `GET /api/v1/affiliate/geo`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "country_code": "US",
      "clicks": 450,
      "conversions": 15
    },
    {
      "country_code": "GB",
      "clicks": 280,
      "conversions": 8
    }
  ]
}
```

---

### 6. Get Recent Clicks
Retrieve recent click data.

**Endpoint:** `GET /api/v1/affiliate/clicks`

**Query Parameters:**
- `limit` (optional): Number of clicks to return (default: 50, max: 100)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 12345,
      "clicked_at": "2025-01-15T14:30:00Z",
      "country_code": "US",
      "city": "New York",
      "utm_source": "facebook",
      "utm_campaign": "summer_promo",
      "converted": true,
      "converted_at": "2025-01-15T14:35:00Z"
    },
    {
      "id": 12344,
      "clicked_at": "2025-01-15T13:20:00Z",
      "country_code": "GB",
      "city": "London",
      "utm_source": "twitter",
      "utm_campaign": null,
      "converted": false,
      "converted_at": null
    }
  ]
}
```

---

### 7. Get Recent Conversions
Retrieve recent conversion data.

**Endpoint:** `GET /api/v1/affiliate/conversions`

**Query Parameters:**
- `limit` (optional): Number of conversions to return (default: 50, max: 100)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 456,
      "converted_at": "2025-01-15T14:35:00Z",
      "commission_amount": 1.99,
      "commission_currency": "USD",
      "status": "approved",
      "is_suspicious": false,
      "fraud_score": 10
    },
    {
      "id": 455,
      "converted_at": "2025-01-14T10:20:00Z",
      "commission_amount": 1.99,
      "commission_currency": "USD",
      "status": "pending",
      "is_suspicious": false,
      "fraud_score": 15
    }
  ]
}
```

---

### 8. Get Payout History
Retrieve payout history.

**Endpoint:** `GET /api/v1/affiliate/payouts`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 78,
      "requested_at": "2025-01-10T09:00:00Z",
      "amount": 50.00,
      "usdt_amount": 50.00,
      "currency": "USD",
      "status": "completed",
      "processed_at": "2025-01-11T15:30:00Z",
      "transaction_hash": "0xabc123..."
    },
    {
      "id": 77,
      "requested_at": "2024-12-15T10:00:00Z",
      "amount": 75.00,
      "usdt_amount": 75.00,
      "currency": "USD",
      "status": "completed",
      "processed_at": "2024-12-16T14:20:00Z",
      "transaction_hash": "0xdef456..."
    }
  ]
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "error": "UNAUTHENTICATED",
  "message": "Authentication is required to access this endpoint"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "error": "FORBIDDEN",
  "message": "You do not have permission to access this resource"
}
```

### 429 Too Many Requests
```json
{
  "success": false,
  "error": "RATE_LIMIT_EXCEEDED",
  "message": "Too many requests. Please try again later.",
  "retry_after": 60
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "error": "INTERNAL_SERVER_ERROR",
  "message": "An internal server error occurred"
}
```

---

## Rate Limiting
- **Limit:** 60 requests per minute per affiliate
- **Headers:** Rate limit information is included in response headers
  - `X-RateLimit-Limit`: Total requests allowed
  - `X-RateLimit-Remaining`: Remaining requests
  - `X-RateLimit-Reset`: Unix timestamp when limit resets

---

## Best Practices

### 1. Cache Responses
Cache API responses when appropriate to reduce API calls:
```javascript
// Cache stats for 5 minutes
const cacheKey = 'affiliate_stats';
const cachedData = localStorage.getItem(cacheKey);
if (cachedData && Date.now() - cachedData.timestamp < 300000) {
  return JSON.parse(cachedData.data);
}
```

### 2. Handle Errors Gracefully
```javascript
try {
  const response = await fetch('/api/v1/affiliate/stats');
  const data = await response.json();
  
  if (!data.success) {
    console.error('API Error:', data.message);
  }
} catch (error) {
  console.error('Network Error:', error);
}
```

### 3. Use Appropriate Time Ranges
- For dashboards: 7-30 days
- For detailed analytics: 90 days
- For historical reports: 365 days

### 4. Pagination
For endpoints returning large datasets, use the `limit` parameter:
```bash
GET /api/v1/affiliate/clicks?limit=20
```

---

## Code Examples

### JavaScript/Fetch
```javascript
async function getAffiliateStats() {
  const response = await fetch('/api/v1/affiliate/stats?days=30', {
    method: 'GET',
    credentials: 'include', // Include session cookie
    headers: {
      'Accept': 'application/json',
    }
  });
  
  const data = await response.json();
  
  if (data.success) {
    console.log('Total Earnings:', data.data.total_earnings);
  }
}
```

### PHP/cURL
```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://thetradevisor.com/api/v1/affiliate/stats');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$data = json_decode($response, true);

if ($data['success']) {
    echo "Total Earnings: $" . $data['data']['total_earnings'];
}

curl_close($ch);
```

### Python/Requests
```python
import requests

session = requests.Session()
# Login first to get session cookie
session.post('https://thetradevisor.com/affiliate/login', data={
    'email': 'your@email.com',
    'password': 'yourpassword'
})

# Make API request
response = session.get('https://thetradevisor.com/api/v1/affiliate/stats')
data = response.json()

if data['success']:
    print(f"Total Earnings: ${data['data']['total_earnings']}")
```

---

## Webhooks (Coming Soon)
Future support for webhooks to notify external systems of:
- New conversions
- Payout completions
- Status changes

---

## Support
For API issues or questions:
- Email: api@thetradevisor.com
- Documentation: https://thetradevisor.com/docs/affiliate-api
- Status Page: https://status.thetradevisor.com
