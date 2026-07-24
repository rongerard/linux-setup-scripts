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


ufw status

---

# ✅ STEP 6 — Generate an SSH Key (On Your Local Windows PC)

On your Windows computer, open PowerShell:

ssh-keygen

Press Enter to accept the default values:

File location
Passphrase (optional)

This creates:

C:\Users\YourName\.ssh\id_ed25519
C:\Users\YourName\.ssh\id_ed25519.pub

To display your public key, run:

type $env:USERPROFILE\.ssh\id_ed25519.pub

Leave this window open or copy the output—you'll use it in the next step.

---

# ✅ STEP 7 — Log in Using Your Password and Install Your Public Key

From Windows PowerShell, connect to your Debian server using your newly created user:

ssh myuser@SERVER_IP

Example:

ssh myuser@192.168.1.10

The first time you connect, you'll see a message similar to:

The authenticity of host 'SERVER_IP' can't be established.

Type:

yes

When prompted, enter the password you created for myuser.

After logging in successfully:

1. Create the .ssh directory
mkdir -p ~/.ssh
2. Create (or edit) the authorized_keys file
nano ~/.ssh/authorized_keys
3. Paste your public key

Return to your Windows PowerShell, copy the output of:

type $env:USERPROFILE\.ssh\id_ed25519.pub

Paste the entire key into authorized_keys.

Save and exit:

Ctrl + O, Enter
Ctrl + X
4. Set the correct permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

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
