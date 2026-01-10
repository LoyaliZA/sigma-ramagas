#!/bin/bash
echo "Iniciando despliegue de SIGMA..."

# 1. Bajar ultimos cambios de git
git pull origin main

# 2. Construir y levantar contenedores en segundo plano
docker compose -f docker-compose.prod.yml up -d --build

# 3. Mantenimiento de Laravel (Migraciones y Cache)
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

echo "Despliegue finalizado exitosamente."