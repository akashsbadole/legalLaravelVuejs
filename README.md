# LegalLaravel CRM

A comprehensive legal practice management system built with Laravel, featuring case management, client portal, billing, document handling, and AI-powered productivity tools.

## Features

- **Case Management**: Complete case lifecycle management with timeline tracking
- **Client Portal**: Secure client access to cases, documents, and invoices
- **Billing & Invoicing**: Automated invoicing with time tracking and expense management
- **Document Management**: Secure document upload, storage, and sharing
- **CRM Integration**: Lead management, communication tracking, and client relationships
- **Role-Based Access**: Attorney, paralegal, and client role management
- **Two-Factor Authentication**: Enhanced security with TOTP and backup codes
- **API Support**: RESTful API for mobile applications
- **Multi-Language**: Hindi and English language support
- **Cloud Storage**: AWS S3 integration for scalable file storage

## Quick Start

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/legallaravel.git
   cd legallaravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

### Production Deployment

For detailed production deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md).

### Connecting AWS S3 (Production)

To use AWS S3 for secure document storage, update your `.env` file with your credentials:

```bash
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket_name
AWS_URL=https://your_bucket_name.s3.your_region.amazonaws.com
```

#### Quick Docker Deployment

```bash
# Clone and configure
git clone https://github.com/yourusername/legallaravel.git
cd legallaravel
cp .env.example .env

# Edit .env with production values
nano .env

# Deploy with Docker
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force
```

## Environment Variables

See `.env.example` for all required environment variables. Key configurations:

- **Database**: MySQL/PostgreSQL with Redis for caching
- **Mail**: Mailgun/SMTP for email delivery
- **Storage**: AWS S3 for file storage (Recommended for Production)
- **SMS**: Msg91/Twilio for SMS notifications
- **Security**: Force HTTPS, security headers

## API Documentation

The application provides a RESTful API for mobile applications:

- **Base URL**: `/api/v1`
- **Authentication**: Laravel Sanctum tokens
- **Endpoints**: Cases, invoices, documents, users

## Security Features

- CSRF protection on all forms
- XSS prevention with Content Security Policy
- SQL injection protection via Eloquent ORM
- Secure file upload validation
- Two-factor authentication
- Role-based access control
- Encrypted sensitive data storage

## Performance

- Redis caching for sessions and data
- Database query optimization with eager loading
- Image optimization and compression
- CDN-ready asset compilation
- Queue processing for background jobs

## Monitoring

- Laravel Telescope for debugging
- Automated database backups
- Health check endpoints
- Error tracking with Sentry
- Uptime monitoring

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Documentation: [DEPLOYMENT.md](DEPLOYMENT.md)
- Issues: GitHub Issues
- Email: support@legallaravel.com

## Roadmap

- [ ] AI-powered case analysis
- [ ] Advanced reporting dashboards
- [ ] Mobile application
- [ ] Calendar integration
- [ ] Video conferencing
- [ ] E-signature integration

---

Built with ❤️ using Laravel 11
