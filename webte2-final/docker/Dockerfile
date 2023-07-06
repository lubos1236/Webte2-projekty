FROM node:18 AS build-frontend

RUN npm install -g pnpm

WORKDIR /app

COPY pnpm-lock.yaml ./

RUN pnpm fetch

COPY package.json tsconfig.json vite.config.ts ./
COPY .env .env
COPY frontend/ ./frontend/

RUN pnpm install -r --offline
RUN pnpm build

RUN cp -r ./frontend/dist ./dist
RUN rm -rf ./frontend


FROM composer AS build-backend

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader --ignore-platform-req=ext-gd && rm -rf /root/.composer

COPY . ./
RUN rm -rf ./frontend
RUN composer dump-autoload --no-scripts --no-dev --optimize


FROM caddy:builder AS build-caddy
RUN xcaddy build


FROM php:8.2-fpm AS production

ARG user=www-data

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    supervisor \
    python3 \
    python3-pip \
    python-is-python3

COPY --from=build-caddy /usr/bin/caddy /usr/bin/caddy

# clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# install php extensions (gd, pdo_mysql)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo_mysql && \
    docker-php-source delete

# copy php-fpm config
COPY ./docker/conf/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/conf/php.ini "$PHP_INI_DIR/php.ini"

# copy caddy config
COPY ./docker/conf/Caddyfile /etc/caddy/Caddyfile

# copy supervisor config
COPY ./docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www

COPY --from=build-backend /app ./
COPY --from=build-frontend /app/dist ./dist

RUN cd ./scripts && pip3 install -r requirements.txt

# create symlink for images
RUN php artisan storage:link

RUN chown -R ${user}:${user} /var/www/storage /var/www/bootstrap/cache
RUN chown ${user}:${user} /var/www

EXPOSE 80

USER $user

# Let supervisord start caddy & php-fpm
COPY --chown=${user}:${user} ./docker/run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh

ENTRYPOINT [ "/usr/local/bin/run.sh" ]