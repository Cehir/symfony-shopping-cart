# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
PHPSTAN  = $(PHP) vendor/bin/phpstan

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up dev start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

dev: build
	@$(DOCKER_COMP) -f compose.yaml -f compose.override.yaml up --detach

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	$(MAKE) up
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

phpstan: ## run codebase analysis, pass the parameter "c=" to add options to phpstan, example: c="analyse src test --level=6"
	@$(eval c ?=)
	@$(PHPSTAN) $(c)

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

routes: c="debug:router" ## list routes
routes: sf

autowires: c="debug:autowiring" ## list autowiring services
autowires: sf

entity: c=make:entity --regenerate ## create or update an entity
entity: sf

factory: c=make:factory --all-fields ## create factory template(s)
factory: sf

loadfixtures: c=doctrine:fixtures:load --purge-with-truncate ## dumps all database data & loads fixtures
loadfixtures: sf

migration: c=make:migration ## create migration
migration: sf

migrate: c=doctrine:migrations:migrate ## run migrations
migrate: sf
