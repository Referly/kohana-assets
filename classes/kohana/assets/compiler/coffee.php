<?php

class Kohana_Assets_Compiler_Coffee extends Assets_Compiler {

  public function __construct()
  {
    parent::__construct();

    $level = error_reporting();
    error_reporting(0);

    $this->vendor('coffeescript/coffeescript');

    error_reporting($level);
  }

  public function compile($coffee)
  {
    return Assets::compiler('js')->compile(CoffeeScript\compile($coffee));
  }

  public function dependencies($coffee)
  {
    return NULL;
  }

}

?>
