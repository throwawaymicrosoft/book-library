.PHONY: run migrate

build:
	docker-compose down
	docker-compose build --parallel
	docker-compose up -d

migrate:
	docker-compose exec php bin/console --no-interaction doctrine:migrations:migrate

user: migrate
	docker-compose exec php bin/console fos:user:create admin admin@admin.com admin

test:
	php bin/phpunit

clean:
	docker-compose down
	docker system prune -af
	docker image prune -af

