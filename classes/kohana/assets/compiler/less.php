<?php

class Kohana_Assets_Compiler_Less extends Kohana_Assets_Compiler {

  public function __construct()
  {
    parent::__construct();

    $this->vendor('lessphp/lessc.inc');

    $this->less = new Less();
    $this->less->importDisabled = FALSE;
  }

  public function compile($less)
  {
    return Assets::compiler('css')->compile($this->less->parse($less));
  }

  public function compile_asset(array $sources, $target)
  {
    $result = '';

    $less = $this->less;

    foreach ($sources as $source)
    {
      $less->importDirs = Assets::include_paths();

      // Determine the base include path that the source resides under.
      $include_path = '';

      foreach ($less->importDirs as $path)
      {
        if (strpos($source, $path) === 0)
        {
          $include_path = $path;
          break;
        }
      }

      // Set relative path for imports.
      $less->importRelativeDir = substr(dirname($source), strlen($include_path));

      // Compile.
      $result.= $this->compile(file_get_contents($source));
    }

    file_put_contents($target, $result, FILE_APPEND);
  }

  public function dependencies($less)
  {
    $this->less->importsCheck = TRUE;
    $this->less->parse($less);

    // Done checking
    $this->less->importsCheck = FALSE;

    return $this->less->imports;
  }

}

?>
