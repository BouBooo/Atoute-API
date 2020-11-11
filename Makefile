dc := docker-compose
de := $(dc) exec
sy := $(de) php php bin/console

.DEFAULT_GOAL := help
.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: ## Installe les différentes dépendances (docker-compose up doit être lancé)
	$(de) php composer install

.PHONY: build
build: ## Crée les container docker
	$(dc) build

.PHONY: dev
dev: ## Lance l'environnement de développement
	$(dc) up -d

.PHONY: php
php: ## Permet de rentrer dans le container php
	$(de) php bash

.PHONY: mysql
mysql: ## Permet de rentrer dans le container mysql
	$(de) mysql bash

.PHONY: stop
stop: ## Stop les container docker
	$(dc) stop

.PHONY: seed
seed: vendor/autoload.php ## Génère des données dans la base de données (docker-compose up doit être lancé)
	$(sy) doctrine:database:create --if-not-exists
	$(sy) doctrine:schema:update -f
	$(sy) doctrine:fixtures:load -n

.PHONY: clear ## Vide le cache
clear: vendor/autoload.php
	$(sy) cache:clear