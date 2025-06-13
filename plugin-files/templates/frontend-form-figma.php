<?php
/**
 * Frontend form template for AAAI AI Writing Tool
 * Based on Figma Design Specifications - Screens 1-4
 * Pixel-perfect implementation of the AAAI design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get default values from options or shortcode attributes
$default_wordcount = isset($atts['default_wordcount']) ? $atts['default_wordcount'] : get_option('aaai_ai_writing_tool_default_wordcount', '1000');
$default_tone = isset($atts['default_tone']) ? $atts['default_tone'] : get_option('aaai_ai_writing_tool_default_tone', 'professional');
$show_creative_potions = isset($atts['show_creative_potions']) ? $atts['show_creative_potions'] === 'true' : true;
?>

<div class="aaai-ai-writing-tool">
    <!-- Background Content -->
    <div class="aaai-content-background">
        <div class="aaai-background-frame">
            <div class="aaai-background-image"></div>
        </div>
    </div>

    <!-- Navigation Bar (Hidden by default, matches Figma) -->
    <div class="aaai-navigation-bar" style="display: none;">
        <div class="aaai-nav-bg"></div>
        <div class="aaai-nav-frame">
            <div class="aaai-logo"></div>
            <div class="aaai-nav-items">
                <span class="aaai-nav-item"><?php _e('Browse all Categories', 'aaai-ai-writing-tool'); ?></span>
                <span class="aaai-nav-item active"><?php _e('Best AI Tools', 'aaai-ai-writing-tool'); ?></span>
                <span class="aaai-nav-item"><?php _e('Reviews', 'aaai-ai-writing-tool'); ?></span>
                <span class="aaai-nav-item"><?php _e('Comparisons', 'aaai-ai-writing-tool'); ?></span>
                <span class="aaai-nav-item"><?php _e('Guides', 'aaai-ai-writing-tool'); ?></span>
                <span class="aaai-nav-item"><?php _e('News', 'aaai-ai-writing-tool'); ?></span>
                <span class="aaai-nav-item"><?php _e('Submit your tool', 'aaai-ai-writing-tool'); ?></span>
            </div>
            <div class="aaai-region-search">
                <div class="aaai-flag-group">
                    <div class="aaai-flag-icon"></div>
                </div>
                <div class="aaai-search-group">
                    <svg class="aaai-search-icon" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" fill="none"/>
                        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="aaai-container">
        <!-- Badge Group - Figma Screen 1 Design -->
        <div class="aaai-badge-group">
            <div class="aaai-badge-frame">
                <div class="aaai-badge">
                    <div class="aaai-badge-base">
                        <span class="aaai-badge-text"><?php _e('AI Writing tool', 'aaai-ai-writing-tool'); ?></span>
                    </div>
                </div>
                <div class="aaai-badge-message"><?php _e('Turn Thoughts Into Text, Effortlessly.', 'aaai-ai-writing-tool'); ?></div>
            </div>
        </div>

        <!-- Heading Section - Figma Design -->
        <div class="aaai-heading-section">
            <h1 class="aaai-main-heading"><?php _e('Free AI Writing Tool', 'aaai-ai-writing-tool'); ?></h1>
            <p class="aaai-supporting-text">
                <?php _e('Effortlessly turn your thoughts into clear, compelling copy from emails to articles in seconds. Write like a pro, for Free!', 'aaai-ai-writing-tool'); ?>
            </p>
        </div>

        <!-- Form Container - Figma Screen 1 Design -->
        <div class="aaai-form-container">
            <div class="aaai-form-group">
                <div class="aaai-form-frame">
                    <div class="aaai-form-background"></div>
                    
                    <form id="aaai-ai-writing-form" method="post">
                        <?php wp_nonce_field('aaai_ai_writing_nonce', 'aaai_ai_writing_nonce'); ?>

                        <!-- Form Header Section -->
                        <div class="aaai-form-header">
                            <!-- Form Fields Grid - Matching Figma Layout -->
                            <div class="aaai-form-fields">
                                <!-- Topic Field -->
                                <div class="aaai-form-field-container">
                                    <div class="aaai-field-container">
                                        <label for="topic" class="aaai-form-label"><?php _e('Topic', 'aaai-ai-writing-tool'); ?></label>
                                    </div>
                                    <div class="aaai-input-frame">
                                        <div class="aaai-input-span">
                                            <textarea 
                                                id="topic" 
                                                name="topic" 
                                                class="aaai-form-textarea" 
                                                placeholder="<?php _e('Enter Topic...', 'aaai-ai-writing-tool'); ?>" 
                                                required
                                                maxlength="500"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keyword Field -->
                                <div class="aaai-form-field-container">
                                    <div class="aaai-field-container">
                                        <label for="keyword" class="aaai-form-label"><?php _e('Keyword', 'aaai-ai-writing-tool'); ?></label>
                                    </div>
                                    <div class="aaai-input-frame">
                                        <div class="aaai-input-span">
                                            <input 
                                                type="text" 
                                                id="keyword" 
                                                name="keyword" 
                                                class="aaai-form-input" 
                                                placeholder="<?php _e('Enter Keyword...', 'aaai-ai-writing-tool'); ?>"
                                                maxlength="100"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Word Count Field -->
                                <div class="aaai-form-field-container">
                                    <div class="aaai-field-container">
                                        <label for="wordcount" class="aaai-form-label"><?php _e('Wordcount', 'aaai-ai-writing-tool'); ?></label>
                                    </div>
                                    <div class="aaai-input-frame">
                                        <div class="aaai-input-span">
                                            <input 
                                                type="number" 
                                                id="wordcount" 
                                                name="wordcount" 
                                                class="aaai-form-input" 
                                                value="<?php echo esc_attr($default_wordcount); ?>"
                                                min="50"
                                                max="5000"
                                                step="50"
                                            />
                                        </div>
                                        <div class="aaai-caret-controls">
                                            <div class="aaai-caret-up-down">
                                                <button type="button" class="aaai-caret-button increment" data-target="wordcount" data-step="50">
                                                    <svg class="aaai-caret-icon" viewBox="0 0 16 16">
                                                        <path d="M4.5 12L8 8.5L11.5 12" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="aaai-caret-button decrement" data-target="wordcount" data-step="50">
                                                    <svg class="aaai-caret-icon" viewBox="0 0 16 16">
                                                        <path d="M4.5 4L8 7.5L11.5 4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tone Field -->
                                <div class="aaai-form-field-container">
                                    <div class="aaai-field-container">
                                        <label for="tone" class="aaai-form-label"><?php _e('Tone', 'aaai-ai-writing-tool'); ?></label>
                                    </div>
                                    <div class="aaai-input-frame">
                                        <div class="aaai-input-span">
                                            <select id="tone" name="tone" class="aaai-form-select">
                                                <option value="" disabled selected><?php _e('Select tone', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="professional" <?php selected($default_tone, 'professional'); ?>><?php _e('Professional', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="casual" <?php selected($default_tone, 'casual'); ?>><?php _e('Casual', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="friendly" <?php selected($default_tone, 'friendly'); ?>><?php _e('Friendly', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="formal" <?php selected($default_tone, 'formal'); ?>><?php _e('Formal', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="conversational" <?php selected($default_tone, 'conversational'); ?>><?php _e('Conversational', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="persuasive" <?php selected($default_tone, 'persuasive'); ?>><?php _e('Persuasive', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="informative" <?php selected($default_tone, 'informative'); ?>><?php _e('Informative', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="creative" <?php selected($default_tone, 'creative'); ?>><?php _e('Creative', 'aaai-ai-writing-tool'); ?></option>
                                            </select>
                                        </div>
                                        <div class="aaai-dropdown-frame">
                                            <svg class="aaai-dropdown-icon" viewBox="0 0 8 4">
                                                <path d="M0 0L4 4L8 0" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- LLM Provider Field -->
                                <div class="aaai-form-field-container">
                                    <div class="aaai-field-container">
                                        <label for="llm" class="aaai-form-label"><?php _e('LLM', 'aaai-ai-writing-tool'); ?></label>
                                    </div>
                                    <div class="aaai-input-frame">
                                        <div class="aaai-input-span">
                                            <select id="llm" name="llm" class="aaai-form-select">
                                                <option value="openai"><?php _e('OpenAI GPT', 'aaai-ai-writing-tool'); ?></option>
                                                <option value="anthropic" selected><?php _e('Claude', 'aaai-ai-writing-tool'); ?></option>
                                            </select>
                                        </div>
                                        <div class="aaai-dropdown-frame">
                                            <svg class="aaai-dropdown-icon" viewBox="0 0 8 4">
                                                <path d="M0 0L4 4L8 0" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Generate Button - Figma Design -->
                                <div class="aaai-button-container">
                                    <button type="submit" class="aaai-generate-button" id="aaai-generate-btn">
                                        <div class="aaai-button-content">
                                            <div class="aaai-svg-margin">
                                                <div class="aaai-svg-container">
                                                    <div class="aaai-svg-frame">
                                                        <svg class="aaai-generate-icon" viewBox="0 0 16 16">
                                                            <path d="M1.33 1.33H14.67V14.67" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                                            <path d="M13.33 2V4.67" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                                            <path d="M12 3.33H14.67" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                                            <path d="M2.67 11.33V13.33" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                                            <path d="M2 12H3.33" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="aaai-button-text"><?php _e('Generate Text', 'aaai-ai-writing-tool'); ?></span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Content Type Field -->
                        <input type="hidden" id="content-type" name="content_type" value="blog_post" />
                    </form>
                </div>
            </div>
        </div>
