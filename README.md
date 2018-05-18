Symfony Multisite bundle
======================

The repository contains the Symfony multisite bundle for Symfony 3. 
This bundle allows you to manage multisite in symfony application, each site can have its own database connection or use the same database.

## Installation

```

composer require prodigious/symfony-multisite-bundle

```

## Setup

```php
    $bundles = array(
        ...
        new Prodigious\MultisiteBundle\MultisiteBundle(),
        ...
    );
```

Then add these lines to your composer.json of your Symfony project:

```
    "scripts": {
        "post-install-cmd": [
            ...
            "Prodigious\\MultisiteBundle\\Composer\\ScriptHandler::installBundle"
        ],
        "post-update-cmd": [
            ...
            "Prodigious\\MultisiteBundle\\Composer\\ScriptHandler::installBundle"
        ]
    }
```

Afterwards, initialize the bundle using

```

composer install

```

After the installation, the bundle will create some files in your project :

- sites/
- app/MultisiteKernel.php

And you need to modify some files :

##### Add these lines to .htaccess in the folder web:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Multisite conditions
    RewriteCond %{DOCUMENT_ROOT}/robots/%{HTTP_HOST}.txt -f
    RewriteRule ^robots\.txt$ robots/%{HTTP_HOST}.txt [L]

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^public/([^/]+)(?:/(.*)|$) /$2 [QSA,R,L]
    # End
</IfModule>
```

##### Modifiy your app.php and app_dev.php in the folder web:

Replace
`
require __DIR__.'/../vendor/autoload.php
`
To
`
require __DIR__.'/../sites/autoload/sites.php';
`

And Relace

`php
    $kernel = new AppKernel('prod', false);
    $kernel = new AppKernel('dev', true);
`

To

`php
    $kernel = new MultisiteKernel('prod', false);
    $kernel = new MultisiteKernel('dev', true);
`

Like this :

```php
// require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../sites/autoload/sites.php';

if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new MultisiteKernel('prod', false);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
```

## Instructions

There is a list of commands to manage your sites.

#### Create a new site

```

php bin/console site:create

```
![screenshot](https://github.com/nan-guo/Multisite-Bundle/blob/master/Resources/public/imgs/screenshot-1.png)

#### Get the list of sites

```

php bin/console site:list

```
![screenshot](https://github.com/nan-guo/Multisite-Bundle/blob/master/Resources/public/imgs/screenshot-2.png)

#### Disable a site

```

php bin/console site:disable --name=demo_1

```
![screenshot](https://github.com/nan-guo/Multisite-Bundle/blob/master/Resources/public/imgs/screenshot-3.png)

#### Enable a site

```

php bin/console site:enable --name=demo_1

```
![screenshot](https://github.com/nan-guo/Multisite-Bundle/blob/master/Resources/public/imgs/screenshot-4.png)

#### Delete a site

```

php bin/console site:delete --name=demo_1

```
![screenshot](https://github.com/nan-guo/Multisite-Bundle/blob/master/Resources/public/imgs/screenshot-5.png)
