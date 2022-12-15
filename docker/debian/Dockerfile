FROM debian:bullseye-slim

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends wget gnupg2 unzip curl cron \
    locales ca-certificates apt-transport-https default-mysql-client-core software-properties-common \
    default-jre-headless ca-certificates-java

RUN curl -sSL https://packages.sury.org/php/README.txt | bash -x

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive \
    apt-get install -y --no-install-recommends \
        php8.0 \
        php8.0-mysql \
        php8.0-gd \
        php8.0-mbstring \
        php8.0-curl \
        libapache2-mod-php8.0 \
        php8.0-intl \
        php8.0-zip \
        php8.0-gd \
        php8.0-xml \
        default-mysql-client-core \
        apache2 && \
    update-alternatives --set php /usr/bin/php8.0 && \
        a2enmod php8.0 && \
        a2enmod rewrite && \
        a2enmod ssl

RUN curl -Ss https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data. /var/www/html

WORKDIR /var/www/html

EXPOSE 80

ENTRYPOINT /usr/sbin/apache2ctl -D FOREGROUND