#!/bin/bash
# Despliegue del entorno biblio-ribeira

# --- Comprobaciones previas ---

# Comprobar que Docker está instalado
if ! command -v docker > /dev/null; then
    echo "ERROR: Docker no está instalado." >&2
    exit 1
fi

# Comprobar permisos sobre Docker; si faltan, relanzar con sudo
if ! docker info > /dev/null 2>&1; then
    if [ "$EUID" -ne 0 ]; then
        echo "Sin permisos sobre Docker, relanzando con sudo..."
        exec sudo "$0" "$@"
    fi
    echo "ERROR: El servicio Docker no responde." >&2
    exit 1
fi

# Si se lanzó con sudo, revisar si el usuario está en el grupo docker
if [ -n "$SUDO_USER" ]; then
    if id -nG "$SUDO_USER" | grep -qw docker; then
        echo "AVISO: $SUDO_USER ya está en el grupo docker. Cierra sesión y vuelve a entrar."
    else
        read -p "¿Añadir a $SUDO_USER al grupo docker para no necesitar sudo? (s/n) " resp
        if [ "$resp" = "s" ]; then
            usermod -aG docker "$SUDO_USER"
            echo "AVISO: $SUDO_USER añadido. Cierra sesión y vuelve a entrar."
        fi
    fi
fi

# Comprobar conexión con Docker Hub
if ! curl -s --max-time 5 -o /dev/null https://registry-1.docker.io/v2/; then
    echo "ERROR: Sin conexión con Docker Hub." >&2
    exit 1
fi

# --- Redes ---
docker network create biblio-front
docker network create --internal biblio-back

# --- Volúmenes ---
docker volume create biblio-datos
docker volume create biblio-visitas
