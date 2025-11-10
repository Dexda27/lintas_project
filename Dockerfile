FROM oven/bun:alpine AS bun-builder
COPY . /app
WORKDIR /app
RUN bun install && bun run build

FROM m4nzm333/php:8.3-fpm-nginx-alpine AS prod
USER www-data
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=bun-builder /app/public/build /var/www/html/public/build
RUN composer install
RUN php artisan storage:link
EXPOSE 80


