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

	private $chart_id;

	/**
	 * A simple call to init when constructed
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->chart_id = 0;
	}

	/**
	 * BeerXML initialization routines
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

	function highcharts_shortcode( $atts, $content = '' ) {
		$this->include_highcharts();
		extract( shortcode_atts( array(
			'container' => 'highchart-' . $this->chart_id++
		), $atts ) );

		return 'foo';
	}
}

// The fun starts here!
new Highcharts_Shortcode();
