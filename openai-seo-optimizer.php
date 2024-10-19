<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://yoviajocr.com/about
 * @since             1.0.0
 * @package           Openai_Seo_Optimizer
 *
 * @wordpress-plugin
 * Plugin Name:       OpenAI SEO Optimizer
 * Plugin URI:        https://yoviajocr.com
 * Description:       OpenAI SEO Element Generator automatically creates missing SEO elements like titles and meta descriptions for posts, pages, products, and custom post types.
 * Version:           1.0.0
 * Author:            Dagoberto Medina
 * Author URI:        https://yoviajocr.com/about/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       openai-seo-optimizer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OPENAI_SEO_OPTIMIZER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-openai-seo-optimizer-activator.php
 */
function activate_openai_seo_optimizer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-openai-seo-optimizer-activator.php';
	Openai_Seo_Optimizer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-openai-seo-optimizer-deactivator.php
 */
function deactivate_openai_seo_optimizer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-openai-seo-optimizer-deactivator.php';
	Openai_Seo_Optimizer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_openai_seo_optimizer' );
register_deactivation_hook( __FILE__, 'deactivate_openai_seo_optimizer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-openai-seo-optimizer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_openai_seo_optimizer() {

	$plugin = new Openai_Seo_Optimizer();
	$plugin->run();

}
run_openai_seo_optimizer();
