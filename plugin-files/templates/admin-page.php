<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('ai_outline_settings', 'ai_outline_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="api_provider"><?php _e('AI Provider', 'ai-outline-generator'); ?></label>
                </th>
                <td>
                    <select name="api_provider" id="api_provider">
                        <option value="openai" <?php selected($api_provider, 'openai'); ?>>OpenAI</option>
                        <option value="anthropic" <?php selected($api_provider, 'anthropic'); ?>>Anthropic (Claude)</option>
                    </select>
                    <p class="description"><?php _e('Choose your preferred AI provider.', 'ai-outline-generator'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_key"><?php _e('API Key', 'ai-outline-generator'); ?></label>
                </th>
                <td>
                    <input type="password" name="api_key" id="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                    <p class="description">
                        <?php _e('Enter your AI provider API key. This is required for the plugin to work.', 'ai-outline-generator'); ?>
                        <br>
                        <strong><?php _e('OpenAI:', 'ai-outline-generator'); ?></strong> 
                        <a href="https://platform.openai.com/api-keys" target="_blank"><?php _e('Get your API key here', 'ai-outline-generator'); ?></a>
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <hr>
    
    <h2><?php _e('Usage Instructions', 'ai-outline-generator'); ?></h2>
    <div class="ai-outline-instructions">
        <h3><?php _e('Shortcode Usage', 'ai-outline-generator'); ?></h3>
        <p><?php _e('Use the following shortcode to display the AI Outline Generator on any page or post:', 'ai-outline-generator'); ?></p>
        <code>[ai_outline_generator]</code>
        
        <h4><?php _e('Shortcode Parameters', 'ai-outline-generator'); ?></h4>
        <ul>
            <li><code>show_samples</code> - <?php _e('Show sample content cards (default: true)', 'ai-outline-generator'); ?></li>
            <li><code>max_chars</code> - <?php _e('Maximum character limit for input (default: 1000)', 'ai-outline-generator'); ?></li>
        </ul>
        
        <h4><?php _e('Examples', 'ai-outline-generator'); ?></h4>
        <p><code>[ai_outline_generator show_samples="false"]</code></p>
        <p><code>[ai_outline_generator max_chars="500"]</code></p>
    </div>
    
    <style>
    .ai-outline-instructions {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 5px;
        margin-top: 20px;
    }
    .ai-outline-instructions code {
        background: #fff;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
    }
    .ai-outline-instructions ul {
        margin-left: 20px;
    }
    </style>
</div>
