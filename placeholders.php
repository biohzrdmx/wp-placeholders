<?php

	/**
	 * Plugin Name: Placeholders
	 * Description: Generate placeholder images on your WP instance
	 * Author: biohzrdmx
	 * Version: 1.1
	 * Plugin URI: http://github.com/biohzrdmx/
	 * Author URI: http://github.com/biohzrdmx/wp-placeholders
	 */

	if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	if( ! class_exists('Placeholders') ) {

		class Placeholders {

			static function init() {
				if ( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) ) {
					# Hook into do_parse_request
					add_action('do_parse_request', 'Placeholders::routeRequest', 30, 2);
					# Disable canonical redirects
					add_action('routing_matched_vars', 'Placeholders::removeCanonicalRedirect', 30);
				}
			}

			static function removeCanonicalRedirect() {
				remove_action('template_redirect', 'redirect_canonical');
			}

			static function getCurrentUrl() {
				$ret = trim(esc_url_raw(add_query_arg([])), '/');
				$home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
				if ($home_path && strpos($ret, $home_path) === 0) {
					$ret = trim(substr($ret, strlen($home_path)), '/');
				}
				return $ret;
			}

			static function url($width = 320, $height = 320, $echo = false) {
				$ret = home_url("/placeholder/{$width}x{$height}");
				if ($echo) {
					echo $ret;
				}
				return $ret;
			}

			/**
			 * Process current request
			 * @return boolean TRUE if WordPress should do its routing
			 */
			static function routeRequest($do_parse, $wp) {
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
					$width = $dimensions[0] ?: 320;
					$height = $dimensions[1] ?: 320;
					header("Content-Type: image/png");
					$image = imagecreatetruecolor($width, $height);
					$color_fg = imagecolorallocate($image, 150, 150, 150);
					$color_bg = imagecolorallocate($image, 204, 204, 204);
					$font_file = dirname(__FILE__) . '/Roboto-Regular.ttf';
					$font_size = $height / 8;
					$bbox = imagettfbbox($font_size, 0, $font_file, $text);
					$x = ($width - $bbox[2]) / 2;
					$y = ($height - $bbox[5]) / 2;
					imagealphablending($image, true);
					imagefill($image, 0, 0, $color_bg);
					imagettftext($image, $font_size, 0, $x, $y, $color_fg, $font_file, $text);
					imagepng($image);
					imagedestroy($image);
					exit;
				}

				return $do_parse;
			}
		}

		add_action( 'init', 'Placeholders::init' );

	}


?>