FROM php:7.1-cli-jessie

RUN apt-get update && apt-get install -y libevent

RUN docker-php-ext-install -j$(nproc) iconv mbstring intl json mcrypt bcmath pcntl redis \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

RUN pecl install libevent && echo "extension=libevent.so" > /usr/local/etc/php/conf.d/libevent.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

WORKDIR /usr/src/app

COPY . .

ENTRYPOINT [ "./cc", "watch" ]