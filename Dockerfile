FROM php:8.3-cli-bookworm AS php-base

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y --no-install-recommends \
    bash \
    ca-certificates \
    curl \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd mbstring pcntl pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /opt/render/project/src
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

FROM php-base AS vendors
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

FROM node:20-bookworm-slim AS assets
WORKDIR /opt/render/project/src
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php-base AS app
COPY . .
COPY --from=vendors /opt/render/project/src/vendor ./vendor
COPY --from=assets /opt/render/project/src/public/build ./public/build

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

EXPOSE 10000

CMD ["bash", "scripts/render-start.sh"]
