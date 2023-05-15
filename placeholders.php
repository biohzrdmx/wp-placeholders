<?php

declare(strict_types = 1);

/**
 * Plugin Name: Placeholders
 * Description: Generate placeholder images on your WP instance
 * Author: biohzrdmx
 * Version: 2.0
 * Plugin URI: http://github.com/biohzrdmx/wp-placeholders
 * Author URI: http://github.com/biohzrdmx/
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('Placeholders') ) {

	class Placeholders {

		/**
		 * Init callback
		 * @return void
		 */
		static function init(): void {
			if ( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) {
				# Hook into do_parse_request
				add_action('do_parse_request', 'Placeholders::routeRequest', 30, 2);
				# Disable canonical redirects
				add_action('routing_matched_vars', 'Placeholders::removeCanonicalRedirect', 30);
			}
			# Add shortcode
			add_shortcode( 'placeholder', 'Placeholders::shortcode' );
		}

		/**
		 * Routing matched vars callback
		 * @return void
		 */
		static function removeCanonicalRedirect(): void {
			remove_action('template_redirect', 'redirect_canonical');
		}

		/**
		 * Get current URL
		 * @return string
		 */
		static function getCurrentUrl(): string {
			$ret = trim(esc_url_raw(add_query_arg([])), '/');
			$home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
			if ($home_path && strpos($ret, $home_path) === 0) {
				$ret = trim(substr($ret, strlen($home_path)), '/');
			}
			return $ret;
		}

		/**
		 * Get placeholder URL
		 * @param  int  $width  Placeholder width
		 * @param  int  $height Placeholder height
		 * @param  bool $echo   Whether to echo or not
		 * @return string
		 */
		static function url(int $width = 320, int $height = 320, bool $echo = false): string {
			$ret = home_url("/placeholder/{$width}x{$height}");
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Filters whether to parse the request.
		 * @param bool  $bool  Whether or not to parse the request. Default true.
		 * @param WP    $wp    Current WordPress environment instance.
		 * @param mixed $extra Extra passed query variables.
		 */
		static function routeRequest(bool $bool, WP $wp, mixed $extra = null): bool {
			# Get current URL
			$url = self::getCurrentUrl();

			# Get the segments
			$segments = explode('?', $url);
			$cur_route = array_shift($segments);
			$parts = explode('/', $cur_route);

			# The placeholder generator
			if ( isset( $parts[0] ) && $parts[0] == 'placeholder' ) {
				$size = isset( $parts[1] ) ? $parts[1] : '320x320';
				$text = isset( $parts[2] ) ? $parts[2] : $size;
				$text = urldecode($text);
				$dimensions = explode('x', $size);
				$width = (int) ($dimensions[0] ?: 320);
				$height = (int) ($dimensions[1] ?: 320);
				header("Content-Type: image/png");
				$image = imagecreatetruecolor($width, $height);
				$color_fg = imagecolorallocate($image, 150, 150, 150);
				$color_bg = imagecolorallocate($image, 204, 204, 204);
				$font_file = dirname(__FILE__) . '/Roboto-Regular.ttf';
				$font_size = $height / 8;
				$bbox = imagettfbbox($font_size, 0, $font_file, $text);
				$x = (int) (($width - $bbox[2]) / 2);
				$y = (int) (($height - $bbox[5]) / 2);
				imagealphablending($image, true);
				imagefill($image, 0, 0, $color_bg);
				imagettftext($image, $font_size, 0, $x, $y, $color_fg, $font_file, $text);
				imagepng($image);
				imagedestroy($image);
				exit;
			}

			return $bool;
		}

		/**
		 * Shortcode callback
		 * @param  array   $atts    Shortcode attributes
		 * @param  string  $content Shortcode content
		 * @param  string  $tag     Shortcode tag (name)
		 * @return string
		 */
		static function shortcode($atts = [], $content = null, $tag = '') {
			extract(shortcode_atts([
				'width' => 320,
				'height' => 320,
				'class' => '',
			], $atts));
			$url = self::url( (int) $width, (int) $height );
			return sprintf('<img src="%s" alt="" class="%s">', $url, $class);
		}
	}

	add_action( 'init', 'Placeholders::init' );

}
