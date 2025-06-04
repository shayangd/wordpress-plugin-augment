# AI Outline Generator WordPress Plugin

A powerful WordPress plugin that generates AI-powered outlines for various content types including blog posts, essays, articles, video scripts, and more. Built with a beautiful, responsive design based on the Wellows + AAAI Design Figma template.

## 🚀 Features

- **AI-Powered Outline Generation**: Uses OpenAI's GPT models to create structured, detailed outlines
- **Multiple Content Types**: Support for research papers, blog posts, essays, articles, video scripts, presentations, and book chapters
- **Customizable Sections**: Choose from 3-7 main sections for your outline
- **Multi-Language Support**: Generate outlines in English, Spanish, French, German, Italian, Portuguese, Chinese, and Japanese
- **Beautiful UI**: Pixel-perfect implementation of the Figma design with responsive layout
- **Character Limit Control**: Configurable character limits (default 1000 characters)
- **Sample Content**: Includes sample outline cards for inspiration
- **Copy Functionality**: One-click copy of generated outlines
- **AJAX-Powered**: Smooth, real-time outline generation without page reloads
- **Admin Dashboard**: Easy configuration of API settings

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- OpenAI API key (required for AI functionality)

## 🛠 Installation

1. **Download or Clone**: Download the plugin files or clone this repository
2. **Upload to WordPress**: Upload the plugin folder to `/wp-content/plugins/`
3. **Activate**: Go to WordPress Admin → Plugins and activate "AI Outline Generator"
4. **Configure API**: Go to Settings → AI Outline Generator and enter your OpenAI API key

## ⚙️ Configuration

### Getting an OpenAI API Key

1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign up or log in to your account
3. Create a new API key
4. Copy the key and paste it in the plugin settings

### Plugin Settings

Navigate to **Settings → AI Outline Generator** in your WordPress admin:

- **AI Provider**: Choose your AI provider (currently supports OpenAI)
- **API Key**: Enter your OpenAI API key

## 📖 Usage

### Using the Shortcode

Add the AI Outline Generator to any page or post using the shortcode:

```
[ai_outline_generator]
```

### Shortcode Parameters

- `show_samples` - Show sample content cards (default: true)
- `max_chars` - Maximum character limit for input (default: 1000)

### Examples

```
[ai_outline_generator show_samples="false"]
[ai_outline_generator max_chars="500"]
[ai_outline_generator show_samples="true" max_chars="1500"]
```

### How to Use the Generator

1. **Enter Your Topic**: Describe your content topic or paste existing content
2. **Select Content Type**: Choose from research paper, blog post, essay, article, video script, presentation, or book chapter
3. **Choose Sections**: Select the number of main sections (3-7)
4. **Pick Language**: Select your preferred language
5. **Generate**: Click the "Generate Outline" button
6. **Copy & Use**: Copy the generated outline and use it for your content creation

## 🎨 Design Features

The plugin implements a pixel-perfect recreation of the Figma design including:

- **Typography**: Inter and Montserrat fonts for professional appearance
- **Color Scheme**: Purple accent colors (#7756B1) with clean grays
- **Responsive Layout**: Mobile-first design that works on all devices
- **Interactive Elements**: Hover effects, smooth transitions, and loading states
- **Form Controls**: Custom-styled dropdowns and input fields
- **Sample Cards**: Beautiful content examples with step-by-step outlines

## 🔧 Technical Details

### File Structure

```
ai-outline-generator/
├── ai-outline-generator.php     # Main plugin file
├── templates/
│   ├── admin-page.php          # Admin settings page
│   └── frontend-form.php       # Frontend form template
├── assets/
│   ├── css/
│   │   ├── style.css          # Frontend styles
│   │   └── admin.css          # Admin styles
│   └── js/
│       └── script.js          # Frontend JavaScript
└── README.md                   # This file
```

### Key Features

- **AJAX Processing**: Real-time outline generation without page reloads
- **Security**: Nonce verification and input sanitization
- **Database Logging**: Optional logging of generated outlines
- **Error Handling**: Comprehensive error handling and user feedback
- **Responsive Design**: Mobile-first CSS with breakpoints
- **Accessibility**: Proper form labels and keyboard navigation

## 🔒 Security

The plugin implements several security measures:

- Nonce verification for all AJAX requests
- Input sanitization and validation
- Capability checks for admin functions
- Secure API key storage
- XSS protection for output

## 🐛 Troubleshooting

### Common Issues

1. **"Security check failed"**: Clear your browser cache and try again
2. **"Failed to generate outline"**: Check your API key in settings
3. **Styling issues**: Ensure your theme doesn't override plugin styles
4. **JavaScript errors**: Check browser console for conflicts

### Debug Mode

To enable debug mode, add this to your `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📝 Changelog

### Version 1.0.0

- Initial release
- AI-powered outline generation
- Beautiful Figma-based design
- Multi-language support
- Responsive layout
- Admin configuration panel

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 👨‍💻 Author

**Shayan Rais**

- GitHub: [@shayangd](https://github.com/shayangd)
- Email: shayan.rais@disrupt.com

## 🙏 Acknowledgments

- Design based on Wellows + AAAI Design Figma template
- Built with WordPress best practices
- Powered by OpenAI's GPT models
