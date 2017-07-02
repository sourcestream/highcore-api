# API

Dependencies:
```
php5-json
php5-curl
php5-mcrypt
php5-pgsql
```

## API Docs:
Run php artisan vendor:publish to push swagger-ui to your public folder.
After that they become available at localhost URI: /api-docs (swagger json available at /docs).
Changes in PHP classes annotations are auto-scanned, so there's no need to run artisan again.
Configured via config/swaggervel.php (make sure to disable on production).

## Authentication

API uses basic auth for users authentication. The password can be generated with openssl:
 
```
openssl passwd -1 "your-password"
```

Docker
======

## build your own docker image
    docker-compose build api-package