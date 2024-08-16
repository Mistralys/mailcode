install: composer.lock
	/usr/bin/php /usr/bin/composer install

update composer.lock: composer.json
	/usr/bin/php /usr/bin/composer update

autoload composer.lock: composer.json
	/usr/bin/php /usr/bin/composer dumpautoload

testsuite: composer.lock
ifdef testsuite
	vendor/bin/phpunit --testsuite $(testsuite)
else
	@echo you must supply a testsuite
endif

phpstan:
	vendor/bin/phpstan analyse -c tests/phpstan/config.neon -l 9 --memory-limit=900M > tests/phpstan/result.txt
