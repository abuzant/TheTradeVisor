# TheTradeVisor

A comprehensive trading analytics and account management platform built with Laravel 11 and modern web technologies.

## 🚀 Features

- **Trading Account Management** - Connect and manage multiple trading accounts
- **Performance Analytics** - Detailed analytics and performance metrics for trading activities
- **Trade History** - Complete trade history tracking and analysis
- **Broker Integration** - Support for multiple broker integrations
- **Symbol Mapping** - Advanced symbol mapping and management
- **Data Export** - Export trading data in various formats
- **User Management** - Role-based access control and user administration
- **Real-time Dashboard** - Interactive dashboard with real-time updates
- **Currency Conversion** - Multi-currency support with automatic rate conversion

## 📋 Requirements

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **Database**: SQLite (default) or MySQL/PostgreSQL
- **Redis** (optional, for caching and queues)

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone git@github.com:yourusername/TheTradeVisor.git
cd TheTradeVisor
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure your database and other settings:

```env
APP_NAME=TheTradeVisor
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=sqlite
# Or configure MySQL/PostgreSQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tradevisor
# DB_USERNAME=root
# DB_PASSWORD=

# AWS Configuration (for file storage)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
```

### 5. Database Setup

```bash
# Create SQLite database (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Seed database with sample data
php artisan db:seed
```

### 6. Laravel Passport Setup

```bash
php artisan passport:install
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

```bash
# For production
npm run build

# For development
npm run dev
```

## 🚀 Running the Application

### Development

```bash
# Start all services (server, queue, logs, vite)
composer dev
```

Or run services individually:

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Vite dev server
npm run dev
```

### Production

```bash
# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue worker (use supervisor in production)
php artisan queue:work --daemon

# Serve with your web server (Nginx/Apache)
```

## 📦 Tech Stack

### Backend
- **Laravel 11** - PHP Framework
- **Laravel Passport** - API Authentication
- **Laravel Breeze** - Authentication scaffolding
- **DomPDF** - PDF generation
- **AWS SDK** - Cloud storage integration
- **Predis** - Redis client

### Frontend
- **Vite** - Build tool
- **TailwindCSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Axios** - HTTP client

### Database
- **SQLite/MySQL/PostgreSQL** - Primary database
- **Redis** - Caching and queue management

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── ...
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── public/                   # Public assets
├── resources/
│   ├── views/                # Blade templates
│   ├── js/                   # JavaScript files
│   └── css/                  # CSS files
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── ...
├── storage/                  # Application storage
└── tests/                    # Test files
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📚 Documentation

- [Export and Filter Features](EXPORT_AND_FILTER_FEATURES.md)
- [User Guide - Exports](USER_GUIDE_EXPORTS.md)
- [Project Structure](PROJECT_STRUCTURE.md)

## 🔒 Security

- All sensitive data is encrypted
- API authentication via Laravel Passport
- CSRF protection enabled
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating

If you discover any security vulnerabilities, please email hello@thetradevisor.com.

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 👥 Support

For support, email hello@thetradevisor.com or visit https://thetradevisor.com/

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI components styled with [TailwindCSS](https://tailwindcss.com)
- Icons from various open-source projects
