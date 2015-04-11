<?php
/*
Plugin Name: Highcharts Shortcode
Plugin URI: http://wordpress.org/extend/plugins/highcharts-shortcode/
Description: Load Highchart data directly to the page.
Author: Derek Springer
Author URI: http://derekspringer.wordpress.com
Version: 0.1
License: GPL2 or later
Text Domain: highcharts-shortcode
*/

class Highcharts_Shortcode {

	/**
	 * A simple call to init when constructed
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Highcharts initialization routines
	 */
	function init() {
		// I18n
		load_plugin_textdomain(
			'highcharts-shortcode',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( ! defined( 'HIGHCHARTS_URL' ) )
			define( 'HIGHCHARTS_URL', plugin_dir_url( __FILE__ ) );

		if ( ! defined( 'HIGHCHARTS_PATH' ) )
			define( 'HIGHCHARTS_PATH', plugin_dir_path( __FILE__ ) );

		if ( ! defined( 'HIGHCHARTS_BASENAME' ) )
			define( 'HIGHCHARTS_BASENAME', plugin_basename( __FILE__ ) );

		add_shortcode( 'highcharts', array( $this, 'highcharts_shortcode' ) );
		add_filter( 'no_texturize_shortcodes', array( $this, 'remove_texturize' ) );

		// Shortcake interface
		if ( is_admin() && function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			shortcode_ui_register_for_shortcode(
				'highcharts',
				array(
					'label'         => __( 'Highcharts', 'highcharts-shortcode' ),
					'listItemImage' => 'dashicons-chart-line',
					'attrs'         => array(
						array(
							'label'       => __( 'Highchart snippet', 'highcharts-shortcode' ),
							'attr'        => 'content',
							'type'        => 'textarea',
							'description' => __(
								'The js snippet(s) to load your Highchart',
								'highcharts-shortcode'
							),
						),
						array(
							'label'       => __( 'Highchart Container', 'highcharts-shortcode' ),
							'attr'        => 'container',
							'type'        => 'text',
							'placeholder' => __( 'some-id', 'highcharts-shortcode' ),
							'description' => __(
								'Required. ID for highchart div. Default is highchart-[0..n]',
								'highcharts-shortcode'
							),
						),
						array(
							'label'       => __( 'Style', 'highcharts-shortcode' ),
							'attr'        => 'style',
							'type'        => 'text',
							'placeholder' => __(
								'css-selector: foo; other-selector: bar;',
								'highcharts-shortcode'
							),
							'description' => __(
								'Optional. CSS styles for Highchart container.',
								'highcharts-shortcode'
							),
						),
					),
				)
			);
		}
	}

	function include_highcharts() {
		wp_enqueue_script(
			'highcharts',
			'//code.highcharts.com/highcharts.js',
			array( 'jquery' )
		);

		wp_enqueue_script(
			'highcharts-more',
			'//code.highcharts.com/highcharts-more.js',
			array( 'jquery', 'highcharts' )
		);

		wp_enqueue_script(
			'highcharts-exporting',
			'//code.highcharts.com/modules/exporting.js',
			array( 'jquery', 'highcharts' )
		);
	}

	function remove_texturize( $shortcodes ) {
		$shortcodes[] = 'highcharts';
		return $shortcodes;
	}

	function highcharts_shortcode( $atts, $content = '' ) {
		if ( ! isset( $atts['container'] ) ) {
			return 'Highcharts error: container ID not set.';
		}

		$this->include_highcharts();
		extract( shortcode_atts( array(
			'container' => '',
			'style'     => ''
		), $atts ) );

		$content = strip_tags( $content );
		$style = ! empty( $style ) && is_string( $style ) ?
			'style="' . esc_attr( $style ) . '"' :
			'';

		$admin = '';
		if ( is_admin() ) {
			// Workaround for Shortcake loading iframe in editor
			$admin = <<<HTML
			<script type="text/javascript" src="//code.jquery.com/jquery-1.11.2.min.js"></script>
			<script type="text/javascript" src="//code.highcharts.com/highcharts.js?ver=4.1.1"></script>
			<script type="text/javascript" src="//code.highcharts.com/highcharts-more.js?ver=4.1.1"></script>
			<script type="text/javascript" src="//code.highcharts.com/modules/exporting.js?ver=4.1.1"></script>
HTML;
		}

		return <<<HTML
		$admin
		<div id="$container" $style></div>
		<script type="text/javascript">
		(function($) {
			$(function(){
				$content
			});
		})(jQuery);
		</script>
HTML;
	}
}

// The fun starts here!
new Highcharts_Shortcode();
