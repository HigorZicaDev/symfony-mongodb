#!/bin/sh

PROJECT_DIR="/var/www"

# Ajusta permissões para o usuário e grupo do host (para evitar problemas no VSCode)
chown -R "${USER_ID}:${GROUP_ID}" $PROJECT_DIR

echo "🎉 Aguardando o MongoDB iniciar..."

# Espera até que o MongoDB esteja disponível
until nc -z -v -w30 mongodb 27017
do
  echo "Aguardando MongoDB..."
  sleep 3
done

echo "🎉 MongoDB está pronto!"

# Passa o controle para o processo padrão do container (php-fpm)
exec php-fpm
