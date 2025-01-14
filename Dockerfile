# Imagem base com PHP e Apache
FROM php:8.1-apache

# Instale extensões necessárias (ex.: cURL)
RUN docker-php-ext-install curl

# Copie os arquivos do projeto para o diretório do servidor
COPY . /var/www/html/

# Ajuste permissões
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta do servidor web
EXPOSE 80
