# Cloudlib

Cloudlib is a flexible PHP framework for web development.  
Inspired by frameworks like [Flask][flasklink] / [Bottle][bottlelink] (Python) and [Laravel][laravellink] (PHP).  

Since Cloudlib´s syntax is the same as many other lightweight PHP frameworks it is easy to learn and understand. It also follows common principles of [REST][restlink] and [MVC][mvclink].

`NOTE` Cloudlib is under active development; therefore the documentation and codebase is subject to change.
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

Follow the framework on Twitter([@cloudlibfwork](https://twitter.com/#!/cloudlibfwork)). If you have any questions / feedback send an email to cloudlibframework@gmail.com

## Installation

Download from Github  
https://github.com/trumtomte/cloudlib/downloads

Clone with git  
`$ git clone git@github.com:trumtomte/cloudlib.git`

Install with packagist  
http://packagist.org/packages/cloudlib/Cloudlib

Example of a `composer.json`.
```json
{
    "require": {
        "cloudlib\Cloudlib": "0.1.0"
    }
}
```
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
When creating a new object of Cloudlib you have a couple of options (these are passed as the constructor arguments).  

The first and second parameter.
```php
<?php

// The first parameter tells Cloudlib where it is, the easiest way of doing this is with __DIR__.
$app = new cloudlib\Cloudlib(__DIR__);

// The second parameter specifies the base uri (if you have your application in an subfolder this is it),  
// ex. www.domain.com/myproject would have the base uri "/myproject".
$app = new cloudlib\Cloudlib(__DIR__, '/myproject');

// Defaults to "/".
```
The third parameter is an array of two keys with boolean values.
```php
<?php

// First, "autoloader" if set to false it requires you to specify your own autoloader for classes.
// It defaults to true which means Cloudlib uses its own autoloader for classes (it follows the PSR-0 standard).

// Example with SplClassLoader (https://gist.github.com/221634), also tested with UniversalClassLoader (Symfony).
require 'path/to/SplClassLoader.php';
$loader = new SplClassLoader('cloudlib', 'path/to/cloudlib');
$loader->register();

// We now specify that we don't want to use the default autoloader.
$app = new cloudlib\Cloudlib(__DIR__, '/', array('autoloader' => false));
```
```php
<?php

// Second, "bootstrap" if set to false it lets us
// modify the default directory structure (see this repository),
// adding aliases for namespaced classes (lazy loading) and to register more namespaces (if you use other libraries)
$app = new cloudlib\Cloudlib(__DIR__, '/', array('bootstrap' => false)); 
```
**Directory paths**
You can define your own paths or alter the default paths.  

**Default paths**  

* `controllers` path to your Controllers
* `models` path to your Models
* `views` path to your Views
* `layouts` path to your Layouts
* `logs` path to your Logs
* `config` this is a complete path to the config file (ex `path/to/config.php`)
* `uploader` path to where your files will be uploaded to
* `image` path to where your image(s) will be loaded/saved
* `css` relative path to your CSS files
* `js` relative path to your JavaScript files
* `img` relative path to your Image files
* `classes` path to which core classes are loaded **do not alter unless you really have to**

```php
<?php

// Example of defining directory paths
$app = new cloudlib\Cloudlib(__DIR__, '/', array('bootstrap' => false));

// Define paths with an array
$app->setPaths(array(
    'controllers' => 'path/to/controllers',
    'models' => 'path/to/models'
));

// Define a path
$app->setPath('controllers', 'path/to/controllers');

// Important! After you have set 'bootstrap' to false you have
// to call the bootstrap before adding routes or calling classes
$app->bootstrap();
```
**Working with the default class autoloader**  
You are able to register aliases and namespaces

**Aliases**
```php
<?php

$app = new cloudlib\Cloudlib(__DIR__, '/', array('bootstrap' => false));

// To register an aliases you pass an array of `'ClassName' => 'namespace\\ClassName'` pairs
$app->loader->registerAliases(array(
    'MyClassName' => 'my\\namespace\\MyClassName'
));

// Important! After you have set 'bootstrap' to false you have
// to call the bootstrap before adding routes or calling classes
$app->bootstrap();

// This would enable you to load classes without namespaces (lazy)
$class = new MyClassName();  // Instead of $class = new my\namespace\MyClassName();

// You can also add the class to the $app variable, to enable usage of the class in all routes.
$app->myclass = new MyClassName();
```
**Namespaces**
```php
<?php

$app = new cloudlib\Cloudlib(__DIR__, '/', array('bootstrap' => false));

// To register a namespace you pass an array of `'namespace' => 'path/to/namespace'` pairs.
$app->loader->registerNamespaces(array(
    'mynamespace' => 'path/to/mynamespace'
));

// Important! After you have set 'bootstrap' to false you have
// to call the bootstrap before adding routes or calling classes
$app->bootstrap();

// The new namespace is now registered!
```

## Configuration

TODO

## Errors

Defining errors (404, 500 etc) is just like adding new routes. You assign a status code (ex 404) to a response function.
```php
<?php

// Custom 404 (Page Not Found), an array is passed as the first function argument
// containing two keys, statusCode and statusMessage
$app->error(404, function($error)  use ($app)  
{  
    $app->set('status' => $error['statusCode'])
        ->set('message' => $error['statusMessage']);

    // This would be the View in `path/to/views/errors/404.php`
    return $app->render('errors/404');
});
```
```php
// Example of a 404.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" \\>
    <title><?php echo $message; ?></title>
</head>
<body>
    <h1><?php echo "$status: $message"; ?></h1>
</body>
</html> 
```
```php
<?php

// The same as above could be done for 405 (Method not allowed), or any other status.
// The two main error status codes used by Cloudlib is 404 and 405
// There is one exception, 500 (Internal Server Error) - if defined it will be passed
// an Exception object instead of an array of status/message.

// This is like working with regular exceptions together with a View
$app->error(500, function($e) use ($app)  
{  
    $app->set('message', $e->getMessage())
        ->set('line', $e->getLine());

    return $app->render('errors/500');
});
```

Sometimes you would want to return an error page in a route.
```php
<?php

$app->get('/', function() use ($app)
{  
    if( /* Condition */ )
    {
        // If you have defined an error page for 404 it will be rendered.
        return $app->errorPage(404);
    }
});
```

## MVC

### Models

TODO

### Controllers

TODO

## Helpers

TODO

## Versioning

[Semantic Versioning](http://semver.org/)

## License

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) license.

[flasklink]: http://flask.pocoo.org/
[bottlelink]: http://bottlepy.org/docs/dev/
[laravellink]: http://laravel.com/
[restlink]: http://en.wikipedia.org/wiki/Representational_state_transfer
[mvclink]: http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller
