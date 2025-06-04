<?php
/**
 * Plugin Name: AI Outline Generator
 * Plugin URI: https://github.com/shayangd/wordpress-plugin-augment
 * Description: A WordPress plugin that generates AI-powered outlines for various content types. Based on Wellows + AAAI Design.
 * Version: 1.0.0
 * Author: Shayan Rais
 * Author URI: https://github.com/shayangd
 * License: GPL v2 or later
 * Text Domain: ai-outline-generator
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AI_OUTLINE_GENERATOR_VERSION', '1.0.0');
define('AI_OUTLINE_GENERATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_OUTLINE_GENERATOR_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main AI Outline Generator Class
 */
class AI_Outline_Generator {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_generate_outline', array($this, 'ajax_generate_outline'));
        add_action('wp_ajax_nopriv_generate_outline', array($this, 'ajax_generate_outline'));
        
        // Register shortcode
        add_shortcode('ai_outline_generator', array($this, 'shortcode_display'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        load_plugin_textdomain('ai-outline-generator', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'ai-outline-generator-style',
            AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/css/style.css',
            array(),
            AI_OUTLINE_GENERATOR_VERSION
        );
        
        wp_enqueue_script(
            'ai-outline-generator-script',
            AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            AI_OUTLINE_GENERATOR_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('ai-outline-generator-script', 'ai_outline_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ai_outline_nonce'),
            'generating_text' => __('Generating outline...', 'ai-outline-generator'),
            'error_text' => __('Error generating outline. Please try again.', 'ai-outline-generator')
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if ('settings_page_ai-outline-generator' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'ai-outline-generator-admin-style',
            AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            AI_OUTLINE_GENERATOR_VERSION
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('AI Outline Generator Settings', 'ai-outline-generator'),
            __('AI Outline Generator', 'ai-outline-generator'),
            'manage_options',
            'ai-outline-generator',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        if (isset($_POST['submit'])) {
            $this->save_settings();
        }
        
        $api_key = get_option('ai_outline_generator_api_key', '');
        $api_provider = get_option('ai_outline_generator_api_provider', 'openai');
        
        include AI_OUTLINE_GENERATOR_PLUGIN_PATH . 'templates/admin-page.php';
    }
    
    /**
     * Save admin settings
     */
    private function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['ai_outline_nonce'], 'ai_outline_settings')) {
            return;
        }
        
        $api_key = sanitize_text_field($_POST['api_key']);
        $api_provider = sanitize_text_field($_POST['api_provider']);
        
        update_option('ai_outline_generator_api_key', $api_key);
        update_option('ai_outline_generator_api_provider', $api_provider);
        
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'ai-outline-generator') . '</p></div>';
    }
    
    /**
     * Shortcode display
     */
    public function shortcode_display($atts) {
        $atts = shortcode_atts(array(
            'show_samples' => 'true',
            'max_chars' => '1000'
        ), $atts);
        
        ob_start();
        include AI_OUTLINE_GENERATOR_PLUGIN_PATH . 'templates/frontend-form.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX handler for outline generation
     */
    public function ajax_generate_outline() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'ai_outline_nonce')) {
            wp_die(__('Security check failed', 'ai-outline-generator'));
        }
        
        $content = sanitize_textarea_field($_POST['content']);
        $content_type = sanitize_text_field($_POST['content_type']);
        $sections = intval($_POST['sections']);
        $language = sanitize_text_field($_POST['language']);
        
        // Validate input
        if (empty($content) || strlen($content) > 1000) {
            wp_send_json_error(__('Invalid content length', 'ai-outline-generator'));
        }
        
        // Generate outline using AI
        $outline = $this->generate_ai_outline($content, $content_type, $sections, $language);
        
        if ($outline) {
            wp_send_json_success(array('outline' => $outline));
        } else {
            wp_send_json_error(__('Failed to generate outline', 'ai-outline-generator'));
        }
    }
    
    /**
     * Generate AI outline
     */
    private function generate_ai_outline($content, $content_type, $sections, $language) {
        $api_key = get_option('ai_outline_generator_api_key');
        $api_provider = get_option('ai_outline_generator_api_provider', 'openai');
        
        if (empty($api_key)) {
            return false;
        }
        
        // Create prompt
        $prompt = $this->create_prompt($content, $content_type, $sections, $language);
        
        // Call AI API based on provider
        switch ($api_provider) {
            case 'openai':
                return $this->call_openai_api($prompt, $api_key);
            default:
                return false;
        }
    }
    
    /**
     * Create AI prompt
     */
    private function create_prompt($content, $content_type, $sections, $language) {
        $prompt = "Create a detailed outline for a {$content_type} in {$language} language with {$sections} main sections. ";
        $prompt .= "The topic/content is: {$content}\n\n";
        $prompt .= "Please provide a structured outline with:\n";
        $prompt .= "1. A compelling title\n";
        $prompt .= "2. {$sections} main sections with descriptive headings\n";
        $prompt .= "3. 2-3 sub-points under each main section\n";
        $prompt .= "4. Brief descriptions for each point\n\n";
        $prompt .= "Format the response as HTML with proper heading tags (h2 for main sections, h3 for sub-points).";
        
        return $prompt;
    }
    
    /**
     * Call OpenAI API
     */
    private function call_openai_api($prompt, $api_key) {
        $url = 'https://api.openai.com/v1/chat/completions';

        $data = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a professional content outline generator. Create well-structured, detailed outlines that help writers organize their thoughts and create compelling content.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'max_tokens' => 1500,
            'temperature' => 0.7
        );

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data),
            'timeout' => 60
        );

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            error_log('AI Outline Generator - API Error: ' . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('AI Outline Generator - API Response Code: ' . $response_code);
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('AI Outline Generator - JSON Decode Error: ' . json_last_error_msg());
            return false;
        }

        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];

            // Log the generation for analytics (optional)
            $this->log_generation($prompt, $content);

            return $content;
        }

        if (isset($data['error'])) {
            error_log('AI Outline Generator - OpenAI Error: ' . $data['error']['message']);
        }

        return false;
    }

    /**
     * Log outline generation for analytics
     */
    private function log_generation($prompt, $outline) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ai_outline_generator_logs';

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => get_current_user_id(),
                'content' => substr($prompt, 0, 500), // Limit stored content
                'content_type' => sanitize_text_field($_POST['content_type'] ?? ''),
                'sections' => intval($_POST['sections'] ?? 0),
                'language' => sanitize_text_field($_POST['language'] ?? ''),
                'outline' => substr($outline, 0, 2000), // Limit stored outline
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        add_option('ai_outline_generator_api_provider', 'openai');
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Cleanup if needed
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_outline_generator_logs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            content text NOT NULL,
            content_type varchar(100) NOT NULL,
            sections int(11) NOT NULL,
            language varchar(50) NOT NULL,
            outline longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialize the plugin
new AI_Outline_Generator();
