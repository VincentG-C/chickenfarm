web: heroku-php-nginx -C docker/nginx/heroku.conf public/
release: php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
