# PHP Web Proxy

A simple PHP web proxy.

## Usage

### Install

```bash
composer require "isaeken/proxier"
```

### Proxy an URL

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use IsaEken\Proxier\Proxier;

$proxier = new Proxier();
$proxier->setUrl('http://isaeken.com.tr');
$proxier->run();
```

### Proxy an URL with custom html header

```php
$proxier = new Proxier();
$proxier->setUrl('http://isaeken.com.tr');
$proxier->setHeader(<<<HTML
<!--
Your html code is here.
-->
HTML
);
$proxier->run();
```

### Proxy with custom logger

#### Create a logger class

```php
<?php
class Logger implements \IsaEken\Proxier\LoggerInterface
{
    public function log(string $url): void
    {
        echo $url;
    }
}
```

#### Create your proxy

```php
$proxier = new Proxier();
$proxier->setUrl('http://isaeken.com.tr');
$proxier->setLogger(new Logger());
$proxier->run();
```

## Notice

Do not use this package with a framework.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
