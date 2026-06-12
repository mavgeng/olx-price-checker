init: env build composer-install key up migrate

build:
	docker compose build

env:
	cp -n .env.example .env
	cp -n src/.env.example src/.env

composer-install:
	docker compose run --rm composer install

key:
	docker compose run --rm php-cli php artisan key:generate

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f

migrate:
	docker compose run --rm php-cli php artisan migrate

laravel-new:
	docker compose run --rm composer create-project laravel/laravel .

php:
	docker compose run --rm php-cli bash

phpdocs-models:
	docker compose run --rm php-cli php artisan ide-helper:models --write-mixin

pint:
	docker compose run --rm php-cli composer run pint