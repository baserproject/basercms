# Dockerfile for Anthropic OSS Scanner (https://github.com/anthropics/oss-scanner)
#
# Builds a self-contained baserCMS environment (PHP 8.4 + Apache + MariaDB).
# The checkout lives at /var/www/html (several unit tests hard-code that path
# and https://localhost, as in the project's own Docker/CI setup); /src is a
# symlink to it for the scanner. The scanner runs this image as root,
# without network access and without an init system, so everything needed
# for building and testing is fetched during this build.
#
# Inside the container:
#   basercms-start   start MariaDB and Apache (site: http://localhost/)
#   basercms-stop    stop them again
#   basercms-test    run the PHPUnit suite (bin/cake setup test && vendor/bin/phpunit)
#
# Admin login: http://localhost/baser/admin/  foo@example.com / baserCMS1234
# Database:    basercms / basercms (database "basercms" on 127.0.0.1)

FROM php:8.4-apache

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1

# --- system packages, PHP extensions, Composer ------------------------------
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
        ca-certificates curl git unzip procps \
        mariadb-server mariadb-client \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
        libicu-dev libzip-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" gd intl pdo_mysql mysqli zip opcache \
 && a2enmod rewrite \
 && printf '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-enabled/basercms.conf \
 && printf 'session.cache_limiter = none\nmemory_limit = -1\n' > /usr/local/etc/php/conf.d/99-basercms.ini \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY .oss-scanner/bin/ /usr/local/bin/

# --- application -------------------------------------------------------------
WORKDIR /var/www/html
COPY . /var/www/html
RUN ln -s /var/www/html /src

# Install PHP dependencies, create the config, install baserCMS into MariaDB
# (the same steps as "composer run-script app-install" in CI, against a local
# database), then run a short smoke test so that a broken build fails here
# rather than inside the scanner.
RUN chmod +x /usr/local/bin/basercms-* \
 && composer install --no-plugins --no-interaction --prefer-dist \
 && cp config/.env.example config/.env \
 && basercms-start \
 && mariadb -uroot -e "CREATE DATABASE basercms CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; CREATE DATABASE test_basercms CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; CREATE USER 'basercms'@'%' IDENTIFIED BY 'basercms'; GRANT ALL ON *.* TO 'basercms'@'%'; FLUSH PRIVILEGES;" \
 && bin/cake setup install \
 && bin/cake install https://localhost foo@example.com baserCMS1234 basercms --host 127.0.0.1 --username basercms --password basercms \
 && basercms-test --testsuite BcSeo \
 && basercms-stop \
 && chown -R www-data:www-data /var/www/html \
 && bin/cake cache clear_all \
 && rm -rf /root/.composer/cache /root/.cache/composer

CMD ["bash"]
