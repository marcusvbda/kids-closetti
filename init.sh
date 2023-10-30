#!/bin/bash

echo "Iniciando configuração de permissões e inicialização do Apache..."

USER="www-data"
GROUP="www-data"

echo "Configurando permissões para o diretório /var/www/html..."
chown -R $USER:$GROUP /var/www/html/wp-content
find /var/www/html -type f -exec chmod 644 {} \;
find /var/www/html -type d -exec chmod 755 {} \;

echo "Permissões configuradas com sucesso."

echo "Iniciando o servidor Apache..."
exec apache2-foreground