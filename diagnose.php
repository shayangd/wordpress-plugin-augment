<?php
/**
 * WordPress Plugin Diagnostic Tool
 * 
 * This script helps diagnose why the AI Outline Generator plugin isn't showing up
 * Place this file in your WordPress root directory and run it
 */

// Check if we're in WordPress environment
if (!file_exists('wp-config.php')) {
    die("❌ Error: This script must be run from your WordPress root directory.\n");
}

echo "🔍 AI Outline Generator Plugin Diagnostic Tool\n";
echo "=============================================\n\n";

// Load WordPress
define('WP_USE_THEMES', false);
require_once('wp-config.php');
require_once('wp-load.php');

// Check WordPress version
echo "📊 WordPress Information:\n";
echo "  Version: " . get_bloginfo('version') . "\n";
echo "  PHP Version: " . phpversion() . "\n";
echo "  MySQL Version: " . $GLOBALS['wpdb']->db_version() . "\n\n";

// Check plugins directory
$plugins_dir = WP_CONTENT_DIR . '/plugins';
$plugin_dir = $plugins_dir . '/ai-outline-generator';

echo "📁 Directory Information:\n";
echo "  WordPress Root: " . ABSPATH . "\n";
echo "  Plugins Directory: $plugins_dir\n";
echo "  Plugin Directory: $plugin_dir\n";
echo "  Plugins Dir Exists: " . (is_dir($plugins_dir) ? "✅ Yes" : "❌ No") . "\n";
echo "  Plugin Dir Exists: " . (is_dir($plugin_dir) ? "✅ Yes" : "❌ No") . "\n\n";

// Check if plugin directory exists
if (!is_dir($plugin_dir)) {
    echo "❌ ISSUE FOUND: Plugin directory doesn't exist!\n";
    echo "   Expected location: $plugin_dir\n\n";
    
    // Look for plugin files in current directory
    echo "🔍 Looking for plugin files in current directory...\n";
    $current_files = scandir('.');
    $plugin_files_found = [];
    
    foreach ($current_files as $file) {
        if (strpos($file, 'ai-outline') !== false || $file === 'ai-outline-generator.php') {
            $plugin_files_found[] = $file;
        }
    }
    
    if (!empty($plugin_files_found)) {
        echo "  ✅ Found plugin files in current directory:\n";
        foreach ($plugin_files_found as $file) {
            echo "    - $file\n";
        }
        echo "\n  💡 SOLUTION: Run the install-plugin.php script to move files to correct location.\n\n";
    } else {
        echo "  ❌ No plugin files found in current directory.\n\n";
    }
    
    return;
}

// Check plugin files
echo "📄 Plugin Files Check:\n";
$required_files = [
    'ai-outline-generator.php' => 'Main plugin file',
    'templates/admin-page.php' => 'Admin page template',
    'templates/frontend-form.php' => 'Frontend form template',
    'assets/css/style.css' => 'Frontend styles',
    'assets/js/script.js' => 'Frontend JavaScript',
    'uninstall.php' => 'Uninstall script'
];

$missing_files = [];
foreach ($required_files as $file => $description) {
    $file_path = $plugin_dir . '/' . $file;
    if (file_exists($file_path)) {
        echo "  ✅ $file ($description)\n";
    } else {
        echo "  ❌ $file ($description) - MISSING\n";
        $missing_files[] = $file;
    }
}

if (!empty($missing_files)) {
    echo "\n❌ ISSUE FOUND: Missing plugin files!\n";
    echo "   Missing files: " . implode(', ', $missing_files) . "\n\n";
}

// Check main plugin file
$main_file = $plugin_dir . '/ai-outline-generator.php';
if (file_exists($main_file)) {
    echo "\n📋 Main Plugin File Analysis:\n";
    
    $content = file_get_contents($main_file);
    $lines = explode("\n", $content);
    
    // Check for PHP opening tag
    if (trim($lines[0]) === '<?php') {
        echo "  ✅ PHP opening tag found\n";
    } else {
        echo "  ❌ PHP opening tag missing or incorrect\n";
        echo "    First line: " . trim($lines[0]) . "\n";
    }
    
    // Check for plugin header
    $header_found = false;
    $plugin_name_found = false;
    
    for ($i = 0; $i < min(20, count($lines)); $i++) {
        if (strpos($lines[$i], 'Plugin Name:') !== false) {
            $plugin_name_found = true;
            echo "  ✅ Plugin Name header found: " . trim($lines[$i]) . "\n";
        }
        if (strpos($lines[$i], '*/') !== false && $plugin_name_found) {
            $header_found = true;
            break;
        }
    }
    
    if (!$header_found) {
        echo "  ❌ Plugin header not found or incomplete\n";
    }
    
    // Check file size
    $file_size = filesize($main_file);
    echo "  📏 File size: " . number_format($file_size) . " bytes\n";
    
    if ($file_size < 1000) {
        echo "  ⚠️  Warning: File seems too small, might be incomplete\n";
    }
}

// Check WordPress plugin recognition
echo "\n🔌 WordPress Plugin Recognition:\n";

// Get all plugins
if (!function_exists('get_plugins')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

$all_plugins = get_plugins();
$plugin_found = false;

foreach ($all_plugins as $plugin_file => $plugin_data) {
    if (strpos($plugin_file, 'ai-outline-generator') !== false) {
        $plugin_found = true;
        echo "  ✅ Plugin recognized by WordPress\n";
        echo "    File: $plugin_file\n";
        echo "    Name: " . $plugin_data['Name'] . "\n";
        echo "    Version: " . $plugin_data['Version'] . "\n";
        echo "    Active: " . (is_plugin_active($plugin_file) ? "Yes" : "No") . "\n";
        break;
    }
}

if (!$plugin_found) {
    echo "  ❌ Plugin NOT recognized by WordPress\n";
    echo "    This means WordPress can't see the plugin in the plugins list\n";
}

// Check file permissions
echo "\n🔒 File Permissions Check:\n";
$plugin_dir_perms = substr(sprintf('%o', fileperms($plugin_dir)), -4);
echo "  Plugin directory permissions: $plugin_dir_perms\n";

if ($plugin_dir_perms !== '0755') {
    echo "  ⚠️  Warning: Directory permissions should be 0755\n";
}

if (file_exists($main_file)) {
    $main_file_perms = substr(sprintf('%o', fileperms($main_file)), -4);
    echo "  Main file permissions: $main_file_perms\n";
    
    if ($main_file_perms !== '0644') {
        echo "  ⚠️  Warning: File permissions should be 0644\n";
    }
}

// Check for PHP errors
echo "\n🐛 PHP Error Check:\n";
if (file_exists($main_file)) {
    $php_check = shell_exec("php -l " . escapeshellarg($main_file) . " 2>&1");
    if (strpos($php_check, 'No syntax errors') !== false) {
        echo "  ✅ No PHP syntax errors found\n";
    } else {
        echo "  ❌ PHP syntax errors found:\n";
        echo "    $php_check\n";
    }
}

// Summary and recommendations
echo "\n📋 SUMMARY AND RECOMMENDATIONS:\n";
echo "================================\n";

if (!is_dir($plugin_dir)) {
    echo "❌ CRITICAL: Plugin directory missing\n";
    echo "   → Run install-plugin.php to install correctly\n\n";
} elseif (!empty($missing_files)) {
    echo "❌ CRITICAL: Missing plugin files\n";
    echo "   → Re-upload all plugin files\n\n";
} elseif (!$plugin_found) {
    echo "❌ CRITICAL: WordPress doesn't recognize the plugin\n";
    echo "   → Check plugin header in main file\n";
    echo "   → Verify file permissions\n";
    echo "   → Check for PHP syntax errors\n\n";
} else {
    echo "✅ Plugin appears to be installed correctly!\n";
    echo "   → Go to WordPress Admin → Plugins to activate\n\n";
}

echo "🔧 Quick Fixes to Try:\n";
echo "1. Run: php install-plugin.php (from WordPress root)\n";
echo "2. Check WordPress Admin → Plugins page\n";
echo "3. Refresh the plugins page\n";
echo "4. Clear any caching plugins\n";
echo "5. Check wp-content/debug.log for errors\n\n";

echo "📞 If issues persist:\n";
echo "1. Enable WordPress debug mode\n";
echo "2. Check server error logs\n";
echo "3. Try deactivating other plugins\n";
echo "4. Contact hosting provider about plugin restrictions\n\n";
?>
