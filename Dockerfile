FROM php:7.4-fpm

WORKDIR /app

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl wget zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql bcmath intl xml zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy app
COPY . .

# Install PHP deps
RUN composer install --no-interaction --no-dev --optimize-autoloader --ignore-platform-req=php --no-scripts

# Install Node deps & build
RUN npm install && NODE_OPTIONS=--openssl-legacy-provider npm run dev

# Skip key generation - will be done at runtime

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
