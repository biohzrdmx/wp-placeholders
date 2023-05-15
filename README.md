# wp-placeholders

Generate placeholder images on your WP instance

## Requirements

 - WordPress 6.2+
 - PHP 8.2+

## Installation

Download and unzip into a subfolder of your `wp-content/plugins` folder.

In your WordPress go to the **Dashboard** and then to **Plugins**, find the **Placeholders** plugin and activate it.

## Usage

Create an `img` tag and in its `src` attribute call the `url` function from the `Placeholders` class:

```php
<img src="<?php Placeholders::url(720, 480, true); ?>" alt="" class="img-responsive">
```

The first parameter is the **width**, the second one is the **height** and the last one controls whether to **echo** the generated URL or not.

Also, you can use a shortcode:

```
[placeholder width="1024" height="768"]
```

And you may also specify a custom class:

```
[placeholder width="1024" height="768" class="img-fluid"]
```

## Licensing

MIT licensed

Includes the _Roboto Regular_ font, which is covered by the Apache License 2.0, you can [find more details here](https://github.com/google/roboto/).

## Author

Author: biohzrdmx [<github.com/biohzrdmx>](https://github.com/biohzrdmx)