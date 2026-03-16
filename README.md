# Passport Suvidha API

This is the API backend for the Passport Suvidha application, which manages passport application processing.

## CI/CD Pipeline

This project uses GitHub Actions for continuous integration and deployment. The workflow supports testing, linting, building assets, and deploying to staging and production environments.

### Workflow Stages

1. **Test**: Runs PHPUnit tests against a MySQL database
2. **Lint**: Performs code quality checks using PHP_CodeSniffer and ESLint
3. **Build**: Compiles assets for production
4. **Deploy**: Deploys to either staging or production environments
5. **Notify**: Sends notifications about deployment status

### Required GitHub Secrets

The following secrets need to be configured in your GitHub repository settings:

**Shared Secrets:**
- `APP_KEY`: Laravel application key
- `SMS_API_USERNAME`: SMS API username
- `SMS_API_PASSWORD`: SMS API password
- `SMS_API_SENDER_ID`: SMS API sender ID
- `SLACK_WEBHOOK_URL`: Webhook URL for Slack notifications

**Production Secrets:**
- `SSH_PRIVATE_KEY`: SSH key for connecting to the production server
- `SERVER_IP`: Production server IP address
- `SERVER_USER`: Production server username
- `DEPLOY_PATH`: Path to deploy the application on production server
- `DB_HOST`: Production database host
- `DB_DATABASE`: Production database name
- `DB_USERNAME`: Production database username
- `DB_PASSWORD`: Production database password
- `REDIS_HOST`: Production Redis host
- `REDIS_PASSWORD`: Production Redis password

**Staging Secrets:**
- `STAGING_SSH_PRIVATE_KEY`: SSH key for connecting to the staging server
- `STAGING_SERVER_IP`: Staging server IP address
- `STAGING_SERVER_USER`: Staging server username
- `STAGING_DEPLOY_PATH`: Path to deploy the application on staging server
- `STAGING_DB_HOST`: Staging database host
- `STAGING_DB_DATABASE`: Staging database name
- `STAGING_DB_USERNAME`: Staging database username
- `STAGING_DB_PASSWORD`: Staging database password

### GitHub Actions Variables

- `PRODUCTION_URL`: URL of the production environment
- `STAGING_URL`: URL of the staging environment

### Deployment Triggers

- **Automatic**: Push to `main` branch deploys to production, push to `develop` deploys to staging
- **Manual**: Workflow can be manually triggered with environment selection

## Local Development with Docker

You can run the application locally using Docker:

```bash
# Copy environment file
cp .env.example .env

# Configure environment variables
# Edit .env file with your local settings

# Start Docker containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Run migrations
docker-compose exec app php artisan migrate

# Generate application key
docker-compose exec app php artisan key:generate

# Build assets
docker-compose exec app npm run dev
```

The application will be available at http://localhost:8000

## Local Development without Docker

```bash
# Install dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Start development server
php artisan serve

# Watch for asset changes
npm run watch
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

## Application Structure

The application follows the standard Laravel project structure with the following key directories:

- `app/Http/Controllers`: API and Admin controllers
- `app/Models`: Database models
- `database/migrations`: Database schema migrations
- `routes`: API and web routes
- `resources`: Frontend assets and views


____________________________________________________________________



