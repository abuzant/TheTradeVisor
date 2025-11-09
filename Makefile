.PHONY: help build up down logs shell migrate seed clean

help: ## Show this help message
	@echo 'Usage: make [TARGET]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build all Docker images
	docker-compose build

up: ## Start all containers in detached mode
	docker-compose up -d

down: ## Stop and remove all containers
	docker-compose down

logs: ## Show logs from all containers
	docker-compose logs -f

logs-app: ## Show logs from app container only
	docker-compose logs -f app

logs-postgres: ## Show logs from postgres container only
	docker-compose logs -f postgres

logs-redis: ## Show logs from redis container only
	docker-compose logs -f redis

shell: ## Open a shell in the app container
	docker-compose exec app sh

shell-root: ## Open a root shell in the app container
	docker-compose exec --user root app sh

artisan: ## Run Laravel artisan command (usage: make artisan COMMAND="migrate:status")
	docker-compose exec app php artisan $(COMMAND)

migrate: ## Run Laravel migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Seed the database
	docker-compose exec app php artisan db:seed

cache-clear: ## Clear Laravel cache
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

passport-install: ## Install Laravel Passport
	docker-compose exec app php artisan passport:install

horizon: ## Start Laravel Horizon
	docker-compose exec app php artisan horizon

queue-work: ## Start queue worker
	docker-compose exec app php artisan queue:work

clean: ## Remove all Docker containers, images, and volumes
	docker-compose down -v --rmi all
	docker system prune -f

restart: ## Restart all containers
	docker-compose restart

ps: ## Show running containers
	docker-compose ps

stats: ## Show container resource usage
	docker stats $(docker-compose ps -q)

backup: ## Backup database
	docker-compose exec postgres pg_dump -U thetradevisor thetradevisor > backup_$(shell date +%Y%m%d_%H%M%S).sql

restore: ## Restore database (usage: make restore FILE=backup.sql)
	docker-compose exec -T postgres psql -U thetradevisor thetradevisor < $(FILE)

install: ## Complete installation (build, up, migrate, seed, passport)
	make build
	make up
	sleep 10
	make migrate
	make seed
	make passport-install
	@echo "Installation complete! Visit http://localhost to access TheTradeVisor."

dev: ## Start development environment
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up

prod: ## Start production environment
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up
