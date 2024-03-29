FROM php:7.4-fpm-buster
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=Asia/Tokyo \
  # locale
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer \
  #memory使用を無制限に
  COMPOSER_MEMORY_LIMIT=-1 \
  # Laravel environment
  DB_CONNECTION=mysql \
  DB_HOST=db \
  DB_PORT=3306 \
  DB_DATABASE=database \
  DB_USERNAME=admin \
  DB_PASSWORD=pass \
  LOG_CHANNEL=stderr

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
  apt-get -y install git libicu-dev libonig-dev libzip-dev zip unzip locales zlib1g-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libwebp-dev  libxpm-dev libxml2 libxml2-dev && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  locale-gen en_US.UTF-8 && \
  localedef -f UTF-8 -i en_US en_US.UTF-8 && \
  mkdir /var/run/php-fpm && \
  docker-php-ext-configure gd && \
  docker-php-ext-install intl pdo_mysql zip bcmath mbstring -j$(nproc) gd && \
  composer config -g process-timeout 3600
  # && \
#  composer config -g repos.packagist composer https://packagist.org && \
#  composer global require hirak/prestissimo

COPY ./php-fpm.d/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./php.ini /usr/local/etc/php/php.ini

WORKDIR /work/laravel
