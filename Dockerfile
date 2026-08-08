# ---- Stage 1: build frontend assets ----
FROM node:20-alpine AS assets
WORKDIR /app

ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https
ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ---- Stage 2: PHP app + Reverb + Caddy, one container ----
FROM php:8.4-cli-alpine

RUN apk add --no-cache sqlite sqlite-dev caddy $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_sqlite pcntl \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && mkdir -p database \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
