<?php

/**
 * Main controller.
 *
 * @package  Kohana/Assets
 * @author   Alex Little
 */
class Controller_Assets extends Controller {

  public function action_serve()
  {
    $target = Assets::target_dir().$this->request->param('target');

    // Look for source files.
    if ($sources = Assets::find_sources($target))
    {
      if (is_dir($dir = dirname($target)) || mkdir($dir, 0777, TRUE))
      {
        $sources_by_type = array();

        foreach ((array) $sources as $source)
        {
          // Determine type
          $type = Assets::get_type(pathinfo($source, PATHINFO_EXTENSION));

          if ( ! $type)
          {
            // Simple, single-source asset with no compilation step. Just link
            // to it and we're done.
            symlink($source, $target);

            break;
          }
          else
          {
            // Some monkeying around is necessary to support concatable assets,
            // since they can come from multiple different source types.
            $sources_by_type[$type][] = $source;
          }
        }

        if ($sources_by_type)
        {
          file_put_contents($target, '');

          foreach ((array) $sources_by_type as $type => $sources)
          {
            // Compile asset.
            Assets::compiler($type)->compile_asset($sources, $target);
          }
        }

        if (is_file($target) || is_link($target))
        {
          // Success!
          $this->request->redirect($this->request->uri());
        }
      }
    }

    throw new HTTP_Exception_404();
  }

}

?>
