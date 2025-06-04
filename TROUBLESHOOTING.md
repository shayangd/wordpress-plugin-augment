# 🔧 Troubleshooting: Plugin Not Showing in WordPress

## 📋 Common Issues & Solutions

### Issue 1: Plugin Not Appearing in Plugins List

**Possible Causes:**
- Incorrect folder structure
- Missing plugin header
- File permissions
- WordPress not recognizing the plugin

**Solutions:**

#### ✅ Check Folder Structure
Your WordPress plugins folder should look like this:
```
/wp-content/plugins/ai-outline-generator/
├── ai-outline-generator.php     # Main plugin file
├── templates/
├── assets/
└── other files...
```

**NOT like this:**
```
/wp-content/plugins/wordpress-plugin-augment/ai-outline-generator.php
```

#### ✅ Verify Plugin Header
The main file `ai-outline-generator.php` should start with:
```php
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
```

### Issue 2: File Permissions

**Check Permissions:**
```bash
# For directories
chmod 755 /wp-content/plugins/ai-outline-generator/
chmod 755 /wp-content/plugins/ai-outline-generator/templates/
chmod 755 /wp-content/plugins/ai-outline-generator/assets/

# For files
chmod 644 /wp-content/plugins/ai-outline-generator/*.php
chmod 644 /wp-content/plugins/ai-outline-generator/templates/*.php
chmod 644 /wp-content/plugins/ai-outline-generator/assets/css/*.css
chmod 644 /wp-content/plugins/ai-outline-generator/assets/js/*.js
```

### Issue 3: WordPress Environment

**Check WordPress Requirements:**
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

**Enable Debug Mode:**
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `/wp-content/debug.log` for errors.

## 🚀 Step-by-Step Installation

### Method 1: Manual Upload (Recommended)

1. **Create Plugin Folder**
   ```bash
   mkdir /path/to/wordpress/wp-content/plugins/ai-outline-generator
   ```

2. **Copy All Files**
   Copy these files to the new folder:
   - `ai-outline-generator.php`
   - `templates/` folder
   - `assets/` folder
   - `uninstall.php`
   - `README.md`

3. **Set Permissions**
   ```bash
   chmod -R 755 /path/to/wordpress/wp-content/plugins/ai-outline-generator
   find /path/to/wordpress/wp-content/plugins/ai-outline-generator -type f -exec chmod 644 {} \;
   ```

4. **Refresh WordPress Admin**
   - Go to WordPress Admin → Plugins
   - Refresh the page
   - Look for "AI Outline Generator"

### Method 2: ZIP Upload

1. **Create ZIP File**
   ```bash
   cd /path/to/plugin/files
   zip -r ai-outline-generator.zip ai-outline-generator.php templates/ assets/ uninstall.php
   ```

2. **Upload via WordPress**
   - WordPress Admin → Plugins → Add New
   - Upload Plugin → Choose File
   - Select the ZIP file
   - Install Now

### Method 3: FTP Upload

1. **Connect via FTP**
   - Use FileZilla, WinSCP, or similar
   - Connect to your hosting account

2. **Navigate to Plugins Folder**
   ```
   /public_html/wp-content/plugins/
   ```

3. **Create New Folder**
   ```
   ai-outline-generator/
   ```

4. **Upload All Files**
   Upload all plugin files to this folder

## 🔍 Verification Steps

### Step 1: Check Plugin Files
Verify these files exist in `/wp-content/plugins/ai-outline-generator/`:
- ✅ `ai-outline-generator.php`
- ✅ `templates/admin-page.php`
- ✅ `templates/frontend-form.php`
- ✅ `assets/css/style.css`
- ✅ `assets/js/script.js`

### Step 2: Test Plugin Recognition
1. Go to WordPress Admin → Plugins
2. Look for "AI Outline Generator" in the list
3. If not visible, check debug logs

### Step 3: Activate Plugin
1. Click "Activate" next to the plugin
2. Check for any error messages
3. Look for "Settings → AI Outline Generator" in admin menu

### Step 4: Test Shortcode
1. Create a new page/post
2. Add shortcode: `[ai_outline_generator]`
3. Preview/publish the page
4. Check if the form appears

## 🐛 Common Error Messages

### "Plugin could not be activated because it triggered a fatal error"

**Cause:** PHP syntax error or missing dependencies

**Solution:**
1. Check PHP version (needs 7.4+)
2. Enable debug mode
3. Check debug.log for specific error
4. Verify all files uploaded correctly

### "The plugin does not have a valid header"

**Cause:** Missing or malformed plugin header

**Solution:**
1. Verify `ai-outline-generator.php` starts with proper header
2. Check for any characters before `<?php`
3. Ensure no BOM (Byte Order Mark) in file

### "Plugin file does not exist"

**Cause:** Incorrect file structure

**Solution:**
1. Ensure main file is named `ai-outline-generator.php`
2. Check folder structure matches requirements
3. Verify file permissions

## 🔧 Quick Fixes

### Fix 1: Rename Plugin Folder
If your folder is named differently:
```bash
mv wordpress-plugin-augment ai-outline-generator
```

### Fix 2: Check Main File Name
Ensure the main file is exactly:
```
ai-outline-generator.php
```

### Fix 3: Clear WordPress Cache
If using caching plugins:
1. Clear all caches
2. Refresh plugins page
3. Try activating again

### Fix 4: Deactivate Other Plugins
Temporarily deactivate other plugins to check for conflicts:
1. Deactivate all plugins
2. Try activating AI Outline Generator
3. Reactivate other plugins one by one

## 📞 Still Having Issues?

### Check These:

1. **Hosting Restrictions**
   - Some hosts block plugin uploads
   - Check with hosting provider

2. **WordPress Multisite**
   - Network admin may need to enable plugin
   - Check network settings

3. **File Corruption**
   - Re-download plugin files
   - Upload again

4. **PHP Memory Limit**
   - Increase memory limit in wp-config.php:
   ```php
   ini_set('memory_limit', '256M');
   ```

### Debug Information to Collect:

- WordPress version
- PHP version
- Active theme
- Other active plugins
- Error messages from debug.log
- Server error logs

With this information, we can provide more specific help!
