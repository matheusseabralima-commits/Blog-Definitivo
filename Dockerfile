FROM php:7.4-apache

# 1. Instalar dependências necessárias do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 2. Ativar o mod_rewrite do Apache (necessário para o CakePHP)
RUN a2enmod rewrite

# 3. Copiar o código da aplicação para o container
COPY . /var/www/html/

# 4. Ajustar permissões
RUN chown -R www-data:www-data /var/www/html

# 5. (Opcional) definir diretório de trabalho
WORKDIR /var/www/html
