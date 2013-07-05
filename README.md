# Cloudlib
Cloudlib is a simple router framework for rapid web development.

Cloudlib conforms to the [PSR-0 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) which makes it easy to use external libraries.

## Installation

#### Github

[Download from github](https://github.com/trumtomte/cloudlib/downloads)


#### Git

`$ git clone git@github.com:trumtomte/cloudlib.git`


#### Composer


```json
{
    "require": {
        "cloudlib/core": "dev-master"
    }
}
```

#### Apache

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

#### Requirements
Cloudlib requires PHP >= 5.4.4

## Examples

under construction...

```php
<?php



```

## Versioning

Versioned with [Semantic Versioning](http://semver.org/)

## License

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) license.
