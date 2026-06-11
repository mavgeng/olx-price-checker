init: build up

build:
	docker compose build --no-cache

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f

laravel-new:
	docker compose run --rm composer create-project laravel/laravel .

php:
	docker compose run --rm php-cli bash

phpdocs-models:
	docker compose run --rm php-cli php artisan ide-helper:models --write-mixin