<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="ai-outline-generator-container">
    <!-- Hero Section -->
    <div class="ai-outline-hero">
        <div class="ai-outline-badge-group">
            <div class="ai-outline-badge">
                <span class="badge-text"><?php _e('AI Outline Generator', 'ai-outline-generator'); ?></span>
            </div>
            <div class="ai-outline-message">
                <?php _e('Turn Thoughts Into Text, Effortlessly.', 'ai-outline-generator'); ?>
            </div>
        </div>
        
        <h1 class="ai-outline-title">
            <?php _e('Free AI Outline Generator', 'ai-outline-generator'); ?>
        </h1>
        
        <p class="ai-outline-description">
            <?php _e('Jumpstart your writing with structured, ready-to-go outlines. Whether it\'s a blog post, essay, article, or video script — our AI helps you organize ideas fast, so you can focus on creating, not stressing.', 'ai-outline-generator'); ?>
        </p>
    </div>
    
    <!-- Main Form Section -->
    <div class="ai-outline-form-container">
        <form id="ai-outline-form" class="ai-outline-form">
            <div class="form-header">
                <h2><?php _e('Create Your Outline', 'ai-outline-generator'); ?></h2>
            </div>
            
            <div class="form-content">
                <!-- Text Input Area -->
                <div class="input-group">
                    <textarea 
                        id="outline-content" 
                        name="content" 
                        placeholder="<?php _e('Describe your topic or paste your content here...', 'ai-outline-generator'); ?>"
                        maxlength="<?php echo esc_attr($atts['max_chars']); ?>"
                        required
                    ></textarea>
                    <div class="char-counter">
                        <span id="char-count">0</span>/<span id="char-limit"><?php echo esc_attr($atts['max_chars']); ?></span>
                    </div>
                </div>
                
                <!-- Form Controls -->
                <div class="form-controls">
                    <div class="control-group">
                        <label for="content-type"><?php _e('Outline/Content Type', 'ai-outline-generator'); ?></label>
                        <select id="content-type" name="content_type" required>
                            <option value="research-paper"><?php _e('Research Paper', 'ai-outline-generator'); ?></option>
                            <option value="blog-post"><?php _e('Blog Post', 'ai-outline-generator'); ?></option>
                            <option value="essay"><?php _e('Essay', 'ai-outline-generator'); ?></option>
                            <option value="article"><?php _e('Article', 'ai-outline-generator'); ?></option>
                            <option value="video-script"><?php _e('Video Script', 'ai-outline-generator'); ?></option>
                            <option value="presentation"><?php _e('Presentation', 'ai-outline-generator'); ?></option>
                            <option value="book-chapter"><?php _e('Book Chapter', 'ai-outline-generator'); ?></option>
                        </select>
                    </div>
                    
                    <div class="control-group">
                        <label for="sections"><?php _e('Outline Sections', 'ai-outline-generator'); ?></label>
                        <select id="sections" name="sections" required>
                            <option value="3"><?php _e('3 Sections', 'ai-outline-generator'); ?></option>
                            <option value="4"><?php _e('4 Sections', 'ai-outline-generator'); ?></option>
                            <option value="5"><?php _e('5 Sections', 'ai-outline-generator'); ?></option>
                            <option value="6"><?php _e('6 Sections', 'ai-outline-generator'); ?></option>
                            <option value="7"><?php _e('7 Sections', 'ai-outline-generator'); ?></option>
                        </select>
                    </div>
                    
                    <div class="control-group">
                        <label for="language"><?php _e('Language', 'ai-outline-generator'); ?></label>
                        <select id="language" name="language" required>
                            <option value="English"><?php _e('English', 'ai-outline-generator'); ?></option>
                            <option value="Spanish"><?php _e('Spanish', 'ai-outline-generator'); ?></option>
                            <option value="French"><?php _e('French', 'ai-outline-generator'); ?></option>
                            <option value="German"><?php _e('German', 'ai-outline-generator'); ?></option>
                            <option value="Italian"><?php _e('Italian', 'ai-outline-generator'); ?></option>
                            <option value="Portuguese"><?php _e('Portuguese', 'ai-outline-generator'); ?></option>
                            <option value="Chinese"><?php _e('Chinese', 'ai-outline-generator'); ?></option>
                            <option value="Japanese"><?php _e('Japanese', 'ai-outline-generator'); ?></option>
                        </select>
                    </div>
                </div>
                
                <!-- Generate Button -->
                <div class="button-container">
                    <button type="submit" id="generate-btn" class="generate-button">
                        <svg class="button-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="1.17" stroke-linecap="round"/>
                        </svg>
                        <span class="button-text"><?php _e('Generate Outline', 'ai-outline-generator'); ?></span>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Loading State -->
        <div id="loading-state" class="loading-state" style="display: none;">
            <div class="loading-spinner"></div>
            <p><?php _e('Generating your outline...', 'ai-outline-generator'); ?></p>
        </div>
        
        <!-- Results Section -->
        <div id="outline-results" class="outline-results" style="display: none;">
            <div class="results-header">
                <h3><?php _e('Your Generated Outline', 'ai-outline-generator'); ?></h3>
                <button type="button" id="copy-outline" class="copy-button">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M4 4V2C4 1.44772 4.44772 1 5 1H14C14.5523 1 15 1.44772 15 2V11C15 11.5523 14.5523 12 14 12H12M4 4H2C1.44772 4 1 4.44772 1 5V14C1 14.5523 1.44772 15 2 15H11C11.5523 15 12 14.5523 12 14V12M4 4V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php _e('Copy', 'ai-outline-generator'); ?>
                </button>
            </div>
            <div id="outline-content-result" class="outline-content"></div>
        </div>
    </div>
    
    <?php if ($atts['show_samples'] === 'true'): ?>
    <!-- Sample Content Cards -->
    <div class="ai-outline-samples">
        <div class="sample-card">
            <h3><?php _e('How to Humanize AI-Generated Text', 'ai-outline-generator'); ?></h3>
            <div class="sample-content">
                <p><?php _e('AI can be a great tool for generating content, but the text it produces can sometimes sound robotic. Here\'s a step-by-step guide to humanizing AI-generated text, making it sound more natural and engaging for your readers:', 'ai-outline-generator'); ?></p>
                <div class="sample-steps">
                    <div class="step"><?php _e('Step 1. Talk Like a Human:', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 2. Show, Don\'t Tell:', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 3. Rewrite passive voice to Active voice:', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 4. Fact-Check for Accuracy', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 5. Don\'t Forget to Add Visuals', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 6. Take the Help of AI tools', 'ai-outline-generator'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="sample-card">
            <h3><?php _e('Content Marketing Strategy Guide', 'ai-outline-generator'); ?></h3>
            <div class="sample-content">
                <p><?php _e('Build a comprehensive content marketing strategy that drives engagement and converts visitors into customers with this structured approach:', 'ai-outline-generator'); ?></p>
                <div class="sample-steps">
                    <div class="step"><?php _e('Step 1. Define Your Target Audience', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 2. Set Clear Content Goals', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 3. Choose Content Types and Formats', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 4. Create a Content Calendar', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 5. Optimize for SEO', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 6. Measure and Analyze Performance', 'ai-outline-generator'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="sample-card">
            <h3><?php _e('Effective Email Marketing Campaigns', 'ai-outline-generator'); ?></h3>
            <div class="sample-content">
                <p><?php _e('Create email marketing campaigns that engage subscribers and drive conversions with these proven strategies and best practices:', 'ai-outline-generator'); ?></p>
                <div class="sample-steps">
                    <div class="step"><?php _e('Step 1. Build a Quality Email List', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 2. Craft Compelling Subject Lines', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 3. Design Mobile-Friendly Templates', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 4. Personalize Your Messages', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 5. Test and Optimize', 'ai-outline-generator'); ?></div>
                    <div class="step"><?php _e('Step 6. Track Key Metrics', 'ai-outline-generator'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
