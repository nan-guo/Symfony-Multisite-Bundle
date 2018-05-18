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

