#!/bin/bash
echo "Iniciando despliegue de SIGMA..."

# 1. Bajar codigo
git pull origin main

# 2. Construir y levantar
docker-compose -f docker-compose.prod.yml up -d --build

echo "⏳ Esperando 20 segundos a que la Base de Datos despierte..."
sleep 20

# 3. Mantenimiento
# Ahora si, atacamos con todo
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link

# Arreglamos clases
docker-compose -f docker-compose.prod.yml exec -T app composer dump-autoload

# Limpiamos cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

echo "Despliegue finalizado exitosamente."