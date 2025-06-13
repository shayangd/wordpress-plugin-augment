<?php
/**
 * Frontend form template for AAAI AI Writing Tool
 * Based on Figma Design Specifications - Screens 1-4
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
    <!-- Background Image -->
    <div class="aaai-content-background">
        <div class="aaai-background-image"></div>
    </div>

    <!-- Navigation Bar (Optional - can be hidden via CSS) -->
    <div class="aaai-navigation-bar" style="display: none;">
        <div class="aaai-nav-container">
            <div class="aaai-logo"></div>
            <div class="aaai-nav-items">
                <span>Browse all Categories</span>
                <span class="active">Best AI Tools</span>
                <span>Reviews</span>
                <span>Comparisons</span>
                <span>Guides</span>
                <span>News</span>
                <span>Submit your tool</span>
            </div>
            <div class="aaai-region-search">
                <div class="aaai-flag-icon"></div>
                <div class="aaai-search-icon"></div>
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
    <!-- Form Container -->
    <div class="aaai-form-container">
        <form id="aaai-ai-writing-form" method="post">
            <?php wp_nonce_field('aaai_ai_writing_nonce', 'aaai_ai_writing_nonce'); ?>

            <div class="aaai-form-grid">
                <!-- Topic Field -->
                <div class="aaai-form-field">
                    <label for="topic" class="aaai-form-label"><?php _e('Topic', 'aaai-ai-writing-tool'); ?></label>
                    <textarea
                        id="topic"
                        name="topic"
                        class="aaai-form-textarea"
                        placeholder="<?php _e('Enter your topic or main idea here...', 'aaai-ai-writing-tool'); ?>"
                        rows="3"
                        required
                    ></textarea>
                </div>

                <!-- Keyword Field -->
                <div class="aaai-form-field">
                    <label for="keyword" class="aaai-form-label"><?php _e('Keyword', 'aaai-ai-writing-tool'); ?></label>
                    <input
                        type="text"
                        id="keyword"
                        name="keyword"
                        class="aaai-form-input"
                        placeholder="<?php _e('Enter focus keyword (optional)', 'aaai-ai-writing-tool'); ?>"
                    />
                </div>

                <!-- Word Count Field -->
                <div class="aaai-form-field">
                    <label for="wordcount" class="aaai-form-label"><?php _e('Wordcount', 'aaai-ai-writing-tool'); ?></label>
                    <div class="aaai-number-input-container">
                        <input
                            type="number"
                            id="wordcount"
                            name="wordcount"
                            class="aaai-form-input"
                            value="<?php echo esc_attr($default_wordcount); ?>"
                            min="50"
                            max="5000"
                            step="50"
                            required
                        />
                        <div class="aaai-number-controls">
                            <button type="button" class="aaai-number-control increment">▲</button>
                            <button type="button" class="aaai-number-control decrement">▼</button>
                        </div>
                    </div>
                </div>

                <!-- Tone Field -->
                <div class="aaai-form-field">
                    <label for="tone" class="aaai-form-label"><?php _e('Tone', 'aaai-ai-writing-tool'); ?></label>
                    <select id="tone" name="tone" class="aaai-form-select" required>
                        <option value="professional" <?php selected($default_tone, 'professional'); ?>><?php _e('Professional', 'aaai-ai-writing-tool'); ?></option>
                        <option value="casual" <?php selected($default_tone, 'casual'); ?>><?php _e('Casual', 'aaai-ai-writing-tool'); ?></option>
                        <option value="friendly" <?php selected($default_tone, 'friendly'); ?>><?php _e('Friendly', 'aaai-ai-writing-tool'); ?></option>
                        <option value="formal" <?php selected($default_tone, 'formal'); ?>><?php _e('Formal', 'aaai-ai-writing-tool'); ?></option>
                        <option value="conversational" <?php selected($default_tone, 'conversational'); ?>><?php _e('Conversational', 'aaai-ai-writing-tool'); ?></option>
                        <option value="persuasive" <?php selected($default_tone, 'persuasive'); ?>><?php _e('Persuasive', 'aaai-ai-writing-tool'); ?></option>
                        <option value="informative" <?php selected($default_tone, 'informative'); ?>><?php _e('Informative', 'aaai-ai-writing-tool'); ?></option>
                    </select>
                </div>

                <!-- LLM Field -->
                <div class="aaai-form-field">
                    <label for="llm" class="aaai-form-label"><?php _e('LLM', 'aaai-ai-writing-tool'); ?></label>
                    <select id="llm" name="llm" class="aaai-form-select" required>
                        <option value="claude"><?php _e('Claude', 'aaai-ai-writing-tool'); ?></option>
                        <option value="gpt-4"><?php _e('GPT-4', 'aaai-ai-writing-tool'); ?></option>
                        <option value="gpt-3.5"><?php _e('GPT-3.5', 'aaai-ai-writing-tool'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Hidden Content Type Field -->
            <input type="hidden" id="content-type" name="content_type" value="blog_post" />

            <!-- Generate Button -->
            <button type="submit" class="aaai-generate-button">
                <?php _e('Generate Text', 'aaai-ai-writing-tool'); ?>
            </button>
        </form>
    </div>
    <!-- Creative Potions Section -->
    <div class="aaai-creative-potions">
        <h2 class="aaai-potions-title"><?php _e('Creative Potions', 'aaai-ai-writing-tool'); ?></h2>
        <div class="aaai-potions-grid">
            <div class="aaai-potion-card" data-type="blog_post">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Blog Post', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Create engaging blog content', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="ad">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Ad', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Persuasive advertising copy', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="social_media_post">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Social Media Post', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Engaging social content', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="paragraph">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Paragraph', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Well-structured paragraphs', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="email">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Email', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Professional email content', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="blog_introduction">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Blog Introduction', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Compelling blog intros', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="blog_outline">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Blog Outline', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Structured blog outlines', 'aaai-ai-writing-tool'); ?></div>
            </div>

            <div class="aaai-potion-card" data-type="product_description">
                <div class="aaai-potion-icon"></div>
                <div class="aaai-potion-title"><?php _e('Product Description', 'aaai-ai-writing-tool'); ?></div>
                <div class="aaai-potion-description"><?php _e('Compelling product copy', 'aaai-ai-writing-tool'); ?></div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="aaai-results-section">
        <div class="aaai-results-header">
            <h3 class="aaai-results-title"><?php _e('Generated Content', 'aaai-ai-writing-tool'); ?></h3>
            <div class="aaai-results-actions">
                <button class="aaai-action-button aaai-copy-button">
                    📋 <?php _e('Copy', 'aaai-ai-writing-tool'); ?>
                </button>
                <button class="aaai-action-button aaai-regenerate-button">
                    🔄 <?php _e('Regenerate', 'aaai-ai-writing-tool'); ?>
                </button>
                <button class="aaai-action-button aaai-feedback-button">
                    ⭐ <?php _e('Feedback', 'aaai-ai-writing-tool'); ?>
                </button>
            </div>
        </div>
        <div class="aaai-results-content">
            <!-- Generated content will be inserted here -->
        </div>
    </div>
    <!-- Feedback Modal -->
    <div class="aaai-feedback-modal">
        <div class="aaai-feedback-content">
            <h3 class="aaai-feedback-title"><?php _e('How was the generated content?', 'aaai-ai-writing-tool'); ?></h3>

            <div class="aaai-emotion-selector">
                <button class="aaai-emotion-button" data-emotion="love">😍</button>
                <button class="aaai-emotion-button" data-emotion="like">😊</button>
                <button class="aaai-emotion-button" data-emotion="neutral">😐</button>
                <button class="aaai-emotion-button" data-emotion="dislike">😞</button>
                <button class="aaai-emotion-button" data-emotion="hate">😡</button>
            </div>

            <div class="aaai-rating-stars">
                <span class="aaai-star" data-rating="1">⭐</span>
                <span class="aaai-star" data-rating="2">⭐</span>
                <span class="aaai-star" data-rating="3">⭐</span>
                <span class="aaai-star" data-rating="4">⭐</span>
                <span class="aaai-star" data-rating="5">⭐</span>
            </div>

            <textarea
                id="feedback-text"
                class="aaai-feedback-textarea"
                placeholder="<?php _e('Tell us more about your experience (optional)', 'aaai-ai-writing-tool'); ?>"
            ></textarea>

            <div class="aaai-feedback-actions">
                <button id="submit-feedback" class="aaai-feedback-button primary">
                    <?php _e('Submit Feedback', 'aaai-ai-writing-tool'); ?>
                </button>
                <button id="close-feedback" class="aaai-feedback-button secondary">
                    <?php _e('Close', 'aaai-ai-writing-tool'); ?>
                </button>
            </div>
        </div>
    </div>
    </div> <!-- /.aaai-container -->
</div>
