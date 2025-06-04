<?php
/**
 * AI Outline Generator Debug Tool
 * 
 * Add this to your WordPress site to debug the plugin issues
 * Access via: yoursite.com/wp-content/plugins/ai-outline-generator/debug-plugin.php
 */

// Load WordPress
$wp_load_paths = [
    '../../../wp-load.php',
    '../../../../wp-load.php',
    '../../../../../wp-load.php'
];

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('Could not load WordPress. Please run this from the plugin directory.');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>AI Outline Generator Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status-good { color: green; font-weight: bold; }
        .status-bad { color: red; font-weight: bold; }
        .status-warning { color: orange; font-weight: bold; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .debug-section h3 { margin-top: 0; color: #333; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .test-form { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .test-form input, .test-form select, .test-form textarea { margin: 5px; padding: 8px; }
        .test-form button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        .test-form button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>🔍 AI Outline Generator Debug Tool</h1>
    
    <?php
    // Check plugin status
    echo '<div class="debug-section">';
    echo '<h3>📊 Plugin Status</h3>';
    
    // Check if plugin class exists
    if (class_exists('AI_Outline_Generator')) {
        echo '<p class="status-good">✅ Plugin class loaded successfully</p>';
    } else {
        echo '<p class="status-bad">❌ Plugin class not found - plugin may not be activated</p>';
    }
    
    // Check if shortcode exists
    if (shortcode_exists('ai_outline_generator')) {
        echo '<p class="status-good">✅ Shortcode [ai_outline_generator] registered</p>';
    } else {
        echo '<p class="status-bad">❌ Shortcode not registered</p>';
    }
    
    // Check API configuration
    $api_key = get_option('ai_outline_generator_api_key', '');
    $api_provider = get_option('ai_outline_generator_api_provider', 'openai');
    
    echo '<h4>🔑 API Configuration</h4>';
    echo '<p><strong>Provider:</strong> ' . esc_html($api_provider) . '</p>';
    
    if (empty($api_key)) {
        echo '<p class="status-bad">❌ No API key configured</p>';
        echo '<p><strong>Solution:</strong> Go to WordPress Admin → Settings → AI Outline Generator and add your OpenAI API key</p>';
    } else {
        echo '<p class="status-good">✅ API key configured</p>';
        echo '<p><strong>Key length:</strong> ' . strlen($api_key) . ' characters</p>';
        echo '<p><strong>Key format:</strong> ' . (preg_match('/^sk-[a-zA-Z0-9]{48}$/', $api_key) ? 'Valid' : 'Invalid') . '</p>';
        
        if (!preg_match('/^sk-[a-zA-Z0-9]{48}$/', $api_key)) {
            echo '<p class="status-warning">⚠️ API key format appears invalid. OpenAI keys should start with "sk-" and be 51 characters long.</p>';
        }
    }
    
    echo '</div>';
    
    // Check file structure
    echo '<div class="debug-section">';
    echo '<h3>📁 File Structure</h3>';
    
    $plugin_dir = plugin_dir_path(__FILE__);
    $required_files = [
        'ai-outline-generator.php' => 'Main plugin file',
        'templates/admin-page.php' => 'Admin page template',
        'templates/frontend-form.php' => 'Frontend form template',
        'assets/css/style.css' => 'Frontend styles',
        'assets/js/script.js' => 'Frontend JavaScript'
    ];
    
    foreach ($required_files as $file => $description) {
        $file_path = $plugin_dir . $file;
        if (file_exists($file_path)) {
            echo '<p class="status-good">✅ ' . $file . ' (' . $description . ')</p>';
        } else {
            echo '<p class="status-bad">❌ ' . $file . ' (' . $description . ') - MISSING</p>';
        }
    }
    
    echo '</div>';
    
    // WordPress environment info
    echo '<div class="debug-section">';
    echo '<h3>🌐 WordPress Environment</h3>';
    echo '<p><strong>WordPress Version:</strong> ' . get_bloginfo('version') . '</p>';
    echo '<p><strong>PHP Version:</strong> ' . phpversion() . '</p>';
    echo '<p><strong>MySQL Version:</strong> ' . $GLOBALS['wpdb']->db_version() . '</p>';
    echo '<p><strong>WordPress Debug:</strong> ' . (WP_DEBUG ? 'Enabled' : 'Disabled') . '</p>';
    echo '<p><strong>WordPress Debug Log:</strong> ' . (WP_DEBUG_LOG ? 'Enabled' : 'Disabled') . '</p>';
    echo '</div>';
    
    // Test API connection (if API key is configured)
    if (!empty($api_key) && preg_match('/^sk-[a-zA-Z0-9]{48}$/', $api_key)) {
        echo '<div class="debug-section">';
        echo '<h3>🧪 API Connection Test</h3>';
        
        if (isset($_POST['test_api'])) {
            echo '<h4>Test Results:</h4>';
            
            $test_content = sanitize_text_field($_POST['test_content']);
            $test_type = sanitize_text_field($_POST['test_type']);
            $test_sections = intval($_POST['test_sections']);
            $test_language = sanitize_text_field($_POST['test_language']);
            
            // Create a test instance
            if (class_exists('AI_Outline_Generator')) {
                $plugin_instance = new AI_Outline_Generator();
                
                // Use reflection to access private method
                $reflection = new ReflectionClass($plugin_instance);
                $method = $reflection->getMethod('generate_ai_outline');
                $method->setAccessible(true);
                
                $start_time = microtime(true);
                $result = $method->invoke($plugin_instance, $test_content, $test_type, $test_sections, $test_language);
                $end_time = microtime(true);
                
                if ($result) {
                    echo '<p class="status-good">✅ API test successful!</p>';
                    echo '<p><strong>Response time:</strong> ' . round(($end_time - $start_time), 2) . ' seconds</p>';
                    echo '<h5>Generated Outline:</h5>';
                    echo '<div style="border: 1px solid #ccc; padding: 10px; background: white;">' . $result . '</div>';
                } else {
                    echo '<p class="status-bad">❌ API test failed</p>';
                    
                    // Check for stored error
                    $last_error = get_transient('ai_outline_generator_last_error');
                    if ($last_error) {
                        echo '<p><strong>Error:</strong> ' . esc_html($last_error) . '</p>';
                        delete_transient('ai_outline_generator_last_error');
                    }
                }
            }
        } else {
            echo '<div class="test-form">';
            echo '<h4>Test API Connection</h4>';
            echo '<form method="post">';
            echo '<p><label>Test Content:</label><br>';
            echo '<textarea name="test_content" rows="3" cols="50" placeholder="Enter test content here...">How to create effective content marketing strategies</textarea></p>';
            echo '<p><label>Content Type:</label> ';
            echo '<select name="test_type">';
            echo '<option value="blog-post">Blog Post</option>';
            echo '<option value="article">Article</option>';
            echo '<option value="essay">Essay</option>';
            echo '</select></p>';
            echo '<p><label>Sections:</label> ';
            echo '<select name="test_sections">';
            echo '<option value="3">3 Sections</option>';
            echo '<option value="4">4 Sections</option>';
            echo '<option value="5">5 Sections</option>';
            echo '</select></p>';
            echo '<p><label>Language:</label> ';
            echo '<select name="test_language">';
            echo '<option value="English">English</option>';
            echo '<option value="Spanish">Spanish</option>';
            echo '</select></p>';
            echo '<button type="submit" name="test_api">Test API Connection</button>';
            echo '</form>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    // Recent error logs
    echo '<div class="debug-section">';
    echo '<h3>📋 Recent Error Logs</h3>';
    
    if (WP_DEBUG_LOG && file_exists(WP_CONTENT_DIR . '/debug.log')) {
        $log_file = WP_CONTENT_DIR . '/debug.log';
        $log_content = file_get_contents($log_file);
        $log_lines = explode("\n", $log_content);
        
        // Filter for plugin-related errors
        $plugin_errors = array_filter($log_lines, function($line) {
            return strpos($line, 'AI Outline Generator') !== false;
        });
        
        if (!empty($plugin_errors)) {
            echo '<h4>Plugin-related errors:</h4>';
            echo '<pre>' . esc_html(implode("\n", array_slice($plugin_errors, -10))) . '</pre>';
        } else {
            echo '<p class="status-good">✅ No plugin-related errors found in debug log</p>';
        }
    } else {
        echo '<p class="status-warning">⚠️ WordPress debug logging not enabled</p>';
        echo '<p>To enable debug logging, add these lines to wp-config.php:</p>';
        echo '<pre>define(\'WP_DEBUG\', true);
define(\'WP_DEBUG_LOG\', true);
define(\'WP_DEBUG_DISPLAY\', false);</pre>';
    }
    
    echo '</div>';
    
    // Quick fixes
    echo '<div class="debug-section">';
    echo '<h3>🔧 Quick Fixes</h3>';
    echo '<h4>Common Solutions:</h4>';
    echo '<ol>';
    echo '<li><strong>No API key:</strong> Go to WordPress Admin → Settings → AI Outline Generator</li>';
    echo '<li><strong>Invalid API key:</strong> Get a new key from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a></li>';
    echo '<li><strong>Insufficient credits:</strong> Check your <a href="https://platform.openai.com/account/billing" target="_blank">OpenAI billing</a></li>';
    echo '<li><strong>Network issues:</strong> Check your server\'s outbound connections</li>';
    echo '<li><strong>Plugin conflicts:</strong> Temporarily deactivate other plugins</li>';
    echo '</ol>';
    echo '</div>';
    ?>
    
    <div class="debug-section">
        <h3>📞 Support Information</h3>
        <p>If you're still having issues after checking the above:</p>
        <ol>
            <li>Copy the information from this debug page</li>
            <li>Check the WordPress debug log for detailed error messages</li>
            <li>Verify your OpenAI API key works in other applications</li>
            <li>Contact your hosting provider about API connectivity</li>
        </ol>
    </div>
</body>
</html>
