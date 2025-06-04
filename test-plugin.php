<?php
/**
 * Test file for AI Outline Generator Plugin
 * 
 * This file can be used to test the plugin functionality
 * Run this in a WordPress environment to verify everything works
 */

// Only run if WordPress is loaded
if (!defined('ABSPATH')) {
    die('WordPress not loaded');
}

/**
 * Test the AI Outline Generator functionality
 */
function test_ai_outline_generator() {
    echo "<h2>AI Outline Generator Plugin Test</h2>";
    
    // Test 1: Check if plugin is loaded
    echo "<h3>Test 1: Plugin Loading</h3>";
    if (class_exists('AI_Outline_Generator')) {
        echo "✅ Plugin class loaded successfully<br>";
    } else {
        echo "❌ Plugin class not found<br>";
        return;
    }
    
    // Test 2: Check if shortcode is registered
    echo "<h3>Test 2: Shortcode Registration</h3>";
    if (shortcode_exists('ai_outline_generator')) {
        echo "✅ Shortcode [ai_outline_generator] registered<br>";
    } else {
        echo "❌ Shortcode not registered<br>";
    }
    
    // Test 3: Check if assets are enqueued
    echo "<h3>Test 3: Asset Files</h3>";
    $plugin_url = plugin_dir_url(__FILE__);
    $css_file = $plugin_url . 'assets/css/style.css';
    $js_file = $plugin_url . 'assets/js/script.js';
    
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/style.css')) {
        echo "✅ CSS file exists<br>";
    } else {
        echo "❌ CSS file missing<br>";
    }
    
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/js/script.js')) {
        echo "✅ JavaScript file exists<br>";
    } else {
        echo "❌ JavaScript file missing<br>";
    }
    
    // Test 4: Check database table creation
    echo "<h3>Test 4: Database</h3>";
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_outline_generator_logs';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    
    if ($table_exists) {
        echo "✅ Database table created<br>";
    } else {
        echo "❌ Database table not found<br>";
    }
    
    // Test 5: Check options
    echo "<h3>Test 5: Plugin Options</h3>";
    $api_provider = get_option('ai_outline_generator_api_provider');
    if ($api_provider) {
        echo "✅ Default options set (Provider: $api_provider)<br>";
    } else {
        echo "❌ Default options not set<br>";
    }
    
    // Test 6: Render shortcode
    echo "<h3>Test 6: Shortcode Output</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo do_shortcode('[ai_outline_generator show_samples="false"]');
    echo "</div>";
    
    echo "<h3>Test Complete</h3>";
    echo "<p>If you see the form above and all tests pass, the plugin is working correctly!</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to Settings → AI Outline Generator</li>";
    echo "<li>Add your OpenAI API key</li>";
    echo "<li>Test the outline generation functionality</li>";
    echo "</ol>";
}

// Add admin page for testing
function add_test_page() {
    add_management_page(
        'AI Outline Generator Test',
        'AI Outline Test',
        'manage_options',
        'ai-outline-test',
        'test_ai_outline_generator'
    );
}

// Only add test page if in admin and plugin is active
if (is_admin() && class_exists('AI_Outline_Generator')) {
    add_action('admin_menu', 'add_test_page');
}

/**
 * Sample data for testing
 */
function get_sample_test_data() {
    return array(
        'content' => 'How to create effective content marketing strategies that drive engagement and conversions',
        'content_type' => 'blog-post',
        'sections' => 5,
        'language' => 'English'
    );
}

/**
 * Test AJAX functionality (for developers)
 */
function test_ajax_functionality() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $sample_data = get_sample_test_data();
    
    echo "<h4>Sample AJAX Test Data:</h4>";
    echo "<pre>" . json_encode($sample_data, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<p>Use this data to test the AJAX functionality manually.</p>";
}

// Add test data display to admin
if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'ai-outline-test') {
    add_action('admin_footer', 'test_ajax_functionality');
}
