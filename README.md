Status: Workin progress...

readonly zugriff auf kategorien und dateien funktioniert im browser als auch als netzlaufwerk.
im gegensatz zu ftp zugriff aufs /files verzeichnis berücksichtigt das webdav addon die metadaten aus der datenbank.
daher werden ordner - wie im medienpool kategorien - dargestellt und nicht wie im plain filesystem als ein rießiges verzeichnis.

Setup:
- Addon installieren
- Hinweise beachten
  - http://sabre.io/dav/install/
  - https://forum.owncloud.org/viewtopic.php?f=17&t=7536
- webdav url: www.mydomain.org/redaxo/webdav.php
- zugangsdaten: redaxo backend user/passwort
