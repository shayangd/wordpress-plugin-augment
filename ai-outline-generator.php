<?php
/**
 * Plugin Name: AAAI AI Writing Tool
 * Plugin URI: https://github.com/shayangd/wordpress-plugin-augment
 * Description: A comprehensive AI-powered writing tool that generates various content types including blog posts, ads, emails, and social media content. Based on AAAI Design specifications.
 * Version: 1.0.0
 * Author: Shayan Rais
 * Author URI: https://github.com/shayangd
 * License: GPL v2 or later
 * Text Domain: aaai-ai-writing-tool
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AAAI_AI_WRITING_TOOL_VERSION', '1.0.0');
define('AAAI_AI_WRITING_TOOL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AAAI_AI_WRITING_TOOL_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main AAAI AI Writing Tool Class
 */
class AAAI_AI_Writing_Tool {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // AJAX handlers for AI writing tool - Updated for Figma design
        add_action('wp_ajax_aaai_generate_content', array($this, 'ajax_generate_ai_content'));
        add_action('wp_ajax_nopriv_aaai_generate_content', array($this, 'ajax_generate_ai_content'));
        add_action('wp_ajax_aaai_submit_feedback', array($this, 'ajax_submit_feedback'));
        add_action('wp_ajax_nopriv_aaai_submit_feedback', array($this, 'ajax_submit_feedback'));

        // Keep backward compatibility with old action names
        add_action('wp_ajax_generate_ai_content', array($this, 'ajax_generate_ai_content'));
        add_action('wp_ajax_nopriv_generate_ai_content', array($this, 'ajax_generate_ai_content'));
        add_action('wp_ajax_submit_feedback', array($this, 'ajax_submit_feedback'));
        add_action('wp_ajax_nopriv_submit_feedback', array($this, 'ajax_submit_feedback'));

        // Register shortcode
        add_shortcode('aaai_ai_writing_tool', array($this, 'shortcode_display'));

        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        load_plugin_textdomain('aaai-ai-writing-tool', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue Google Fonts for Figma design
        wp_enqueue_style(
            'aaai-google-fonts',
            'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;500;600&display=swap',
            array(),
            null
        );

        // Enqueue Figma-based CSS
        wp_enqueue_style(
            'aaai-ai-writing-tool-figma-style',
            AAAI_AI_WRITING_TOOL_PLUGIN_URL . 'assets/css/figma-style.css',
            array(),
            AAAI_AI_WRITING_TOOL_VERSION
        );

        // Fallback to original CSS if Figma CSS doesn't exist
        if (!file_exists(AAAI_AI_WRITING_TOOL_PLUGIN_PATH . 'assets/css/figma-style.css')) {
            wp_enqueue_style(
                'aaai-ai-writing-tool-style',
                AAAI_AI_WRITING_TOOL_PLUGIN_URL . 'assets/css/style.css',
                array(),
                AAAI_AI_WRITING_TOOL_VERSION
            );
        }

        // Enqueue Figma-based JavaScript
        wp_enqueue_script(
            'aaai-ai-writing-tool-figma-script',
            AAAI_AI_WRITING_TOOL_PLUGIN_URL . 'assets/js/figma-script.js',
            array('jquery'),
            AAAI_AI_WRITING_TOOL_VERSION,
            true
        );

        // Fallback to original JS if Figma JS doesn't exist
        if (!file_exists(AAAI_AI_WRITING_TOOL_PLUGIN_PATH . 'assets/js/figma-script.js')) {
            wp_enqueue_script(
                'aaai-ai-writing-tool-script',
                AAAI_AI_WRITING_TOOL_PLUGIN_URL . 'assets/js/script.js',
                array('jquery'),
                AAAI_AI_WRITING_TOOL_VERSION,
                true
            );
        }

        // Localize script for AJAX - Updated for Figma design
        wp_localize_script('aaai-ai-writing-tool-figma-script', 'aaai_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aaai_ai_writing_nonce'),
            'generating_text' => __('Generating...', 'aaai-ai-writing-tool'),
            'generate_text' => __('Generate Text', 'aaai-ai-writing-tool'),
            'error_text' => __('Error generating content. Please try again.', 'aaai-ai-writing-tool'),
            'copy_success' => __('Content copied to clipboard!', 'aaai-ai-writing-tool'),
            'feedback_success' => __('Thank you for your feedback!', 'aaai-ai-writing-tool'),
            'network_error' => __('Network error. Please check your connection and try again.', 'aaai-ai-writing-tool'),
            'timeout_error' => __('Request timed out. Please try again.', 'aaai-ai-writing-tool'),
            'validation_error' => __('Please fill in all required fields.', 'aaai-ai-writing-tool')
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if ('settings_page_aaai-ai-writing-tool' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'aaai-ai-writing-tool-admin-style',
            AAAI_AI_WRITING_TOOL_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            AAAI_AI_WRITING_TOOL_VERSION
        );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('AAAI AI Writing Tool Settings', 'aaai-ai-writing-tool'),
            __('AI Writing Tool', 'aaai-ai-writing-tool'),
            'manage_options',
            'aaai-ai-writing-tool',
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

        $api_key = get_option('aaai_ai_writing_tool_api_key', '');
        $api_provider = get_option('aaai_ai_writing_tool_api_provider', 'openai');
        $default_tone = get_option('aaai_ai_writing_tool_default_tone', 'professional');
        $default_wordcount = get_option('aaai_ai_writing_tool_default_wordcount', '1000');

        include AAAI_AI_WRITING_TOOL_PLUGIN_PATH . 'templates/admin-page.php';
    }

    /**
     * Save admin settings
     */
    private function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!wp_verify_nonce($_POST['aaai_ai_writing_nonce'], 'aaai_ai_writing_settings')) {
            return;
        }

        $api_key = sanitize_text_field($_POST['api_key']);
        $api_provider = sanitize_text_field($_POST['api_provider']);
        $default_tone = sanitize_text_field($_POST['default_tone']);
        $default_wordcount = sanitize_text_field($_POST['default_wordcount']);

        update_option('aaai_ai_writing_tool_api_key', $api_key);
        update_option('aaai_ai_writing_tool_api_provider', $api_provider);
        update_option('aaai_ai_writing_tool_default_tone', $default_tone);
        update_option('aaai_ai_writing_tool_default_wordcount', $default_wordcount);

        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'aaai-ai-writing-tool') . '</p></div>';
    }
    
    /**
     * Shortcode display
     */
    public function shortcode_display($atts) {
        $atts = shortcode_atts(array(
            'show_creative_potions' => 'true',
            'default_wordcount' => '1000',
            'default_tone' => 'professional',
            'template' => 'figma' // Use Figma template by default
        ), $atts);

        ob_start();

        // Use Figma template if it exists, otherwise fallback to original
        $figma_template = AAAI_AI_WRITING_TOOL_PLUGIN_PATH . 'templates/frontend-form-figma.php';
        $original_template = AAAI_AI_WRITING_TOOL_PLUGIN_PATH . 'templates/frontend-form.php';

        if ($atts['template'] === 'figma' && file_exists($figma_template)) {
            include $figma_template;
        } elseif (file_exists($original_template)) {
            include $original_template;
        } else {
            echo '<div class="aaai-error">Template not found. Please check plugin installation.</div>';
        }

        return ob_get_clean();
    }
    
    /**
     * AJAX handler for AI content generation
     */
    public function ajax_generate_ai_content() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'aaai_ai_writing_nonce')) {
            wp_die(__('Security check failed', 'aaai-ai-writing-tool'));
        }

        $topic = sanitize_textarea_field($_POST['topic']);
        $keyword = sanitize_text_field($_POST['keyword']);
        $wordcount = intval($_POST['wordcount']);
        $tone = sanitize_text_field($_POST['tone']);
        $llm = sanitize_text_field($_POST['llm']);
        $content_type = sanitize_text_field($_POST['content_type']);

        // Validate input
        if (empty($topic)) {
            wp_send_json_error(__('Please enter a topic to generate content.', 'aaai-ai-writing-tool'));
        }

        if (strlen($topic) < 5) {
            wp_send_json_error(__('Topic is too short. Please provide at least 5 characters.', 'aaai-ai-writing-tool'));
        }

        if ($wordcount < 50 || $wordcount > 5000) {
            wp_send_json_error(__('Word count must be between 50 and 5000.', 'aaai-ai-writing-tool'));
        }

        // Check if API key is configured
        $api_key = get_option('aaai_ai_writing_tool_api_key');
        if (empty($api_key)) {
            wp_send_json_error(__('API key not configured. Please go to Settings → AI Writing Tool to add your API key.', 'aaai-ai-writing-tool'));
        }

        // Generate content using AI
        $content = $this->generate_ai_content($topic, $keyword, $wordcount, $tone, $llm, $content_type);

        if ($content) {
            wp_send_json_success(array('content' => $content));
        } else {
            // Check for specific error reasons
            $last_error = get_transient('aaai_ai_writing_tool_last_error');
            if ($last_error) {
                delete_transient('aaai_ai_writing_tool_last_error');
                wp_send_json_error($last_error);
            } else {
                wp_send_json_error(__('Failed to generate content. Please check your API key and try again.', 'aaai-ai-writing-tool'));
            }
        }
    }

    /**
     * AJAX handler for feedback submission
     */
    public function ajax_submit_feedback() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'aaai_ai_writing_nonce')) {
            wp_die(__('Security check failed', 'aaai-ai-writing-tool'));
        }

        $rating = intval($_POST['rating']);
        $emotion = sanitize_text_field($_POST['emotion']);
        $feedback_text = sanitize_textarea_field($_POST['feedback_text']);

        // Validate input
        if ($rating < 1 || $rating > 5) {
            wp_send_json_error(__('Invalid rating value.', 'aaai-ai-writing-tool'));
        }

        // Save feedback
        $this->save_feedback($rating, $emotion, $feedback_text);

        wp_send_json_success(array('message' => __('Thank you for your feedback!', 'aaai-ai-writing-tool')));
    }
    
    /**
     * Generate AI content
     */
    private function generate_ai_content($topic, $keyword, $wordcount, $tone, $llm, $content_type) {
        $api_key = get_option('aaai_ai_writing_tool_api_key');
        $api_provider = get_option('aaai_ai_writing_tool_api_provider', 'openai');

        // Debug logging
        error_log('AAAI AI Writing Tool - Debug Info:');
        error_log('API Key exists: ' . (!empty($api_key) ? 'Yes' : 'No'));
        error_log('API Key length: ' . strlen($api_key));
        error_log('API Provider: ' . $api_provider);
        error_log('Content Type: ' . $content_type);
        error_log('Topic length: ' . strlen($topic));

        if (empty($api_key)) {
            error_log('AAAI AI Writing Tool - Error: No API key configured');
            return false;
        }

        // Validate API key format based on provider
        if ($api_provider === 'openai') {
            if (!preg_match('/^sk-[a-zA-Z0-9]{48}$/', $api_key)) {
                error_log('AAAI AI Writing Tool - Error: Invalid OpenAI API key format');
                set_transient('aaai_ai_writing_tool_last_error', 'Invalid OpenAI API key format. Should start with "sk-" and be 51 characters long.', 300);
                return false;
            }
        } elseif ($api_provider === 'anthropic') {
            if (!preg_match('/^sk-ant-[a-zA-Z0-9\-_]{95,}$/', $api_key)) {
                error_log('AAAI AI Writing Tool - Error: Invalid Anthropic API key format');
                set_transient('aaai_ai_writing_tool_last_error', 'Invalid Claude API key format. Should start with "sk-ant-".', 300);
                return false;
            }
        }

        // Create prompt
        $prompt = $this->create_content_prompt($topic, $keyword, $wordcount, $tone, $content_type);

        // Call AI API based on provider
        switch ($api_provider) {
            case 'openai':
                return $this->call_openai_api($prompt, $api_key);
            case 'anthropic':
                return $this->call_anthropic_api($prompt, $api_key);
            default:
                error_log('AAAI AI Writing Tool - Error: Unsupported API provider: ' . $api_provider);
                return false;
        }
    }
    
    /**
     * Create AI content prompt
     */
    private function create_content_prompt($topic, $keyword, $wordcount, $tone, $content_type) {
        $content_type_instructions = array(
            'blog_post' => 'Write a complete, engaging blog post',
            'ad' => 'Create persuasive advertising copy',
            'social_media_post' => 'Compose an engaging social media post',
            'paragraph' => 'Generate well-structured paragraphs',
            'email' => 'Write a professional email',
            'blog_introduction' => 'Create an engaging blog introduction',
            'blog_outline' => 'Generate a detailed blog outline',
            'product_description' => 'Write compelling product descriptions'
        );

        $instruction = isset($content_type_instructions[$content_type])
            ? $content_type_instructions[$content_type]
            : 'Generate high-quality content';

        $prompt = "{$instruction} about the topic: {$topic}\n\n";

        if (!empty($keyword)) {
            $prompt .= "Focus keyword: {$keyword}\n";
        }

        $prompt .= "Word count: approximately {$wordcount} words\n";
        $prompt .= "Tone: {$tone}\n\n";

        $prompt .= "Requirements:\n";
        $prompt .= "- Write in a {$tone} tone\n";
        $prompt .= "- Target approximately {$wordcount} words\n";
        $prompt .= "- Make it engaging and informative\n";
        $prompt .= "- Use proper formatting with headings and paragraphs\n";

        if (!empty($keyword)) {
            $prompt .= "- Naturally incorporate the keyword '{$keyword}'\n";
        }

        $prompt .= "\nPlease provide well-structured, high-quality content that meets these requirements.";

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
            error_log('AAAI AI Writing Tool - API Error: ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
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

            error_log('AAAI AI Writing Tool - API Response Code: ' . $response_code . ' - ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
            return false;
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid response format: ' . json_last_error_msg();
            error_log('AAAI AI Writing Tool - JSON Decode Error: ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
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
            error_log('AAAI AI Writing Tool - OpenAI Error: ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
        } else {
            $error_message = 'Unexpected response format from OpenAI API';
            error_log('AAAI AI Writing Tool - Unexpected Response: ' . print_r($data, true));
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
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
            error_log('AAAI AI Writing Tool - Anthropic API Error: ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
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

            error_log('AAAI AI Writing Tool - Anthropic API Response Code: ' . $response_code . ' - ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
            return false;
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid response format: ' . json_last_error_msg();
            error_log('AAAI AI Writing Tool - Anthropic JSON Decode Error: ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
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
            error_log('AAAI AI Writing Tool - Anthropic Error: ' . $error_message);
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
        } else {
            $error_message = 'Unexpected response format from Anthropic API';
            error_log('AAAI AI Writing Tool - Unexpected Anthropic Response: ' . print_r($data, true));
            set_transient('aaai_ai_writing_tool_last_error', $error_message, 300);
        }

        return false;
    }

    /**
     * Save user feedback
     */
    private function save_feedback($rating, $emotion, $feedback_text) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'aaai_ai_writing_tool_feedback';

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => get_current_user_id(),
                'rating' => $rating,
                'emotion' => $emotion,
                'feedback_text' => $feedback_text,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s')
        );
    }

    /**
     * Log content generation for analytics
     */
    private function log_generation($prompt, $content) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'aaai_ai_writing_tool_logs';

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => get_current_user_id(),
                'topic' => substr(sanitize_text_field($_POST['topic'] ?? ''), 0, 500),
                'keyword' => sanitize_text_field($_POST['keyword'] ?? ''),
                'content_type' => sanitize_text_field($_POST['content_type'] ?? ''),
                'wordcount' => intval($_POST['wordcount'] ?? 0),
                'tone' => sanitize_text_field($_POST['tone'] ?? ''),
                'llm' => sanitize_text_field($_POST['llm'] ?? ''),
                'generated_content' => substr($content, 0, 2000), // Limit stored content
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        add_option('aaai_ai_writing_tool_api_provider', 'openai');
        add_option('aaai_ai_writing_tool_default_tone', 'professional');
        add_option('aaai_ai_writing_tool_default_wordcount', '1000');
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

        // Create logs table
        $logs_table = $wpdb->prefix . 'aaai_ai_writing_tool_logs';

        $charset_collate = $wpdb->get_charset_collate();

        $sql_logs = "CREATE TABLE $logs_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            topic text NOT NULL,
            keyword varchar(255) DEFAULT '',
            content_type varchar(100) NOT NULL,
            wordcount int(11) NOT NULL,
            tone varchar(50) NOT NULL,
            llm varchar(50) NOT NULL,
            generated_content longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Create feedback table
        $feedback_table = $wpdb->prefix . 'aaai_ai_writing_tool_feedback';

        $sql_feedback = "CREATE TABLE $feedback_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            rating int(1) NOT NULL,
            emotion varchar(50) DEFAULT '',
            feedback_text text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_logs);
        dbDelta($sql_feedback);
    }
}

// Initialize the plugin
new AAAI_AI_Writing_Tool();
