#!/bin/bash

echo "==============================="
echo " NGINX INSTALLER"
echo "==============================="

# ---- Check if root ----

if [ "$EUID" -ne 0 ]; then
echo "❌ Please run as root or with sudo."
exit 1
fi

# ---- Update packages ----

echo "📦 Updating system packages..."
sudo apt update -y

# ---- Install nginx ----

echo "🌐 Installing NGINX..."
sudo apt install nginx -y

# ---- Enable nginx on boot ----

echo "⚙️ Enabling NGINX service..."
sudo systemctl enable nginx

# ---- Start nginx ----

echo "🚀 Starting NGINX..."
sudo systemctl start nginx

sudo nginx -t

# ---- Configure firewall if UFW exists ----

if command -v ufw >/dev/null 2>&1; then
echo "🔥 Configuring firewall..."
sudo ufw allow 'Nginx Full'
fi

# ---- Show status ----

echo "🔍 Checking NGINX status..."
sudo systemctl status nginx --no-pager

echo ""
echo "✅ NGINX installation completed!"
echo "🌍 Open your browser and visit: http://YOUR_SERVER_IP"
