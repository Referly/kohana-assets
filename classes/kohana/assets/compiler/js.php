<?php

class Kohana_Assets_Compiler_Js extends Kohana_Assets_Compiler {

  public function __construct()
  {
    parent::__construct();

    $this->vendor('jsminplus');
  }

  public function compile($js)
  {
    // Strips the end semi-colon, which can break concatenated assets; should
    // try to find a better solution for this.
    return JsMinPlus::minify($js).';';
  }

  public function dependencies($js)
  {
    return NULL;
  }

}

?>
