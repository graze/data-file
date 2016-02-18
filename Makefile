SHELL = /bin/sh

.PHONY: install composer clean help
.PHONY: test test-unit test-integration test-matrix

.SILENT: help

install: ## Download the dependencies then build the image :rocket:.
	make 'composer-install --optimize-autoloader --ignore-platform-reqs'
	docker build --tag graze/data-file:latest .

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	docker run -t --rm \
    -v $$(pwd):/app \
    -v ~/.composer:/root/composer \
    -v ~/.ssh:/root/.ssh:ro \
    composer/composer --ansi --no-interaction $* $(filter-out $@,$(MAKECMDGOALS))

test: ## Run the unit and integration testsuites.
test: lint test-matrix test-integration

lint: ## Run phpcs against the code.
	docker run --rm -t -v $$(pwd):/opt/graze/dataFile graze/data-file \
	composer lint --ansi

test-unit: ## Run the unit testsuite.
	docker run --rm -t -v $$(pwd):/opt/graze/dataFile graze/data-file \
	composer test:unit --ansi

test-matrix:
	docker run --rm -t -v $$(pwd):/opt/graze/dataFile graze/data-file:latest \
	vendor/bin/phpunit --testsuite unit

test-integration:
	docker run --rm -t -v $$(pwd):/opt/graze/dataFile graze/data-file:latest \
    vendor/bin/phpunit --testsuite integration

clean: ## Clean up any images.
	docker rmi graze/data-file:latest

run: ## Run a command on the docker image
	docker run --rm -t -v $$(pwd):/opt/graze/dataFile graze/data-file:latest \
	$(filter-out $@,$(MAKECMDGOALS))

help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	fgrep --no-filename "##" $(MAKEFILE_LIST) | fgrep --invert-match $$'\t' | sed -e 's/: ## / - /'
