# radius_login

Ermöglicht Usern sich über einen RADIUS Server an UliCMS anzumelden.

## Abhängigkeiten

* [KLogger](https://extend.ulicms.de/klogger.html)
* PHP 7.0 oder 7.1
* [PHP radius Modul](http://php.net/manual/de/book.radius.php)

## Installationsanleitung

1. Fügen Sie vor der Installation des Moduls folgendes Snippet in die Datei **CMSConfig.php** ein. Passen Sie die Konfiguration wie gewünscht an

```php
	var $radius_login = [ 
			"radius_host" => [ 
					"server1",
					"server2",
					"server3",
					"server4" 
			],
			"create_user" => true,
			"default_firstname" => "John",
			"default_lastname" => "Doe",
			"skip_on_error" => true,
			"log_enabled" => false,
			"shared_secret" => "mysecret",
			"mail_suffix" => "firma.de",
			"sync_passwords" => true 
	];
```

2. Installieren Sie **KLogger**
3. Installiere Sie **radius_login**

## Konfiguration

radius_login enthält einige anpassbare Konfigurationsparameter

**radius_host** Die Addresse oder der Hostname eines oder mehrerer RADIUS Server

**log_enabled** Ob Log-Dateien erzeugt werden sollen. Die Logs werden im Ordner ULICMS_ROOT/content/logs/radius_login erzeugt.

**skip_on_error** Wenn diese Option **true** ist, erfolgt ein Login über das reguläre Login-System von UliCMS, wenn der Login mehr radius fehl schlägt.

**create_user** Erzeuge einen Lokalen Benutzer, wenn dieser nicht existiert.

**sync_passwords**
Sollen radius Passwörter mit UliCMS synchronisiert werden?

**default_lastname**
Standard Nachname für automatisch erzeugte User

**default_firstname**
Standard Vorname für automatisch erzeugte User

**mail_suffix**
Mail Suffix für automatisch erzeugte User
E-Mail Adressen werden in dem Schema [Username]@[mail_suffix] generiert.