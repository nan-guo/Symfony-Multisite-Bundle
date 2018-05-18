Symfony Multisite bundle
======================

The repository contains the Symfony multisite bundle for Symfony 3. 
This bundle allows you to manage multisite in symfony application.

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

## Instructions

There is a list of commands to manage your sites.

#### Get the list of sites

```

php bin/console site:list

```
![screenshot](https://github.com/nan-guo/Multisite-Bundle/blob/master/Resources/public/imgs/screenshot-1.png)

#### Create a new site

```

php bin/console site:create

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
