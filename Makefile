# set default shell
SHELL := $(shell which bash)

# default shell options
.SHELLFLAGS = -c

.SILENT: ;               # no need for @
.ONESHELL: ;             # recipes execute in same shell
.NOTPARALLEL: ;          # wait for this target to finish
.EXPORT_ALL_VARIABLES: ; # send all vars to shell

.PHONY: help

help: ## Show Help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Dev environment. Require php and symfony executables

start: ## [dev] Start local dev server
	docker-compose up -d
	symfony serve -d

ps: ## [dev] docker-compose ps
	docker-compose ps

server-logs: ## [dev] Follow logs on local dev server
	symfony server:log

docker-logs: ## [dev] Follow logs on docker containers
	docker-compose logs -f

stop: ## [dev] Stop local dev server
	symfony server:stop
	docker-compose stop

prune: ## [dev] Prune docker containers
	docker-compose down -v --rmi local --remove-orphans

fix-style: ## [dev] Run php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v

# Test / CI environment. Require docker and docker-compose executables

preinstall: ## Pre-install steps
	docker-compose up -d

install: ## Install steps
	docker-compose run php composer install

test:lint ## Run all tests
test:phpunit
test:behat

lint: ## Run all linters
	# Checks coding standards. Fixable with "make fix-style"
	docker-compose run php ./vendor/bin/php-cs-fixer --config=.php_cs.dist fix --diff --dry-run -v
	# checks that the YAML config files contain no syntax errors
	docker-compose run php ./bin/console lint:yaml config --parse-tags
	# checks that arguments injected into services match type declarations.
	docker-compose run php ./bin/console lint:container
	# checks that the composer.json and composer.lock files are valid
	docker-compose run php composer validate --strict

phpunit: ## Run phpunit
	docker-compose run php vendor/bin/phpunit

behat: ## Run behat
	docker-compose run php vendor/bin/behat
