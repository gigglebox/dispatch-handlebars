# {{handlebars}} for Dispatch

This package lets you use Handlebars templates in Dispatch via the
function `handlebars($template, $locals = array(), $layout = null)`.



## Requirements
Below's the `require` section of this package's `composer.json` file.

```javascript
{
  "require": {
    "php": ">= 5.4.0",
    "dispatch/dispatch": ">= 2.6.2",
    "handlebars/handlebars": ">= 2.4"
  }
}
```



## Code
Get the code at GitHub: <https://github.com/giggleboxstudios/dispatch-handlebars>.



## Installation
To install, add the following line to the `require` section of your
`composer.json` file.

```javascript
"giggleboxstudios/dispatch-handlebars": "dev-master"
```

Once added, just do a `composer install` or `composer update`.



## Configuration
This package relies on the following `config()` entries:

* `handlebars.layout` - layout file to use (must contain `{{{content}}}` for body)
* `handlebars.views` - where the handlebars files are located (views and partials)
* `handlebars.charset` - character set for encoding, defaults to `UTF-8`
* `handlebars.partial_prefix` - prefix string to determine partial files, defaults to `_` (underscore)

If you want template caching, set the following `config()` entry:

* `handlebars.cache` - path where compiled templates will be cached (this feature does not work yet)

For `handlebars.layout` and `handlebars.views`, if these is not defined, values are
pulled from `dispatch.layout` and `dispatch.views` instead.



## How To Use It
`handlebars($template, $locals = array(), $layout = null)` lets you echo out the
contents of a `$template` into a `$layout` file. Use this function instead of
dispatch's `render()` function.


Here's a simple example of how it should be used.

```handlebars
<!-- _template-header.handlebars -->
<!doctype html>
<html>
<head><title>{{title}}</title></head>
<body>
```


```handlebars
<!-- _template-footer.handlebars -->
</body>
</html>
```


```handlebars
<!-- layout file -->
{{>template-header}} <!-- auto prefixes the partial's name with an underscore -->

<!-- this replaces dispatch's content() call -->
{{{content}}}

{{>template-footer}}
```


Here is the route view which will be plugged into the layout file.

```handlebars
<!-- index file -->
<h1>Hello there, {{name}}!</h1>
```


Here's a sample route that uses these templates.

```php
<?php
// setup handlebars
config(array(
  'handlebars.cache'          => 'path/to/cache/directory',
  'handlebars.views'          => 'path/to/views',
  'handlebars.layout'         => 'layout'
  'handlebars.partial_prefix' => '_'
));

// render using a layout
on('GET', '/index', function () {
  handlebars('index', array(
    'title' => 'Handlebars Hello World App',
    'name'  => 'stranger'
  ));
});

// or just echo a partial's content
on('GET', '/index-partial', function () {
  echo handlebars_template('index', array('name' => 'stranger'));
});

dispatch();
?>
```

This will output the following markup.

```html
<!doctype html>
<html>
<head><title>Handlebars Hello World App</title></head>
<body>
<!-- this replaces dispatch's content() call -->
<!-- index file -->
<h1>Hello there, stranger!</h1>
</body>
</html>
```

## Credits

This package was written by [Brandtley McMinn] and is largely based on the [Dispatch-Mustache] package written by [Jesus A. Domingo] as an add-on for the
[Dispatch] PHP micro-framework.

It depends on the [Handlebars PHP] library by [fzerorubigd] and [Behrooz Shabani] aka [everplays].

[Brandtley McMinn]: https://github.com/giggleboxstudios/
[Jesus A. Domingo]: http://noodlehaus.github.io/
[Dispatch]: http://noodlehaus.github.io/dispatch/
[Handlebars PHP]: https://github.com/XaminProject/handlebars.php
[Dispatch-Mustache]: https://github.com/noodlehaus/dispatch-mustache/
[fzerorubigd]: https://github.com/fzerorubigd/
[Behrooz Shabani]: https://github.com/everplays/
[everplays]: https://github.com/everplays/

## LICENSE
MIT <http://brandtleymcminn.mit-license.org/>
