install: composer.lock
	/usr/bin/php7 /usr/bin/composer install

update composer.lock: composer.json
	/usr/bin/php7 /usr/bin/composer update

autoload composer.lock: composer.json
	/usr/bin/php7 /usr/bin/composer dumpautoload

testsuite: composer.lock
ifdef testsuite
	vendor/bin/phpunit --testsuite $(testsuite)
else
	@echo you must supply a testsuite
endif

phpstan:
	vendor/bin/phpstan analyse -c docs/config/phpstan.neon -l 9 > docs/phpstan/output.txt
