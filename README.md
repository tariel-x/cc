# Contract contoller

Control service contracts providers and contracts usages.

## Quick start

### Native

Install PHP 7.1+, composer and Redis.

```bash
git clone https://github.com/tariel-x/cc.git
cd cc
composer install
export CC_REDIS=redis_host
export CC_UPDATE_TIME=60
./cc watch type-check
```

### Docker

```bash
git clone https://github.com/tariel-x/cc.git
cd cc
docker-compose up
```

## Usage

Import queries to Postman from `rpc.postman_collection.json`. Register services proving contracts, register servives using contracts. Get providers addresses for contract-using services.

Or see [query examples](query.md).

## Contract checking

By default service compares contracts by hash. With `type-check` flag service provides json-schema type checking.

Schema1 seems to be compatible with schema2 if service accepting schema2 can accept scheme1. E.g. schema1 may contain more strict restrictions.

## Tests

Run tests: `vendor/bin/phpunit --bootstrap vendor/autoload.php tests/`.
