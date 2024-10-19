<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://yoviajocr.com/about
 * @since      1.0.0
 *
 * @package    Openai_Seo_Optimizer
 * @subpackage Openai_Seo_Optimizer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Openai_Seo_Optimizer
 * @subpackage Openai_Seo_Optimizer/public
 * @author     Dagoberto Medina <dmedina@yoviajoapp.com>
 */
class Openai_Seo_Optimizer_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action('wp_head', array($this, 'insert_seo_meta_tags'));
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Openai_Seo_Optimizer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Openai_Seo_Optimizer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/openai-seo-optimizer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Openai_Seo_Optimizer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Openai_Seo_Optimizer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/openai-seo-optimizer-public.js', array( 'jquery' ), $this->version, false );

	}

    public function insert_seo_meta_tags() {
        if (is_singular()) {
            global $post;

            $seo_title = get_post_meta($post->ID, '_openai_seo_title', true);
            $seo_description = get_post_meta($post->ID, '_openai_seo_description', true);
            $seo_keywords = get_post_meta($post->ID, '_openai_seo_keywords', true);

            $seo_title = mb_strimwidth($seo_title, 0, 60);
            $seo_description = mb_strimwidth($seo_description, 0, 160);

            $canonical_url = get_permalink($post);

            $site_name = get_bloginfo('name');

            $post_url = get_permalink($post);

            $post_author = get_the_author_meta('display_name', $post->post_author);

            if ($seo_title) {
                echo '<title>' . esc_html($seo_title) . '</title>' . "\n";
            }

            if ($seo_description) {
                echo '<meta name="description" content="' . esc_attr($seo_description) . '" class="openai-seo-meta-tag">' . "\n";
            }

            if ($seo_keywords) {
                echo '<meta name="keywords" content="' . esc_attr($seo_keywords) . '" class="openai-seo-meta-tag">' . "\n";
            }

            if ($canonical_url) {
                echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" class="openai-seo-meta-tag">' . "\n";
            }

            if ($seo_title) {
                echo '<meta property="og:title" content="' . esc_attr($seo_title) . '" class="openai-seo-meta-tag">' . "\n";
            }
            if ($seo_description) {
                echo '<meta property="og:description" content="' . esc_attr($seo_description) . '" class="openai-seo-meta-tag">' . "\n";
            }
            if ($post_url) {
                echo '<meta property="og:url" content="' . esc_url($post_url) . '" class="openai-seo-meta-tag">' . "\n";
            }
            if ($site_name) {
                echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" class="openai-seo-meta-tag">' . "\n";
            }
            echo '<meta property="og:type" content="article" class="openai-seo-meta-tag">' . "\n";
            echo '<meta property="og:locale" content="' . get_locale() . '" class="openai-seo-meta-tag">' . "\n";

            if ($post_author) {
                echo '<meta name="author" content="' . esc_attr($post_author) . '" class="openai-seo-meta-tag">' . "\n";
            }
        }
    }

}
