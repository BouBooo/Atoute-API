# Atoute 👋 - back

[![pipeline status](https://gitlab.com/QuentG/atoute-back/badges/develop/pipeline.svg)](https://gitlab.com/QuentG/atoute-back/-/commits/develop)

### Prerequisites

Install the docker stack as defined in the [tools repository](https://gitlab.com/atoute/back/-/tree/master/docker)

## Install

Connect to the apache container and follow steps :

```bash
# Connect to container
docker-compose exec php bash

cd back/

# Composer
composer install --no-interaction

# Install a fresh & empty database
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update --force
```

## Try local

http://back-local.atoute.com:8080

## GrumPHP
[GrumPHP Documentation](https://github.com/phpro/grumphp).  
Be sure that GrumPHP is connected to your commits. Verify that the 2 files into git_hooks/ have been copied
in your project folder .git/hooks/.  
If they are not present, copy them : 
```bash
cp git_hooks/* .git/hooks/

chmod +X .git/hooks/*
```

When you do a commit, GrumpPHP is triggered and execute some tasks. You can find them in [grumphp.yml](./grumphp.yml).  
Some tasks have configuration here : 
- PHPStan : [phpstan.neon](./phpstan.neon)
- PHP-CS-Fixer : [php_cs](./.php_cs)


## Consuming Messages 

Inside php container
```bash
# Running the worker
# use -vv to see details about what's happening
php bin/console messenger:consume async
```

Dashboard is available to retry or reject failed tasks :

http://localhost:8080/admin/jobs/

## Unit tests

Inside php container
```bash
# Execute all tests inside tests directory
php bin/phpunit

# Excecute particular test
php bin/phpunit --filter SecurityControllerTest
```

## More

Mysql container available here : 

```bash
# Connect to container
docker-compose exec mysql bash
```