# {{handlebars}} for Dispatch

An extension for the [Dispatch] PHP micro-framework that provides support for [Handlebars] templates.


## Requires: ##

```javascript
{
  "require": {
    "php": ">= 5.4.0",
    "dispatch/dispatch": ">= 2.6.2",
    "xamin/handlebars.php": "dev-master"
  }
}
```


## Installation ##

This repo assumes you know how to install dependencies via [Composer]

### composer.json: ###
```javascript
{
  "require": {
    "php": ">= 5.4.0",
    "dispatch/dispatch": ">= 2.6.2",
    "xamin/handlebars.php": "dev-master",
    "gigglebox/dispatch-handlebars": "dev-master"
  }
}
```

Open a new terminal to your working directory and `composer install` or `composer update`.


## Configuration ##

### your-main-app-file.php
```php
  // main layout template file; must contain `{{{content}}}` in <body>
  config('handlebars.layout') = 'layout';

  // set location of .handlbars templates (views and partials)
  config('handlebars.views') = 'path/to/tempaltes';

  // set character encoding for template files; defaults to `UTF-8`
  config('handlebars.charset') = 'utf-8';

  // prefix string to determine partial files, defaults to `_` (underscore)
  config('handlebars.partial_prefix') = '_';

  // [associative array](http://www.php.net/manual/en/language.types.array.php) of tagname and function names
  config('handlebars.helpers') = array('tagName' => 'callback_function');

  // path where compiled templates will be cached (this feature does not work yet)
  config('handlebars.cache') = 'path/to/cached/tempaltes';
```


>NOTE: If you do not define `handlebars.layout` and `handlebars.views`, handlebars will use the default `dispatch.layout` and `dispatch.views` values instead.


## Using Handlebars with Dispatch ##

### layout.handlebars: ###
```handlebars
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{#capitalize page.title }}</title>
</head>
<body>
  {{{ content }}}
</body>
</html>
```

### homepage.handlebars: ###
```handlebars
  <!-- homepage.handlebars -->
  <h1>Hello there, {{ user.notLoggedIn.name }}!</h1>
```

In our example `index.php` file, we'll use the `handlebars()` function to render our template within our route:

### index.php ###
```php
<?php

  require "vendor/autoload.php";
  require "app/functions.php";

  //
  // INIT MODEL
  //
  $model = compile_model();


  //
  // CONFIGURE DISPATCH
  //
  config(array(
    'dispatch.url'              =>  'http://localhost/appname'
  , 'handlebars.views'          =>  'app/templates'
  , 'handlebars.layout'         =>  'layout'
  , 'handlebars.partial_prefix' =>  '_'

  , 'handlebars.helpers' => [
        'capitalize' => 'handlebars_capitalize'
      , 'upper'      => 'handlebars_upper'
      , 'lower'      => 'handlebars_lower'
      ]
  ));


  //
  // EXTEND HANDLEBARS
  //
  function handlebars_capitalize($template, $context, $args, $source) {
    return ucwords($context->get($args));
  }

  function handlebars_upper($template, $context, $args, $source) {
    return strtoupper($context->get($args));
  }

  function handlebars_lower($template, $context, $args, $source) {
    return strtolower($context->get($args));
  }


  //
  // DEFINE ROUTES
  //
  on('GET', '/', function () {
    global $model;
    handlebars('homepage', $model);
  });


  //
  // RUN THE APPLICATION
  //
  dispatch();
?>
```

The `handlebars()` function accepts three arguments:

```php
handlebars();
```

The resulting HTML from our demo above renders something like this:

```html
<!doctype html>
<html>
<head><title>Handlebars Hello World App</title></head>
<body>
  <!-- homepage.handlebars -->
  <h1>Hello there, stranger!</h1>
</body>
</html>
```


## Extending Handlebars with Helpers ##

To define helpers for Handlebars, you need to define the functions you wish to use and pass them into the `handlebars.helpers` array as defined above.

### example: ###
```php
  config('handlebars.helpers') = array(
      'capitalize'  => 'handlebars_capitalize'
    , 'upper'       => 'handlebars_upper'
    , 'lower'       => 'handlebars_lower'
    );

  // extend handlebars

  function handlebars_capitalize($template, $context, $args, $source) {
    return ucwords($context->get($args));
  }

  function handlebars_upper($template, $context, $args, $source) {
    return strtoupper($context->get($args));
  }

  function handlebars_lower($template, $context, $args, $source) {
    return strtolower($context->get($args));
  }
```

In `handlebars.helpers`, the `key` is the tag name that gets used in your handlebars templates, and the `value` is the name of the callback function we want to use when that helper is defined.

Read more about defining helpers at [mardix/Handlebars](https://github.com/mardix/Handlebars#writing-your-own-helpers)




## Credits ##

This package was written by [Brandtley McMinn] and is largely based on the [Dispatch-Mustache] package written by [Jesus A. Domingo] as an add-on for the
[Dispatch] PHP micro-framework.

It depends on the [Handlebars PHP] library by [fzerorubigd] and [Behrooz Shabani] aka [everplays].

[Brandtley McMinn]: https://github.com/giggleboxstudios/
[Jesus A. Domingo]: http://noodlehaus.github.io/
[Dispatch]: http://noodlehaus.github.io/dispatch/
[Handlebars]: http://handlebarsjs.com/
[Handlebars PHP]: https://github.com/XaminProject/handlebars.php
[Dispatch-Mustache]: https://github.com/noodlehaus/dispatch-mustache/
[fzerorubigd]: https://github.com/fzerorubigd/
[Behrooz Shabani]: https://github.com/everplays/
[everplays]: https://github.com/everplays/
[Composer]: https://getcomposer.org/

## LICENSE
MIT <http://brandtleymcminn.mit-license.org/>
