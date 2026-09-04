SHELL := /bin/sh
POC_DIR := $(abspath $(dir $(lastword $(MAKEFILE_LIST))))
COMPOSE := docker compose --project-directory $(POC_DIR) -f $(POC_DIR)/docker-compose.yml

.PHONY: prepare up init test evidence down status

prepare:
	$(POC_DIR)/bin/prepare-runtime.sh

up: prepare
	$(COMPOSE) up -d db wordpress

init: up
	$(COMPOSE) run --rm cli sh /workspace/bin/init-wordpress.sh

test:
	$(POC_DIR)/bin/run-validation.sh

evidence:
	python3 $(POC_DIR)/tests/catalog_test.py --capture

status:
	$(COMPOSE) ps

down:
	$(COMPOSE) down
