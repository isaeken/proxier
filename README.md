# PHP Web Proxy

A simple PHP web proxy.

## Install

### As a standalone package

```bash
composer require isaeken/proxier
```

### As a global tool

```bash
composer global require isaeken/proxier
```

## Tool Usage

```bash
proxier <url> --method=<method> --headers=<headers>
``` 

### Proxy an URL

```bash
proxier http://isaeken.com.tr
```

### Proxy an URL with custom html header

```bash
proxier http://isaeken.com.tr --headers="{'Content-Type': 'text/html'}"
```

### Proxy an URL with custom method

```bash
proxier http://isaeken.com.tr --method=POST
```

### Proxy an URL and save to a file

```bash
proxier http://isaeken.com.tr --output=index.html
```

## Usage

### Proxy an URL

```php
use IsaEken\Proxier\Proxier;

$proxier = new Proxier();
$proxier->boot();
$proxier->proxyUsingGlobals('http://isaeken.com.tr');
```

## Notice

This package is under development. Please do not use it in production.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
