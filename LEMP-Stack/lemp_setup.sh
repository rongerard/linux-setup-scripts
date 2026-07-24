#!/bin/bash

# =============================================================
# LEMP Stack Auto-Deployment Script for Debian (MariaDB)
# Usage: sudo bash lemp_setup.sh
# =============================================================

set -e  # Exit on any error

# ---------------------------------------------------------------
# CONFIGURATION — Edit these before running
# ---------------------------------------------------------------
APP_NAME="myapp"
APP_DIR="/var/www/$APP_NAME"
DB_NAME="my_database"
DB_USER="my_user"
DB_PASS="my_password"        # Change this!
MARIADB_ROOT_PASS="rootpassword"  # Change this!
PHP_VERSION="8.4" # Change this to match your Debian version:
                   # Debian 12 → PHP 8.2
                   # Debian 13 → PHP 8.4
# ---------------------------------------------------------------

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

log()   { echo -e "${GREEN}[✔] $1${NC}"; }
warn()  { echo -e "${YELLOW}[!] $1${NC}"; }
error() { echo -e "${RED}[✘] $1${NC}"; exit 1; }

# Must be run as root
if [ "$EUID" -ne 0 ]; then
  error "Please run as root: sudo bash lemp_setup.sh"
fi

# ---------------------------------------------------------------
# STEP 1 — Get server local IP
# ---------------------------------------------------------------
log "Detecting server IP address..."
SERVER_IP=$(hostname -I | awk '{print $1}')
log "Server IP: $SERVER_IP"

# ---------------------------------------------------------------
# STEP 2 — Update system
# ---------------------------------------------------------------
log "Updating system packages..."
apt update && apt upgrade -y
log "System updated."

# ---------------------------------------------------------------
# STEP 3 — Install Nginx
# ---------------------------------------------------------------
log "Installing Nginx..."
apt install nginx -y
systemctl start nginx
systemctl enable nginx
log "Nginx installed and running."

# ---------------------------------------------------------------
# STEP 4 — Install MariaDB
# ---------------------------------------------------------------
log "Installing MariaDB..."
apt install mariadb-server mariadb-client -y
systemctl start mariadb
systemctl enable mariadb
log "MariaDB installed and running."

# ---------------------------------------------------------------
# STEP 5 — Secure MariaDB (replaces mysql_secure_installation)
# ---------------------------------------------------------------
log "Securing MariaDB..."
mariadb -u root <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED BY '${MARIADB_ROOT_PASS}';
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
EOF
log "MariaDB secured."

# ---------------------------------------------------------------
# STEP 6 — Install PHP and PHP-FPM
# ---------------------------------------------------------------
log "Installing PHP ${PHP_VERSION} and extensions..."
apt install php-fpm php-mysql php-curl php-xml php-mbstring php-zip php-gd -y
systemctl start php${PHP_VERSION}-fpm
systemctl enable php${PHP_VERSION}-fpm
log "PHP ${PHP_VERSION} installed and running."

# ---------------------------------------------------------------
# STEP 7 — Check and configure UFW
# ---------------------------------------------------------------
log "Checking UFW..."
if ! command -v ufw &> /dev/null; then
    log "UFW not found, installing..."
    apt install ufw -y
else
    log "UFW already installed, skipping install."
fi

# Check if SSH is already allowed
if ! ufw status | grep -q "22/tcp"; then
    ufw allow 22/tcp
    log "SSH (port 22) allowed."
else
    warn "SSH rule already exists, skipping."
fi

# Check if Nginx Full is already allowed
if ! ufw status | grep -q "Nginx Full"; then
    ufw allow 'Nginx Full'
    log "Nginx Full allowed."
else
    warn "Nginx Full rule already exists, skipping."
fi

# Enable UFW if not already active
if ! ufw status | grep -q "Status: active"; then
    ufw --force enable
    log "UFW enabled."
else
    warn "UFW already active, skipping."
fi

ufw status
log "UFW configured."

# ---------------------------------------------------------------
# STEP 9 — Create app folder structure
# ---------------------------------------------------------------
log "Creating app directory structure at $APP_DIR..."
mkdir -p $APP_DIR/{public/assets/{css,js,images},src,config,storage/{logs,cache,uploads},vendor}

# Set permissions
CURRENT_USER=$(logname 2>/dev/null || echo "${SUDO_USER:-www-data}")
chown -R $CURRENT_USER:www-data $APP_DIR
find $APP_DIR -type d -exec chmod 755 {} \;
find $APP_DIR -type f -exec chmod 644 {} \;
chmod -R 775 $APP_DIR/storage

log "App directory created with correct permissions."

# ---------------------------------------------------------------
# STEP 9 — Create a default index.php
# ---------------------------------------------------------------
log "Creating default index.php..."
cat > $APP_DIR/public/index.php <<'PHPEOF'
<?php
echo "<h1>LEMP Stack is working!</h1>";
echo "<p>Server: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
PHPEOF
log "Default index.php created."

# ---------------------------------------------------------------
# STEP 10 — Create Nginx config for the app
# ---------------------------------------------------------------
log "Creating Nginx config for $APP_NAME..."
cat > /etc/nginx/sites-available/$APP_NAME <<EOF
server {
    listen 80;
    server_name $SERVER_IP localhost;
    root $APP_DIR/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ =404;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/${APP_NAME}_access.log;
    error_log  /var/log/nginx/${APP_NAME}_error.log;
}
EOF
log "Nginx config created."

# ---------------------------------------------------------------
# STEP 11 — Enable site, disable default, reload Nginx
# ---------------------------------------------------------------
log "Enabling site and disabling default..."
ln -sf /etc/nginx/sites-available/$APP_NAME /etc/nginx/sites-enabled/

# Disable default site
if [ -f /etc/nginx/sites-enabled/default ]; then
    rm /etc/nginx/sites-enabled/default
    log "Default Nginx site disabled."
fi

# Test Nginx config
nginx -t || error "Nginx config test failed! Check your config."

# Reload Nginx
systemctl reload nginx
log "Nginx reloaded."

# ---------------------------------------------------------------
# STEP 12 — Setup MariaDB database and user
# ---------------------------------------------------------------
log "Setting up database and user..."
mariadb -u root -p"${MARIADB_ROOT_PASS}" <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME}
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';

GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';

FLUSH PRIVILEGES;

USE ${DB_NAME};

CREATE TABLE IF NOT EXISTS contacts (
    id    INT          NOT NULL AUTO_INCREMENT,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30)  NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
EOF
log "Database '$DB_NAME', user '$DB_USER', and contacts table created."

# ---------------------------------------------------------------
# DONE
# ---------------------------------------------------------------
echo ""
echo -e "${GREEN}=================================================${NC}"
echo -e "${GREEN}  LEMP Stack Setup Complete!${NC}"
echo -e "${GREEN}=================================================${NC}"
echo -e "  App directory  : ${YELLOW}$APP_DIR/public${NC}"
echo -e "  Local access   : ${YELLOW}http://localhost${NC}"
echo -e "  Network access : ${YELLOW}http://$SERVER_IP${NC}"
echo -e "  Database       : ${YELLOW}$DB_NAME${NC}"
echo -e "  DB User        : ${YELLOW}$DB_USER${NC}"
echo -e "  PHP Version    : ${YELLOW}$(php -v | head -1)${NC}"
echo -e "${GREEN}=================================================${NC}"
echo ""
warn "Remember to place your PHP app files in: $APP_DIR/public"
warn "Change default passwords in the script before reusing it!"
