<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://yoviajocr.com/about
 * @since      1.0.0
 *
 * @package    Openai_Seo_Optimizer
 * @subpackage Openai_Seo_Optimizer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Openai_Seo_Optimizer
 * @subpackage Openai_Seo_Optimizer/includes
 * @author     Dagoberto Medina <dmedina@yoviajoapp.com>
 */
class Openai_Seo_Optimizer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'openai-seo-optimizer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
