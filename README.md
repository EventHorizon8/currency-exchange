# Currency exchange service

### Stack
- PHP >= 8.4
- Symfony 7.3

### Setup local environment

```shell
# 1. Setup .env file - the dist is ready to use
cp .env.dist .env.local

# 2. Run Docker environment
docker compose up

# 3. Get inside fpm container (to run php-based commands)
docker compose exec fpm sh

# 4. Install dependencies: (run inside fpm container)
composer install

# 5. Migrate the Database: (run inside fpm container)
php bin/console doctrine:migrations:migrate

```
