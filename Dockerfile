# Use uma imagem base do PHP com Apache
FROM php:8.1-apache

# Atualize pacotes e instale as dependências para compilar extensões
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev pkg-config zip unzip git \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# Copie os arquivos do projeto para o diretório raiz do servidor web
COPY . /var/www/html/

# Ajuste permissões
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta padrão do Apache
EXPOSE 80

# Comando para iniciar o Apache no container
CMD ["apache2-foreground"]
