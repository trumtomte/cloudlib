# Cloudlib

Cloudlib is a flexible PHP framework for web development.  
Inspired by frameworks like Flask / Bottle (Python) and Laravel (PHP).  

Since Cloudlib´s syntax is the same as many other lightweight PHP frameworks it is easy to learn and understand.

```php
<?php

require 'cloudlib/Cloudlib.php';

$app = new cloudlib\Cloudlib(__DIR__);

$app->get('/', function() use ($app)  
{  
    return 'Hello World!';  
});

$app->run();
```

* Cloudlib requires PHP 5.3.6 or later.

## Installation

Download from Github  
https://github.com/trumtomte/cloudlib/downloads

Clone with git  
`$ git clone git@github.com:trumtomte/cloudlib.git`

Install with packagist  
http://packagist.org/packages/cloudlib/Cloudlib

#### Apache
Example of a `.htaccess` file for pretty URLs
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

## Routes

Cloudlib supports the four main HTTP request methods: `GET`, `POST`, `PUT`, `DELETE`. 
 
`NOTE` the request method `HEAD` also works (the same as `GET`) but only outputs headers.

```php
<?php

$app->get('/', function() use ($app)  
{  
    return 'This is a GET request';  
});

$app->post('/', function() use ($app)  
{  
    return 'This is a POST request';  
});

$app->put('/', function() use ($app)  
{  
    return 'This is a PUT request';  
});

$app->delete('/', function() use ($app)  
{  
    return 'This is a DELETE request';  
});

// Defining multiple request methods to one route is done
// by passing them via an array using the route() method
$app->route('/', array('GET', 'POST', 'PUT', 'DELETE'), function() use ($app)  
{  
    return 'This is a route with multiple request methods';  
});
```

### Routes with parameters

Adding parameters to routes is done by adding a colon `:` in front of a word.  
The parameters will be passed to the corresponding route response function.

```php
<?php

$app->get('/view/:page', function($page) use ($app)  
{  
  return "Viewing the page: $page";  
});

// Multiple parameters also works
$app->get('/:year/:month/:day', function($year, $month, $day) use ($app)
{  
    return "The date: $day - $month - $year";
});
```

## Views & Layouts

Returing a simple string as a response is most likely not what you want to do, this is what Views are for.  

`NOTE` View and Layout names are suffixed with `.php`.

```php
<?php

$app->get('/', function() use ($app)  
{  
    // Here we have a View called `home` and a Layout called `main`
    return $app->render('home', 'main');  
});
```
This is what they could contain.  

The View `home`  
```php
<?php

    echo '<p>This would be the only contents of the view!</p>';  

```

The Layout `main`  
```php
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="utf-8" \\>  
    <title>Example</title>  
</head>  
<body>  
<?php
    // View contents are loaded via a variable called $body  
    echo $body;  
?>  
</body>  
</html>  
```
`NOTE` Layouts are optional, you can have everything inside a View if you prefer that. Just skip or pass `null` to the Layout parameter of `render()`.  
### View variables

```php
<?php

$app->get('/', function() use ($app)  
{  
    // Setting View variables (name, value)
    $app->set('hello', 'Hello')
        ->set('world', 'World!');

    return $app->render('home');
});  
```

The View `home.php`
```php
<?php

    // Outputs: Hello World!
    echo "$hello $world";  
```
Passing variables without the `set()` method also works, or together
```php
<?php

$app->get('/hello/:world', function($world) use ($app)  
{  
    // Set a View variable like before  
    $app->set('world', $world);  

    // Define your own array of values  
    $array = array('foo' => 'Foo', 'bar' => 'Bar!');  

    // $array will be merged with your other variables, also notice that we are not using a Layout  
    return $app->render('home', null, $array);  
});
```

The View `home.php`
```php
<?php
    // This would be an example of http://www.domain.com/World

    // Outputs: Hello World!, Foo Bar!
    echo "Hello $world!, $foo $bar";  
```

## Customization

TODO

## Configuration

TODO

## Errors

TODO

## Models

TODO

## Controllers

TODO

## Helpers

TODO

## License/Versioning/About

TODO
