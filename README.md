Szükséges csomagok:

sudo apt update
sudo apt install php-sqlite3 acl

sudo systemctl restart apache2

vagy

sudo systemctl restart php8.2-fpm

SQLite adatbázis olvasási jog

sudo setfacl -m u:www-data:r /var/log/fail2ban.logsultságok (ACL)

Hogy a webszerver (www-data) közvetlenül tudja olvasni a Fail2Ban SQLite adatbázisát:

sudo setfacl -m u:www-data:r /var/lib/fail2ban/fail2ban.sqlite3
sudo setfacl -m u:www-data:rx /var/lib/fail2ban/

Fail2Ban Log Fájl olvasási jogosultság (/var/log/fail2ban.log)
Mivel az élő logok olvasásához a backend a tail parancsot futtatja a logfájlon, biztosítani kell, hogy a www-data user olvashassa azt:

sudo setfacl -m u:www-data:r /var/log/fail2ban.log

Ezen kívül a logrotate.d/fail2ban fájlban is írd át a group-ot www-data-ra!

Socket és fail2ban-client jogosultságok (Sudoers helyett socket alapú hozzáférés)

Mivel a kódban a fail2ban-client parancsokat közvetlenül, sudo nélkül futtatjuk a socketen keresztül, ellenőrizd a /etc/fail2ban/fail2ban.conf-ban, hogy a socket fájl jogosultsága engedélyezi-e a hozzáférést (alapesetben a Fail2Ban socketet a root vagy a fail2ban csoport hozza létre megfelelő socketperms = 660-al vagy 777-el, illetve a Unix socket fájl jogai biztosítják, hogy a daemon végrehajtsa a socketen érkező banip/unbanip kéréseket).

Ha a socket eléréséhez mégis szükség lenne sudo-ra (vagy ha a rendszer nem engedélyezi közvetlenül a socket írását a www-data-nak), akkor a /etc/sudoers fájlba (a visudo paranccsal) az alábbi sor beillesztésével engedélyezhető a jelszómentes futtatás kizárólag a fail2ban-client-hez:

www-data ALL=(root) NOPASSWD: /usr/bin/fail2ban-client
(A jelenlegi socket-alapú api.php implementációnk megpróbálja közvetlenül futtatni a klienst; ha a fenti sudoers sornak adsz jogot, az api.php-ban a parancs elejére beilleszthető a sudo).
