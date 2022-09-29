web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --seed
worker: php artisan queue:work --tries=5
