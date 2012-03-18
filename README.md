# Cloudlib

Cloudlib is a flexible PHP framework for web development.
Inspired by frameworks like [Flask][flasklink] / [Bottle][bottlelink] (Python) and [Laravel][laravellink] (PHP).

Cloudlib follows the PSR-0 standard so you can use all the libraries you want! Cloudlib also lets you define your own directory structure, more on this in the customization section.

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
Example of a `.htaccess` file.

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

`NOTE` the request method `HEAD` is supported but is the same as `GET` except that it only outputs headers.

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

// Define multiple request methods with route(), pass an array of methods as the second argument.
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
Example View `home.php`

```php
<?php

echo '<p>This would be the only contents of the view!</p>';

```

Example Layout `main.php`

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Example</title>
</head>
<body>
<?php
    // Important! View contents are loaded via a variable called $body
    echo $body;
?>
</body>
</html>
```

`NOTE` Layouts are optional, skip or pass `null` to the second parameter of `render()`.
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

Example View `home.php`

```php
<?php

// Outputs: Hello World!
echo "$hello $world";
```

The `set()` method is not required to define View variables, passing an `array` as the third parameter of `render()` will do the same (using both methods of defining View variables works).

```php
<?php

$app->get('/hello/:world', function($world) use ($app)
{
    $array = array('foo' => 'bar');
    $array['hello'] = $world;
    $array['apples'] = 'oranges';

    $app->set('name', 'joe');

    // $array will be merged with your other variables (also notice that we are not using a Layout).
    return $app->render('home', null, $array);
});
```

Example View `home.php`

```php
<?php
// This could be an example of http://www.domain.com/hello/world

// Outputs: bar, world, oranges, joe
echo "$foo, $hello, $apples, $name";
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
#### Directory paths
You can define your own paths or alter the default paths.

**Pathname**|**Default**|**Description**
--------|-------|-----------
`controllers`|/application/controllers/|The path to your Controllers
`models`|/application/models/|The path to your Models
`view`|/application/views/|The path to your Views
`layouts`|/application/views/layouts/|The path to your Layouts
`logs`|/application/logs/error_php.log|The path to the PHP error log
`config`|/application/config.php|The path to your configuration file
`uploader`|/public/|The path for uploaded files used by the Uploader class
`image`|/public/img/|The path for uploaded images used by the Image class
`css`|/(baseUri)/public/css/|Relative path to your CSS files
`js`|/(baseUri)/public/js/|Relative path to your JavaScript files
`img`|/(baseUri)/public/img/|Relative path to your Image files

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
#### Default class autoloader
You are able, as with many other, to register Namespaces and Aliases (for lazy loading without namespacing class names).

**Aliases**

```php
<?php

$app = new cloudlib\Cloudlib(__DIR__, '/', array('bootstrap' => false));

// To register an aliases you pass an array of 'ClassName' => 'namespace\\ClassName' pairs
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

// To register a namespace you pass an array of 'namespace' => 'path/to/namespace' pairs.
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
#### Escaping
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
    <meta charset="utf-8">
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

The `Request` helper provides simple methods for getting/validating $_SERVER variables. This helper is mainly used in routes.

```php
<?php

$app->get('/', function() use ($app)
{
    // Get a server variable
    $app->request->server('variablename'); // ex $app->request->server('REQUEST_METHOD');

    // Following methods will return true or false depending on the current request method,
    $app->request->isGet();
    $app->request->isPost();
    $app->request->isPut();
    $app->request->isDelete();
    $app->request->isHead();

    // If you just want the request method, call method()
    $app->request->method();

    // Check if it was an AJAX request. 
    $app->request->isAjax();

    // Check if we are using https.
    $app->request->isSecure();

    // Get the currently used protocol, if none is set return HTTP/1.1
    $app->request->protocol();

    return $app->render('someview');
});
```

### Session

The `Session` helper provides simple methods for session usage. A session will always start by default and set a session token if there is none.

```php
<?php

// Set a session name
Session::name('name');
// Get the session name
Session::name();

// Set a session id
Session::id('id');
// Get the session id
Session::id();

// Set a session variable
Session::set('variablename');

// Get a session variable
Session::get('variablename');

// Unset a session variable
Session::del('variablename');

// Check if a session variable has been set
Session::has('variablename');

// Get the session token (the token can be used, as an example, to confirm secure form usage)
Session::token();

// Compare a input token with the session token
Session::compareToken('input token'); // will return a boolean value

// Create a new token
Session::generateToken();

// Refresh an Session (for example, if a user logs in you might want to get a new session token)
Sesssion::refresh();

// Write session data and end session
Session::close();

// Destroy a session (for exmaple if someone logs out)
Session::destroy();

// Start a session
Session::start();

```

### Logger

The `Logger` helper provides simple methods for logging messages (debug/info/warning/error).

```php
<?php

// This creates a new log object which would write to the chosen file at shutdown.
$logger = new Logger('path/to/file.log');

// Debug message
$logger->debug('my message');

// Info message
$logger->info('my message');

// Warning message
$logger->warning('my message');

// Error message
$logger->error('my message');

// Arrays also works.
$array = array('first message', 'second message', 'third message');
$logger->debug($array);

// You are also able to use the log() function, but it takes a second parameter to declare the severity of the message
// Debug(0), Info(2), Warning(3), Error(4).
$logger->log('debugging!', 0);

// These message will be written to the file at shutdown.
// If you want to do it manually follow this example.

$logger = new Logger('path/to/file.log', false); // Dont use register_shutdown_function()
// Write some message.
$logger->debug('debuggin!');

// This would write all the messages to the file.
$logger->write();

// If you later on want to register your logger to write at shutdown you can call register().
$logger->register(); // Will write messages to file at shutdown.

```

### Form

TODO

### Html

The `Html` helper provides simple methods for common html tags.

```php
<?php

// a() (method for creating anchor, <a>, tags) takes three arguments, the path, the content and attributes.
// The path will be relative to your project (most often that is '/').
// The content is what will be inside the anchor tags.
// The attributes can be any attribute you want and is defined by an array, ex array('class' => 'myclass').

// Outputs: <a href="/view/this/page" id="anchorID">hey this is a link!</a>
echo Html::a('/view/this/page', 'hey this is a link!', array('id' => 'anchorID'));

// If you want to have a link outside of your application you need to specify the attribute "relative" to false.
// Outputs: <a href="http://www.google.com">google it!</a>
echo Html::a('http://www.google.com', 'google it!', array('relative' => false));


// img() (method for creating img, <img>, tags) takes two arguments, the path and attributes.
// The path is the relative path to your img directory (default: /(base uri)/public/img/, unless specified).
// The attributes can be any attribute you want and is defined by an array, ex array('class' => 'myclass').

// Outputs: <img src="/public/img/myimage.jpg" class="imgclass" />
echo Html::img('myimage.jpg', array('class' => 'imgclass'));

// If you want to create an image from outside of your application follow the same steps as for the anchor method a().


// Creating links for your JavaScript and CSS files is done with the methods css() and js().
// Both take one argument, which can be a string or an array of multiple files. The path to the files
// is, by default, /(base uri)/public/css/ & /(base uri)/public/js/, this can be changed - read the Customization section.

// Outputs: <link rel="stylesheet" href="/public/css/mystyle.css" />
echo Html::css('mystyle');

// Same can be done with an array
echo Html::css(array('mystyle', 'navstyle'));

// The js() method works the same.
// Outputs: <script src="/public/js/jquery.js"></script>
echo Html::js('jquery');


// To create script/style blocks use the methods script() and style().
echo Html::style('html { background: #eeeeee; }');
echo Html::script('alert("hello");');

// And last, and most likely not most useful, br()!
echo Html::br(100); // 100 <br />'s!
```

### Uploader

The `Uploader` helper provides simple methods for uploading files.

```php
<?php


```

### Image

The `Image` helper provides simple methods for image manipulation.

```php
<?php

// Create a new image object.
$image = new Image();

// Load a file, relative to the defined Image directory (see Customization section), or absolute if you specify a second parameter as false, ->load('file', false);
$image->load('path/to/image.jpg');

// Resize the image to a certain width (px).
$image->resizeToWidth(100);

// Resize the image to a certain height (px).
$image->resizeToHeight(100);

// Scale the image by a certain percentage.
$image->scale(20); // 20%

// Crop the image from the center.
$image->cropCenter(100, 100); // 100px X 100px

// Set the compression level for jpg files.
$image->setCompression(100); // Default: 75

// Save a file, relative to the defined Image directory (see Customization section), or absolute if you specify a second parameter as false, ->save('file', false);
$image->save('path/to/image.jpg');


// Instead of manually using load() and setCompression() you can specify these in the Image constructor.
$image = new Image('path/to/image.jpg', 100);
// resizing/scaling
$image->save('path/to/image.jpg');

// If something would go wrong call the getError() method.
$error = $image->getError();

```

### Benchmark

The `Benchmark` helper provides simple methods of benchmarking your application.

```php
<?php

// Define a start time.
Benchmark::start('start_time');

// Echo the time that has passed since you defined your start time.
echo Benchmark::time('start_time');

// Doing the same with defined variables/constants.
define('start_time', microtime(true));
echo Benchmark::compare(start_time);

// Note: both time() and compare() takes a second parameter, the number of decimals (default: 5).

// You can use the static method calls as the parameter for time().
Benchmark::start('my_time');

// Echos the time rounded to 6 decimals.
echo Benchmark::my_time(6);


// Get memory usage or peak usage.

// Get the memory usage in megabytes rounded to 5 decimals.
echo Benchmark::memory(5);

// Same as memory() but with peak usage.
echo Benchmark::peak(5);

```

### Hash

The `Hash` helper currently provides two methods, `create()` and `compare()`.

```php
<?php

// The creation of a new hash requires the config item 'app.secret' to be set.
// create() takes three parameters, the password, the salt and number of rounds (for blowfish, default: 8).

// The Config item 'app.secret' has a value of 'MySuperSecretSalt' in this example
// Outputs: YThlMWZiN2M0MDA0ZGE0M.rgcNiFJqhocFa.WQjf0gUpYUCvvw3Eq
echo Hash::create('mypassword', 'mysalt');

// compare() takes four parameters, the hash, the password, the salt and number of rounds (for blowfish, defalt: 8).

// Output: bool(true)
echo var_dump(Hash::compare('YThlMWZiN2M0MDA0ZGE0M.rgcNiFJqhocFa.WQjf0gUpYUCvvw3Eq', 'mypassword', 'mysalt');

```

### String

The `String` helper currently provides two methods, `repeat()` and `trim()`.

```php
<?php

// repeat() takes three parameters, the string, number of times (default: 2) and the separator (default: null).

// Outputs: Hello, Hello, Hello, Hello
echo String::repeat('Hello', 4, ', ');

// trim() takes four parameters, the string, max width, start position (default: 0) and the marker (default: '...').

// Outputs: Hello World...
echo String::trim('Hellow World, Foo Bar', 14, 0, '...');

```

### Number

The `Number` helper currently provides conversion from (to MBs) or to bytes (from KB, MB, GB and TB).

```php
<?php

// Outputs: 7340032
echo Number::toBytes('7MB');

// Outputs: 13.0464MB, the second parameter is the number of decimals (defaults to 3).
echo Number::fromBytes(13680100, 4);

// The shorthand function byte() will do the same as above.

// Outputs: 7340032
echo Number::byte('7MB');

// Outputs: 13.0464MB
echo Number::byte(13680100, 4);

```

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
