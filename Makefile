up:
	docker-compose up -d

down:
	docker-compose down

ssh:
	docker-compose exec -it app bash

test:
	./vendor/bin/phpunit --verbose tests

lint:
	docker-compose run --rm app bash -c "./vendor/bin/php-cs-fixer fix"

lint-affect:
	docker-compose run --rm app bash -c "./vendor/bin/php-cs-fixer fix -vvv --show-progress=dots --dry-run"
