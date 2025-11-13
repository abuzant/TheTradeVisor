# TheTradeVisor API - Quick Reference for MT4/MT5 Integration

## Base URL
```
https://thetradevisor.com/api
```

**Note**: All API endpoints return **JSON only** - no HTML error pages will ever be sent to MT4/MT5 terminals.

## Authentication
All endpoints (except `/health`) require API key authentication:

```
Authorization: Bearer YOUR_API_KEY
```

Get your API key from: https://thetradevisor.com/settings/api-key

## Quick Error Reference

| HTTP Code | Error Code | Meaning | Action |
|-----------|------------|---------|--------|
| 200 | - | Success | Continue |
| 400 | VALIDATION_ERROR | Invalid data | Fix request data |
| 401 | UNAUTHENTICATED | No API key | Add Authorization header |
| 401 | Invalid API key | Wrong/inactive key | Check API key |
| 403 | Account suspended | User suspended | Contact support |
| 403 | Demo account not allowed | Demo/contest account | Use real account |
| 403 | Account paused | Account paused | Unpause from dashboard |
| 404 | NOT_FOUND | Wrong endpoint | Check URL |
| 405 | METHOD_NOT_ALLOWED | Wrong HTTP method | Use correct method |
| 429 | Rate limit exceeded | Too many requests | Wait and retry |
| 500 | INTERNAL_SERVER_ERROR | Server error | Retry later |

## Common Endpoints

### Health Check (No Auth Required)
```
GET /api/health
```

### Send Trading Data (MT4/MT5 EA)
```
POST /api/v1/data/collect
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json

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
  "positions": [],
  "deals": []
}
```

**Success Response**:
```json
{
  "success": true,
  "message": "Data received successfully",
  "data_type": "current",
  "timestamp": "2025-11-13T06:30:00Z",
  "queued": true
}
```

## MQL5 Error Handling Template

```mql5
// Send data to API
int SendToAPI(string jsonData)
{
    char post[];
    char result[];
    string headers = "Authorization: Bearer " + API_KEY + "\r\n";
    headers += "Content-Type: application/json\r\n";
    
    StringToCharArray(jsonData, post, 0, StringLen(jsonData));
    
    int res = WebRequest(
        "POST",
        "https://thetradevisor.com/api/v1/data/collect",
        headers,
        5000,
        post,
        result,
        headers
    );
    
    if(res == -1) {
        Print("WebRequest error: ", GetLastError());
        return -1;
    }
    
    string response = CharArrayToString(result);
    
    // Handle responses
    switch(res) {
        case 200:
            Print("✓ Data sent successfully");
            return 0;
            
        case 401:
            Print("✗ ERROR: Invalid API key - Check EA settings");
            return -1;
            
        case 403:
            Print("✗ ERROR: Access denied - ", response);
            // Could be: suspended account, demo account, or paused account
            return -1;
            
        case 429:
            Print("⚠ WARNING: Rate limit exceeded - Waiting 60 seconds");
            Sleep(60000);
            return 1; // Retry
            
        case 500:
            Print("⚠ WARNING: Server error - Will retry later");
            return 1; // Retry
            
        default:
            Print("✗ ERROR: HTTP ", res, " - ", response);
            return -1;
    }
}

// Usage in OnTimer or OnTick
void OnTimer()
{
    string data = BuildJsonData(); // Your function to build JSON
    
    int result = SendToAPI(data);
    
    if(result == 0) {
        // Success - continue
    }
    else if(result == 1) {
        // Retry later
    }
    else {
        // Fatal error - stop or alert user
    }
}
```

## Testing Your Integration

### 1. Test Health Endpoint (No Auth)
```bash
curl https://thetradevisor.com/api/health
```

Expected: `{"status":"ok",...}`

### 2. Test With Invalid API Key
```bash
curl -X POST https://thetradevisor.com/api/v1/data/collect \
  -H "Authorization: Bearer invalid_key" \
  -H "Content-Type: application/json" \
  -d '{"meta":{},"account":{}}'
```

Expected: `{"success":false,"error":"Invalid API key",...}`

### 3. Test With Valid API Key
```bash
curl -X POST https://thetradevisor.com/api/v1/data/collect \
  -H "Authorization: Bearer YOUR_REAL_API_KEY" \
  -H "Content-Type: application/json" \
  -d @sample_data.json
```

Expected: `{"success":true,"message":"Data received successfully",...}`

## Rate Limits

- **API Key**: 1000 requests/hour
- **IP Address**: 500 requests/hour

Rate limit headers included in all responses:
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Requests remaining
- `X-RateLimit-Reset`: Unix timestamp when limit resets

## Support

- **Email**: hello@thetradevisor.com
- **Full Documentation**: https://thetradevisor.com/api-docs
- **Detailed Error Codes**: See `/www/docs/API_ERROR_CODES.md`

## Important Notes

✅ **Always JSON**: All responses are JSON - no HTML error pages  
✅ **Check `success` field**: Every response has `"success": true/false`  
✅ **Demo accounts rejected**: Only real trading accounts accepted  
✅ **Rate limiting**: Respect rate limits to avoid 429 errors  
✅ **Error logging**: All API errors are logged server-side for debugging  

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
