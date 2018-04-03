# Contract contoller

Control service contracts providers and usages.

## Quick start

### Native

Install PHP 7.1+, composer and Redis.

```bash
git clone http://github.com/tariel-x/cc.git
cd cc
composer install
./cc watch
```

## Usage

Import queries to Postman from `rpc.postman_collection.json`. Register services proving contracts, register servives using contracts. Get providers addresses for contract-using services.

## Contract checking

At current moment contracts checking only by hash.

## Tests

Run tests: `vendor/bin/phpunit --bootstrap vendor/autoload.php tests/`.