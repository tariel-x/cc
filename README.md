# Contract contoller

Control service contracts providers and contracts usages.

## Quick start

### Native

Install PHP 7.1+, composer and Redis.

```bash
git clone https://github.com/tariel-x/cc.git
cd cc
composer install
./cc watch type-check
```

### Docker

In process...

## Usage

Import queries to Postman from `rpc.postman_collection.json`. Register services proving contracts, register servives using contracts. Get providers addresses for contract-using services.

Or see [query examples](query.md).

## Contract checking

At current moment contracts are checking only by hash.

### Type checking

Type checking is expected to compare json schemes and to be simple.

## Tests

Run tests: `vendor/bin/phpunit --bootstrap vendor/autoload.php tests/`.
