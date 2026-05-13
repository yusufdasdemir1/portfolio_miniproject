#!/bin/bash
set -e

# Ensure upload directories exist and are writable
mkdir -p /var/www/html/assets/images/projects
chmod -R 777 /var/www/html/assets/images

exec "$@"
