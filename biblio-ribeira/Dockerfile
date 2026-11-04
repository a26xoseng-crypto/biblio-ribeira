# Imaxe para a web PHP das bibliotecas
FROM php:8.3-apache

# 1. A imaxe oficial de PHP non trae a extensión para conectar con MySQL/MariaDB
# 2. Cartafol de datos da aplicación, propiedade do usuario de Apache (www-data).
#    Cando se monte un volume baleiro aquí, Docker copiará estes permisos.
RUN docker-php-ext-install pdo_mysql \
 && mkdir -p /var/www/datos \
 && chown www-data:www-data /var/www/datos

# O código NON se copia na imaxe: nesta práctica chega por un bind mount.
