# just-core-auth-page
##JCORE\SERVICE\AUTH\PAGE_FILTER

basic add on service to authenticate an aplication based on a basic "WHITELIST or BLACKLIST" permission scheam

####WHITELIST

to provide the most security, only allow specific pages to be viewed

####BLACKLIST
to provide a more public based site, allow specific pages to be blocked


-----------------------------------------------------------------------------


## Installation 

#### Composer
Add the project to your composer file `"just-core/auth-page" : "dev-master",` 

```
{
	"name" : "your project",
	"description" : "info about your project",
	"license" : "GNU",
	"version" : "1.0.0",
	"require" : {
		"php" : ">=5.3.3",
		"just-core/foundation" : "0.5.*",
		"just-core/foundation" : "dev-master",
		"just-core/auth-page" : "dev-master"
	},
	"autoload" : {
		"classmap" : [
			"SERVICES"
		]
	}
}

```
#### Configuration
You will also need to take the example files `CONFIG.AUTOLOAD.auth.page.local.php` and `harness.example.php` modify the examples and make them available in your application, ie.
```
[application_root]/.../[http_exposed_dir]/harness.php
[application_root]/CONFIG/AUTOLOAD/auth[.login].local.php
```

