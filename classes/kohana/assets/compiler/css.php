<?php

class Kohana_Assets_Compiler_Css extends Kohana_Assets_Compiler {

  public function __construct()
  {
    parent::__construct();

    $this->vendor('cssmin');
  }

  public function compile($css)
  {
    return CssMin::minify($css);
  }

  public function dependencies($css)
  {
    return NULL;
  }

}

?>
