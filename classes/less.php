<?php

/**
 * Less PHP compiler, modified to make specifying import paths a bit easier.
 *
 * @package  Kohana/Assets
 * @author   Alex Little
 */
class Less extends lessc {

  // Imported files.
  public $imports = array();

  // Don't actually import files, just look for them.
  public $importsCheck = FALSE;

  public $importRelativeDir = '';
  public $importDirs = array();

  public function findImport($url)
  {
    if ($url)
    {
      if (substr($url, 0, 1) !== '/')
      {
        $url = $this->importRelativeDir.'/'.$url;
      }

      foreach ((array) $this->importDirs as $dir)
      {
        $full = $dir.'/'.$url;

        if ($this->fileExists($file = $full.'.less') || $this->fileExists($file = $full))
        {
          $this->imports[] = $file;

          return $this->importsCheck ? NULL : $file;
        }
      }
    }

    return NULL;
  }

  public function parse($str = null, $initial_variables = null)
  {
    $this->imported = array();

    return parent::parse($str, $initial_variables);
  }

}

?>
