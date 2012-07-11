# Extending

In the (untested) example below we add an optimizer for our PNG images. This
case is a bit different from the rest since a). we aren't dealing with text,
and b). we're using an external program.

**Step 1**: Specify the PNG asset type in `APPPATH/config/assets.php`:

    'types' => array(
      'png' => array('.png')
    ),

    'target_types' => array(
        'png' => array('png')
    )

**Step 2**: Create the compiler in `APPPATH/classes/assets/compiler/png.php`.

    class Assets_Compiler_Png extends Assets_Compiler {

      // This method is provided for reusability. If you don't need it, don't
      // implement it.
      function compile($png) {}

      function compile_asset(array $sources, $target)
      {
        exec("optipng {$sources[0]} -out $target");
      }

    }

**Step 3**: Clear `DOCROOT/assets/` to make sure any PNGs are recompiled.


