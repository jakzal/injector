IS_PHP81:=$(shell php -r 'echo (int)version_compare(PHP_VERSION, "8.1", ">=");')

default: build

build: install test
.PHONY: build

install:
	composer install
.PHONY: install

update:
	composer update
.PHONY: update

update-min:
	composer update --prefer-stable --prefer-lowest
.PHONY: update-min

test: vendor cs deptrac phpunit infection
.PHONY: test

test-min: update-min cs deptrac phpunit infection
.PHONY: test-min

ifeq ($(IS_PHP81),1)
cs:
else
cs: tools/php-cs-fixer
	tools/php-cs-fixer --dry-run --allow-risky=yes --no-interaction --ansi fix
endif
.PHONY: cs

ifeq ($(IS_PHP81),1)
cs-fix:
else
cs-fix: tools/php-cs-fixer
	tools/php-cs-fixer --allow-risky=yes --no-interaction --ansi fix
endif
.PHONY: cs-fix

deptrac: tools/deptrac
	tools/deptrac --no-interaction --ansi
.PHONY: deptrac

infection: tools/infection tools/infection.pubkey
	phpdbg -qrr ./tools/infection --no-interaction --formatter=progress --min-msi=100 --min-covered-msi=100 --ansi
.PHONY: infection

phpunit: tools/phpunit
	tools/phpunit
.PHONY: phpunit

tools: tools/php-cs-fixer tools/deptrac tools/infection
.PHONY: tools

vendor: install

vendor/bin/phpunit: install

tools/phpunit: vendor/bin/phpunit
	ln -sf ../vendor/bin/phpunit tools/phpunit

tools/php-cs-fixer:
	curl -Ls http://cs.symfony.com/download/php-cs-fixer-v2.phar -o tools/php-cs-fixer && chmod +x tools/php-cs-fixer

tools/deptrac:
	curl -Ls https://github.com/sensiolabs-de/deptrac/releases/download/0.19.1/deptrac.phar -o tools/deptrac && chmod +x tools/deptrac

tools/infection: tools/infection.pubkey
	curl -Ls https://github.com/infection/infection/releases/download/0.26.1/infection.phar -o tools/infection && chmod +x tools/infection

tools/infection.pubkey:
	curl -Ls https://github.com/infection/infection/releases/download/0.26.1/infection.phar.pubkey -o tools/infection.pubkey
