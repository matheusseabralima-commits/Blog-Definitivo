FROM php:7.4-apache

# 1. Instalar dependências e extensões PHP
RUN apt-get update && apt-get install -y \
    # Dependências do sistema (libs)
    libpq-dev \
    libzip-dev \
    libgmp-dev \
    zlib1g-dev \
    unzip \
    # Instala as extensões que precisam de compilação
    && docker-php-ext-install \
    pdo_pgsql \
    gmp \
    zip \
    # Limpa o cache
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_pgsql zip gmp

# 2. Ativar o mod_rewrite do Apache
RUN a2enmod rewrite

# 3. Definir o diretório de trabalho
WORKDIR /var/www/html

# 4. Copiar o código da aplicação
COPY . .

# 5. Ajustar permissões
RUN chown -R www-data:www-data /var/www/html