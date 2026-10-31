<div align="right">
  <a href="README.en.md">English</a> | 
  <a href="README.md">Magyar</a> | 
  <a href="README.cz.md">中文</a>
</div>

# f2b-web 1.0

A simple web interface for managing and monitoring **Fail2ban**.

---

## 🛠️ Installation Guide

Follow the steps below to install the application, required dependencies, and set up the correct permissions.

### 1. Prerequisites and Package Installation

Install the minimum required packages on your system:

```bash
sudo apt update
sudo apt install apache2 php php-sqlite3 acl fail2ban
```

> **Note:** On most modern Linux distributions, `php-sqlite3` is used instead of `php-sqlite2`.

---

### 2. Cloning the Application

Navigate to your web server root directory, clone the repository, and set the appropriate ownership:

```bash
cd /var/www
sudo git clone https://github.com/agostonl119/f2b-web.git
sudo chown -R www-data:www-data /var/www/f2b-web/
```

---

### 3. Configuring the Apache Web Server

Create a new configuration file for Apache:

```bash
sudo nano /etc/apache2/sites-available/f2b-web.conf
```

Paste the following configuration:

```apache
Alias /f2b /var/www/f2b-web

<Directory /var/www/f2b-web/>
    Options FollowSymLinks
    AllowOverride None
    AuthType Basic
    AuthName "Restricted area"
    AuthUserFile /etc/apache2/.htpasswd
    Require valid-user
</Directory>
```

Enable the new virtual host:

```bash
sudo a2ensite f2b-web
```

---

### 4. Creating HTTP Basic Auth Password

Create a password file to restrict access (replace `user` with your desired username):

```bash
sudo htpasswd -c /etc/apache2/.htpasswd user
```

---

### 5. Modifying File and Folder Permissions (ACL)

Grant the web server (`www-data`) read and execute permissions for the Fail2ban log files and database:

```bash
sudo setfacl -m u:www-data:r /var/log/fail2ban.log
sudo setfacl -m u:www-data:r /var/lib/fail2ban/fail2ban.sqlite3
sudo setfacl -m u:www-data:rx /var/lib/fail2ban/
```

---

### 6. Modifying Sudoers Settings

Allow the `www-data` user to execute the required commands without a password:

```bash
sudo visudo /etc/sudoers.d/f2b-web
```

Paste the following lines:

```text
www-data ALL=(ALL) NOPASSWD: /usr/bin/fail2ban-client
www-data ALL=(ALL) NOPASSWD: /usr/bin/tail
```

---

### 7. Setting up Logrotate

Edit the Fail2ban logrotate configuration file:

```bash
sudo nano /etc/logrotate.d/fail2ban
```

Modify the `create` line in the file so that the `www-data` group also has access to freshly rotated log files:

**Original:**
```text
create 640 root adm
```

**Modified:**
```text
create 640 root www-data
```

---

### 8. Reloading the Web Server

Reload the Apache web server to apply the changes:

```bash
sudo systemctl reload apache2
```

### 9. nftables IP Range Processing

If you are using nftables, add the following lines to `/etc/fail2ban/action.d/nftables.local` to process IP ranges correctly:

```bash
[Definition]
_nft_add_set = <nftables> add set <table_family> <table> <addr_set> \{ type <addr_type>\; flags interval\; \}
_nft_check = <nftables> list chain <table_family> <table> <chain> | grep -q '@<addr_set>'

actionban = <nftables> add set <table_family> <table> <addr_set> \{ type <addr_type>; flags interval; \}; \
            <nftables> add element <table_family> <table> <addr_set> \{ <ip> \}

actionunban = <nftables> delete element <table_family> <table> <addr_set> \{ <ip> \}
```
Then restart Fail2ban!

```bash
sudo systemctl reload fail2ban
```

You can now access the interface in your browser at `http://<server-ip>/f2b`!