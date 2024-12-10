up:
	docker-compose up -d

down:
	docker-compose down

ssh:
	docker-compose exec -it app bash

test:
	./vendor/bin/phpunit --verbose tests
