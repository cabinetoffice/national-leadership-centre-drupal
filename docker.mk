include .env

.PHONY: up down stop prune ps shell drush logs composer

default: up

DRUPAL_ROOT ?= /var/www/html/web
SHELL_ARG = php

# If the first argument is "shell"...
ifeq (shell,$(firstword $(MAKECMDGOALS)))
# and there is a second argument
ifneq ($(word 2, $(MAKECMDGOALS)),)
	# use the second argument for the container for a shell
	SHELL_ARG := $(word 2, $(MAKECMDGOALS))
endif
endif

up:
	@echo "Starting up containers for $(PROJECT_NAME)..."
	docker-compose pull
	docker-compose up -d --remove-orphans
	@echo "Site up at http://$(PROJECT_BASE_URL)"

down: stop

stop:
	@echo "Stopping containers for $(PROJECT_NAME)..."
	@docker-compose stop

prune:
	@echo "Removing containers for $(PROJECT_NAME)..."
	@docker-compose down -v

ps:
	@docker ps --filter name='$(PROJECT_NAME)*'

shell:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='$(PROJECT_NAME)_$(SHELL_ARG)' --format "{{ .ID }}") sh

drush:
	docker exec $(shell docker ps --filter name='$(PROJECT_NAME)_php' --format "{{ .ID }}") drush -r $(DRUPAL_ROOT) $(filter-out $@,$(MAKECMDGOALS))

logs:
	@docker-compose logs -f $(filter-out $@,$(MAKECMDGOALS))

composer:
	docker run --interactive --tty --volume $(shell pwd):/app composer --ignore-platform-reqs $(filter-out $@,$(MAKECMDGOALS))

# https://stackoverflow.com/a/6273809/1826109
%:
	@: