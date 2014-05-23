<?php

//
// Big shoutout to @noodlehaus for his initial build of https://github.com/noodlehaus/dispatch-mustache
//
// I simply substituted the `mustache` portions for `handlebars` since I needed
// a slightly more robust templating system and I really like the mustache like
// syntax offered by Handlebars, seemed like a natural fit :) Plus I get to give
// back to an amazing microframework that I've grown to love in my workflow!
//


/**
 * Renders and echoes a handlebars template using the given locals and
 * layout. Most of the work is delegated to handlebars_template()
 *
 * @param string $path name of the .handlebars file to render
 * @param array $locals scope variables to load inside the template
 * @param string $layout which layout file to use, false for no layout
 *
 * @return void
 */
function handlebars($path, $locals = array(), $layout = null) {

  // load the inner partial
  $content = handlebars_template($path, $locals);

  // use a layout (fall back to sensible defaults)
  if ($layout !== false) {

    if ($layout == null) {
      $layout = config('handlebars.layout') ?: config('dispatch.layout');
      $layout = ($layout == null) ? 'layout' : $layout;
    }

    // render the layout while plugging in $content
    echo handlebars_template($layout, array('content' => $content) + $locals);

  } else {
    // if we just want the partial rendered without the layout
    echo $content;
  }
}

/**
 * Renders a handlebars template specified by $path, using scope variables
 * defined inside $locals. This function uses config('dispatch.views')
 * for the location of the templates and partials, unless overridden
 * by config('handlebars.views').
 * Settings for 'handlebars.charset' and 'handlebars.layout' are also
 * pulled from config(), if present.
 *
 * @param string $path name of the .handlebars file to render
 * @param array $locals scope variables to load inside the template
 * @param string $layout layout file to use, or flag to not use one
 *
 * @return string rendered code for the template
 */
function handlebars_template($path, $locals = array()) {

  static $engine = null;

  // create the engine once
  if (!$engine) {

    $views_path = config('handlebars.views') ?: config('dispatch.views');

    //
    // Handlebars
    //
    $opts = array(
        'loader'            => new \Handlebars\Loader\FilesystemLoader($views_path)
      , 'partials_loader'   => new \Handlebars\Loader\FilesystemLoader($views_path, array( 'prefix' => config('handlebars.partials_prefix') ?: '_' ))
      , 'charset'           => config('handlebars.charset') ?: 'UTF-8'
      );

    if ($cache_path = config('handlebars.cache')) {
      $opts['cache'] = $cache_path;
    }

    $engine = new Handlebars\Handlebars($opts);


    //
    // Handlebars Helpers
    //
    $helpers = config('handlebars.helpers');

    if ($helpers) {
      foreach ($helpers as $helper => $callback) {
        $engine->addHelper($helper, function($template, $context, $args, $source) use ($callback) {
            return call_user_func($callback, $template, $context, $args, $source);
          });
      }
    }

  }

  // render partial using $locals
  return $engine->render($path, $locals);
}
