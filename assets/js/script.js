/**
 * AI Outline Generator Frontend JavaScript
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initializeOutlineGenerator();
    });

    function initializeOutlineGenerator() {
        // Character counter functionality
        initCharacterCounter();
        
        // Form submission handler
        initFormSubmission();
        
        // Copy functionality
        initCopyFunctionality();
        
        // Input validation
        initInputValidation();
    }

    /**
     * Initialize character counter
     */
    function initCharacterCounter() {
        const textarea = $('#outline-content');
        const charCount = $('#char-count');
        const charLimit = $('#char-limit');
        
        if (textarea.length && charCount.length) {
            // Update counter on input
            textarea.on('input', function() {
                const currentLength = $(this).val().length;
                const maxLength = parseInt(charLimit.text()) || 1000;
                
                charCount.text(currentLength);
                
                // Add warning class when approaching limit
                if (currentLength > maxLength * 0.9) {
                    charCount.addClass('warning');
                } else {
                    charCount.removeClass('warning');
                }
                
                // Prevent exceeding limit
                if (currentLength >= maxLength) {
                    charCount.addClass('error');
                } else {
                    charCount.removeClass('error');
                }
            });
            
            // Initialize counter
            textarea.trigger('input');
        }
    }

    /**
     * Initialize form submission
     */
    function initFormSubmission() {
        const form = $('#ai-outline-form');
        const generateBtn = $('#generate-btn');
        const loadingState = $('#loading-state');
        const resultsSection = $('#outline-results');
        
        form.on('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                return;
            }
            
            // Disable button and show loading
            generateBtn.prop('disabled', true);
            generateBtn.find('.button-text').text(ai_outline_ajax.generating_text);
            
            // Hide previous results and show loading
            resultsSection.hide();
            loadingState.show();
            
            // Prepare form data
            const formData = {
                action: 'generate_outline',
                nonce: ai_outline_ajax.nonce,
                content: $('#outline-content').val().trim(),
                content_type: $('#content-type').val(),
                sections: $('#sections').val(),
                language: $('#language').val()
            };
            
            // Make AJAX request
            $.ajax({
                url: ai_outline_ajax.ajax_url,
                type: 'POST',
                data: formData,
                timeout: 60000, // 60 seconds timeout
                success: function(response) {
                    handleAjaxSuccess(response);
                },
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, status, error);
                },
                complete: function() {
                    // Re-enable button and hide loading
                    generateBtn.prop('disabled', false);
                    generateBtn.find('.button-text').text('Generate Outline');
                    loadingState.hide();
                }
            });
        });
    }

    /**
     * Handle successful AJAX response
     */
    function handleAjaxSuccess(response) {
        if (response.success && response.data.outline) {
            // Display the generated outline
            $('#outline-content-result').html(response.data.outline);
            $('#outline-results').show();
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#outline-results').offset().top - 100
            }, 500);
            
            // Show success message
            showNotification('Outline generated successfully!', 'success');
        } else {
            // Handle error response
            const errorMessage = response.data || ai_outline_ajax.error_text;
            showNotification(errorMessage, 'error');
        }
    }

    /**
     * Handle AJAX error
     */
    function handleAjaxError(xhr, status, error) {
        let errorMessage = ai_outline_ajax.error_text;
        
        if (status === 'timeout') {
            errorMessage = 'Request timed out. Please try again.';
        } else if (xhr.responseJSON && xhr.responseJSON.data) {
            errorMessage = xhr.responseJSON.data;
        }
        
        showNotification(errorMessage, 'error');
        console.error('AJAX Error:', status, error);
    }

    /**
     * Validate form before submission
     */
    function validateForm() {
        const content = $('#outline-content').val().trim();
        const contentType = $('#content-type').val();
        const sections = $('#sections').val();
        const language = $('#language').val();
        
        // Check if content is provided
        if (!content) {
            showNotification('Please enter some content to generate an outline.', 'error');
            $('#outline-content').focus();
            return false;
        }
        
        // Check content length
        const maxLength = parseInt($('#char-limit').text()) || 1000;
        if (content.length > maxLength) {
            showNotification(`Content must be ${maxLength} characters or less.`, 'error');
            $('#outline-content').focus();
            return false;
        }
        
        // Check if all required fields are selected
        if (!contentType || !sections || !language) {
            showNotification('Please fill in all required fields.', 'error');
            return false;
        }
        
        return true;
    }

    /**
     * Initialize copy functionality
     */
    function initCopyFunctionality() {
        $(document).on('click', '#copy-outline', function() {
            const outlineContent = $('#outline-content-result');
            
            if (outlineContent.length) {
                // Create a temporary textarea to copy text content
                const tempTextarea = $('<textarea>');
                tempTextarea.val(outlineContent.text());
                $('body').append(tempTextarea);
                tempTextarea.select();
                
                try {
                    document.execCommand('copy');
                    showNotification('Outline copied to clipboard!', 'success');
                    
                    // Update button text temporarily
                    const copyBtn = $(this);
                    const originalText = copyBtn.text();
                    copyBtn.text('Copied!');
                    
                    setTimeout(function() {
                        copyBtn.text(originalText);
                    }, 2000);
                    
                } catch (err) {
                    console.error('Copy failed:', err);
                    showNotification('Failed to copy outline. Please select and copy manually.', 'error');
                }
                
                tempTextarea.remove();
            }
        });
    }

    /**
     * Initialize input validation
     */
    function initInputValidation() {
        // Real-time validation for textarea
        $('#outline-content').on('blur', function() {
            const content = $(this).val().trim();
            if (content.length < 10) {
                $(this).addClass('error');
                showNotification('Please provide more detailed content (at least 10 characters).', 'warning');
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Remove error class on focus
        $('#outline-content').on('focus', function() {
            $(this).removeClass('error');
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type) {
        // Remove existing notifications
        $('.ai-outline-notification').remove();
        
        // Create notification element
        const notification = $('<div>')
            .addClass('ai-outline-notification')
            .addClass('notification-' + type)
            .text(message);
        
        // Add to page
        $('.ai-outline-generator-container').prepend(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
        
        // Allow manual dismissal
        notification.on('click', function() {
            $(this).fadeOut(300, function() {
                $(this).remove();
            });
        });
    }

    /**
     * Utility function to scroll to element
     */
    function scrollToElement(element, offset) {
        offset = offset || 0;
        
        if (element.length) {
            $('html, body').animate({
                scrollTop: element.offset().top + offset
            }, 500);
        }
    }

    /**
     * Add smooth scrolling for better UX
     */
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this.getAttribute('href'));
            if (target.length) {
                scrollToElement(target, -100);
            }
        });
    }

    // Initialize smooth scrolling
    initSmoothScrolling();

})(jQuery);

// Add notification styles dynamically
jQuery(document).ready(function($) {
    if (!$('#ai-outline-notification-styles').length) {
        $('<style id="ai-outline-notification-styles">')
            .text(`
                .ai-outline-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 6px;
                    font-family: 'Inter', sans-serif;
                    font-size: 14px;
                    font-weight: 500;
                    z-index: 10000;
                    cursor: pointer;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    animation: slideInRight 0.3s ease;
                    max-width: 300px;
                    word-wrap: break-word;
                }
                
                .notification-success {
                    background: #10b981;
                    color: white;
                }
                
                .notification-error {
                    background: #ef4444;
                    color: white;
                }
                
                .notification-warning {
                    background: #f59e0b;
                    color: white;
                }
                
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                .char-counter .warning {
                    color: #f59e0b !important;
                }
                
                .char-counter .error {
                    color: #ef4444 !important;
                }
                
                textarea.error {
                    border-color: #ef4444 !important;
                    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
                }
            `)
            .appendTo('head');
    }
});
