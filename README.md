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

```dotenv
# Set up CURRENCY_API_KEY in .env.local file
CURRENCY_API_KEY=your_api_key_here
# You can get a free API key from https://www.exchangerate-api.com/

```


### Testing

***Preparing the Database***
```shell
# Create
php bin/console --env=test doctrine:database:create
# Migrate
php bin/console --env=test doctrine:migrations:migrate
```

***Running Unit tests***
```shell
# Run all Unit Tests
php bin/phpunit

```
