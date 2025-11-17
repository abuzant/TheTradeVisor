# Digest Feature Setup (Template-Only)

## Overview

The Digest feature generates concise trading summaries for users (daily/weekly). It aggregates key analytics and renders them using lightweight, predictable templates—no external LLM required.

## Components

- **DigestService**: Computes analytics (PnL, win rate, top pairs, volume trend, best/worst times, risky symbols, long-running positions).
- **DigestInsightService**: Turns analytics into plain-English insights using templates. No external dependencies.
- **Admin Panel**: `/admin/digest-control` lets admins enable/disable the feature and test generation.
- **My Digest Page**: `/my-digest` shows users their current digest (preview).

## Installation

### 1. Environment variables

Add to your `.env`:

```env
# Enable/disable digest generation and sending globally
DIGEST_ENABLED=false

# LLM integration (optional, not required for template mode)
DIGEST_LLM_ENABLED=false
DIGEST_LLM_ENDPOINT=http://127.0.0.1:11434/api/generate
DIGEST_MODEL=gemma2:2b
```

### 2. Clear config cache

```bash
php artisan config:clear
```

## Admin Controls

Visit `/admin/digest-control` (Admin dropdown) to:

- Enable/disable the digest feature globally.
- Test digest generation for a sample user.
- View last run status and errors.

## Usage

### Users

- Opt in/out from `/profile` (Daily/Weekly checkboxes).
- View their current digest at `/my-digest`.

### Sending

When ready to enable sending:

```bash
# Daily
php artisan digests:send daily

# Weekly
php artisan digests:send weekly
```

Add to your cron/scheduler to run automatically.

## Optional LLM Integration (Future)

If you later want natural language generation:

1. Install Ollama and pull a model (e.g., `gemma2:2b`).
2. Set `DIGEST_LLM_ENABLED=true` in `.env`.
3. The service will call the LLM; templates remain as fallback.

## Security Notes

- Admin-only controls prevent non-admin users from enabling the feature.
- All generated content is deterministic via templates; no external API calls.

## Troubleshooting

- **Digest page shows disabled**: Ensure `DIGEST_ENABLED=true` and config cache cleared.
- **Test generation fails**: Check logs for database errors or missing user data.

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
