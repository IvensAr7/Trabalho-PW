# Usa uma imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala extensões do PHP necessárias (ex: PDO para banco de dados)
RUN docker-php-ext-install pdo pdo_mysql

# Habilita o módulo de reescrita do Apache (comum para frameworks como Laravel/Symfony)
RUN a2enmod rewrite

# Define a pasta de trabalho dentro do container
WORKDIR /
