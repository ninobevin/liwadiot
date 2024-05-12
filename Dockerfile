FROM php:7.3-fpm-alpine

# Install required extensions
RUN apk --no-cache add libpng-dev libzip-dev

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd zip

RUN ln -snf /usr/share/zoneinfo/Asia/Manila /etc/localtime && echo Asia/Manila > /etc/timezone


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN apk update && apk add --no-cache nodejs npm && apk add --no-cache openjdk8
