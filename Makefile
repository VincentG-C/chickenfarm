.DEFAULT_GOAL := help

install:
	docker compose up -d --build

sh:
	docker compose exec -it php sh

cache:
	docker compose exec -it php php bin/console cache:clear

migrate:
	docker compose exec -it php php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker compose exec -it php php bin/console doctrine:fixtures:load --no-interaction

db: migrate fixtures
	@echo "==> Base de données prête (migrations + fixtures) !"

logs:
	docker compose logs -f --tail=100 php

up start:
	docker compose up -d && \
    echo "==> Les services ont été démarrés avec succès" && \
    echo "==> Vous pouvez accéder à l'application : http://localhost:8089" && \
    echo "==> Vous pouvez accéder à l'interface de la BDD : http://localhost:8088" && \
    echo "==> Vous pouvez accéder à l'interface de mailpit : http://localhost:8025"

down stop:
	docker compose down

restart: down up

lint-container:
	docker compose exec -it php php bin/console lint:container

lint-twig:
	docker compose exec -it php php bin/console lint:twig templates/

phpstan:
	docker compose exec -it php php -d memory_limit=512M vendor/bin/phpstan analyse --level=5

phpstan-clear:
	docker compose exec -it php php vendor/bin/phpstan clear-result-cache

test:
	docker compose exec -it php php bin/phpunit

test-functional:
	docker compose exec -it php php bin/phpunit tests/Functional/

ci: lint-container lint-twig phpstan test

help:
	@echo "Makefile commands:"
	@echo "  install         - Premier lancement : build + démarrage de tout"
	@echo "  sh              - Execute a shell inside the PHP container"
	@echo "  up              - Start the Docker containers"
	@echo "  down            - Stop and remove the Docker containers"
	@echo "  restart         - Restart the Docker containers"
	@echo "  cache           - Clear the Symfony cache"
	@echo "  migrate         - Run Doctrine migrations"
	@echo "  fixtures        - Load test data (AppFixtures)"
	@echo "  db              - Run migrations + fixtures (one shot)"
	@echo "  logs            - Follow the logs of the PHP container"
	@echo "  lint-container  - Validate Symfony container configuration"
	@echo "  lint-twig       - Validate Twig templates syntax"
	@echo "  phpstan         - Run PHPStan static analysis (level 5)"
	@echo "  phpstan-clear   - Clear PHPStan result cache"
	@echo "  test            - Run all PHPUnit tests"
	@echo "  test-functional - Run functional tests only"
	@echo "  ci              - Run all CI checks (lint + phpstan + test)"
	@echo "  help            - Show this help message"
