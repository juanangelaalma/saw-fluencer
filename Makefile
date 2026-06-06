.PHONY: help sail-install up down restart shell install setup dev serve vite queue logs migrate migrate-fresh seed rollback migrate-status test lint format build optimize clear cache-clear route-list tinker

SAIL := ./vendor/bin/sail
ARTISAN := $(SAIL) artisan
COMPOSER := $(SAIL) composer
NPM := $(SAIL) npm

help:
	@printf "Available targets:\n"
	@printf "  make sail-install   Publish Sail docker-compose.yml\n"
	@printf "  make up             Start Sail containers\n"
	@printf "  make down           Stop Sail containers\n"
	@printf "  make restart        Restart Sail containers\n"
	@printf "  make shell          Open Sail app shell\n"
	@printf "  make install         Install PHP and JS dependencies\n"
	@printf "  make setup           Run Laravel setup script\n"
	@printf "  make dev             Serve app, queue, logs, and Vite together\n"
	@printf "  make serve           Start Laravel dev server in Sail\n"
	@printf "  make vite            Start Vite dev server\n"
	@printf "  make queue           Start queue listener\n"
	@printf "  make logs            Start Laravel Pail logs\n"
	@printf "  make migrate         Run database migrations\n"
	@printf "  make migrate-fresh   Rebuild database and seed\n"
	@printf "  make seed            Run database seeders\n"
	@printf "  make rollback        Roll back last migration batch\n"
	@printf "  make migrate-status  Show migration status\n"
	@printf "  make test            Run Laravel tests\n"
	@printf "  make lint            Run Pint in test mode\n"
	@printf "  make format          Format PHP with Pint\n"
	@printf "  make build           Build frontend assets\n"
	@printf "  make optimize        Cache Laravel config/routes/views\n"
	@printf "  make clear           Clear Laravel caches\n"
	@printf "  make route-list      Show routes\n"
	@printf "  make tinker          Open Laravel Tinker\n"

sail-install:
	php artisan sail:install

up:
	$(SAIL) up -d

down:
	$(SAIL) down

restart:
	$(SAIL) restart

shell:
	$(SAIL) shell

install:
	$(COMPOSER) install
	$(NPM) install

setup:
	$(COMPOSER) run setup

dev:
	$(SAIL) up

serve:
	$(ARTISAN) serve

vite:
	$(NPM) run dev

queue:
	$(ARTISAN) queue:listen --tries=1 --timeout=0

logs:
	$(ARTISAN) pail --timeout=0

migrate:
	$(ARTISAN) migrate

migrate-fresh:
	$(ARTISAN) migrate:fresh --seed

seed:
	$(ARTISAN) db:seed

rollback:
	$(ARTISAN) migrate:rollback

migrate-status:
	$(ARTISAN) migrate:status

test:
	$(COMPOSER) run test

lint:
	./vendor/bin/pint --test

format:
	./vendor/bin/pint

build:
	$(NPM) run build

optimize:
	$(ARTISAN) optimize

clear:
	$(ARTISAN) optimize:clear

cache-clear:
	$(ARTISAN) cache:clear

route-list:
	$(ARTISAN) route:list

tinker:
	$(ARTISAN) tinker
