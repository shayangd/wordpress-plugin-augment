/**
 * AAAI AI Writing Tool - Figma Design JavaScript
 * Handles all interactions for the 4 Figma screens
 * Based on design specifications from Figma file z2P8B0NgJ3QYlBHSxuiiQ5
 */

(function ($) {
  "use strict";

  // Global variables
  let currentContentType = "blog_post";
  let selectedEmotion = null;
  let selectedRating = 0;
  let isGenerating = false;

  // Initialize when document is ready
  $(document).ready(function () {
    initializePlugin();
  });

  /**
   * Initialize all plugin functionality
   */
  function initializePlugin() {
    initializeFormHandlers();
    initializeCreativePotions();
    initializeNumberControls();
    initializeResultsSection();
    initializeFeedbackModal();
    initializeKeyboardShortcuts();
  }

  /**
   * Initialize form submission and validation
   */
  function initializeFormHandlers() {
    const $form = $("#aaai-ai-writing-form");
    const $generateBtn = $("#aaai-generate-btn");

    // Form submission handler
    $form.on("submit", function (e) {
      e.preventDefault();

      if (isGenerating) return;

      if (validateForm()) {
        generateContent();
      }
    });

    // Real-time form validation
    $form.find("input, textarea, select").on("input change", function () {
      validateField($(this));
    });

    // Character counter for textarea
    $("#topic").on("input", function () {
      updateCharacterCounter($(this), 500);
    });
  }

  /**
   * Initialize Creative Potions interaction
   */
  function initializeCreativePotions() {
    $(".aaai-potion-card").on("click", function () {
      const $card = $(this);
      const contentType = $card.data("type");

      // Update active state
      $(".aaai-potion-card").removeClass("active");
      $card.addClass("active");

      // Update hidden field
      $("#content-type").val(contentType);
      currentContentType = contentType;

      // Update form placeholders based on content type
      updateFormPlaceholders(contentType);

      // Add visual feedback
      $card.addClass("aaai-fade-in");
      setTimeout(() => $card.removeClass("aaai-fade-in"), 500);
    });
  }

  /**
   * Initialize number input controls (word count)
   */
  function initializeNumberControls() {
    $(".aaai-caret-button").on("click", function () {
      const $button = $(this);
      const target = $button.data("target");
      const step = parseInt($button.data("step")) || 50;
      const $input = $("#" + target);
      const currentValue = parseInt($input.val()) || 1000;
      const min = parseInt($input.attr("min")) || 50;
      const max = parseInt($input.attr("max")) || 5000;

      let newValue;
      if ($button.hasClass("increment")) {
        newValue = Math.min(currentValue + step, max);
      } else {
        newValue = Math.max(currentValue - step, min);
      }

      $input.val(newValue).trigger("change");

      // Visual feedback
      $button.addClass("active");
      setTimeout(() => $button.removeClass("active"), 150);
    });
  }

  /**
   * Initialize Results Section functionality
   */
  function initializeResultsSection() {
    // Copy button
    $("#aaai-copy-btn").on("click", function () {
      copyToClipboard();
    });

    // Regenerate button
    $("#aaai-regenerate-btn").on("click", function () {
      if (!isGenerating) {
        generateContent();
      }
    });

    // Feedback button
    $("#aaai-feedback-btn").on("click", function () {
      showFeedbackModal();
    });
  }

  /**
   * Initialize Feedback Modal functionality
   */
  function initializeFeedbackModal() {
    // Emotion buttons
    $(".aaai-emotion-button").on("click", function () {
      const $button = $(this);
      const emotion = $button.data("emotion");
      const value = $button.data("value");

      $(".aaai-emotion-button").removeClass("active");
      $button.addClass("active");

      selectedEmotion = emotion;
      selectedRating = value;

      // Auto-update star rating
      updateStarRating(value);
    });

    // Star rating
    $(".aaai-star").on("click", function () {
      const rating = parseInt($(this).data("rating"));
      selectedRating = rating;
      updateStarRating(rating);
    });

    // Star hover effects
    $(".aaai-star").on("mouseenter", function () {
      const rating = parseInt($(this).data("rating"));
      highlightStars(rating);
    });

    $(".aaai-stars-group").on("mouseleave", function () {
      highlightStars(selectedRating);
    });

    // Modal actions
    $("#submit-feedback").on("click", function () {
      submitFeedback();
    });

    $("#close-feedback").on("click", function () {
      hideFeedbackModal();
    });

    // Close modal on overlay click
    $(".aaai-modal-background").on("click", function () {
      hideFeedbackModal();
    });
  }

  /**
   * Initialize keyboard shortcuts
   */
  function initializeKeyboardShortcuts() {
    $(document).on("keydown", function (e) {
      // Escape key closes modal
      if (e.key === "Escape") {
        hideFeedbackModal();
      }

      // Ctrl/Cmd + Enter submits form
      if ((e.ctrlKey || e.metaKey) && e.key === "Enter") {
        if (!isGenerating) {
          $("#aaai-ai-writing-form").submit();
        }
      }
    });
  }

  /**
   * Validate entire form
   */
  function validateForm() {
    let isValid = true;
    const $form = $("#aaai-ai-writing-form");

    // Validate required fields
    $form.find("[required]").each(function () {
      if (!validateField($(this))) {
        isValid = false;
      }
    });

    return isValid;
  }

  /**
   * Validate individual field
   */
  function validateField($field) {
    const value = $field.val().trim();
    const fieldType =
      $field.attr("type") || $field.prop("tagName").toLowerCase();
    let isValid = true;

    // Remove previous error states
    $field.closest(".aaai-input-frame").removeClass("error");

    // Required field validation
    if ($field.prop("required") && !value) {
      isValid = false;
    }

    // Specific field validations
    if (fieldType === "number") {
      const min = parseInt($field.attr("min"));
      const max = parseInt($field.attr("max"));
      const numValue = parseInt(value);

      if (numValue < min || numValue > max) {
        isValid = false;
      }
    }

    // Add error state if invalid
    if (!isValid) {
      $field.closest(".aaai-input-frame").addClass("error");
    }

    return isValid;
  }

  /**
   * Update character counter
   */
  function updateCharacterCounter($field, maxLength) {
    const currentLength = $field.val().length;
    const $counter = $field.siblings(".aaai-char-counter");

    if ($counter.length) {
      $counter.find("span").text(currentLength);

      if (currentLength > maxLength * 0.9) {
        $counter.addClass("warning");
      } else {
        $counter.removeClass("warning");
      }
    }
  }

  /**
   * Update form placeholders based on content type
   */
  function updateFormPlaceholders(contentType) {
    const placeholders = {
      blog_post: {
        topic: "Enter your blog post topic...",
        keyword: "Enter SEO keyword...",
      },
      ad: {
        topic: "Describe your product or service...",
        keyword: "Enter target keyword...",
      },
      social_media_post: {
        topic: "What do you want to share?",
        keyword: "Enter hashtag or keyword...",
      },
      email: {
        topic: "Describe your email purpose...",
        keyword: "Enter key message...",
      },
      paragraph: {
        topic: "What should the paragraph be about?",
        keyword: "Enter focus keyword...",
      },
      blog_introduction: {
        topic: "Describe your blog topic...",
        keyword: "Enter main keyword...",
      },
      blog_outline: {
        topic: "What is your blog about?",
        keyword: "Enter primary keyword...",
      },
      product_description: {
        topic: "Describe your product...",
        keyword: "Enter product keyword...",
      },
    };

    const typeData = placeholders[contentType] || placeholders["blog_post"];

    $("#topic").attr("placeholder", typeData.topic);
    $("#keyword").attr("placeholder", typeData.keyword);
  }

  /**
   * Generate content - Figma Screen 2 (Loading State)
   */
  function generateContent() {
    if (isGenerating) return;

    isGenerating = true;
    const $generateBtn = $("#aaai-generate-btn");
    const $form = $("#aaai-ai-writing-form");

    // Update button to loading state (Figma Screen 2)
    $generateBtn.addClass("loading");
    $generateBtn.find(".aaai-button-text").text("Generating...");

    // Collect form data
    const formData = {
      action: "aaai_generate_content",
      nonce: $("#aaai_ai_writing_nonce").val(),
      topic: $("#topic").val(),
      keyword: $("#keyword").val(),
      wordcount: $("#wordcount").val(),
      tone: $("#tone").val(),
      llm: $("#llm").val(),
      content_type: currentContentType,
    };

    // Make AJAX request
    $.ajax({
      url: aaai_ajax.ajax_url,
      type: "POST",
      data: formData,
      timeout: 60000, // 60 seconds timeout
      success: function (response) {
        if (response.success) {
          displayResults(response.data.content);
        } else {
          showError(
            response.data.message || "Generation failed. Please try again."
          );
        }
      },
      error: function (xhr, status, error) {
        let errorMessage =
          "Network error. Please check your connection and try again.";

        if (status === "timeout") {
          errorMessage = "Request timed out. Please try again.";
        } else if (xhr.responseJSON && xhr.responseJSON.data) {
          errorMessage = xhr.responseJSON.data.message;
        }

        showError(errorMessage);
      },
      complete: function () {
        // Reset button state
        isGenerating = false;
        $generateBtn.removeClass("loading");
        $generateBtn.find(".aaai-button-text").text("Generate Text");
      },
    });
  }

  /**
   * Display results - Figma Screen 3
   */
  function displayResults(content) {
    const $resultsSection = $("#aaai-results-section");
    const $contentText = $("#aaai-generated-content");

    // Update content
    $contentText.text(content);

    // Show results section with animation
    $resultsSection.show().addClass("show aaai-slide-up");

    // Scroll to results
    $("html, body").animate(
      {
        scrollTop: $resultsSection.offset().top - 100,
      },
      800
    );
  }

  /**
   * Copy content to clipboard
   */
  function copyToClipboard() {
    const content = $("#aaai-generated-content").text();

    if (!content) {
      showNotification("No content to copy", "warning");
      return;
    }

    // Modern clipboard API
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard
        .writeText(content)
        .then(function () {
          showNotification("Content copied to clipboard!", "success");
          updateCopyButton();
        })
        .catch(function () {
          fallbackCopyToClipboard(content);
        });
    } else {
      fallbackCopyToClipboard(content);
    }
  }

  /**
   * Fallback copy method for older browsers
   */
  function fallbackCopyToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      document.execCommand("copy");
      showNotification("Content copied to clipboard!", "success");
      updateCopyButton();
    } catch (err) {
      showNotification("Failed to copy content", "error");
    }

    document.body.removeChild(textArea);
  }

  /**
   * Update copy button visual feedback
   */
  function updateCopyButton() {
    const $copyBtn = $("#aaai-copy-btn");
    const $copyText = $copyBtn.find(".aaai-action-text");
    const originalText = $copyText.text();

    $copyText.text("Copied!");
    $copyBtn.addClass("success");

    setTimeout(function () {
      $copyText.text(originalText);
      $copyBtn.removeClass("success");
    }, 2000);
  }

  /**
   * Show feedback modal - Figma Screen 4
   */
  function showFeedbackModal() {
    const $modal = $("#aaai-feedback-modal");
    $modal.show().addClass("show");

    // Reset form
    resetFeedbackForm();

    // Focus on first interactive element
    setTimeout(function () {
      $(".aaai-emotion-button").first().focus();
    }, 300);
  }

  /**
   * Hide feedback modal
   */
  function hideFeedbackModal() {
    const $modal = $("#aaai-feedback-modal");
    $modal.removeClass("show");

    setTimeout(function () {
      $modal.hide();
    }, 300);
  }

  /**
   * Reset feedback form
   */
  function resetFeedbackForm() {
    $(".aaai-emotion-button").removeClass("active");
    $(".aaai-star").removeClass("active");
    $("#feedback-text").val("");
    selectedEmotion = null;
    selectedRating = 0;
    highlightStars(0);
  }

  /**
   * Update star rating display
   */
  function updateStarRating(rating) {
    $(".aaai-star").removeClass("active");
    for (let i = 1; i <= rating; i++) {
      $(`.aaai-star[data-rating="${i}"]`).addClass("active");
    }
  }

  /**
   * Highlight stars on hover
   */
  function highlightStars(rating) {
    $(".aaai-star").each(function () {
      const starRating = parseInt($(this).data("rating"));
      if (starRating <= rating) {
        $(this).addClass("hover");
      } else {
        $(this).removeClass("hover");
      }
    });
  }

  /**
   * Submit feedback
   */
  function submitFeedback() {
    const feedbackText = $("#feedback-text").val();

    // Validate feedback
    if (!selectedEmotion && selectedRating === 0) {
      showNotification("Please select an emotion or star rating", "warning");
      return;
    }

    const feedbackData = {
      action: "aaai_submit_feedback",
      nonce: $("#aaai_ai_writing_nonce").val(),
      emotion: selectedEmotion,
      rating: selectedRating,
      feedback_text: feedbackText,
      content_type: currentContentType,
    };

    // Disable submit button
    const $submitBtn = $("#submit-feedback");
    $submitBtn.addClass("aaai-loading").prop("disabled", true);

    $.ajax({
      url: aaai_ajax.ajax_url,
      type: "POST",
      data: feedbackData,
      success: function (response) {
        if (response.success) {
          showNotification("Thank you for your feedback!", "success");
          hideFeedbackModal();
        } else {
          showNotification(
            response.data.message || "Failed to submit feedback",
            "error"
          );
        }
      },
      error: function () {
        showNotification("Network error. Please try again.", "error");
      },
      complete: function () {
        $submitBtn.removeClass("aaai-loading").prop("disabled", false);
      },
    });
  }

  /**
   * Show notification message
   */
  function showNotification(message, type = "info") {
    // Remove existing notifications
    $(".aaai-notification").remove();

    const $notification = $(`
            <div class="aaai-notification aaai-notification-${type}">
                <div class="aaai-notification-content">
                    <span class="aaai-notification-text">${message}</span>
                    <button class="aaai-notification-close">&times;</button>
                </div>
            </div>
        `);

    $("body").append($notification);

    // Show with animation
    setTimeout(function () {
      $notification.addClass("show");
    }, 100);

    // Auto-hide after 5 seconds
    setTimeout(function () {
      hideNotification($notification);
    }, 5000);

    // Close button handler
    $notification.find(".aaai-notification-close").on("click", function () {
      hideNotification($notification);
    });
  }

  /**
   * Hide notification
   */
  function hideNotification($notification) {
    $notification.removeClass("show");
    setTimeout(function () {
      $notification.remove();
    }, 300);
  }

  /**
   * Show error message
   */
  function showError(message) {
    showNotification(message, "error");

    // Also log to console for debugging
    console.error("AAAI AI Writing Tool Error:", message);
  }

  /**
   * Utility function to debounce function calls
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Export functions for external use if needed
  window.aaaiWritingTool = {
    generateContent: generateContent,
    showFeedbackModal: showFeedbackModal,
    hideFeedbackModal: hideFeedbackModal,
    copyToClipboard: copyToClipboard,
    showNotification: showNotification,
  };
})(jQuery);
