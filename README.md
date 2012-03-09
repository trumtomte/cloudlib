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

#### Ngnix

TODO

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

* `controllers` path to your Controllers.
* `models` path to your Models.
* `views` path to your Views.
* `layouts` path to your Layouts.
* `logs` path to your Logs.
* `config` this is a complete path to the config file (ex `path/to/config.php`).
* `uploader` path to where your files will be uploaded to.
* `image` path to where your image(s) will be loaded/saved.
* `css` relative path to your CSS files.
* `js` relative path to your JavaScript files.
* `img` relative path to your Image files.
* `classes` path to which core classes are loaded **do not alter unless you really have to**.

```php
<?php

// Example of defining directory paths
$app = new cloudlib\Cloudlib(__DIR__, '/', array('bootstrap' => false));

// Define paths with an array
$app->setPaths(array(
    'controllers' => 'path/to/controllers',
    'models' => 'path/to/models',
    'config' => 'path/to/config.php'
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

## Input
Whenever a request is made and data is sent (via $_GET, $_POST etc) it is stored in an array called `input`.

Lets say someone sign in to your website with the form input fields "username" and "password".

```php
<?php

$app->post('/sign_in', function() use ($app)
{
    // This is how you would get the username and password.
    $username = $app->input['username'];
    $password = $app->input['password'];

    // Authentication etc..
});
```

You will most likely also want to escape output to the browser, is is super easy as Cloudlib provides a simple function for this.
The `escape()` method can be passed a string, array (or an array of objects) or an object to be esxaped.

```php
<?php

$app->get('/home', function() use ($app)
{
    $string = '<h1>My String!</h1>';
    
    // Escape the string.
    $app->set('string', $app->escape($string));
    
    // The escape function is recursive, so multidimensional arrays is no problem.
    $array = array
    (
        'foo' => 'bar',
        'apple' => 'orange',
        'array' => array
        (
            'hello' => 'world'
        )
    );

    // Escape the array.
    $app->set('array', $app->escape($array));

    // The escape method will escape the visible (public) class properties
    $object = new Class();

    // Escape the object.
    $app->set('object', $app->escape($object));

    $arrayOfObjects = array
    (
        'first' => new ClassA(),
        'second' => new ClassB()
        // And so on
    );

    // Escape the array of objects.
    $app-set('arrayOfObjects', $app->escape($arrayOfObjects));

    return $app->render('view');
});
```
`$_FILES` and `$_COOKIES` will not be sent to the input array instead they are aquired via the Request object (more on this in the Helpers section).

```php
<?php

$app->get('/', function() use ($app)
{
    // The array $_FILES
    $files = $app->request->files;

    // The array $_COOKIES
    $cookies = $app->request->cookies;
});
```


## Configuration

The configuration file is located in `application/config.php`, unless specified elsewhere as shown in the **Customization** section.

The Config file will (by default) look like this.
`NOTE` these items are required, but you are able to add more if you want.

```php
<?php

// Here we have the config items under the category name 'default' (which is default for every application)
return array
(
    'default' => array(
        // Application

        // Define the Timezone
        'app.timezone'      => 'Europe/Stockholm',
        // Define the Locale
        'app.locale'        => null,
        // Secret for Hasing
        'app.secret'        => 'MySuperSecretSalt',
        // Encoding to be used for databases/string functions etc..
        'app.encoding'      => 'utf8',
        // Display errors?
        'app.errors'        => 1,
        // Log php errors?
        'app.logs'          => 1,

        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'root',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
    )
);
```

You are able to specify as many categories as you want

```php
<?php

return array(
    'default' => array(
        // Config items..
    ),

    'myproject' => array(
        // Config items..
    ),

    'custom' => array(
        // Config items..
    )
);
```

To get config items we use `Config::get()`

```php
<?php

    // Returns: 'Europe/Stockholm'
    Config::get('app.timezone');

    // Returns: 'root'
    Config::get('db.password');

    // Returns an array of all config items prefixed with 'db'
    Config::get('db');

    // To get config items of categories other then 'default' we just pass another argument to the get() method
    // Returns an array of all config items prefixed with 'db' from the category 'myproject'
    Config::get('db', 'myproject');
```

## Errors

Defining errors (404, 500 etc) is just like adding new routes. You assign a status code (ex 404) to a response function.

```php
<?php

// Custom 404 (Page Not Found), an array is passed as the first function argument
// containing two keys, statusCode and statusMessage
$app->error(404, function($error) use ($app)  
{  
    $app->set('status' => $error['statusCode'])
        ->set('message' => $error['statusMessage']);

    // This would be the View in `path/to/views/errors/404.php`
    return $app->render('errors/404');
});
```

`404.php` could then contain:

```php
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

The same as above could be done for 405 (Method not allowed), or any other status.  
The two main error status codes used by Cloudlib is 404 and 405.  
There is one exception though, 500 (Internal Server Error) - if defined it will be passed  
an Exception object instead of an array of status/message.

```php
<?php

// This is like working with regular exceptions together with a View
$app->error(500, function($e) use ($app)  
{  
    $app->set('message', $e->getMessage())
        ->set('line', $e->getLine());

    // This could also be a good place to log your errors,
    // more about the Logger helper class in the Helpers section.

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

An example on a Model. `NOTE` when a Model is created it uses the Database configurations specified in your config file.

```php
<?php

// Model class names should be suffixed with 'Model' and be a subclass of 'Model'
class testModel extends Model
{  
    // Pointless method returing 'World!'
    public function hello()
    {
        return 'World!';
    }
}
```

Accessing the Model is done via the `model()` method.

```php
<?php

$app->get('/', function() use ($app)
{
    // We set the $word variable to 'World!'.
    // The model() method takes the model name as a parameter, without the 'Model' suffix.
    $word = $app->model('test')->hello(); 
});
```

A little more practical example

**The model.**

```php
<?php

// The model
class testModel extends Model
{
    public function getName($name)
    {
        // This would return an object containing variables of the first result from the query.
        // By using PDO we protect ourselves from SQL injection,
        // more about the Database helper class in the Helpers section.
        return $this->database->fetchFirst('SELECT Name FROM table WHERE Name = ?', array($name));
    }
}
```

**The route.**

```php
<?php

$app->get('/view/:name', function($name) use ($app)
{
    // Example for www.domain.com/view/<name>
    // $name would be an object in the View,
    // so $name->Name would contain the result name we got from the query.
    $app->set('name', $app->model('test')->getName($name));
});
```
`NOTE` Models can also be used in Controllers (see **Controllers** section).

### Views

Scroll up!

### Controllers

First lets create a Controller we'll be using for the next examples that connects it to a certain route.

```php
<?php

// Controller names should be suffixed with 'Controller' and be a subclass of 'Controller'
class testController extends Controller
{
    // A simple function that just returns 'Hello World!'
    public function hello()
    {
        return 'Hello World!';
    }

    // A function that sets a parameter to a View variable then returns a rendered View
    public function test($parameter)
    {
        $this->set('param', $parameter);

        // Would render foo.php with the variable $param
        return $this->render('foo');
    }

    // This function would be called if the request method is GET and you did not specify a method name
    public function get()
    {
        // Would only return the View (bar.php)
        return $this->render('bar');
    }

    // Same as above but for the request method POST
    public function post()
    {
        return $this->render('bar');
    }
}
```

Assigning routes to controllers is the same as you've done before but instead of passing a response function you pass an array.

```php
<?php

// This would load testController and call the method hello().
$app->get('/home', array('controller' => 'test', 'method' => 'hello'));

// This would load testController and pass a parameter to the test() method.
$app->get('/view/:page', array('controller' => 'test', 'method' => 'test'));

// This would load testController and call the get() method since no method
// was defined we use the request method as the method name.
$app->get('/', array('controller' => 'test'));

// Same as above but for post
$app->post('/save', array('controller' => 'test'));
```

Using Models with Controllers

**Controller**

```php
<?php

class testController extends Controller
{
    public function test()
    {
        // This would call a Model named testModel (it uses the controller class name).
        $this->set('SQLresult', $this->model->getResult());

        // This would call the Model 'foobarModel'
        $this->set('TestResult', $this->model('foobar')->getResult());

        return $this->render('view');
    }
}
```

**Route**

```php
<?php

// To use a default Model (with the same name as the controller) you set the model name in the array.
$app->get('/', array('controller' => 'test', 'method' => 'test', 'model' => 'test'));
```

`NOTE` when you call Models / assign Models to Controllers it will try to establish a database connection based on the configuration you specified in the config file.

## Helpers

TODO

### Database

TODO

### Request

TODO

### Session

TODO

### Logger

TODO

### Form

TODO

### Html

TODO

### Uploader

TODO

### Image

TODO

### Benchmark

TODO

### Hash

TODO

### String / Number

TODO

## Examples

TODO

## Versioning

Versioned with [Semantic Versioning](http://semver.org/)

## License

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) license.

[flasklink]: http://flask.pocoo.org/
[bottlelink]: http://bottlepy.org/docs/dev/
[laravellink]: http://laravel.com/
[restlink]: http://en.wikipedia.org/wiki/Representational_state_transfer
[mvclink]: http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller
