<div align="right">
  <a href="README.en.md">English</a> | 
  <a href="README.md">Magyar</a> | 
  <a href="README.cz.md">中文</a>
</div>

# f2b-web 1.0

Egy egyszerű webes felület a **Fail2ban** kezeléséhez és monitorozásához.

---

## 🛠️ Telepítési Útmutató

Kövesd az alábbi lépéseket az alkalmazás és a szükséges függőségek telepítéséhez, valamint a jogosultságok beállításához.

### 1. Előfeltételek és csomagok telepítése

Telepítsd a minimálisan szükséges csomagokat a rendszeredre:

```bash
sudo apt update
sudo apt install apache2 php php-sqlite3 acl fail2ban
```

> **Megjegyzés:** A `php-sqlite2` helyett a legtöbb modern Linux disztribúción a `php-sqlite3` csomag használatos.

---

### 2. Alkalmazás klónozása

Navigálj a webszerver gyökérkönyvtárába, klónozd a tárolót, majd állítsd be a megfelelő tulajdonost:

```bash
cd /var/www
sudo git clone https://github.com/agostonl119/f2b-web.git
sudo chown -R www-data:www-data /var/www/f2b-web/
```

---

### 3. Apache webszerver konfigurálása

Hozz létre egy új konfigfájlt az Apache számára:

```bash
sudo nano /etc/apache2/sites-available/f2b-web.conf
```

Másold be az alábbi konfigurációt:

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

Engedélyezd az új virtuális hostot:

```bash
sudo a2ensite f2b-web
```

---

### 4. HTTP Basic Auth jelszó elkészítése

Hozz létre egy jelszófájlt a hozzáférés korlátozásához (cseréld ki a `user` nevet a kívánt felhasználónévre):

```bash
sudo htpasswd -c /etc/apache2/.htpasswd user
```

---

### 5. Fájl- és mappajogok módosítása (ACL)

Biztosítsd a webszerver (`www-data`) számára az olvasási és végrehajtási jogokat a Fail2ban naplófájljaihoz és adatbázisához:

```bash
sudo setfacl -m u:www-data:r /var/log/fail2ban.log
sudo setfacl -m u:www-data:r /var/lib/fail2ban/fail2ban.sqlite3
sudo setfacl -m u:www-data:rx /var/lib/fail2ban/
```

---

### 6. Sudoers beállítások módosítása

Engedélyezd a `www-data` felhasználónak, hogy jelszó nélkül futtathassa a szükséges parancsokat:

```bash
sudo visudo /etc/sudoers.d/f2b-web
```

Illeszd be a következő sorokat:

```text
www-data ALL=(ALL) NOPASSWD: /usr/bin/fail2ban-client
www-data ALL=(ALL) NOPASSWD: /usr/bin/tail
```

---

### 7. Logrotate beállítása

Szerkeszd a Fail2ban logrotate konfigurációs fájlját:

```bash
sudo nano /etc/logrotate.d/fail2ban
```

Módosítsd a fájlban lévő `create` sort úgy, hogy a `www-data` csoport is hozzáférjen a frissen rotált naplófájlokhoz:

**Eredeti:**
```text
create 640 root adm
```

**Módosított:**
```text
create 640 root www-data
```

---

### 8. Webszerver újratöltése

A beállítások érvénybe léptetéséhez töltsd újra az Apache webszervert:

```bash
sudo systemctl reload apache2
```

### 9. nftables ip tartomány feldolgozása

Ha nftables-t használsz, az ip tartomány feldoglozásához a /etc/fail2ban/action.d/nftables.local fájlba tedd bele az alábbi sorokat:

```bash
[Definition]
_nft_add_set = <nftables> add set <table_family> <table> <addr_set> \{ type <addr_type>\; flags interval\; \}
_nft_check = <nftables> list chain <table_family> <table> <chain> | grep -q '@<addr_set>'

actionban = <nftables> add set <table_family> <table> <addr_set> \{ type <addr_type>; flags interval; \}; \
            <nftables> add element <table_family> <table> <addr_set> \{ <ip> \}

actionunban = <nftables> delete element <table_family> <table> <addr_set> \{ <ip> \}
```
majd indítsd újra a fail2ban-t!


```bash
sudo systemctl reload fail2ban
```

Most már elérheted a felületet a böngésződben a `http://<szerver-ip>/f2b` címen! 
