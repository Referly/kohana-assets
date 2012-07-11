<?php

abstract class Kohana_Assets_Compiler {

  /**
   * Constructor (does nothing).
   */
  public function __construct() {}

  /**
   * Compile the contents of some source file.
   *
   * @param  string  The contents of the source file being compiled.
   * @return string  The compiled result.
   */
  public abstract function compile($contents);

  /**
   * Return a list of any dependencies specified in the $contents of the given
   * source file. For example, in LESS these would be any local @imports.
   *
   * @param  string  The contents of the source file to examine for dependencies.
   * @return array   List of dependencies or NULL.
   */
  public abstract function dependencies($contents);

  /**
   * Compile an asset's source files and write out to the destination. It should
   * APPEND if the asset type is concatable.
   *
   * @param  array   The target's source files.
   * @param  string  The target.
   *
   * @return string The result.
   */
  public function compile_asset(array $sources, $target)
  {
    $result = '';

    foreach ($sources as $source)
    {
      $result.= $this->compile(file_get_contents($source));
    }

    file_put_contents($target, $result, FILE_APPEND);
  }

  /**
   * Find and include vendor libraries.
   */
  public function vendor()
  {
    foreach (func_get_args() as $file)
    {
      require_once Kohana::find_file('vendor', $file);
    }
  }

}

?>
