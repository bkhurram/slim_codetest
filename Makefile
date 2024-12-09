up:
	docker-compose up -d

down:
	docker-compose down

ssh:
	docker-compose exec -it app bash

test:
	./vendor/bin/phpunit --verbose tests

root-user:
	php bin/console.php user:create root root my.root@gmail.com password
