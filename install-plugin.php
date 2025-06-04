<?php
/**
 * WordPress Plugin Installation Helper
 * 
 * This script helps install the AI Outline Generator plugin correctly
 * Run this from your WordPress root directory
 */

// Check if we're in WordPress environment
if (!file_exists('wp-config.php')) {
    die("❌ Error: This script must be run from your WordPress root directory.\n");
}

echo "🚀 AI Outline Generator Plugin Installation Helper\n";
echo "================================================\n\n";

// Define paths
$wp_root = getcwd();
$plugins_dir = $wp_root . '/wp-content/plugins';
$plugin_dir = $plugins_dir . '/ai-outline-generator';
$source_dir = __DIR__;

echo "📁 WordPress Root: $wp_root\n";
echo "📁 Plugins Directory: $plugins_dir\n";
echo "📁 Target Plugin Directory: $plugin_dir\n";
echo "📁 Source Directory: $source_dir\n\n";

// Check if plugins directory exists
if (!is_dir($plugins_dir)) {
    die("❌ Error: WordPress plugins directory not found at $plugins_dir\n");
}

// Check if plugin directory already exists
if (is_dir($plugin_dir)) {
    echo "⚠️  Warning: Plugin directory already exists. Removing old version...\n";
    removeDirectory($plugin_dir);
}

// Create plugin directory
echo "📂 Creating plugin directory...\n";
if (!mkdir($plugin_dir, 0755, true)) {
    die("❌ Error: Could not create plugin directory\n");
}

// Files to copy
$files_to_copy = [
    'ai-outline-generator.php',
    'uninstall.php',
    'README.md',
    'TROUBLESHOOTING.md',
    'QUICK_SETUP.md',
    'install.md'
];

// Directories to copy
$dirs_to_copy = [
    'templates',
    'assets',
    'languages'
];

// Copy main files
echo "📄 Copying main files...\n";
foreach ($files_to_copy as $file) {
    $source_file = $source_dir . '/' . $file;
    $target_file = $plugin_dir . '/' . $file;
    
    if (file_exists($source_file)) {
        if (copy($source_file, $target_file)) {
            echo "  ✅ Copied: $file\n";
        } else {
            echo "  ❌ Failed to copy: $file\n";
        }
    } else {
        echo "  ⚠️  File not found: $file\n";
    }
}

// Copy directories
echo "\n📁 Copying directories...\n";
foreach ($dirs_to_copy as $dir) {
    $source_dir_path = $source_dir . '/' . $dir;
    $target_dir_path = $plugin_dir . '/' . $dir;
    
    if (is_dir($source_dir_path)) {
        if (copyDirectory($source_dir_path, $target_dir_path)) {
            echo "  ✅ Copied directory: $dir\n";
        } else {
            echo "  ❌ Failed to copy directory: $dir\n";
        }
    } else {
        echo "  ⚠️  Directory not found: $dir\n";
    }
}

// Set permissions
echo "\n🔒 Setting file permissions...\n";
setPermissions($plugin_dir);

// Verify installation
echo "\n🔍 Verifying installation...\n";
$verification_passed = verifyInstallation($plugin_dir);

if ($verification_passed) {
    echo "\n🎉 SUCCESS! Plugin installed successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Go to WordPress Admin → Plugins\n";
    echo "2. Find 'AI Outline Generator' and click 'Activate'\n";
    echo "3. Go to Settings → AI Outline Generator\n";
    echo "4. Add your OpenAI API key\n";
    echo "5. Test the plugin with shortcode: [ai_outline_generator]\n\n";
} else {
    echo "\n❌ Installation completed with some issues. Check the verification results above.\n";
}

/**
 * Copy directory recursively
 */
function copyDirectory($source, $destination) {
    if (!is_dir($source)) {
        return false;
    }
    
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        
        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            copy($item, $target);
        }
    }
    
    return true;
}

/**
 * Remove directory recursively
 */
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            rmdir($item->getRealPath());
        } else {
            unlink($item->getRealPath());
        }
    }
    
    return rmdir($dir);
}

/**
 * Set proper file permissions
 */
function setPermissions($plugin_dir) {
    // Set directory permissions
    $directories = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($plugin_dir),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($directories as $item) {
        if ($item->isDir()) {
            chmod($item->getRealPath(), 0755);
        } else {
            chmod($item->getRealPath(), 0644);
        }
    }
    
    echo "  ✅ File permissions set\n";
}

/**
 * Verify installation
 */
function verifyInstallation($plugin_dir) {
    $required_files = [
        'ai-outline-generator.php',
        'templates/admin-page.php',
        'templates/frontend-form.php',
        'assets/css/style.css',
        'assets/js/script.js'
    ];
    
    $all_good = true;
    
    foreach ($required_files as $file) {
        $file_path = $plugin_dir . '/' . $file;
        if (file_exists($file_path)) {
            echo "  ✅ $file exists\n";
        } else {
            echo "  ❌ $file missing\n";
            $all_good = false;
        }
    }
    
    // Check main plugin file header
    $main_file = $plugin_dir . '/ai-outline-generator.php';
    if (file_exists($main_file)) {
        $content = file_get_contents($main_file);
        if (strpos($content, 'Plugin Name: AI Outline Generator') !== false) {
            echo "  ✅ Plugin header found\n";
        } else {
            echo "  ❌ Plugin header missing or invalid\n";
            $all_good = false;
        }
    }
    
    return $all_good;
}

echo "\n📋 Installation log saved. Check above for any issues.\n";
?>
