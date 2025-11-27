# Analytics Hardening Playbook
**Author:** Cascade AI (with Ruslan Abuzant)  
**Last Updated:** 2025-11-27  

This playbook documents the safeguards we implemented across TheTradeVisor analytics stack in November 2025. It is written for webmasters and operations engineers who need to understand *what* protections are active, *why* they matter, and *how* to validate them in production-like environments.

---

## 1. Executive Overview
We completed a full remediation of the logic-flow risks identified during the LOGIC_FLOW_ANALYSIS investigation. Each fix below is already live in the codebase and protects real traffic today:

1. **API credential hardening** — strict format enforcement and contextual logging for both retail and enterprise keys.  
2. **Enterprise brokerage validation** — keys only authenticate if their broker account is active and loaded into scope.  
3. **Analytics cache isolation** — namespaced keys, configurable TTLs, and lifecycle logging prevent data bleed and stale dashboards.  
4. **Sensitive-route throttling** — regeneration endpoints now resist brute force attempts through rate limiting.  
5. **Ingestion-time validation & observability** — MT5 data collection rejects malformed payloads and surfaces helper failures as actionable warnings.

These controls eliminate the high-risk items from the audit and align with our production safety rules.

---

## 2. Safeguards Implemented

### 2.1 API Key Validation
- Retail middleware now requires keys to match the `tvsr_` prefix, exact length, and SHA-256 entropy expectations before any database lookup.  
  - See @app/Support/ApiKeyValidator.php#7-64 and @app/Http/Middleware/ValidateApiKey.php#33-75.  
- Enterprise middleware applies the same detection, only allowing keys detected as `enterprise`, and rejects inactive brokers.  
  - See @app/Http/Middleware/EnterpriseApiAuth.php#54-102.

**Operational benefit:** malformed or recycled credentials are stopped before they hit business logic, and logs capture enough context (prefix, client IP, user agent) to investigate abuse.

### 2.2 Enterprise Broker Re-validation
- Enterprise API keys are hydrated with their broker relationship and checked for `is_active` as well as subscription grace periods before passing requests onward.  
- When a key is valid, the middleware attaches the broker and key models to the request for controllers to consume safely.  

**Operational benefit:** stale or revoked enterprise contracts can no longer make API calls, protecting downstream analytics from unauthorized access.

### 2.3 Cache Isolation & Observability
- Performance and broker analytics now build cache keys with user namespaces and deterministic hashes, preventing cross-user bleed.  
- Cache TTLs come from `config/analytics.php`, which supports `ANALYTICS_PERFORMANCE_TTL` and `ANALYTICS_BROKER_TTL` overrides.  
- Rebuild events are logged with the full context (account IDs, currency, broker counts) for audit trails.  
  - Implementation: @app/Services/PerformanceMetricsService.php#27-55, @app/Services/BrokerAnalyticsService.php#69-95, and @config/analytics.php#1-8.

**Operational benefit:** analytics data stays scoped to each user, stale caches can be tuned without code changes, and rebuilds are observable.

### 2.4 Rate Limiting for Sensitive Actions
- The web and enterprise dashboards throttle API key regeneration to three requests per minute, blocking brute force attempts against key rotation interfaces.  
  - See @routes/web.php#45-57 and @routes/web.php#174-178.

**Operational benefit:** prevents credential churn abuse without affecting legitimate administrative workflows.

### 2.5 Data Collection Validation & Warning Surfacing
- MT5 ingestion (`/api/v1/data/collect`) now validates payload shape before touching nested arrays, guaranteeing required fields exist.  
- When helper services (whitelisted usage tracking, missing data detection) encounter issues, the API returns a `warnings` array instead of failing silently.  
  - Implementation: @app/Http/Controllers/Api/DataCollectionController.php#39-395.

**Operational benefit:** protects ingestion from malformed payloads and gives operators immediate signals when analytics-side helpers misbehave.

---

## 3. Validation Checklist for Webmasters
Follow this sequence in a staging or QA environment after each deployment.

1. **Retail API key validation**  
   - Crafted malformed keys (wrong prefix, length) should be rejected with HTTP 401 and log entries tagged `Malformed API key format`.  
   - A valid key should pass and attach `authenticated_user` to the request.

2. **Enterprise API key workflow**  
   - Use a known active `ent_` key to call `/api/enterprise/v1/accounts`; expect 200.  
   - Mark the broker inactive, repeat the call; expect HTTP 403 `BROKER_INACTIVE` and a warning log.  
   - Submit malformed prefixes; expect HTTP 401 `INVALID_API_KEY_FORMAT`.

3. **Analytics cache rebuild visibility**  
   - Hit `/analytics/performance` and `/broker-analytics`; tail `storage/logs/laravel.log` to confirm `cache rebuild` entries with corresponding parameters.  
   - Adjust `.env` overrides, run `php artisan config:clear`, confirm TTL shifts by timing a cache expiry.

4. **Rate limiting guardrails**  
   - From the user dashboard, trigger API key regeneration four times inside one minute; the fourth should return HTTP 429 (or display a rate-limit message).  
   - Repeat inside the enterprise dashboard.

5. **MT5 ingestion warnings**  
   - Send a well-formed payload; response should omit the `warnings` field.  
   - Simulate a helper failure (e.g., temporarily break `WhitelistedBrokerUsage::updateOrCreate`) and re-send; response and logs should contain a `whitelisted_usage` warning without breaking ingestion.

---

## 4. Operational Tips & Configuration
- **Env overrides:** manage analytics cache TTLs via `ANALYTICS_PERFORMANCE_TTL` and `ANALYTICS_BROKER_TTL`; keep the production values in sync with dashboard freshness requirements.  
- **Log review:** filter `laravel.log` for `Broker analytics cache rebuild`, `Performance metrics cache rebuild`, and `Malformed API key format` to track security posture.  
- **Deployment safety:** after releases, run the validation checklist and archive results in the deployment journal for audit compliance.  
- **Documentation updates:** link this playbook in internal runbooks and onboarding materials for anyone operating analytics or credentials.

---

## 5. Conclusion
The safeguards described here are *in production today*. They eliminate the high-risk findings from the audit and strengthen our security posture without sacrificing performance. Use this playbook to brief stakeholders, onboard new team members, or adapt the patterns for future modules.

> 💡 **Reminder:** when introducing new analytics services, copy the patterns above—namespaced cache keys, env-driven TTLs, explicit validation, and contextual logging should be treated as minimum standards.
