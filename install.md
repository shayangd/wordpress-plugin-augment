# Installation Guide for AI Outline Generator WordPress Plugin

## Quick Installation

### Method 1: Direct Upload (Recommended)

1. **Download the Plugin**
   - Download all plugin files from this repository
   - Ensure you have the complete folder structure

2. **Upload to WordPress**
   ```
   /wp-content/plugins/ai-outline-generator/
   ```

3. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "AI Outline Generator" 
   - Click "Activate"

4. **Configure Settings**
   - Go to Settings → AI Outline Generator
   - Enter your OpenAI API key
   - Save settings

### Method 2: FTP Upload

1. **Connect via FTP**
   - Use your preferred FTP client
   - Connect to your WordPress hosting

2. **Upload Files**
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `ai-outline-generator` folder

3. **Set Permissions**
   ```bash
   chmod 755 ai-outline-generator/
   chmod 644 ai-outline-generator/*.php
   chmod 644 ai-outline-generator/assets/css/*.css
   chmod 644 ai-outline-generator/assets/js/*.js
   ```

4. **Activate via WordPress Admin**

## File Structure Verification

After installation, verify this structure exists:

```
wp-content/plugins/ai-outline-generator/
├── ai-outline-generator.php     # Main plugin file
├── uninstall.php               # Cleanup script
├── test-plugin.php             # Test functionality
├── README.md                   # Documentation
├── install.md                  # This file
├── templates/
│   ├── admin-page.php          # Admin settings
│   └── frontend-form.php       # Frontend form
├── assets/
│   ├── css/
│   │   ├── style.css          # Frontend styles
│   │   └── admin.css          # Admin styles
│   ├── js/
│   │   └── script.js          # Frontend JavaScript
│   └── images/                 # (Empty, for future use)
└── languages/                  # (For translations)
```

## Configuration Steps

### 1. Get OpenAI API Key

1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign up or log in
3. Create a new API key
4. Copy the key (starts with `sk-`)

### 2. Configure Plugin

1. **WordPress Admin → Settings → AI Outline Generator**
2. **AI Provider**: Select "OpenAI" 
3. **API Key**: Paste your OpenAI API key
4. **Save Changes**

### 3. Test Installation

1. **Go to Tools → AI Outline Test** (if test file is included)
2. **Or create a test page with shortcode:**
   ```
   [ai_outline_generator]
   ```

## Usage Examples

### Basic Usage
```
[ai_outline_generator]
```

### Without Sample Cards
```
[ai_outline_generator show_samples="false"]
```

### Custom Character Limit
```
[ai_outline_generator max_chars="500"]
```

### Combined Parameters
```
[ai_outline_generator show_samples="false" max_chars="1500"]
```

## Troubleshooting

### Common Issues

1. **Plugin Not Appearing**
   - Check file permissions
   - Verify folder structure
   - Check WordPress error logs

2. **Styles Not Loading**
   - Clear browser cache
   - Check theme compatibility
   - Verify CSS file exists

3. **JavaScript Errors**
   - Check browser console
   - Disable other plugins temporarily
   - Verify jQuery is loaded

4. **API Errors**
   - Verify API key is correct
   - Check OpenAI account credits
   - Test API key independently

### Debug Mode

Enable WordPress debug mode in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check logs in `/wp-content/debug.log`

### File Permissions

If you encounter permission issues:

```bash
# For directories
find /wp-content/plugins/ai-outline-generator/ -type d -exec chmod 755 {} \;

# For files
find /wp-content/plugins/ai-outline-generator/ -type f -exec chmod 644 {} \;
```

## Security Notes

- **API Key Storage**: Keys are stored securely in WordPress options
- **Input Validation**: All user inputs are sanitized
- **Nonce Protection**: AJAX requests use WordPress nonces
- **Capability Checks**: Admin functions require proper permissions

## Performance Considerations

- **Caching**: Consider using a caching plugin
- **API Limits**: Monitor OpenAI usage and costs
- **Database**: Plugin creates minimal database impact
- **Assets**: CSS/JS files are minified for production

## Support

If you encounter issues:

1. **Check Requirements**: WordPress 5.0+, PHP 7.4+
2. **Review Logs**: Check WordPress debug logs
3. **Test Environment**: Try on a staging site first
4. **Plugin Conflicts**: Deactivate other plugins temporarily

## Next Steps

After successful installation:

1. **Create Content**: Add the shortcode to pages/posts
2. **Customize Styling**: Modify CSS if needed for your theme
3. **Monitor Usage**: Keep track of API usage and costs
4. **User Training**: Train content creators on how to use the tool

## Uninstallation

To completely remove the plugin:

1. **Deactivate** the plugin first
2. **Delete** from Plugins page
3. The `uninstall.php` script will clean up all data automatically

This ensures no leftover data in your WordPress database.
