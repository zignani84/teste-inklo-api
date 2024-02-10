# Imagem base do PHP com Apache
FROM php:7.4-apache

# Instalação de dependências do Laravel
RUN apt-get update \
    && apt-get install -y lftp libicu-dev libpq-dev unzip p7zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Configurações do Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && service apache2 restart

# Diretório de trabalho
WORKDIR /var/www/html

# Cópia do código fonte para o container
COPY . .

# Permissões de escrita no diretório storage
RUN chmod -R 777 storage

# Instalação do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Exposição da porta do Apache
EXPOSE 80

# Comando de inicialização do container
CMD ["apache2-foreground"]
