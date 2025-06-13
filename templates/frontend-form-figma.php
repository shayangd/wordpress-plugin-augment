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

        <!-- Creative Potions Section - Figma Design -->
        <?php if ($show_creative_potions): ?>
        <div class="aaai-creative-potions">
            <div class="aaai-potions-frame">
                <h2 class="aaai-potions-title"><?php _e('Creative Potions', 'aaai-ai-writing-tool'); ?></h2>
                <div class="aaai-potions-grid">
                    <!-- Blog Post Card -->
                    <div class="aaai-potion-card" data-type="blog_post">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
                                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="10,9 9,9 8,9" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Blog Post', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Create engaging blog content', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Ad Card -->
                    <div class="aaai-potion-card" data-type="ad">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <path d="M12 2L2 7l10 5 10-5-10-5z" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path d="M2 17l10 5 10-5" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path d="M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Ad', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Persuasive advertising copy', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Post Card -->
                    <div class="aaai-potion-card" data-type="social_media_post">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Social Media Post', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Engaging social content', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Paragraph Card -->
                    <div class="aaai-potion-card" data-type="paragraph">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <path d="M10 2v20" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path d="M14 2v20" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path d="M4.5 7h15" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path d="M4.5 17h15" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Paragraph', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Well-structured paragraphs', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div class="aaai-potion-card" data-type="email">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Email', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Professional email content', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Blog Introduction Card -->
                    <div class="aaai-potion-card" data-type="blog_introduction">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Blog Introduction', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Compelling blog intros', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Blog Outline Card -->
                    <div class="aaai-potion-card" data-type="blog_outline">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <line x1="8" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="6" x2="3.01" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="12" x2="3.01" y2="12" stroke="currentColor" stroke-width="2"/>
                                        <line x1="3" y1="18" x2="3.01" y2="18" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Blog Outline', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Structured blog outlines', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Description Card -->
                    <div class="aaai-potion-card" data-type="product_description">
                        <div class="aaai-card-frame">
                            <div class="aaai-card-background"></div>
                            <div class="aaai-card-content">
                                <div class="aaai-potion-icon">
                                    <svg viewBox="0 0 24 24" class="aaai-icon-svg">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2"/>
                                        <path d="M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="aaai-potion-title"><?php _e('Product Description', 'aaai-ai-writing-tool'); ?></div>
                                <div class="aaai-potion-description"><?php _e('Compelling product copy', 'aaai-ai-writing-tool'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Results Section - Figma Screen 3 Design -->
        <div class="aaai-results-section" id="aaai-results-section" style="display: none;">
            <div class="aaai-results-frame">
                <div class="aaai-results-background"></div>

                <!-- Results Header -->
                <div class="aaai-results-header">
                    <div class="aaai-results-title-frame">
                        <h3 class="aaai-results-title"><?php _e('Generated Content', 'aaai-ai-writing-tool'); ?></h3>
                    </div>
                    <div class="aaai-results-actions">
                        <div class="aaai-action-buttons">
                            <!-- Copy Button -->
                            <button class="aaai-action-button aaai-copy-button" id="aaai-copy-btn">
                                <div class="aaai-button-frame">
                                    <div class="aaai-button-bg"></div>
                                    <div class="aaai-button-content">
                                        <svg class="aaai-action-icon" viewBox="0 0 16 16">
                                            <rect x="2" y="2" width="8" height="8" rx="1" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                            <path d="M6 2V1a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                        </svg>
                                        <span class="aaai-action-text"><?php _e('Copy', 'aaai-ai-writing-tool'); ?></span>
                                    </div>
                                </div>
                            </button>

                            <!-- Regenerate Button -->
                            <button class="aaai-action-button aaai-regenerate-button" id="aaai-regenerate-btn">
                                <div class="aaai-button-frame">
                                    <div class="aaai-button-bg"></div>
                                    <div class="aaai-button-content">
                                        <svg class="aaai-action-icon" viewBox="0 0 16 16">
                                            <path d="M1.33 8A6.67 6.67 0 0 1 8 1.33a6.67 6.67 0 0 1 6.67 6.67" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                            <path d="M14.67 8A6.67 6.67 0 0 1 8 14.67 6.67 6.67 0 0 1 1.33 8" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                            <polyline points="11.33,4.67 14.67,8 11.33,11.33" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                        </svg>
                                        <span class="aaai-action-text"><?php _e('Regenerate', 'aaai-ai-writing-tool'); ?></span>
                                    </div>
                                </div>
                            </button>

                            <!-- Feedback Button -->
                            <button class="aaai-action-button aaai-feedback-button" id="aaai-feedback-btn">
                                <div class="aaai-button-frame">
                                    <div class="aaai-button-bg"></div>
                                    <div class="aaai-button-content">
                                        <svg class="aaai-action-icon" viewBox="0 0 16 16">
                                            <polygon points="8,1.33 10.09,5.59 14.67,6.27 11.5,9.37 12.18,13.93 8,11.77 3.82,13.93 4.5,9.37 1.33,6.27 5.91,5.59" stroke="currentColor" stroke-width="1.33" fill="none"/>
                                        </svg>
                                        <span class="aaai-action-text"><?php _e('Feedback', 'aaai-ai-writing-tool'); ?></span>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Content -->
                <div class="aaai-results-content">
                    <div class="aaai-content-frame">
                        <div class="aaai-content-background"></div>
                        <div class="aaai-content-text" id="aaai-generated-content">
                            <!-- Generated content will be inserted here -->
                        </div>
                        <!-- Custom Scrollbar -->
                        <div class="aaai-scrollbar">
                            <div class="aaai-scrollbar-track">
                                <div class="aaai-scrollbar-thumb"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Modal - Figma Screen 4 Design -->
        <div class="aaai-feedback-modal" id="aaai-feedback-modal" style="display: none;">
            <div class="aaai-modal-overlay">
                <div class="aaai-modal-background"></div>
                <div class="aaai-feedback-content">
                    <div class="aaai-feedback-frame">
                        <div class="aaai-feedback-bg"></div>

                        <!-- Modal Header -->
                        <div class="aaai-feedback-header">
                            <h3 class="aaai-feedback-title"><?php _e('How was the generated content?', 'aaai-ai-writing-tool'); ?></h3>
                        </div>

                        <!-- Emotion Selector -->
                        <div class="aaai-emotion-selector">
                            <div class="aaai-emotion-group">
                                <button class="aaai-emotion-button" data-emotion="love" data-value="5">
                                    <span class="aaai-emotion-emoji">😍</span>
                                </button>
                                <button class="aaai-emotion-button" data-emotion="like" data-value="4">
                                    <span class="aaai-emotion-emoji">😊</span>
                                </button>
                                <button class="aaai-emotion-button" data-emotion="neutral" data-value="3">
                                    <span class="aaai-emotion-emoji">😐</span>
                                </button>
                                <button class="aaai-emotion-button" data-emotion="dislike" data-value="2">
                                    <span class="aaai-emotion-emoji">😞</span>
                                </button>
                                <button class="aaai-emotion-button" data-emotion="hate" data-value="1">
                                    <span class="aaai-emotion-emoji">😡</span>
                                </button>
                            </div>
                        </div>

                        <!-- Star Rating -->
                        <div class="aaai-rating-stars">
                            <div class="aaai-stars-group">
                                <button class="aaai-star" data-rating="1">
                                    <svg class="aaai-star-icon" viewBox="0 0 24 24">
                                        <polygon points="12,2 15.09,8.26 22,9 17,14.74 18.18,21.02 12,17.77 5.82,21.02 7,14.74 2,9 8.91,8.26" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </button>
                                <button class="aaai-star" data-rating="2">
                                    <svg class="aaai-star-icon" viewBox="0 0 24 24">
                                        <polygon points="12,2 15.09,8.26 22,9 17,14.74 18.18,21.02 12,17.77 5.82,21.02 7,14.74 2,9 8.91,8.26" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </button>
                                <button class="aaai-star" data-rating="3">
                                    <svg class="aaai-star-icon" viewBox="0 0 24 24">
                                        <polygon points="12,2 15.09,8.26 22,9 17,14.74 18.18,21.02 12,17.77 5.82,21.02 7,14.74 2,9 8.91,8.26" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </button>
                                <button class="aaai-star" data-rating="4">
                                    <svg class="aaai-star-icon" viewBox="0 0 24 24">
                                        <polygon points="12,2 15.09,8.26 22,9 17,14.74 18.18,21.02 12,17.77 5.82,21.02 7,14.74 2,9 8.91,8.26" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </button>
                                <button class="aaai-star" data-rating="5">
                                    <svg class="aaai-star-icon" viewBox="0 0 24 24">
                                        <polygon points="12,2 15.09,8.26 22,9 17,14.74 18.18,21.02 12,17.77 5.82,21.02 7,14.74 2,9 8.91,8.26" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Feedback Text Area -->
                        <div class="aaai-feedback-text-container">
                            <textarea
                                id="feedback-text"
                                class="aaai-feedback-textarea"
                                placeholder="<?php _e('Tell us more about your experience (optional)', 'aaai-ai-writing-tool'); ?>"
                                maxlength="500"
                            ></textarea>
                        </div>

                        <!-- Modal Actions -->
                        <div class="aaai-feedback-actions">
                            <div class="aaai-action-group">
                                <button id="submit-feedback" class="aaai-feedback-submit-button">
                                    <div class="aaai-submit-content">
                                        <span class="aaai-submit-text"><?php _e('Submit Feedback', 'aaai-ai-writing-tool'); ?></span>
                                    </div>
                                </button>
                                <button id="close-feedback" class="aaai-feedback-close-button">
                                    <div class="aaai-close-content">
                                        <span class="aaai-close-text"><?php _e('Close', 'aaai-ai-writing-tool'); ?></span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- /.aaai-container -->
</div>
