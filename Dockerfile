FROM php:7.1-cli-jessie

RUN pecl install redis && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y libev-dev

RUN pecl install ev && docker-php-ext-enable ev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/src/app

COPY . .

RUN chown -R www-data:www-data ./

USER www-data

RUN composer install

ENTRYPOINT [ "./cc", "watch", "type-check" ]