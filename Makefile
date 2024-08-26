vendor: composer.json $(wildcard composer.lock)
	/usr/bin/php /usr/bin/composer install

autoload: vendor
	/usr/bin/php /usr/bin/composer dumpautoload

test: vendor
	vendor/bin/phpunit --verbose --testsuite all

phpstan: vendor
	vendor/bin/phpstan analyse -c tests/phpstan/config.neon -l 9 --memory-limit=900M > tests/phpstan/result.txt
