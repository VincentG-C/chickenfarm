web: heroku-php-nginx -C docker/nginx/heroku.conf public/
release: php bin/console cache:clear --no-interaction --env=prod && php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod
