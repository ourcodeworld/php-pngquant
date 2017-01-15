# What is PHP PNGQuant

PHP-PNGQuant is a non-official wrapper for the great [PNGQuant](https://github.com/pornel/pngquant), the command-line utility and a library for lossy compression of PNG images.

# Requirements

You need `pngquant` accesible from the PATH. If the utility isn't available for any reason, you can change the path to the binary using the `setBinaryPath` method:

```php
<?php

use ourcodeworld\PNGQuant\PNGQuant;

$instance = new PNGQuant();

// Change the path to the binary of pngquant, for example in windows would be (with an example path):
$output = $instance->setBinaryPath("C:\\Users\\sdkca\\Desktop\\pngquant.exe")
      ->setImage("image-original.png")
      ->setOutputImage("image-compressed.png")
      // Overwrite output file if exists
      ->overwriteExistingFile()
      ->setQuality(50,80)
      ->execute();

if($output){
    // Normally, pngquant won't
    echo "Oops, pngquant shouldn't generate output, probably an error : ". $output;
}
```

# Installation

```batch
composer require ourcodeworld/php-pngquant
```

# Methods and examples

An example with all the available method


```php


```
