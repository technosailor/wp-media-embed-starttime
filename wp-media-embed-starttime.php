<?php
/*
Plugin Name: WP Media Embed Start Time
Author: Aaron Brazell
Author URI: http://technosailor.com
Plugin URI:https://github.com/technosailor/wp-media-embed-starttime
Description:
License: MIT
License URI: https://github.com/technosailor/wp-media-embed-starttime/blob/master/LICENSE
*/

class WP_Media_Embed_Start_Time {

	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		add_filter( 'shortcode_atts_audio', array( $this, 'add_attributes' ), 10, 3 );
		add_filter( 'shortcode_atts_video', array( $this, 'add_attributes' ), 10, 3 );
		add_filter( 'wp_audio_shortcode', array( $this, 'add_start_time' ), 10, 5 );
		add_filter( 'wp_video_shortcode', array( $this, 'add_start_time' ), 10, 5 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue() {
		wp_enqueue_script( 'jquery' );
	}

	public function add_attributes( $out, $pairs, $atts ) {
		if( array_key_exists( 'start', $atts ) ) {
			if( is_numeric( $atts['start'] ) ) {
				$out['start'] = $atts['start'];
			}
			else {
				$out['start'] = 0;
			}
		}
		else {
			$out['start'] = 0;
		}
		return $out;
	}

	public function add_start_time( $html, $atts, $audio, $post_id, $library ) {
		preg_match( '#id="((audio|video)?-\d+-\d+)"#', $html, $dom );
		if( !isset( $dom[1] )  )
			return $html;
		$dom_id = esc_js( $dom[1] );
		$start_time = (int) $atts['start'];
		$script = <<<SCRIPT_TAG
		<script>
			jQuery(document).ready(function($) {
				var media = $('#$dom_id');
				media.on('canplay', function (ev) {
					this.currentTime = $start_time;
				});
			});
		</script>
SCRIPT_TAG;
		$html = $html . $script;
		return $html;
	}
}
new WP_Media_Embed_Start_Time;