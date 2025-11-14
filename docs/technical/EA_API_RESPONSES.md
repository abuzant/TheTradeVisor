# TheTradeVisor EA - API Response Handling

## Overview

This document describes how the TheTradeVisor Expert Advisors (MT4 and MT5) handle API responses from the data collection endpoint.

**Endpoint:** `https://api.thetradevisor.com/api/v1/data/collect`  
**Method:** POST  
**Content-Type:** application/json  
**Authentication:** Bearer token (API key)

---

## API Response Format

All API responses are in JSON format with a consistent structure:

```json
{
  "success": true|false,
  "message": "Human-readable message",
  "error": "Error type (only if success=false)",
  "data_type": "current|historical (only on success)",
  "timestamp": "ISO 8601 timestamp",
  "queued": true|false
}
```

---

## HTTP Status Codes

### Success Responses

#### 200 OK - Data Received Successfully
```json
{
  "success": true,
  "message": "Data received successfully",
  "data_type": "current",
  "timestamp": "2025-11-14T05:35:00Z",
  "queued": true
}
```

**EA Behavior:**
- ✅ Returns `true` from `SendDataToServer()` / `SendToAPI()`
- Logs success message (if DEBUG_MODE enabled)
- Continues normal operation

#### 201 Created - Data Received Successfully
Same as 200 OK. Both are treated as success.

---

### Client Error Responses (4xx)

#### 400 Bad Request - Invalid Data Structure
```json
{
  "success": false,
  "error": "Invalid data structure",
  "message": "Required fields: meta, account"
}
```

**EA Behavior:**
- ❌ Returns `false`
- Logs: `"Invalid data format (400): [response]"`
- **Action Required:** This indicates a bug in the EA code

---

#### 401 Unauthorized - Invalid API Key
```json
{
  "success": false,
  "error": "Unauthorized",
  "message": "Invalid or missing API key"
}
```

**EA Behavior:**
- ❌ Returns `false`
- Shows Alert: `"INVALID API KEY (401): Please check your API key in EA settings."`
- Logs error to Experts tab
- **Action Required:** User must update API key in EA settings

---

#### 403 Forbidden - Demo Account Rejected
```json
{
  "success": false,
  "error": "Demo account not allowed",
  "message": "Demo and contest accounts are not supported. Please connect a real trading account to use TheTradeVisor.",
  "account_type": "demo"
}
```

**EA Behavior:**
- ❌ Returns `false`
- Shows Alert: `"DEMO ACCOUNT REJECTED: TheTradeVisor only accepts real trading accounts."`
- Logs error to Experts tab
- **Action Required:** User must connect a real trading account

---

#### 403 Forbidden - Account Suspended
```json
{
  "success": false,
  "error": "Account suspended",
  "message": "Your account has been suspended. Please contact support."
}
```

**EA Behavior:**
- ❌ Returns `false`
- Shows Alert: `"ACCOUNT SUSPENDED: Your TheTradeVisor account has been suspended. Contact support."`
- Logs error to Experts tab
- **Action Required:** User must contact support

---

#### 403 Forbidden - Account Paused
```json
{
  "success": false,
  "error": "Account paused",
  "message": "This trading account has been paused. [reason]",
  "paused_at": "2025-11-14T05:35:00Z"
}
```

**EA Behavior:**
- ❌ Returns `false`
- Logs warning: `"ACCOUNT PAUSED: This trading account has been paused in TheTradeVisor."`
- No alert shown (less critical than suspension)
- **Action Required:** User can unpause account in TheTradeVisor settings

---

### Server Error Responses (5xx)

#### 500 Internal Server Error
```json
{
  "success": false,
  "error": "Internal server error",
  "message": "Failed to process data. Please try again."
}
```

**EA Behavior:**
- ❌ Returns `false`
- Logs: `"Server error (500): [response]"`
- Will retry on next interval
- **Action Required:** Usually temporary - EA will retry automatically

---

### Network Errors

#### -1 WebRequest Failed
This occurs when the WebRequest function fails (not an HTTP error).

**Common Causes:**
1. **Error 4060:** URL not in allowed list
2. **No internet connection**
3. **Firewall blocking request**
4. **DNS resolution failure**

**EA Behavior (Error 4060):**
- ❌ Returns `false`
- Shows Alert with instructions:
  ```
  WebRequest failed. Error code: 4060
  
  IMPORTANT: Add this URL to allowed list:
  Tools -> Options -> Expert Advisors -> Allow WebRequest for listed URL:
  https://api.thetradevisor.com/api/v1/data/collect
  ```

**EA Behavior (Other errors):**
- ❌ Returns `false`
- Logs: `"WebRequest failed. Error code: [code]"`
- Will retry on next interval

---

## Response Parsing Logic

### MT5 Implementation

```mql5
bool SendDataToServer(string jsonData)
{
    // ... WebRequest code ...
    
    string response = CharArrayToString(result);
    
    if(res == 200 || res == 201)
    {
        if(StringFind(response, "\"success\":true") >= 0)
        {
            // Success
            return true;
        }
        else
        {
            // HTTP 200 but JSON says failure
            lastError = "Server returned success code but JSON indicates failure: " + response;
            return false;
        }
    }
    else if(res == 403)
    {
        // Check for specific 403 types
        if(StringFind(response, "Demo account") >= 0 || StringFind(response, "demo") >= 0)
        {
            // Demo account
            Alert("DEMO ACCOUNT REJECTED: TheTradeVisor only accepts real trading accounts.");
        }
        else if(StringFind(response, "suspended") >= 0)
        {
            // Account suspended
            Alert("ACCOUNT SUSPENDED: Your TheTradeVisor account has been suspended. Contact support.");
        }
        else if(StringFind(response, "paused") >= 0)
        {
            // Account paused
            Print("WARNING: ACCOUNT PAUSED: This trading account has been paused in TheTradeVisor.");
        }
        return false;
    }
    else if(res == 400)
    {
        // Bad request
        lastError = "Invalid data format (400): " + response;
        return false;
    }
    else if(res == 401)
    {
        // Unauthorized
        Alert("INVALID API KEY (401): Please check your API key in EA settings.");
        return false;
    }
    else if(res == 500)
    {
        // Server error
        lastError = "Server error (500): " + response;
        return false;
    }
    else if(res == -1)
    {
        // WebRequest error
        int errorCode = GetLastError();
        if(errorCode == 4060)
        {
            Alert("Add URL to allowed list: Tools -> Options -> Expert Advisors");
        }
        return false;
    }
    else
    {
        // Other HTTP error
        lastError = "HTTP error " + IntegerToString(res) + ": " + response;
        return false;
    }
}
```

### MT4 Implementation

Same logic as MT5, with minor syntax differences for MT4 compatibility.

---

## Error Recovery Strategies

### Automatic Retry
The EA automatically retries failed requests on the next interval:
- **MT4:** Every `SendInterval` seconds (default: 60s)
- **MT5:** Every `UPDATE_INTERVAL` seconds (default: 120s)

### Exponential Backoff (MT5 Historical Upload)
For historical data uploads, MT5 uses exponential backoff:
- **Attempt 1:** Retry after `HISTORY_UPLOAD_INTERVAL` seconds (default: 30s)
- **Attempt 2:** Retry after 60s (2x)
- **Attempt 3:** Retry after 120s (4x)
- **Attempt 4:** Retry after 240s (8x)
- **Attempt 5:** Retry after 480s (16x)
- **After 5 failures:** Skip this day and move to next

### No Retry Scenarios
The EA does **NOT** retry for:
- ❌ Demo account rejection (403)
- ❌ Account suspended (403)
- ❌ Invalid API key (401)
- ❌ Invalid data structure (400)

These require user intervention to fix.

---

## Data Flow

```
┌─────────────┐
│   MT4/MT5   │
│     EA      │
└──────┬──────┘
       │
       │ POST /api/v1/data/collect
       │ Authorization: Bearer {API_KEY}
       │ Content-Type: application/json
       │
       ▼
┌─────────────────────────┐
│  API Middleware         │
│  - Authenticate         │
│  - Rate Limit           │
│  - Validate Request     │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ DataCollectionController│
│  - Check account status │
│  - Reject demo accounts │
│  - Store raw JSON       │
│  - Queue processing job │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│   JSON Response         │
│   HTTP Status Code      │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│   EA Response Handler   │
│   - Parse JSON          │
│   - Check status code   │
│   - Log/Alert user      │
│   - Return true/false   │
└─────────────────────────┘
```

---

## Testing Response Handling

### Manual Testing

You can test EA response handling by temporarily modifying the API to return different responses:

1. **Test 401 (Invalid API Key):**
   - Use an invalid API key in EA settings
   - Expected: Alert shown, error logged

2. **Test 403 (Demo Account):**
   - Connect EA to a demo account
   - Expected: Alert shown, data rejected

3. **Test 403 (Account Paused):**
   - Pause account in TheTradeVisor settings
   - Expected: Warning logged, no alert

4. **Test 500 (Server Error):**
   - Temporarily break the API endpoint
   - Expected: Error logged, retry on next interval

5. **Test -1/4060 (WebRequest Not Allowed):**
   - Remove URL from allowed list
   - Expected: Alert with instructions

---

## Security Considerations

### Hardcoded API Endpoint
The API endpoint is **hardcoded** in both EAs and cannot be changed by users:

```mql5
// MT5
string API_URL = "https://api.thetradevisor.com/api/v1/data/collect";

// MT4
string API_URL = "https://api.thetradevisor.com/api/v1/data/collect";
```

**Reasons:**
1. Prevents users from redirecting data to unauthorized servers
2. Ensures data security and integrity
3. Protects against man-in-the-middle attacks
4. Simplifies support (only one endpoint to troubleshoot)

### API Key Security
- API keys are **never logged** in plain text
- Keys are transmitted only via HTTPS
- Keys are stored in EA settings (encrypted by MT4/MT5)
- Users are warned to keep keys secure

### Data Transmission
- All data sent via **HTTPS only**
- TLS 1.2+ required
- Certificate validation enforced by MT4/MT5

---

## Monitoring & Logging

### Success Logging (DEBUG_MODE)
```
✓ Data sent successfully. Server response: {"success":true,"message":"Data received successfully",...}
```

### Error Logging
```
ERROR: DEMO ACCOUNT REJECTED: TheTradeVisor only accepts real trading accounts.
ERROR: INVALID API KEY (401): Please check your API key in EA settings.
ERROR: Server error (500): {"success":false,"error":"Internal server error",...}
WARNING: ACCOUNT PAUSED: This trading account has been paused in TheTradeVisor.
```

### Historical Upload Progress (MT5)
```
Day 10 uploaded successfully. Progress: 2.7% (10/365 days)
ERROR: Failed to upload day 11. Will retry in 60 seconds (attempt 2/5). Error: Server error (500)
```

---

## Changelog

### 2025-11-14
- Hardcoded API endpoint for security
- Enhanced JSON response parsing
- Added specific handling for all HTTP status codes
- Improved error messages and user alerts
- Added exponential backoff for MT5 historical uploads

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
