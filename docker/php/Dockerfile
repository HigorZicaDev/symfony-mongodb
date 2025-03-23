# Usa a imagem base PHP com FPM e Alpine
FROM php:8.3-fpm-alpine

# Define argumentos para o UID e GID, passados durante a construção do container
ARG USER_ID
ARG GROUP_ID

# Define as variáveis de ambiente para o UID e GID dentro do container
ENV USER_ID=${USER_ID}
ENV GROUP_ID=${GROUP_ID}

# Instala as dependências necessárias
RUN apk --no-cache add curl git wget bash dpkg

# Instala o instalador de extensões PHP
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions
RUN chmod +x /usr/local/bin/install-php-extensions

# Instala as extensões PHP necessárias, incluindo a do MongoDB
RUN install-php-extensions opcache iconv soap zip intl fileinfo pdo redis mysqli pdo_mysql gd mongodb

# Instala o Composer para gerenciamento de dependências PHP
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Define o diretório de trabalho no container
WORKDIR /var/www

# Copia o script de entrada para o container
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

# Torna o script executável
RUN chmod +x /usr/local/bin/entrypoint.sh

# Define o script como entrypoint para o container
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
