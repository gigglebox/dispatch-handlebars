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
 * Renders a handlebars template specified by $path, using scope variables defined inside $locals.
 * This function uses config('dispatch.views') for the location of the templates and partials, unless overridden by config('handlebars.views').
 * Settings for 'handlebars.charset' and 'handlebars.layout' are also pulled from config(), if present.
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

    $engine->addHelper('capitalize', function($template, $context, $args, $source) {
        return ucwords($context->get($args));
      });

    $engine->addHelper('upper', function($template, $context, $args, $source) {
        return strtoupper($context->get($args));
      });

    $engine->addHelper('lower', function($template, $context, $args, $source) {
        return strtolower($context->get($args));
      });

    $engine->addHelper('url', function($template, $context, $args, $source) {
        return config('dispatch.url') . $context->get($args);
      });

  }

  // render partial using $locals
  if (config('handlebars.minify')) {
    return handlebars_minify($engine->render($path, $locals));
  }

  return $engine->render($path, $locals);
}



/**
 * [handlebars_templates description]
 * @param  [type] $rootDirectory [description]
 * @return [type]                [description]
 */
function handlebars_templates() {

  $templates        = array();
  $templatesFolder  = config('handlebars.views') ?: config('dispatch.views');
  $layoutFile       = config('handlebars.layout') ?: config('dispatch.layout');
  $templatesTemp    = glob($templatesFolder . '/*.handlebars');

  foreach ($templatesTemp as $index => $path) {
    $fileName     = preg_replace('/.+\//', '', $path);
    $fileName     = preg_replace('/.handlebars/', '', $fileName);
    $cookieName   = preg_replace('/__/', '_', "handlebar_template_$fileName");
    $partial      = '';

    // if the template exists as a cookie...
    if ($template = cookie($cookieName)) {

    } else {

      $fileContent  = file_get_contents($path);

      // if we're parsing the layout file, ignore it
      if (preg_match('/'. $layoutFile .'/', $fileName)) { continue; }

      // Check if template is a partial
      if (preg_match('/^_/', $fileName)) {
        $partial  = 'data-handlebars-type="partial"';
        $fileName = ltrim ($fileName,'_');
      }

      // Compile the template
      $template = '<script type="text/x-handlebars-template" id="'.$fileName.'" data-handlebars-template="'.$fileName.'" '.$partial.'>'.$fileContent.'</script>';

      // Minify the template if we want to
      if (config('handlebars.minify')) { $template = handlebars_minify($template); }

      // Cache the templates string as a cookie
      cookie($cookieName, handlebars_minify($template));
    }

    // Compile the templates list
    array_push($templates, $template);

  }

  return $templates;
}



/**
 * Minifies the HTML source provided by
 * @author --- http://jesin.tk/how-to-use-php-to-minify-html-output/
 * @param  [type] $buffer [description]
 * @return [type]         [description]
 */
function handlebars_minify($buffer) {

  $buffer = preg_replace('/\s{2,}+/', '', $buffer);
  $buffer = preg_replace('/[\r\n\t]/', '', $buffer);

  return $buffer;
}
