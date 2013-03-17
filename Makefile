CURL     ?= curl
PHP      ?= php
ECHO     ?= echo
CONFIG   := app/config/parameters.yml

red      := \033[01;31m
nocolor  := \033[0m
composer ?= composer.phar

.PHONY: boostrap
boostrap: $(CONFIG) $(composer)
	$(PHP) $(composer) update
	$(PHP) app/console doctrine:schema:update --force
	$(PHP) app/console doctrine:fixtures:load

$(CONFIG):
	@$(ECHO) '$(red) Please prepare configuration file $(CONFIG). You can use app/config/parameters.yml.dist as a template.$(nocolor)';
	@exit 1;

$(composer):
	$(CURL) -s https://getcomposer.org/installer | php

