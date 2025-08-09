# Railway Deployment Guide for DEER BAKERY & CAKE Inventory System

This guide will help you deploy your Laravel + Vue.js + Inertia.js inventory system to Railway.

## Prerequisites

1. A Railway account (https://railway.app)
2. Git repository with your code
3. Railway CLI (optional but recommended)

## Deployment Steps

### 1. Prepare Your Repository

Ensure all the following files are in your repository:
- `Dockerfile` - Multi-stage build configuration
- `railway.json` - Railway deployment configuration
- `.dockerignore` - Optimizes Docker build
- `docker/` directory with configuration files
- Updated `package.json` with fixed Vite dependencies

### 2. Create a New Railway Project

1. Go to https://railway.app and sign in
2. Click "New Project"
3. Choose "Deploy from GitHub repo"
4. Select your repository

### 3. Configure Environment Variables

In your Railway project dashboard, go to the "Variables" tab and add these environment variables:

```bash
APP_NAME="DEER BAKERY & CAKE Inventory"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
# Railway will provide these automatically if you add a MySQL service
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

SESSION_DRIVER=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### 4. Add a Database Service

1. In your Railway project, click "New Service"
2. Choose "MySQL" from the database options
3. Railway will automatically provide the database connection variables

### 5. Generate Application Key

You need to generate a Laravel application key:

1. Use an online Laravel key generator, or
2. Run `php artisan key:generate --show` locally and copy the result
3. Add it to your Railway environment variables as `APP_KEY`

### 6. Deploy

1. Railway will automatically start building and deploying your application
2. The build process will:
   - Install Node.js dependencies
   - Build frontend assets with Vite
   - Install PHP dependencies with Composer
   - Configure Nginx and PHP-FPM
   - Start the application

### 7. Run Database Migrations

After the first deployment, you may need to run migrations:

1. Go to your Railway project dashboard
2. Open the "Deployments" tab
3. Click on your latest deployment
4. Use the "Shell" feature to run:
   ```bash
   php artisan migrate --force
   ```

### 8. Set Up Storage Link (if needed)

If your application uses file uploads:

```bash
php artisan storage:link
```

## Troubleshooting

### Build Fails at npm ci

- **Issue**: Dependency conflicts
- **Solution**: The Vite dependency conflict has been fixed in this setup

### Application Key Error

- **Issue**: "No application encryption key has been specified"
- **Solution**: Generate and set the `APP_KEY` environment variable

### Database Connection Error

- **Issue**: Cannot connect to database
- **Solution**: Ensure MySQL service is added and environment variables are set

### 500 Internal Server Error

- **Issue**: Various Laravel configuration issues
- **Solution**: Check logs in Railway dashboard and ensure all environment variables are set

## Performance Optimization

The Dockerfile includes several optimizations:
- Multi-stage build to reduce image size
- OPcache enabled for PHP
- Nginx with gzip compression
- Static asset caching
- Optimized PHP-FPM configuration

## Monitoring

Railway provides built-in monitoring:
- View logs in the "Logs" tab
- Monitor resource usage in the "Metrics" tab
- Set up alerts for downtime or errors

## Custom Domain (Optional)

To use a custom domain:
1. Go to "Settings" in your Railway project
2. Add your custom domain
3. Configure DNS records as instructed by Railway

## Environment-Specific Notes

- The application runs on port 8080 (configured in Nginx)
- Logs are sent to stderr for Railway's log aggregation
- File uploads are stored locally (consider using cloud storage for production)
- Sessions are stored in the database for scalability
