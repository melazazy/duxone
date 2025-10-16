<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

<p align="center">
    <a href="https://github.com/laravel/framework/actions">
        <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
    </a>
</p>

# DuxOne - All-in-One Business Platform

**DuxOne** is a comprehensive SaaS (Software as a Service) platform designed specifically for Small and Medium Enterprises (SMEs) in the MENA region. Built with modern Laravel technology, it provides modular business management solutions starting with Accounting and expanding to HR, CRM, Inventory, and Clinic management.

## 🎯 Project Vision

To provide an intuitive, scalable, and compliant business platform that enables SMEs to manage their operations efficiently while adhering to regional business regulations including VAT compliance and e-invoicing requirements.

## ✨ Key Features

### Core Architecture
- **Multi-tenant SaaS Platform** - Isolated tenant environments with custom domains
- **Modular Design** - Extensible plugin-based architecture
- **Role-based Access Control** - Granular permissions and user management
- **Real-time Dashboard** - Comprehensive business analytics and KPIs

### Accounting Module (Flagship)
- **Financial Dashboard** - Revenue, expenses, profit, and cash flow tracking
- **Invoicing System** - Professional invoices with VAT support and PDF generation
- **Expense Management** - Receipt tracking, categorization, and approval workflows
- **General Ledger** - Automated and manual journal entries
- **Bank Reconciliation** - Manual and CSV import capabilities
- **Client & Vendor Management** - Centralized contact and balance tracking
- **Tax Compliance** - VAT calculations and regulatory reporting
- **Financial Reports** - P&L, Balance Sheet, Cash Flow statements

### Platform Features
- **Responsive Design** - Mobile-first approach with RTL support
- **Real-time Updates** - Live data synchronization via Livewire
- **API Integration** - RESTful APIs for third-party integrations
- **Activity Logging** - Comprehensive audit trails
- **Data Backup** - Automated backup and recovery systems
- **Security First** - Enterprise-grade security with encryption and compliance

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 12.x with PHP 8.2+
- **Database**: MySQL 8.0
- **Multi-tenancy**: Stancl Tenancy
- **Authentication**: Laravel Sanctum
- **Billing**: Laravel Cashier
- **Queue Management**: Laravel Horizon
- **Monitoring**: Laravel Telescope

### Frontend
- **Styling**: TailwindCSS
- **Build Tool**: Vite
- **Components**: Livewire 3.x with Blade components
- **Admin Panel**: Filament v3.x

### DevOps
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint
- **CI/CD**: GitHub Actions

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose
- PHP 8.2+ (for local development)
- Composer
- Node.js and npm

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd business-platform
   ```

2. **Environment Setup**
   ```bash
   cp src/.env.example src/.env
   ```

3. **Start Docker Environment**
   ```bash
   docker-compose up -d
   ```

4. **Install Dependencies**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   ```

5. **Generate Application Key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run Migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. **Install Frontend Dependencies**
   ```bash
   docker-compose exec app npm run build
   ```

8. **Access the Application**
   - Main Application: http://localhost:8081
   - phpMyAdmin: http://localhost:8080

### Local Development

For local development without Docker:

1. **Install PHP dependencies**
   ```bash
   cd src
   composer install
   ```

2. **Install Node dependencies**
   ```bash
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development servers**
   ```bash
   php artisan serve
   npm run dev
   ```

## 📁 Project Structure

```
src/
├── app/
│   ├── Http/Controllers/     # Traditional MVC controllers
│   ├── Livewire/            # Livewire components
│   ├── Models/              # Eloquent models
│   └── Providers/           # Service providers
├── bootstrap/               # Bootstrap files
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── public/                  # Public assets
├── resources/
│   ├── js/                  # JavaScript files
│   ├── css/                 # CSS files
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── storage/                 # Application storage
├── tests/                   # Test files
└── vendor/                  # Composer dependencies
```

## 🔧 Configuration

### Environment Variables
Key environment variables to configure:

```env
APP_NAME=DuxOne
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=duxone
DB_USERNAME=duxone_user
DB_PASSWORD=your_secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

### Multi-tenancy Setup

1. **Configure Tenant Domains**
   ```bash
   php artisan tenancy:install
   ```

2. **Create Tenant**
   ```bash
   php artisan tenant:create
   ```

3. **Tenant Migration**
   ```bash
   php artisan tenants:run 'migrate'
   ```

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=YourTestName

# Run with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Code formatting
php artisan pint

# Static analysis
composer analyse
```

## 🚀 Deployment

### Docker Deployment
```bash
# Build and start production containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan optimize:clear
```

### Manual Deployment
1. **Deploy to server**
2. **Install dependencies**: `composer install --no-dev`
3. **Optimize autoloader**: `composer dump-autoload --optimize`
4. **Run migrations**: `php artisan migrate --force`
5. **Clear caches**: `php artisan optimize:clear`
6. **Build assets**: `npm run build`

## 🔐 Security

### Implemented Security Measures
- **Authentication**: Laravel Sanctum for API authentication
- **Authorization**: Role-based access control with Spatie Permission
- **Data Encryption**: AES-256 encryption for sensitive data
- **CSRF Protection**: Built-in Laravel CSRF protection
- **XSS Prevention**: Blade templating auto-escaping
- **Rate Limiting**: API rate limiting
- **Activity Logging**: Comprehensive audit trails
- **Data Validation**: Form request validation

### Security Best Practices
- Regular security updates
- Environment variable protection
- Database connection encryption
- File upload validation
- SQL injection prevention
- Input sanitization

## 📊 Monitoring & Logging

### Application Monitoring
- **Laravel Telescope**: Real-time application monitoring
- **Laravel Horizon**: Queue monitoring
- **Sentry Integration**: Error tracking (planned)

### Logging
- **Structured Logging**: JSON-formatted logs
- **Log Rotation**: Automatic log rotation
- **Activity Logging**: User action tracking
- **Error Tracking**: Comprehensive error reporting

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Make your changes following coding standards
4. Run tests: `php artisan test`
5. Format code: `php artisan pint`
6. Commit changes: `git commit -m 'Add your feature'`
7. Push to branch: `git push origin feature/your-feature`
8. Create a Pull Request

### Coding Standards
- Follow PSR-1, PSR-4, PSR-12 standards
- Use Laravel conventions
- Write comprehensive tests
- Document your code
- Follow SOLID principles

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Team** - For the excellent framework
- **Filament Team** - For the amazing admin panel
- **Spatie Team** - For the incredible Laravel packages
- **Stancl** - For the multi-tenancy package
- **TailwindCSS Team** - For the utility-first CSS framework

## 📞 Support

For support and questions:
- **Documentation**: Check the `/docs` directory
- **Issues**: Create an issue on GitHub
- **Discussions**: Join our GitHub discussions
- **Email**: support@duxone.com

## 🗺 Roadmap

### Phase 1 (Current)
- [x] Core platform setup
- [x] Multi-tenant architecture
- [x] Authentication system
- [ ] Accounting module completion
- [ ] Basic dashboard

### Phase 2 (Q1 2024)
- [ ] Advanced reporting
- [ ] API development
- [ ] Payment integration
- [ ] Mobile app development

### Phase 3 (Q2 2024)
- [ ] HR module
- [ ] CRM module
- [ ] Advanced analytics
- [ ] Third-party integrations

### Phase 4 (Q3 2024)
- [ ] Inventory module
- [ ] Clinic module
- [ ] Enterprise features
- [ ] Market expansion

---

**Built with ❤️ for MENA SMEs**
