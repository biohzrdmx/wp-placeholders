# wp-placeholders

Generate placeholder images on your WP instance

## Requirements

 - WordPress 5.x
 - PHP 5.3+

## Installation

Download and unzip into a subfolder of your `wp-content/plugins` folder.

In your WordPress go to the **Dashboard** and then to **Plugins**, find the **Placeholders** plugin and activate it.

## Usage

Create an `img` tag and in its `src` attribute call the `url` function from the `Placeholders` class:

```php
<img src="<?php Placeholders::url(720, 480, true); ?>" alt="" class="img-responsive">
```

The first parameter is the **width**, the second one is the **height** and the last one controls whether to **echo** the generated URL or not.

## License

Released under the **MIT License**.

## Author

biohzrdmx - https://github.com/biohzrdmx - https://biohzrdmx.me