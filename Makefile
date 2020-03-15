install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src tests formatters

test:
	composer run-script phpunit tests