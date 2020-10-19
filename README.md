# TDD Demo - ForumPHP 2020

[![Build Status](https://travis-ci.com/JMLamodiere/tdd-demo-forumphp2020.svg?branch=main)](https://travis-ci.com/JMLamodiere/tdd-demo-forumphp2020)

Live coding examples used during Forum PHP 2020 talk (:fr: french) : [Trop de mock tue le test : ce que l'archi hexagonale a changé](https://event.afup.org/forum-php-2020/programme-forum-php-2020/#3414)
(video not yet published).

For a bit of theory, see [De CRUD à DDD, comment Meetic a sauvé son legacy](https://afup.org/talks/3037-de-crud-a-ddd-comment-meetic-a-sauve-son-legacy) (:fr: french)

## Steps by step refactoring

1. [bad_implementation](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/bad_implementation) branch
contains :
    - Architecture mistakes according to [Hexagonal architecture](https://alistair.cockburn.us/hexagonal-architecture/) (aka Port & Adapters)
    - Tests too much coupled with implementation details, and an incorrect usage of mocks
1. [bad_tests](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/bad_tests) branch
[(see Pull Request)](https://github.com/JMLamodiere/tdd-demo-forumphp2020/pull/12) only fixes (some) hexagonal mistakes.
Many obscure changes are required in the tests, proving they do not help much during refactoring
1. [integration_infra_medium_domain](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/integration_infra_medium_domain) branch
[(see Pull Request)](https://github.com/JMLamodiere/tdd-demo-forumphp2020/pull/13) split tests this way:
    - Domain logic (Application/Domain folders): medium Unit tests, mocking only infrastructure
    - Technical logic (Infrastructure folder): integration tests for each specific technology
1. (todo...)

## API documentation

- Local : [docs/openapi.yml](docs/openapi.yml)
- Github Pages : https://jmlamodiere.github.io/tdd-demo-forumphp2020
- Swaggger Hub (with online mock) : https://app.swaggerhub.com/apis/JMLamodiere/tdd-demo_forum_php_2020/1.0.0

## Makefile

Run `make` or `make help` to see available commands.

### Test environment

Pre-requisites :

- [docker](https://www.docker.com/)
- [docker-compose](https://docs.docker.com/compose/)

Run tests with:

    make install
    make test

### Dev environment

Pre-requisites (see [composer.json](composer.json)) :

- PHP >= 7.4
- ext-pgsql
- [Symfony local web server](https://symfony.com/doc/current/setup/symfony_server.html)

Start local dev environment with:

```
composer install
make start
```

- Symfony homepage (404): https://127.0.0.1:8000/
- Symfony profiler: https://127.0.0.1:8000/_profiler/

### SwaggerHub mock server

An online mock server is auto-generated based on openapi doc at https://virtserver.swaggerhub.com/JMLamodiere/tdd-demo_forum_php_2020/1.0.0 . Example :

    curl -i -X PUT "https://virtserver.swaggerhub.com/JMLamodiere/tdd-demo_forum_php_2020/1.0.0/runningsessions/42" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{\"id\":42,\"distance\":5.5,\"shoes\":\"Adadis Turbo2\"}"

## Postgresql

To access Postgresql database, configure a tool such as
[Database Tools and SQL](https://www.jetbrains.com/help/phpstorm/connecting-to-a-database.html#connect-to-postgresql-database)
included in PHPStorm:

- URL: `jdbc:postgresql://localhost:32774/forumphp` (replace `32774` with the port given by `make ps` command)
- login: `forumphp`
- password: `forumphp` (see [docker-compose.yml](docker-compose.yml))

## Wiremock

- Local server: https://hub.docker.com/r/rodolpheche/wiremock/ (see [docker-compose.yml](docker-compose.yml))
- PHP Client: https://github.com/rowanhill/wiremock-php (used in PHP tests)

See Swagger UI documentation at http://localhost:32775/__admin/swagger-ui/

Replace `32775` with the port given by `make ps` command
