web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --seed --force
worker: php artisan queue:work --tries=5
