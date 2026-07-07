web: heroku-php-nginx public/
release: php bin/console cache:clear --no-interaction --env=prod && php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod
