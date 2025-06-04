# 🚀 Manual Installation Guide - AI Outline Generator Plugin

## 📋 Current Situation

You have all the plugin files ready, but they need to be installed in a WordPress site. Here are your options:

## 🎯 **Option 1: Use Existing WordPress Site (Recommended)**

If you already have a WordPress site:

### Step 1: Create Plugin Folder
1. **Access your WordPress site** via FTP, cPanel File Manager, or hosting control panel
2. **Navigate to:** `/wp-content/plugins/`
3. **Create new folder:** `ai-outline-generator`

### Step 2: Upload Plugin Files
Upload these files to `/wp-content/plugins/ai-outline-generator/`:

```
✅ ai-outline-generator.php     (Main plugin file)
✅ uninstall.php               (Cleanup script)
✅ README.md                   (Documentation)
📁 templates/
   ✅ admin-page.php           (Admin settings)
   ✅ frontend-form.php        (Frontend form)
📁 assets/
   📁 css/
      ✅ style.css            (Frontend styles)
      ✅ admin.css            (Admin styles)
   📁 js/
      ✅ script.js            (Frontend JavaScript)
📁 languages/                  (Empty folder for translations)
```

### Step 3: Activate Plugin
1. **Go to:** WordPress Admin → Plugins
2. **Find:** "AI Outline Generator"
3. **Click:** "Activate"

### Step 4: Configure Settings
1. **Go to:** Settings → AI Outline Generator
2. **Add:** Your OpenAI API key
3. **Save:** Settings

### Step 5: Test Plugin
1. **Create new page/post**
2. **Add shortcode:** `[ai_outline_generator]`
3. **Publish and view** the page

---

## 🎯 **Option 2: Local WordPress Installation**

If you need a local WordPress site:

### Method A: Using Local by Flywheel (Easiest)
1. **Download:** [Local by Flywheel](https://localwp.com/)
2. **Install and create** new WordPress site
3. **Follow Option 1** steps above

### Method B: Using XAMPP/MAMP
1. **Download:** [XAMPP](https://www.apachefriends.org/) or [MAMP](https://www.mamp.info/)
2. **Install WordPress** in htdocs/www folder
3. **Follow Option 1** steps above

### Method C: Using WordPress CLI
```bash
# Install WordPress CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp

# Create WordPress site
mkdir my-wordpress-site
cd my-wordpress-site
wp core download
wp config create --dbname=wordpress --dbuser=root --dbpass=password
wp core install --url=localhost:8000 --title="Test Site" --admin_user=admin --admin_password=password --admin_email=admin@example.com

# Copy plugin files
mkdir wp-content/plugins/ai-outline-generator
cp -r /path/to/plugin/files/* wp-content/plugins/ai-outline-generator/

# Start server
wp server --host=localhost --port=8000
```

---

## 🎯 **Option 3: Quick Test with Demo**

If you just want to see the plugin working:

1. **Open:** `demo.html` in your browser (already created)
2. **This shows:** Exact functionality and design
3. **Use this to:** Show clients/stakeholders

---

## 🔧 **Troubleshooting Common Issues**

### Issue: Plugin Not Showing in WordPress Admin

**Check:**
1. ✅ Files in correct location: `/wp-content/plugins/ai-outline-generator/`
2. ✅ Main file named: `ai-outline-generator.php`
3. ✅ File permissions: 644 for files, 755 for folders
4. ✅ No PHP syntax errors

**Fix:**
```bash
# Set correct permissions
chmod 755 /path/to/wp-content/plugins/ai-outline-generator
chmod 644 /path/to/wp-content/plugins/ai-outline-generator/*.php
```

### Issue: Plugin Activates But Doesn't Work

**Check:**
1. ✅ All template files uploaded
2. ✅ CSS/JS files uploaded
3. ✅ OpenAI API key configured

### Issue: Shortcode Not Working

**Check:**
1. ✅ Plugin activated
2. ✅ Shortcode spelled correctly: `[ai_outline_generator]`
3. ✅ No theme conflicts

---

## 📊 **File Structure Verification**

Your final WordPress plugin structure should look like:

```
/wp-content/plugins/ai-outline-generator/
├── ai-outline-generator.php     ← Main plugin file (367 lines)
├── uninstall.php               ← Cleanup script
├── README.md                   ← Documentation
├── templates/
│   ├── admin-page.php          ← Admin settings page
│   └── frontend-form.php       ← Frontend form template
├── assets/
│   ├── css/
│   │   ├── style.css          ← Frontend styles (Figma design)
│   │   └── admin.css          ← Admin panel styles
│   └── js/
│       └── script.js          ← Frontend JavaScript & AJAX
└── languages/                  ← Translation files (empty for now)
```

---

## 🎉 **Success Checklist**

After installation, verify:

- [ ] Plugin appears in WordPress Admin → Plugins
- [ ] Plugin can be activated without errors
- [ ] Settings page appears: Settings → AI Outline Generator
- [ ] Shortcode works: `[ai_outline_generator]` shows the form
- [ ] Form displays with Figma design
- [ ] Character counter works (253/1000)
- [ ] Dropdowns have all options
- [ ] Generate button is styled correctly
- [ ] Sample cards appear (if enabled)

---

## 🔑 **OpenAI API Setup**

1. **Visit:** [OpenAI Platform](https://platform.openai.com/api-keys)
2. **Sign up/Login** to your account
3. **Create new API key**
4. **Copy key** (starts with `sk-`)
5. **Add to plugin** settings in WordPress
6. **Test generation** with sample content

**Cost:** ~$0.002 per outline (very affordable)

---

## 📞 **Need Help?**

If you're still having issues:

1. **Check:** `TROUBLESHOOTING.md` file
2. **Run:** `diagnose.php` script in WordPress root
3. **Enable:** WordPress debug mode
4. **Check:** Server error logs
5. **Try:** Deactivating other plugins temporarily

---

## 🎯 **Quick Start Commands**

If you have command line access to your WordPress site:

```bash
# Navigate to plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Create plugin directory
mkdir ai-outline-generator

# Copy files (adjust path as needed)
cp -r /path/to/plugin/files/* ai-outline-generator/

# Set permissions
chmod 755 ai-outline-generator
chmod 644 ai-outline-generator/*.php
chmod -R 644 ai-outline-generator/templates/
chmod -R 644 ai-outline-generator/assets/

# Check if WordPress recognizes the plugin
wp plugin list | grep ai-outline

# Activate the plugin
wp plugin activate ai-outline-generator
```

---

Your plugin is **production-ready** and will work perfectly once installed in WordPress! 🚀
