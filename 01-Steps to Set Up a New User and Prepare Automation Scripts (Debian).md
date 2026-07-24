# ✅ STEP 1 — Update Debian First

Login as root:

su -

Update system:

apt update && apt upgrade -y

---

# ✅ STEP 2 — Create a New User

Create a normal user (example: `myuser`)

adduser myuser

You’ll be asked to:

- Set password
- Enter optional info (just press Enter if you want)

---

# ✅ STEP 3 — Install sudo (If Not Installed)

Check if sudo exists:

sudo --version

If not installed:

apt install sudo -y

---

# ✅ STEP 4 — Add User to sudo Group

Add your new user to sudo group:

usermod -aG sudo myuser

Verify:

groups myuser

You should see:

myuser : myuser sudo

---

# ✅ STEP 5 — Install OpenSSH & UFW (Firewall)

# 1. Update and Install

apt update && apt install openssh-server ufw -y

# 2. Ensure SSH is active

systemctl enable --now ssh

# 3. Configure Firewall Rules

# We allow SSH first so we don't lock ourselves out!

ufw default deny incoming

ufw default allow outgoing

ufw allow openssh

Also allow the services that will be used. For example, if you're hosting a web server, allow HTTP (port 80) and HTTPS (port 443) through the firewall before enabling it.

# 4. Enable the firewall

# (--force skips the confirmation prompt)

ufw --force enable

# 5. Verification

echo "🛡️ Firewall is ACTIVE and SSH is ALLOWED."
ufw status

---

# ✅ STEP 6 — Generate SSH Key (On Your Local Windows PC)

Now go to your **Windows computer**, open **PowerShell**:

ssh-keygen

Just press Enter for:

- file location
- passphrase (optional)

This creates:

C:\Users\YourName\.ssh\id_ed25519  
C:\Users\YourName\.ssh\id_ed25519.pub

---

# ✅ STEP 7 — Copy Public Key to Debian User

On Debian (as root):

mkdir -p /home/myuser/.ssh  
nano /home/myuser/.ssh/authorized_keys

Now:

1. On Windows PowerShell:

type $env:USERPROFILE\.ssh\id_ed25519.pub

2. Copy the long output
3. Paste it inside Debian `authorized_keys`
4. Save and exit

Fix permissions:

chown -R myuser:myuser /home/myuser/.ssh  
chmod 700 /home/myuser/.ssh  
chmod 600 /home/myuser/.ssh/authorized_keys

---

# ✅ STEP 8 — Test SSH Login from Windows

In PowerShell:

ssh myuser@SERVER_IP

Example:

ssh myuser@192.168.1.10

If working, you should login **without password** 🎉

---

# ✅ STEP 9 — (Optional but Recommended) Disable Root SSH Login

On Debian:

nano /etc/ssh/sshd_config

Find:

PermitRootLogin yes

Change to:

PermitRootLogin no

Restart SSH:

systemctl restart ssh

Now only your user can login via SSH.

---

# ✅ STEP 10 — Add Automation Scripts

Login as your new user:

su - myuser

Move to home directory:

cd ~

Create script example:

nano install.sh

Example content:

#!/bin/bash  
echo "Updating system..."  
sudo apt update -y

Save and exit.

---

# ✅ STEP 11 — Make Script Executable

chmod +x install.sh

Run it:

sudo ./install.sh

or
if it need admin

./install.sh

---

# 🔐 EXTRA SECURITY (Highly Recommended)

After confirming SSH key works:

Disable password login completely.

Edit:

sudo nano /etc/ssh/sshd_config

Change:

PasswordAuthentication no

Restart SSH:

sudo systemctl restart ssh

Now only SSH keys can login
