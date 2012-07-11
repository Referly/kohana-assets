<?php

return array
(
  'concatable' => array(),

  'types' => array(
    'coffee' => array('.coffee'),
    'css'    => array('.css'),
    'js'     => array('.js'),
    'less'   => array('.less'),
  ),

  'target_types' => array(
    'css' => array('css', 'less'),
    'js'  => array('js', 'coffee'),
  ),

  'watch' => Kohana::$environment === Kohana::DEVELOPMENT
);

?>
