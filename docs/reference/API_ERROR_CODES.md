# TheTradeVisor API - Error Codes & Response Format

## Overview

All API responses at `https://api.thetradevisor.com/*` return **JSON only**. No HTML error pages will ever be returned, ensuring compatibility with MT4/MT5 Expert Advisors.

## Response Format

### Success Response
```json
{
  "success": true,
  "data": { ... },
  "message": "Optional success message",
  "meta": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "error": "ERROR_CODE",
  "message": "Human-readable error description",
  "details": "Optional additional details"
}
```

## HTTP Status Codes

| Status Code | Meaning | When It Occurs |
|-------------|---------|----------------|
| 200 | OK | Request succeeded |
| 400 | Bad Request | Invalid request format or missing required fields |
| 401 | Unauthorized | Missing or invalid API key |
| 403 | Forbidden | Valid API key but access denied (suspended account, demo account, paused account) |
| 404 | Not Found | Endpoint or resource doesn't exist |
| 405 | Method Not Allowed | Wrong HTTP method (e.g., GET instead of POST) |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server-side error |
| 503 | Service Unavailable | Maintenance mode or server overload |

## Error Codes

### Authentication Errors (401)

#### UNAUTHENTICATED
```json
{
  "success": false,
  "error": "UNAUTHENTICATED",
  "message": "Authentication is required to access this endpoint"
}
```
**Cause**: No API key provided in Authorization header  
**Solution**: Include `Authorization: Bearer YOUR_API_KEY` header

#### INVALID_API_KEY
```json
{
  "success": false,
  "error": "Invalid API key",
  "message": "The provided API key is invalid or inactive"
}
```
**Cause**: API key doesn't exist or user account is inactive  
**Solution**: Verify API key or regenerate from dashboard

### Authorization Errors (403)

#### ACCOUNT_SUSPENDED
```json
{
  "success": false,
  "error": "Account suspended",
  "message": "Your account has been suspended. Please contact support."
}
```
**Cause**: User account has been suspended by admin  
**Solution**: Contact support@thetradevisor.com

#### DEMO_ACCOUNT_NOT_ALLOWED
```json
{
  "success": false,
  "error": "Demo account not allowed",
  "message": "Demo and contest accounts are not supported. Please connect a real trading account to use TheTradeVisor.",
  "account_type": "demo"
}
```
**Cause**: Attempting to send data from a demo or contest account  
**Solution**: Connect a real trading account

#### ACCOUNT_PAUSED
```json
{
  "success": false,
  "error": "Account paused",
  "message": "This trading account has been paused. User requested pause.",
  "paused_at": "2025-11-13T06:00:00Z"
}
```
**Cause**: Trading account has been paused by user  
**Solution**: Unpause the account from the dashboard

#### FORBIDDEN
```json
{
  "success": false,
  "error": "FORBIDDEN",
  "message": "Access denied",
  "details": "The specified account does not belong to you"
}
```
**Cause**: Attempting to access a resource that doesn't belong to the authenticated user  
**Solution**: Verify account ownership

### Not Found Errors (404)

#### NOT_FOUND
```json
{
  "success": false,
  "error": "NOT_FOUND",
  "message": "The requested endpoint does not exist",
  "path": "api/v1/invalid/endpoint"
}
```
**Cause**: Endpoint URL is incorrect  
**Solution**: Check API documentation for correct endpoints

#### RESOURCE_NOT_FOUND
```json
{
  "success": false,
  "error": "RESOURCE_NOT_FOUND",
  "message": "The requested resource was not found"
}
```
**Cause**: Requested resource (account, trade, etc.) doesn't exist  
**Solution**: Verify resource ID

### Method Not Allowed (405)

#### METHOD_NOT_ALLOWED
```json
{
  "success": false,
  "error": "METHOD_NOT_ALLOWED",
  "message": "The HTTP method is not allowed for this endpoint",
  "allowed_methods": "POST"
}
```
**Cause**: Using wrong HTTP method (e.g., GET on a POST-only endpoint)  
**Solution**: Use the correct HTTP method shown in `allowed_methods`

### Validation Errors (422)

#### VALIDATION_ERROR
```json
{
  "success": false,
  "error": "VALIDATION_ERROR",
  "message": "The provided data is invalid",
  "errors": {
    "account_id": ["The account id field must be an integer."],
    "from_date": ["The from date field must be a valid date."]
  }
}
```
**Cause**: Request data failed validation rules  
**Solution**: Fix the fields listed in `errors` object

#### INVALID_DATA_STRUCTURE
```json
{
  "success": false,
  "error": "Invalid data structure",
  "message": "Required fields: meta, account"
}
```
**Cause**: Missing required top-level fields in data collection request  
**Solution**: Ensure `meta` and `account` objects are present

### Rate Limiting (429)

#### RATE_LIMIT_EXCEEDED
```json
{
  "success": false,
  "error": "Rate limit exceeded",
  "message": "Too many requests. Please try again in 45 seconds.",
  "limit_type": "API Key",
  "limit": 1000,
  "retry_after": 45
}
```
**Cause**: Exceeded rate limit for your API key or IP  
**Solution**: Wait `retry_after` seconds before retrying

**Headers Included**:
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Requests remaining in current window
- `X-RateLimit-Reset`: Unix timestamp when limit resets
- `Retry-After`: Seconds to wait before retry

### Server Errors (500)

#### INTERNAL_SERVER_ERROR
```json
{
  "success": false,
  "error": "INTERNAL_SERVER_ERROR",
  "message": "An internal server error occurred"
}
```
**Cause**: Unexpected server-side error  
**Solution**: Retry the request. If persists, contact support with timestamp

## API Endpoints

### Health Check
```
GET /api/health
```
**No authentication required**

**Success Response (200)**:
```json
{
  "status": "ok",
  "service": "TheTradeVisor API",
  "version": "1.0",
  "timestamp": "2025-11-13T06:30:00Z"
}
```

### Data Collection (MT4/MT5 EA)
```
POST /api/v1/data/collect
```
**Authentication**: Required  
**Rate Limit**: 1000 requests/hour per API key

**Request Headers**:
```
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json
```

**Request Body**:
```json
{
  "meta": {
    "version": "1.0",
    "timestamp": "2025-11-13T06:30:00Z",
    "is_first_run": false,
    "is_historical": false
  },
  "account": {
    "account_number": "12345678",
    "account_hash": "sha256_hash",
    "broker": "Broker Name",
    "server": "BrokerServer-Live",
    "trade_mode": 0,
    "balance": 10000.00,
    "equity": 10500.00,
    "margin": 500.00,
    "free_margin": 10000.00,
    "profit": 500.00,
    "currency": "USD"
  },
  "positions": [...],
  "deals": [...]
}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Data received successfully",
  "data_type": "current",
  "timestamp": "2025-11-13T06:30:00Z",
  "queued": true
}
```

### Get Accounts
```
GET /api/v1/accounts
```
**Authentication**: Required

**Success Response (200)**:
```json
{
  "data": [
    {
      "id": 1,
      "account_number": "12345678",
      "broker_name": "Broker Name",
      "platform_type": "MT5",
      "account_currency": "USD",
      "balance": 10000.00,
      "equity": 10500.00,
      "margin": 500.00,
      "free_margin": 10000.00,
      "profit": 500.00,
      "is_active": true,
      "created_at": "2025-01-01T00:00:00Z",
      "last_sync_at": "2025-11-13T06:30:00Z"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

### Get Specific Account
```
GET /api/v1/accounts/{id}
```
**Authentication**: Required

**Success Response (200)**: Same as single account object above

**Error Response (404)**:
```json
{
  "error": {
    "code": "NOT_FOUND",
    "message": "Account not found",
    "details": "The requested account does not exist or does not belong to you"
  }
}
```

### Get Trades
```
GET /api/v1/trades?account_id=1&symbol=EURUSD&from_date=2025-01-01&to_date=2025-11-13&limit=50&page=1
```
**Authentication**: Required

**Query Parameters**:
- `account_id` (optional): Filter by account ID
- `symbol` (optional): Filter by symbol
- `from_date` (optional): Start date (YYYY-MM-DD)
- `to_date` (optional): End date (YYYY-MM-DD)
- `limit` (optional): Results per page (1-100, default: 50)
- `page` (optional): Page number (default: 1)

**Success Response (200)**:
```json
{
  "data": [
    {
      "id": 123,
      "account_id": 1,
      "symbol": "EURUSD",
      "type": "buy",
      "volume": 0.10,
      "open_price": 1.0850,
      "profit": 25.50,
      "commission": -2.00,
      "swap": -0.50,
      "time": "2025-11-13T06:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total_pages": 5,
    "total": 250,
    "per_page": 50
  }
}
```

### Get Performance Analytics
```
GET /api/v1/analytics/performance?account_id=1&days=30
```
**Authentication**: Required

**Query Parameters**:
- `account_id` (optional): Filter by account ID
- `days` (optional): Time period (1, 7, 30, 90, 365, default: 30)

**Success Response (200)**:
```json
{
  "data": {
    "total_trades": 150,
    "winning_trades": 90,
    "losing_trades": 60,
    "win_rate": 60.00,
    "profit_factor": 1.85,
    "total_profit": 5500.00,
    "total_loss": 2970.00,
    "net_profit": 2530.00,
    "average_win": 61.11,
    "average_loss": 49.50,
    "largest_win": 250.00,
    "largest_loss": 180.00
  }
}
```

## MT4/MT5 Expert Advisor Integration

### Error Handling in MQL5

```mql5
string SendDataToAPI(string jsonData)
{
    char post[];
    char result[];
    string headers;
    string resultString;
    
    // Prepare headers
    headers = "Authorization: Bearer " + API_KEY + "\r\n";
    headers += "Content-Type: application/json\r\n";
    
    // Convert JSON to char array
    StringToCharArray(jsonData, post, 0, StringLen(jsonData));
    
    // Send request
    int timeout = 5000; // 5 seconds
    int res = WebRequest(
        "POST",
        "https://api.thetradevisor.com/v1/data/collect",
        headers,
        timeout,
        post,
        result,
        headers
    );
    
    // Check HTTP status
    if(res == -1) {
        Print("WebRequest error: ", GetLastError());
        return "";
    }
    
    // Convert response to string
    resultString = CharArrayToString(result);
    
    // Parse JSON response
    if(res == 200) {
        // Success
        Print("Data sent successfully: ", resultString);
        return resultString;
    }
    else if(res == 401) {
        // Invalid API key
        Print("ERROR: Invalid API key. Please check your API key in EA settings.");
        return "";
    }
    else if(res == 403) {
        // Account suspended or demo account
        Print("ERROR: Access denied. ", resultString);
        return "";
    }
    else if(res == 429) {
        // Rate limit exceeded
        Print("WARNING: Rate limit exceeded. Waiting before retry...");
        Sleep(60000); // Wait 1 minute
        return "";
    }
    else if(res == 500) {
        // Server error
        Print("ERROR: Server error. Will retry later.");
        return "";
    }
    else {
        // Other error
        Print("ERROR: HTTP ", res, " - ", resultString);
        return "";
    }
}
```

## Testing Error Scenarios

### Test Invalid API Key
```bash
curl -X POST https://api.thetradevisor.com/v1/data/collect \
  -H "Authorization: Bearer invalid_key_here" \
  -H "Content-Type: application/json" \
  -d '{"meta": {}, "account": {}}'
```

### Test Missing API Key
```bash
curl -X POST https://api.thetradevisor.com/v1/data/collect \
  -H "Content-Type: application/json" \
  -d '{"meta": {}, "account": {}}'
```

### Test 404 Not Found
```bash
curl -X GET https://api.thetradevisor.com/v1/invalid/endpoint \
  -H "Authorization: Bearer YOUR_API_KEY"
```

### Test Method Not Allowed
```bash
curl -X GET https://api.thetradevisor.com/v1/data/collect \
  -H "Authorization: Bearer YOUR_API_KEY"
```

### Test Health Check (No Auth)
```bash
curl -X GET https://api.thetradevisor.com/health
```

## Support

For API support and questions:
- Email: hello@thetradevisor.com
- Documentation: https://thetradevisor.com/api-docs

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
