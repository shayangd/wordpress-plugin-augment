<?php
/**
 * Plugin Name: AAAI AI Writing Tool
 * Plugin URI: https://github.com/shayangd/wordpress-plugin-augment
 * Description: A comprehensive AI-powered writing tool that generates various content types including blog posts, ads, emails, and social media content. Based on AAAI Design specifications with Figma UI.
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

        // Add AJAX handlers for Figma design
        add_action('wp_ajax_aaai_generate_content', array($this, 'ajax_generate_content'));
        add_action('wp_ajax_nopriv_aaai_generate_content', array($this, 'ajax_generate_content'));
        add_action('wp_ajax_aaai_submit_feedback', array($this, 'ajax_submit_feedback'));
        add_action('wp_ajax_nopriv_aaai_submit_feedback', array($this, 'ajax_submit_feedback'));
        
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
            AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/css/figma-style.css',
            array(),
            AI_OUTLINE_GENERATOR_VERSION
        );

        // Fallback to original CSS if Figma CSS doesn't exist
        if (!file_exists(AI_OUTLINE_GENERATOR_PLUGIN_PATH . 'assets/css/figma-style.css')) {
            wp_enqueue_style(
                'ai-outline-generator-style',
                AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/css/style.css',
                array(),
                AI_OUTLINE_GENERATOR_VERSION
            );
        }

        // Enqueue Figma-based JavaScript
        wp_enqueue_script(
            'aaai-ai-writing-tool-figma-script',
            AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/js/figma-script.js',
            array('jquery'),
            AI_OUTLINE_GENERATOR_VERSION,
            true
        );

        // Fallback to original JS if Figma JS doesn't exist
        if (!file_exists(AI_OUTLINE_GENERATOR_PLUGIN_PATH . 'assets/js/figma-script.js')) {
            wp_enqueue_script(
                'ai-outline-generator-script',
                AI_OUTLINE_GENERATOR_PLUGIN_URL . 'assets/js/script.js',
                array('jquery'),
                AI_OUTLINE_GENERATOR_VERSION,
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
            'show_creative_potions' => 'true',
            'default_wordcount' => '1000',
            'default_tone' => 'professional',
            'template' => 'figma' // Use Figma template by default
        ), $atts);

        ob_start();

        // Use Figma template if it exists, otherwise fallback to original
        $figma_template = AI_OUTLINE_GENERATOR_PLUGIN_PATH . 'templates/frontend-form-figma.php';
        $original_template = AI_OUTLINE_GENERATOR_PLUGIN_PATH . 'templates/frontend-form.php';

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
     * AJAX handler for Figma design content generation
     */
    public function ajax_generate_content() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'aaai_ai_writing_nonce')) {
            wp_send_json_error(__('Security check failed', 'aaai-ai-writing-tool'));
        }

        $topic = sanitize_textarea_field($_POST['topic']);
        $keyword = sanitize_text_field($_POST['keyword']);
        $wordcount = intval($_POST['wordcount']);
        $tone = sanitize_text_field($_POST['tone']);
        $llm = sanitize_text_field($_POST['llm']);
        $content_type = sanitize_text_field($_POST['content_type']);

        // Validate input
        if (empty($topic) || strlen($topic) > 500) {
            wp_send_json_error(__('Please enter a valid topic (max 500 characters)', 'aaai-ai-writing-tool'));
        }

        if ($wordcount < 50 || $wordcount > 5000) {
            wp_send_json_error(__('Word count must be between 50 and 5000', 'aaai-ai-writing-tool'));
        }

        // Generate content using AI
        $content = $this->generate_ai_content($topic, $keyword, $wordcount, $tone, $llm, $content_type);

        if ($content) {
            wp_send_json_success(array('content' => $content));
        } else {
            wp_send_json_error(__('Failed to generate content. Please check your API settings.', 'aaai-ai-writing-tool'));
        }
    }

    /**
     * AJAX handler for feedback submission
     */
    public function ajax_submit_feedback() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'aaai_ai_writing_nonce')) {
            wp_send_json_error(__('Security check failed', 'aaai-ai-writing-tool'));
        }

        $emotion = sanitize_text_field($_POST['emotion']);
        $rating = intval($_POST['rating']);
        $feedback_text = sanitize_textarea_field($_POST['feedback_text']);

        // Log feedback (you can extend this to save to database)
        error_log("AAAI Feedback - Emotion: {$emotion}, Rating: {$rating}, Text: {$feedback_text}");

        wp_send_json_success(array('message' => __('Thank you for your feedback!', 'aaai-ai-writing-tool')));
    }

    /**
     * Generate AI content for Figma design
     */
    private function generate_ai_content($topic, $keyword, $wordcount, $tone, $llm, $content_type) {
        $api_key = get_option('ai_outline_generator_api_key');
        $api_provider = get_option('ai_outline_generator_api_provider', 'openai');

        // If no API key is configured, return demo content
        if (empty($api_key)) {
            return $this->generate_demo_content($topic, $keyword, $wordcount, $tone, $content_type);
        }

        // Create prompt based on content type
        $prompt = $this->create_content_prompt($topic, $keyword, $wordcount, $tone, $content_type);

        // Call AI API based on provider
        switch ($api_provider) {
            case 'openai':
                return $this->call_openai_content_api($prompt, $api_key, $wordcount);
            case 'anthropic':
                return $this->call_anthropic_api($prompt, $api_key, $wordcount);
            default:
                return $this->generate_demo_content($topic, $keyword, $wordcount, $tone, $content_type);
        }
    }

    /**
     * Generate demo content when API is not configured
     */
    private function generate_demo_content($topic, $keyword, $wordcount, $tone, $content_type) {
        $content_types = array(
            'blog_post' => 'Blog Post',
            'ad' => 'Advertisement',
            'social_media_post' => 'Social Media Post',
            'paragraph' => 'Paragraph',
            'email' => 'Email',
            'blog_introduction' => 'Blog Introduction',
            'blog_outline' => 'Blog Outline',
            'product_description' => 'Product Description'
        );

        $type_name = $content_types[$content_type] ?? 'Content';

        $demo_content = "<h2>Demo {$type_name}: {$topic}</h2>";

        if (!empty($keyword)) {
            $demo_content .= "<p><strong>Focus Keyword:</strong> {$keyword}</p>";
        }

        $demo_content .= "<p><strong>Tone:</strong> " . ucfirst($tone) . "</p>";
        $demo_content .= "<p><strong>Target Word Count:</strong> {$wordcount} words</p>";

        $demo_content .= "<h3>Sample Content</h3>";
        $demo_content .= "<p>This is a demo version of the AI Writing Tool. To generate real AI content, please configure your API key in the WordPress admin panel.</p>";

        switch ($content_type) {
            case 'blog_post':
                $demo_content .= "<p>This would be a comprehensive blog post about <strong>{$topic}</strong> written in a {$tone} tone. The content would be approximately {$wordcount} words and would include:</p>";
                $demo_content .= "<ul><li>An engaging introduction</li><li>Well-structured main points</li><li>Supporting details and examples</li><li>A compelling conclusion</li></ul>";
                break;

            case 'ad':
                $demo_content .= "<p>This would be persuasive advertisement copy for <strong>{$topic}</strong> with a {$tone} tone, designed to drive action and engagement.</p>";
                $demo_content .= "<p><strong>Call to Action:</strong> Learn more about {$topic} today!</p>";
                break;

            case 'social_media_post':
                $demo_content .= "<p>This would be an engaging social media post about <strong>{$topic}</strong> optimized for sharing and engagement.</p>";
                $demo_content .= "<p>📱 Perfect for platforms like Facebook, Twitter, LinkedIn, and Instagram!</p>";
                break;

            case 'email':
                $demo_content .= "<p><strong>Subject Line:</strong> Important information about {$topic}</p>";
                $demo_content .= "<p>This would be a professional email about <strong>{$topic}</strong> written in a {$tone} tone.</p>";
                break;

            default:
                $demo_content .= "<p>This would be high-quality content about <strong>{$topic}</strong> tailored to your specific requirements.</p>";
                break;
        }

        $demo_content .= "<hr><p><em>To generate real AI content, please configure your OpenAI or Anthropic API key in the plugin settings.</em></p>";

        return $demo_content;
    }

    /**
     * Create content prompt based on type
     */
    private function create_content_prompt($topic, $keyword, $wordcount, $tone, $content_type) {
        $content_types = array(
            'blog_post' => 'blog post',
            'ad' => 'advertisement copy',
            'social_media_post' => 'social media post',
            'paragraph' => 'paragraph',
            'email' => 'email',
            'blog_introduction' => 'blog introduction',
            'blog_outline' => 'blog outline',
            'product_description' => 'product description'
        );

        $type_name = $content_types[$content_type] ?? 'content';

        $prompt = "Write a {$tone} {$type_name} about '{$topic}'";

        if (!empty($keyword)) {
            $prompt .= " focusing on the keyword '{$keyword}'";
        }

        $prompt .= ". The content should be approximately {$wordcount} words.";

        // Add specific instructions based on content type
        switch ($content_type) {
            case 'blog_post':
                $prompt .= " Include an engaging introduction, well-structured body paragraphs, and a compelling conclusion.";
                break;
            case 'ad':
                $prompt .= " Make it persuasive and action-oriented with a clear call-to-action.";
                break;
            case 'social_media_post':
                $prompt .= " Make it engaging and shareable, suitable for social media platforms.";
                break;
            case 'email':
                $prompt .= " Include a subject line and format it as a professional email.";
                break;
            case 'blog_introduction':
                $prompt .= " Focus on creating a compelling hook that draws readers in.";
                break;
            case 'blog_outline':
                $prompt .= " Provide a structured outline with main points and subpoints.";
                break;
            case 'product_description':
                $prompt .= " Highlight key features, benefits, and include persuasive selling points.";
                break;
        }

        return $prompt;
    }

    /**
     * Call OpenAI API for content generation
     */
    private function call_openai_content_api($prompt, $api_key, $wordcount) {
        $url = 'https://api.openai.com/v1/chat/completions';

        // Adjust max_tokens based on word count (roughly 1.3 tokens per word)
        $max_tokens = min(4000, intval($wordcount * 1.5));

        $data = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a professional content writer. Create high-quality, engaging content that matches the requested tone and style. Format the output with proper HTML tags for readability.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'max_tokens' => $max_tokens,
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
            error_log('AAAI AI Writing Tool - API Error: ' . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('AAAI AI Writing Tool - API Response Code: ' . $response_code);
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('AAAI AI Writing Tool - JSON Decode Error: ' . json_last_error_msg());
            return false;
        }

        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        if (isset($data['error'])) {
            error_log('AAAI AI Writing Tool - OpenAI Error: ' . $data['error']['message']);
        }

        return false;
    }

    /**
     * Call Anthropic Claude API for content generation
     */
    private function call_anthropic_api($prompt, $api_key, $wordcount) {
        // Placeholder for Anthropic API integration
        // You can implement this if you have Claude API access
        return "This is a sample generated content for: " . $prompt . " (Anthropic API not implemented yet)";
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
