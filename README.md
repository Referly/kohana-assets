# kohana-assets

Easy & efficient asset management for Kohana 3.

## Features

  - Out-of-the-box support for CSS ([CssMin](https://code.google.com/p/cssmin/)), 
    LESS ([lessphp](http://leafo.net/lessphp/)), JavaScript ([JsMinPlus](https://code.google.com/p/minify)), 
    and CoffeeScript ([CoffeeScript PHP](http://github.com/alxlit/coffeescript-php/)).
  - Easy to use and extend
  - Efficient; assets are compiled once and served directly thereafter
  - Watch mode (checks for source modifications)
  - Makes full use of Kohana's cascading file system when sourcing assets
  - Multiple source files into a single asset

## Get it

Clone the repository into `MODPATH/assets/`, enable the module in your
`bootstrap.php`, and have a look at the user guide.

## See also

There are plenty of asset managers for Kohana. Depending on your needs you might
find [one of these](https://github.com/search?type=Repositories&language=PHP&q=kohana-assets)
preferable.
