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
        if (empty($content)) {
            wp_send_json_error(__('Please enter some content to generate an outline.', 'ai-outline-generator'));
        }

        if (strlen($content) > 1000) {
            wp_send_json_error(__('Content is too long. Please limit to 1000 characters.', 'ai-outline-generator'));
        }

        if (strlen($content) < 10) {
            wp_send_json_error(__('Content is too short. Please provide at least 10 characters.', 'ai-outline-generator'));
        }

        // Check if API key is configured
        $api_key = get_option('ai_outline_generator_api_key');
        if (empty($api_key)) {
            wp_send_json_error(__('OpenAI API key not configured. Please go to Settings → AI Outline Generator to add your API key.', 'ai-outline-generator'));
        }

        // Generate outline using AI
        $outline = $this->generate_ai_outline($content, $content_type, $sections, $language);

        if ($outline) {
            wp_send_json_success(array('outline' => $outline));
        } else {
            // Check for specific error reasons
            $last_error = get_transient('ai_outline_generator_last_error');
            if ($last_error) {
                delete_transient('ai_outline_generator_last_error');
                wp_send_json_error($last_error);
            } else {
                wp_send_json_error(__('Failed to generate outline. Please check your API key and try again.', 'ai-outline-generator'));
            }
        }
    }
    
    /**
     * Generate AI outline
     */
    private function generate_ai_outline($content, $content_type, $sections, $language) {
        $api_key = get_option('ai_outline_generator_api_key');
        $api_provider = get_option('ai_outline_generator_api_provider', 'openai');

        // Debug logging
        error_log('AI Outline Generator - Debug Info:');
        error_log('API Key exists: ' . (!empty($api_key) ? 'Yes' : 'No'));
        error_log('API Key length: ' . strlen($api_key));
        error_log('API Provider: ' . $api_provider);
        error_log('Content length: ' . strlen($content));

        if (empty($api_key)) {
            error_log('AI Outline Generator - Error: No API key configured');
            return false;
        }

        // Validate API key format based on provider
        if ($api_provider === 'openai') {
            if (!preg_match('/^sk-[a-zA-Z0-9]{48}$/', $api_key)) {
                error_log('AI Outline Generator - Error: Invalid OpenAI API key format');
                set_transient('ai_outline_generator_last_error', 'Invalid OpenAI API key format. Should start with "sk-" and be 51 characters long.', 300);
                return false;
            }
        } elseif ($api_provider === 'anthropic') {
            if (!preg_match('/^sk-ant-[a-zA-Z0-9\-_]{95,}$/', $api_key)) {
                error_log('AI Outline Generator - Error: Invalid Anthropic API key format');
                set_transient('ai_outline_generator_last_error', 'Invalid Claude API key format. Should start with "sk-ant-".', 300);
                return false;
            }
        }

        // Create prompt
        $prompt = $this->create_prompt($content, $content_type, $sections, $language);

        // Call AI API based on provider
        switch ($api_provider) {
            case 'openai':
                return $this->call_openai_api($prompt, $api_key);
            case 'anthropic':
                return $this->call_anthropic_api($prompt, $api_key);
            default:
                error_log('AI Outline Generator - Error: Unsupported API provider: ' . $api_provider);
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
            $error_message = 'Network error: ' . $response->get_error_message();
            error_log('AI Outline Generator - API Error: ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($response_code !== 200) {
            $error_message = 'API Error (Code: ' . $response_code . ')';

            // Try to get more specific error from response body
            $error_data = json_decode($body, true);
            if (isset($error_data['error']['message'])) {
                $error_message = $error_data['error']['message'];

                // Provide user-friendly error messages
                if (strpos($error_message, 'invalid_api_key') !== false) {
                    $error_message = 'Invalid API key. Please check your OpenAI API key in settings.';
                } elseif (strpos($error_message, 'insufficient_quota') !== false) {
                    $error_message = 'Insufficient API credits. Please check your OpenAI account billing.';
                } elseif (strpos($error_message, 'rate_limit') !== false) {
                    $error_message = 'Rate limit exceeded. Please try again in a few moments.';
                }
            }

            error_log('AI Outline Generator - API Response Code: ' . $response_code . ' - ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
            return false;
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid response format: ' . json_last_error_msg();
            error_log('AI Outline Generator - JSON Decode Error: ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
            return false;
        }

        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];

            // Log the generation for analytics (optional)
            $this->log_generation($prompt, $content);

            return $content;
        }

        if (isset($data['error'])) {
            $error_message = $data['error']['message'];
            error_log('AI Outline Generator - OpenAI Error: ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
        } else {
            $error_message = 'Unexpected response format from OpenAI API';
            error_log('AI Outline Generator - Unexpected Response: ' . print_r($data, true));
            set_transient('ai_outline_generator_last_error', $error_message, 300);
        }

        return false;
    }

    /**
     * Call Anthropic (Claude) API
     */
    private function call_anthropic_api($prompt, $api_key) {
        $url = 'https://api.anthropic.com/v1/messages';

        $data = array(
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1500,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            )
        );

        $args = array(
            'headers' => array(
                'x-api-key' => $api_key,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ),
            'body' => json_encode($data),
            'timeout' => 60
        );

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $error_message = 'Network error: ' . $response->get_error_message();
            error_log('AI Outline Generator - Anthropic API Error: ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($response_code !== 200) {
            $error_message = 'Anthropic API Error (Code: ' . $response_code . ')';

            // Try to get more specific error from response body
            $error_data = json_decode($body, true);
            if (isset($error_data['error']['message'])) {
                $error_message = $error_data['error']['message'];

                // Provide user-friendly error messages
                if (strpos($error_message, 'invalid_api_key') !== false || strpos($error_message, 'authentication') !== false) {
                    $error_message = 'Invalid Claude API key. Please check your Anthropic API key in settings.';
                } elseif (strpos($error_message, 'insufficient_quota') !== false || strpos($error_message, 'billing') !== false) {
                    $error_message = 'Insufficient API credits. Please check your Anthropic account billing.';
                } elseif (strpos($error_message, 'rate_limit') !== false) {
                    $error_message = 'Rate limit exceeded. Please try again in a few moments.';
                }
            }

            error_log('AI Outline Generator - Anthropic API Response Code: ' . $response_code . ' - ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
            return false;
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid response format: ' . json_last_error_msg();
            error_log('AI Outline Generator - Anthropic JSON Decode Error: ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
            return false;
        }

        if (isset($data['content'][0]['text'])) {
            $content = $data['content'][0]['text'];

            // Log the generation for analytics (optional)
            $this->log_generation($prompt, $content);

            return $content;
        }

        if (isset($data['error'])) {
            $error_message = $data['error']['message'];
            error_log('AI Outline Generator - Anthropic Error: ' . $error_message);
            set_transient('ai_outline_generator_last_error', $error_message, 300);
        } else {
            $error_message = 'Unexpected response format from Anthropic API';
            error_log('AI Outline Generator - Unexpected Anthropic Response: ' . print_r($data, true));
            set_transient('ai_outline_generator_last_error', $error_message, 300);
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
