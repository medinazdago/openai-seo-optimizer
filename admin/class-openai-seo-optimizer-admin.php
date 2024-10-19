<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Openai_Seo_Optimizer
 * @subpackage Openai_Seo_Optimizer/admin
 * @author     Dagoberto Medina <dmedina@yoviajoapp.com>
 */
class Openai_Seo_Optimizer_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    public function register_admin_hooks() {
        add_action('admin_menu', array($this, 'openai_seo_optimizer_menu'));
        add_action('admin_init', array($this, 'openai_seo_optimizer_settings_init'));
        add_action('add_meta_boxes', array($this, 'add_seo_meta_box'));

        add_filter('post_row_actions', array($this, 'add_generate_seo_row_action'), 10, 2);
        add_filter('page_row_actions', array($this, 'add_generate_seo_row_action'), 10, 2);
        add_action('admin_notices', array($this, 'show_admin_notices'));

        add_action('wp_ajax_openai_seo_generate_content', array($this, 'generate_seo_for_post'));
        add_action('wp_ajax_save_manual_seo_content', array($this, 'save_manual_seo_content'));
    }

    public function openai_seo_optimizer_menu() {
        add_options_page(
            'OpenAI SEO Optimizer',
            'OpenAI SEO Optimizer',
            'manage_options',
            'openai-seo-optimizer',
            array($this, 'openai_seo_optimizer_settings_page')
        );
    }

    public function add_seo_meta_box() {
        add_meta_box(
            'openai_seo_meta_box',
            'OpenAI SEO',
            array($this, 'render_seo_meta_box'),
            array('post', 'page', 'product'), // <-- Put your custom post types here
            'side',
            'default'
        );
    }

    public function render_seo_meta_box($post) {
        $seo_title = get_post_meta($post->ID, '_openai_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_openai_seo_description', true);
        $seo_keywords = get_post_meta($post->ID, '_openai_seo_keywords', true);

        $seo_title = $seo_title ? $seo_title : '';
        $seo_description = $seo_description ? $seo_description : '';
        $seo_keywords = $seo_keywords ? $seo_keywords : '';

        $generate_button_text = ($seo_title === '' && $seo_description === '' && $seo_keywords === '') ? 'Generate SEO' : 'Regenerate SEO';

        echo '<div class="openai-seo-meta-box" style="display:block;">';

        echo '<h3>SEO Title</h3>';
        echo '<input type="text" name="openai_seo_title" value="' . esc_attr($seo_title) . '" class="widefat" />';
        echo '<p class="description">This is the title that will appear in search engine results.</p>';

        echo '<h3>Meta Description</h3>';
        echo '<textarea name="openai_seo_description" rows="3" class="widefat">' . esc_textarea($seo_description) . '</textarea>';
        echo '<p class="description">This is the description that will appear in search engine results.</p>';

        echo '<h3>Keywords</h3>';
        echo '<textarea name="openai_seo_keywords" rows="2" class="widefat">' . esc_textarea($seo_keywords) . '</textarea>';
        echo '<p class="description">Relevant keywords generated for this post.</p>';

        $nonce = wp_create_nonce('generate_seo_nonce');

        echo '<div style="margin-top: 20px;">';

        echo '<button type="button" class="button openai-save-seo" style="background-color: #28a745; color: white; margin-right: 10px;" data-post-id="' . esc_attr($post->ID) . '" data-nonce="' . esc_attr($nonce) . '">Save SEO</button>';

        echo '<button type="button" class="button openai-generate-seo" style="background-color: #0073aa; color: white;" data-post-id="' . esc_attr($post->ID) . '" data-nonce="' . esc_attr($nonce) . '">' . $generate_button_text . '</button>';

        echo '</div>';

        echo '</div>';
    }


    public function openai_seo_optimizer_settings_page() {
        include_once 'partials/openai-seo-optimizer-admin-display.php';
    }

    public function openai_seo_optimizer_settings_init() {
        register_setting('openai_seo_optimizer_settings', 'openai_optimizer_api_key');
        register_setting('openai_seo_optimizer_settings', 'openai_optimizer_seo_language');
        register_setting('openai_seo_optimizer_settings', 'openai_optimizer_model');

        add_settings_section(
            'openai_seo_optimizer_section',
            'Configuración de OpenAI para el plugin SEO Optimizer',
            null,
            'openai-seo-optimizer'
        );

        add_settings_field(
            'openai_optimizer_api_key',
            'Clave API de OpenAI',
            array($this, 'openai_api_key_render'),
            'openai-seo-optimizer',
            'openai_seo_optimizer_section'
        );
        add_settings_field(
            'openai_optimizer_seo_language',
            'Idioma para SEO',
            array($this, 'openai_seo_language_render'),
            'openai-seo-optimizer',
            'openai_seo_optimizer_section'
        );
        add_settings_field(
            'openai_optimizer_model',
            'Modelo de OpenAI',
            array($this, 'openai_model_render'),
            'openai-seo-optimizer',
            'openai_seo_optimizer_section'
        );
        add_settings_field(
            'openai_optimizer_custom_prompt',
            'Texto adicional para el prompt',
            array($this, 'openai_custom_prompt_render'),
            'openai-seo-optimizer',
            'openai_seo_optimizer_section'
        );
    }

    public function openai_api_key_render() {
        $api_key = get_option('openai_optimizer_api_key');
        $masked_api_key = !empty($api_key) ? str_repeat('*', strlen($api_key)) : '';
        ?>
        <input type="text" name="openai_optimizer_api_key" value="<?php echo esc_attr($masked_api_key); ?>" class="regular-text" id="openai_api_key" readonly>
        <button type="button" id="edit_api_key" class="button">Editar clave API</button>
        <p><small>Ingrese la clave API de OpenAI para utilizar las funcionalidades del plugin. <a href="https://platform.openai.com/account/api-keys" target="_blank">Obtener clave API de OpenAI</a>.</small></p>

        <script type="text/javascript">
            document.getElementById('edit_api_key').addEventListener('click', function() {
                var apiKeyField = document.getElementById('openai_api_key');
                apiKeyField.value = '';
                apiKeyField.readOnly = false;
                apiKeyField.focus();
            });
        </script>
        <?php
    }

    public function openai_seo_language_render() {
        $selected_language = get_option('openai_optimizer_seo_language', 'Spanish');
        echo '<select name="openai_optimizer_seo_language">';
        echo '<option value="English"' . selected($selected_language, 'English', false) . '>English</option>';
        echo '<option value="Spanish"' . selected($selected_language, 'Spanish', false) . '>Español</option>';
        echo '</select>';
    }

    public function openai_model_render() {
        $selected_model = get_option('openai_optimizer_model', 'gpt-4');
        echo '<select name="openai_optimizer_model">';
        echo '<option value="gpt-4o"' . selected($selected_model, 'gpt-4o', false) . '>GPT-4o</option>';
        echo '<option value="gpt-4o-mini"' . selected($selected_model, 'gpt-4o-mini', false) . '>GPT-4o-mini</option>';
        echo '</select>';
    }

    public function openai_custom_prompt_render() {
        $custom_prompt = get_option('openai_optimizer_custom_prompt', '');
        echo '<textarea name="openai_optimizer_custom_prompt" rows="4" class="large-text">' . esc_textarea($custom_prompt) . '</textarea>';
        echo '<p class="description">Agrega un texto personalizado para el Prompt que se incluirá en la generación del SEO.</p>';
    }

    public function add_generate_seo_row_action($actions, $post) {
        if (in_array($post->post_type, array('post', 'page', 'product'))) {
            $nonce = wp_create_nonce('generate_seo_nonce');
            $actions['generate_seo'] = '<a href="' . admin_url('admin-ajax.php?action=openai_seo_generate_content&post_id=' . $post->ID . '&_wpnonce=' . $nonce) . '">Generar SEO</a>';
        }
        return $actions;
    }

    public function show_admin_notices() {
        if (isset($_GET['seo_generated']) && $_GET['seo_generated'] == '1') {
            echo '<div class="notice notice-success is-dismissible"><p>SEO generado exitosamente.</p></div>';
        }
    }

    public function generate_seo_for_post() {
        check_ajax_referer('generate_seo_nonce', '_wpnonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if ($post_id === 0) {
            wp_send_json_error(array('message' => 'No post ID was provided.'));
        }

        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error(array('message' => 'The requested post was not found.'));
        }

        $api_key = get_option('openai_optimizer_api_key');
        if (empty($api_key)) {
            wp_send_json_error(array('message' => 'API Key is missing. Please configure the OpenAI API key in the plugin settings.'));
        }

        $language = get_option('openai_optimizer_seo_language', 'Spanish');
        $model = get_option('openai_optimizer_model', 'gpt-4');
        $blog_title = get_bloginfo('name');
        $blog_description = get_bloginfo('description');
        $post_title = $post->post_title;
        $post_content = wp_strip_all_tags($post->post_content);
        $custom_prompt = get_option('openai_optimizer_custom_prompt', '');
        $prompt = "Generate SEO-friendly content for a post with the following title and content:
    Title: \"$post_title\"
    Content: \"$post_content\"
    The content must be in $language.
    The blog title is \"$blog_title\" and the blog description is \"$blog_description\".
    $custom_prompt
    The generated content must follow these SEO rules:
    - Title must be no longer than 60 characters.
    - Description must be no longer than 160 characters.
    - Generate relevant keywords, including single-word keywords and two-word phrases.
    Please return the content as a JSON object with the following structure:
    {
        \"title\": \"\",
        \"description\": \"\",
        \"keywords\": \"\"
    }";

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'body' => wp_json_encode(array(
                'model' => $model,
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => 'You are an SEO expert. Generate SEO-friendly content.'
                    ),
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'max_tokens' => 800,
            )),
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => 'Failed to connect to OpenAI: ' . $response->get_error_message()));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data['choices'][0]['message']['content'])) {
            wp_send_json_error(array('message' => 'No content returned from OpenAI. Response: ' . print_r($data, true)));
        }

        $raw_content = $data['choices'][0]['message']['content'];
        $clean_content = trim(preg_replace('/^```json|```$/m', '', $raw_content));  // Remover los posibles prefijos/sufijos

        $seo_content = json_decode($clean_content, true);

        if (!empty($seo_content['title']) && !empty($seo_content['description']) && !empty($seo_content['keywords'])) {
            $seo_description = (strlen($seo_content['description']) > 160) ? substr($seo_content['description'], 0, 157) . '...' : $seo_content['description'];

            update_post_meta($post_id, '_openai_seo_title', sanitize_text_field($seo_content['title']));
            update_post_meta($post_id, '_openai_seo_description', sanitize_textarea_field($seo_description));
            update_post_meta($post_id, '_openai_seo_keywords', sanitize_text_field($seo_content['keywords']));

            wp_send_json_success(array(
                'title' => $seo_content['title'],
                'description' => $seo_description,
                'keywords' => $seo_content['keywords']
            ));
        } else {
            wp_send_json_error(array('message' => 'Invalid SEO content received from OpenAI. Cleaned Content: ' . print_r($clean_content, true)));
        }
    }

    public function save_manual_seo_content() {
        check_ajax_referer('generate_seo_nonce', '_wpnonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if ($post_id === 0) {
            wp_send_json_error(array('message' => 'No post ID provided.'));
        }

        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array('message' => 'You do not have permission to edit this post.'));
        }

        if (isset($_POST['seo_title'])) {
            update_post_meta($post_id, '_openai_seo_title', sanitize_text_field($_POST['seo_title']));
        }

        if (isset($_POST['seo_description'])) {
            update_post_meta($post_id, '_openai_seo_description', sanitize_textarea_field($_POST['seo_description']));
        }

        if (isset($_POST['seo_keywords'])) {
            update_post_meta($post_id, '_openai_seo_keywords', sanitize_text_field($_POST['seo_keywords']));
        }

        wp_send_json_success();
    }


    /**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/openai-seo-optimizer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/openai-seo-optimizer-admin.js', array( 'jquery' ), $this->version. time(), true );
        wp_localize_script(
            $this->plugin_name . '-admin',
            'ajaxurl',
            admin_url('admin-ajax.php')
        );
	}

}
