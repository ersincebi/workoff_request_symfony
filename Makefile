build:
	@docker-compose up -d --build
	@docker exec -i debian_php8 composer install
	@docker exec -i debian_php8 cp .env.example .env
	@docker exec -i debian_php8 composer req make
	@docker exec -i debian_php8 php bin/console make:migration
	@docker exec -i debian_php8 php bin/console doctrine:migrations:migrate

remove:
	@docker-compose down
	@docker system prune -af
	@docker volume prune -f