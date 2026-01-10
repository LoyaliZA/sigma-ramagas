#!/bin/bash
echo "Iniciando despliegue de SIGMA..."

# 1. Bajar codigo
git pull origin main

# 2. Construir y levantar (Nota el guion en docker-compose)
docker-compose -f docker-compose.prod.yml up -d --build

# 3. Mantenimiento
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link
docker-compose -f docker-compose.prod.yml exec app composer dump-autoload
docker-compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache


echo "Despliegue finalizado exitosamente."