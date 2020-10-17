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

serve: ## Run local dev server
	symfony serve -d

log: ## Follow logs on local dev server
	symfony server:log

stop: ## Stop local dev server
	symfony server:stop

test:phpunit ## Run all tests
test:behat

phpunit: ## Run phpunit
	vendor/bin/phpunit

behat: ## Run behat
	vendor/bin/behat

fix-style: ## Run php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v
