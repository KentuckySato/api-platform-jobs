# REST API for jobs offer

This project is based on the PHP Symfony 6 and API Platform.

Creating a REST API for jobs offer.

# Prerequisites

-   PHP 8.1 or higher
-   Composer
-   Docker
-   Docker-compose

# Installation

-   Clone the repository
-   Run `composer install`
-   Run `docker compose up -d`
-   Run `symfony server:start`
-   Run `symfony console app:reset-db` to create the database and load the fixtures

# Usage

Go to https://localhost:8000/api

# Migration

## Create migration

```bash
symfony console make:migration
```

## Launch migrations

```bash
symfony console doctrine:migrations:migrate
```

## Add data fixtures

```bash
symfony console doctrine:fixtures:load -n
```

# Tests

## Launch tests

```bash
composer test
```
