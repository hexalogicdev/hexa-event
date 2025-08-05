FROM ubuntu:24.04

# Avoid prompts from apt
ENV DEBIAN_FRONTEND=noninteractive

# Update and install necessary packages
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    gnupg2 \
    vim \
    git \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y \
    nginx \
    libxrender1 \
    supervisor \
    wget \
    cron \
    python3-venv \
    libaugeas0 \
    mysql-client \
    php8.2 \
    php8.2-fpm \
    php8.2-common \
    php8.2-mysql \
    php8.2-xml \
    php8.2-xmlrpc \
    php8.2-curl \
    php8.2-gd \
    php8.2-imagick \
    php8.2-cli \
    php8.2-dev \
    php8.2-imap \
    php8.2-mbstring \
    php8.2-opcache \
    php8.2-soap \
    php8.2-zip \
    php8.2-intl \
    php8.2-bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y --reinstall ca-certificates

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Start Nginx and PHP-FPM
CMD service nginx start && php-fpm8.2 && service cron start && tail -f /dev/null