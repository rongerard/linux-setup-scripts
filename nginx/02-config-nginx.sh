#!/bin/bash

# 1. Root Check

if [ "$EUID" -ne 0 ]; then
echo "Please run this script with sudo."
exit 1
fi

# Configuration Variables

SOURCE_DIR="/home/myuser/myapp/nginx"
TARGET_DIR="/etc/nginx/sites-enabled"
NGINX_CONF="/etc/nginx/nginx.conf"
INCLUDE_LINE=" include $SOURCE_DIR/\*.conf;"

echo "--- Starting Nginx Configuration Integration ---"

# 2. Backup and Modify nginx.conf

if [ -f "$NGINX_CONF" ]; then
cp "$NGINX_CONF" "$NGINX_CONF.bak.$(date +%F-%H%M%S)"
echo "Check: Backup created for nginx.conf"

    if grep -Fq "$SOURCE_DIR/*.conf" "$NGINX_CONF"; then
        echo "Skip: Include line already exists in nginx.conf"
    else
        # Insert the include line after the 'http {' block opener
        sed -i "/http {/a $INCLUDE_LINE" "$NGINX_CONF"
        echo "Success: Include line added to nginx.conf"
    fi

else
echo "Error: $NGINX_CONF not found."
fi

# 3. Create Symlinks (sites-enabled)

if [ -d "$TARGET_DIR" ]; then
for file in "$SOURCE_DIR"/*.conf; do
        if [ -e "$file" ]; then
filename=$(basename "$file")
ln -sf "$file" "$TARGET_DIR/$filename"
echo "Linked: $filename -> $TARGET_DIR"
fi
done
else
echo "Warning: $TARGET_DIR not found, skipping symlinks."
fi

# 4. Set Permissions

# Nginx needs +x on all parent directories to reach the files

chmod -R 755 /home/myuser/myapp/nginx
chmod 644 /home/myuser/myapp/nginx/\*.conf
echo "Success: Permissions updated for $SOURCE_DIR"

# 5. Test and Reload

echo "--- Testing Configuration ---"
nginx -t
if [ $? -eq 0 ]; then
echo "Final Step: NGINX test successful. Reloading..."
systemctl reload nginx
else
echo "Critical: NGINX config test failed. Please check your .conf files."
exit 1
fi
