<?php

/**
 * Compilers and other helper functions.
 *
 * @package  Kohana/Assets
 * @author   Alex Little
 */
class Kohana_Assets {

  public static $config;

  public static function compiler($type)
  {
    $class = 'Assets_Compiler_'.$type;

    return new $class();
  }

  /**
   * Find the source files for a target asset.
   *
   * @param   string  Target asset (e.g. css/style.css)
   *
   * @return  array   NULL or array(include path, source file(s))
   */
  public static function find_sources($target)
  {
    $target = pathinfo(substr($target, strlen(self::target_dir())));

    if ( ! isset($target['extension']))
    {
      $target['extension'] = '';
    }

    $target += array(
      // Path without the extension
      'pathname' => "{$target['dirname']}/{$target['filename']}",

      // Target type
      'type' => self::get_type($target['extension'])
    );

    $source = array(
      // Possible extension(s)
      'extension' => $target['extension'],

      // Possible type(s)
      'type' => (array) Arr::get(self::$config->target_types, $target['type']),
    );

    $concatable = FALSE;

    if ($source['type'])
    {
      // Target could consist of a directory of source files
      $concatable = in_array($target['pathname'], self::$config->concatable, TRUE);

      // It's a known type, so there is a compilation step possibly involving
      // multiple sources of multiple different types
      $source['extension'] = self::get_type_ext($source['type']);
    }

    foreach (self::include_paths() as $include_path)
    {
      // Path to test
      $path = $include_path.$target['pathname'];

      if ($concatable && is_dir($path))
      {
        // Multiple sources
        return self::ls($path, $source['extension']);
      }

      foreach ((array) $source['extension'] as $ext)
      {
        if ($ext && $ext{0} !== '.')
        {
          $ext = ".$ext";
        }

        if (file_exists($file = $path.$ext))
        {
          // Single source
          return $file;
        }
      }
    }

    return NULL;
  }

  /**
   * Determine the asset type given its file extension.
   */
  public static function get_type($ext)
  {
    if ($ext && $ext{0} !== '.')
    {
      $ext = ".{$ext}";
    }

    foreach (self::$config->types as $type => $extensions)
    {
      if (in_array($ext, $extensions, TRUE))
      {
        return $type;
      }
    }

    return NULL;
  }

  /**
   * Get the file extension(s) for the given type(s).
   */
  public static function get_type_ext($types)
  {
    $ext = array();

    foreach ((array) $types as $type)
    {
      $ext = array_merge($ext, Arr::get(self::$config->types, $type, array()));
    }

    return $ext;
  }

  /**
   */
  public static function include_paths($path = '')
  {
    $paths = array();

    foreach (Kohana::include_paths() as $include_path)
    {
      $paths[] = $include_path.self::source_dir().$path;
    }

    return $paths;
  }

  /**
   * Check for modifications (if enabled) and set asset route.
   */
  public static function init()
  {
    self::$config = Kohana::$config->load('assets');

    if (self::$config->watch)
    {
      foreach (self::ls(self::target_dir(), NULL, TRUE) as $asset)
      {
        // Delete assets whose source files have changed (they'll be recompiled
        // the next time they are requested).
        self::modified($asset) && unlink($asset);
      }
    }

    $dir = substr(self::target_dir(), strlen(DOCROOT));

    // Set route.
    Route::set('assets', $dir.'<target>', array('target' => '.+'))
      ->defaults(array(
          'controller' => 'assets',
          'action'     => 'serve'
        ));
  }

  /**
   * List files in a directory. Optionally filter for file extensions and 
   * recurse into sub-directories.
   *
   * @param  string
   * @param  array
   * @param  boolean
   *
   * @return  array  List of files
   */
  public static function ls($dir, $extensions = NULL, $recurse = FALSE)
  {
    $files = array();

    foreach (new DirectoryIterator($dir) as $file)
    {
      if ($file->isFile())
      {
        $ext = '.'.pathinfo($file->getFilename(), PATHINFO_EXTENSION);

        if ($extensions === NULL || in_array($ext, (array) $extensions, TRUE))
        {
          $files[] = $file->getPathname();
        }
      }
      else if ($file->isDir() && ! $file->isDot() && $recurse)
      {
        $files = array_merge($files, self::ls($file->getPathname(), $extensions, TRUE));
      }
    }

    return $files;
  }

  /**
   * Check whether the source files for an asset have been modified since the
   * last time they were compiled.
   *
   * @param  string
   *
   * @return  boolean
   */
  public static function modified($target)
  {
    if (is_file($target))
    {
      $target_modified = filemtime($target);

      foreach ((array) self::find_sources($target) as $source)
      {
        $dependencies = array($source);

        // Determine type
        $type = self::get_type(pathinfo($source, PATHINFO_EXTENSION));

        if ($type)
        {
          $contents = file_get_contents($source);

          $compiler = Assets::compiler($type);
          $dependencies = array_merge($dependencies, (array) $compiler->dependencies($contents));
        }

        foreach ($dependencies as $dependency)
        {
          if (filemtime($dependency) > $target_modified)
          {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  /**
   */
  public static function source_dir()
  {
    return 'assets/';
  }

  /**
   */
  public static function target_dir()
  {
    return DOCROOT.self::source_dir();
  }

}

?>
