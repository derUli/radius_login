# radius_login

Enables users to login using a RADIUS server

## Dependencies

* [KLogger](https://extend.ulicms.de/klogger.html)
* PHP 7.0 or 7.1
* [PHP radius Module](http://php.net/manual/de/book.radius.php)

## Installation instructions

1. Add this snippet to your CMSConfig.php before installation of packages. Adjust it to your individual requirements.

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

2. Install KLogger
3. Install radius_login

## Configuration

radius_login offers some tweakable configuration parameters.

**radius_host** address of one or multiple RADIUS servers 

**log_enabled** Write log files. The log files will be saved in ULICMS_ROOT/content/logs/radius_login.

**skip_on_error** If this is true radius_login will pass credentials to regular UliCMS login procedure, when login with Radius fails.

**create_user** Create local user if it doesn't exists.

**sync_passwords**
Sync Radius passwords with local users on login.

**default_lastname**
Default Lastname for newly created users.

**default_firstname**
Default Firstname for newly created users.

**mail_suffix**
Mail Suffix for newly created users.
Mail addresses will be constructed in scheme [Username]@[mail_suffix]
