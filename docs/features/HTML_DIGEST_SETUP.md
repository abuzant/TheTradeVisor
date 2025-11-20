# HTML Digest Setup (Performance Snapshot)

## Overview

Instead of LLM, this approach renders a user’s performance page as a self-contained HTML file and stores it on disk. At send time, the HTML is read and emailed. It’s lightweight, deterministic, and mirrors your app’s design.

## Components

- **DigestRenderService**: Renders the performance page as a standalone HTML file with inlined CSS.
- **Blade view**: `digest.performance-html` produces a clean, email-friendly layout.
- **Artisan command**: `digests:generate-html {user?}` creates a test HTML file for any user.

## Usage

### Generate a test digest

```bash
# For the first admin user
php artisan digests:generate-html

# For a specific user ID
php artisan digests:generate-html 22
```

The file is saved to `storage/app/private/digests/{user_id}/{Y-m-d}.html`.

### File size and storage

- Typical HTML digest: ~6 KB per user per day.
- Storage path: `storage/app/private/digests/`
- Disk usage: negligible even for thousands of users.

### Automation (future)

- Schedule generation via cron:
  - Daily at 23:00: `0 23 * * * cd /www && php artisan digests:generate-html`
  - Weekly on Friday at 23:30: `30 23 * * 5 cd /www && php artisan digests:generate-html`
- At send time, read the file and embed as email body.

## Security

- Files are stored under `storage/app/private/` (not web-accessible).
- Only the Laravel application can read/send them.

## Verification

Open the generated HTML in a browser to ensure it matches your performance page layout.

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
