# TDD Demo - ForumPHP 2020

[![Build Status](https://travis-ci.com/JMLamodiere/tdd-demo-forumphp2020.svg?branch=main)](https://travis-ci.com/JMLamodiere/tdd-demo-forumphp2020)

Live coding examples used during [Forum PHP 2020 talk](https://event.afup.org/forum-php-2020/programme-forum-php-2020/#3414):

- [:uk: Too many mocks killed the test: What Hexagonal Architecture has changed](https://speakerdeck.com/jmlamodiere/too-many-mocks-killed-the-test-what-hexagonal-architecture-has-changed)
- [:fr: Trop de mock tue le test : ce que l'archi hexagonale a changé](https://speakerdeck.com/jmlamodiere/trop-de-mock-tue-le-test-ce-que-larchi-hexagonale-a-change)

*(video not published yet)*

For a bit of theory, see [:fr: De CRUD à DDD, comment Meetic a sauvé son legacy](https://afup.org/talks/3037-de-crud-a-ddd-comment-meetic-a-sauve-son-legacy)

## Steps by step refactoring

:warning: **Warning** : Only steps 1 & 2 are really considered *bad*. Next steps just show different testing styles.

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
1. [integration_infra_medium_domain_wiremock](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/integration_infra_medium_domain_wiremock) branch
[(see Pull Request)](https://github.com/JMLamodiere/tdd-demo-forumphp2020/pull/14)
only replaces [Guzzle MockHandler](https://docs.guzzlephp.org/en/stable/testing.html) with [Wiremock](#wiremock),
decoupling HTTP tests with the library being used for HTTP calls.
1. [integration_infra_medium_domain_no_di](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/integration_infra_medium_domain_no_di) branch
   [(see Pull Request)](https://github.com/JMLamodiere/tdd-demo-forumphp2020/pull/15)
   removes [Dependency Injection Container](https://www.loosecouplings.com/2011/01/dependency-injection-using-di-container.html)
   usage and manually build tested classes instead.
1. [integration_infra_sociable](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/integration_infra_sociable) branch
   [(see Pull Request)](https://github.com/JMLamodiere/tdd-demo-forumphp2020/pull/16)
   replaces medium sized tests with [Overlapping Sociable Tests](https://www.jamesshore.com/v2/blog/2018/testing-without-mocks#sociable-tests)
   to allow easily test and evolve individual behaviours (ex : class serialization) while still being able to
   split/merge/refactor classes inside some class clusters by not checking specific calls between them.
1. [mock_secondary_ports_in_behat](https://github.com/JMLamodiere/tdd-demo-forumphp2020/tree/mock_secondary_ports_in_behat) branch
   [(see Pull Request)](https://github.com/JMLamodiere/tdd-demo-forumphp2020/pull/18): Mock Secondary Ports
   (according to [Hexagonal architecture](https://alistair.cockburn.us/hexagonal-architecture/)) in
   [Behat](https://behat.org). Makes behat tests much faster and
   easier to write. Pre-requisite : well defined secondary ports and Integration tests on their
   Infrastructure layer implementation.

## API documentation

- Local : [docs/openapi.yml](docs/openapi.yml)
- Github Pages : https://jmlamodiere.github.io/tdd-demo-forumphp2020
- Swaggger Hub (with [SwaggerHub API Auto Mocking](https://app.swaggerhub.com/help/integrations/api-auto-mocking)
activated) : https://app.swaggerhub.com/apis/JMLamodiere/tdd-demo_forum_php_2020/1.0.0

Example :

    curl -i -X PUT "https://virtserver.swaggerhub.com/JMLamodiere/tdd-demo_forum_php_2020/1.0.0/runningsessions/42" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{\"id\":42,\"distance\":5.5,\"shoes\":\"Adadis Turbo2\"}"

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

Create an App on [AccuWeather](https://developer.accuweather.com/) and copy your API Key:

```
# in /.env.dev.local
ACCUWEATHER_API_KEY=xxx
```

Start local dev environment with:

```
composer install
make start
```

- Symfony homepage (404): https://127.0.0.1:8000/
- Symfony profiler: https://127.0.0.1:8000/_profiler/

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
