<div align="right">
  <a href="README.en.md">English</a> | 
  <a href="README.md">Magyar</a> | 
  <a href="README.cz.md">中文</a>
</div>

# f2b-web 1.0

一个用于管理和监控 **Fail2ban** 的简单网页界面。

---

## 🛠️ 安装指南

请按照以下步骤安装应用程序、所需依赖项并设置正确的权限。

### 1. 先决条件与安装软件包

在您的系统上安装所需的最低软件包：

```bash
sudo apt update
sudo apt install apache2 php php-sqlite3 acl fail2ban
```

> **注意：** 在大多数现代 Linux 发行版中，使用 `php-sqlite3` 代替 `php-sqlite2`。

---

### 2. 克隆应用程序

导航到您的网络服务器根目录，克隆仓库并设置适当的所有者：

```bash
cd /var/www
sudo git clone https://github.com/agostonl119/f2b-web.git
sudo chown -R www-data:www-data /var/www/f2b-web/
```

---

### 3. 配置 Apache Web 服务器

为 Apache 创建一个新的配置文件：

```bash
sudo nano /etc/apache2/sites-available/f2b-web.conf
```

粘贴以下配置：

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

启用新的虚拟主机：

```bash
sudo a2ensite f2b-web
```

---

### 4. 创建 HTTP Basic Auth 密码

创建一个密码文件以限制访问（将 `user` 替换为您所需的用户名）：

```bash
sudo htpasswd -c /etc/apache2/.htpasswd user
```

---

### 5. 修改文件和文件夹权限 (ACL)

为网络服务器 (`www-data`) 提供访问 Fail2ban 日志文件和数据库的读取和执行权限：

```bash
sudo setfacl -m u:www-data:r /var/log/fail2ban.log
sudo setfacl -m u:www-data:r /var/lib/fail2ban/fail2ban.sqlite3
sudo setfacl -m u:www-data:rx /var/lib/fail2ban/
```

---

### 6. 修改 Sudoers 设置

允许 `www-data` 用户无需密码即可运行必要的命令：

```bash
sudo visudo /etc/sudoers.d/f2b-web
```

粘贴以下内容：

```text
www-data ALL=(ALL) NOPASSWD: /usr/bin/fail2ban-client
www-data ALL=(ALL) NOPASSWD: /usr/bin/tail
```

---

### 7. 设置 Logrotate

编辑 Fail2ban 的 logrotate 配置文件：

```bash
sudo nano /etc/logrotate.d/fail2ban
```

修改文件中的 `create` 行，以便 `www-data` 组也可以访问新轮转的日志文件：

**原始内容：**
```text
create 640 root adm
```

**修改后：**
```text
create 640 root www-data
```

---

### 8. 重新加载 Web 服务器

重新加载 Apache Web 服务器以应用更改：

```bash
sudo systemctl reload apache2
```

### 9. nftables IP网段处理

如果您使用的是 nftables，请将以下行添加到 `/etc/fail2ban/action.d/nftables.local` 中以正确处理 IP 网段：

```bash
[Definition]
_nft_add_set = <nftables> add set <table_family> <table> <addr_set> \{ type <addr_type>\; flags interval\; \}
_nft_check = <nftables> list chain <table_family> <table> <chain> | grep -q '@<addr_set>'

actionban = <nftables> add set <table_family> <table> <addr_set> \{ type <addr_type>; flags interval; \}; \
            <nftables> add element <table_family> <table> <addr_set> \{ <ip> \}

actionunban = <nftables> delete element <table_family> <table> <addr_set> \{ <ip> \}
```
然后重启 Fail2ban！

```bash
sudo systemctl reload fail2ban
```

现在您可以在浏览器中访问 `http://<server-ip>/f2b` 来打开界面了！