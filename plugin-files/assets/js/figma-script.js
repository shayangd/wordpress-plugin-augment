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
   * Generate content via AJAX
   */
  function generateContent() {
    console.log("Generate content called");

    if (isGenerating) {
      console.log("Already generating, returning");
      return;
    }

    // Validate required fields
    const topic = $("#topic").val().trim();
    const tone = $("#tone").val();

    if (!topic) {
      showNotification("Please enter a topic", "error");
      return;
    }

    if (!tone) {
      showNotification("Please select a tone", "error");
      return;
    }

    isGenerating = true;
    const $generateBtn = $("#aaai-generate-btn");
    const $buttonText = $generateBtn.find(".aaai-button-text");

    // Update button state
    $generateBtn.addClass("loading");
    $buttonText.text(aaai_ajax.generating_text);

    // Collect form data
    const formData = {
      action: "aaai_generate_content",
      nonce: aaai_ajax.nonce,
      topic: topic,
      keyword: $("#keyword").val().trim(),
      wordcount: $("#wordcount").val() || "1000",
      tone: tone,
      llm: $("#llm").val() || "openai",
      content_type: currentContentType,
    };

    console.log("Form data:", formData);

    // Make AJAX request
    $.ajax({
      url: aaai_ajax.ajax_url,
      type: "POST",
      data: formData,
      timeout: 60000,
      success: function (response) {
        console.log("AJAX success:", response);
        if (response.success) {
          displayResults(response.data.content);
          showNotification("Content generated successfully!", "success");
        } else {
          showNotification(response.data || aaai_ajax.error_text, "error");
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX error:", status, error);
        if (status === "timeout") {
          showNotification(aaai_ajax.timeout_error, "error");
        } else {
          showNotification(aaai_ajax.network_error, "error");
        }
      },
      complete: function () {
        isGenerating = false;
        $generateBtn.removeClass("loading");
        $buttonText.text(aaai_ajax.generate_text);
      },
    });
  }

  /**
   * Display generated results
   */
  function displayResults(content) {
    const $resultsSection = $("#aaai-results-section");
    const $contentArea = $("#aaai-generated-content");

    $contentArea.html(content);
    $resultsSection.show();

    // Scroll to results
    $("html, body").animate(
      {
        scrollTop: $resultsSection.offset().top - 100,
      },
      500
    );
  }

  /**
   * Copy content to clipboard
   */
  function copyToClipboard() {
    const content = $("#aaai-generated-content").text();

    if (navigator.clipboard) {
      navigator.clipboard.writeText(content).then(function () {
        showNotification(aaai_ajax.copy_success, "success");
      });
    } else {
      // Fallback for older browsers
      const textArea = document.createElement("textarea");
      textArea.value = content;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand("copy");
      document.body.removeChild(textArea);
      showNotification(aaai_ajax.copy_success, "success");
    }
  }

  /**
   * Show feedback modal
   */
  function showFeedbackModal() {
    $("#aaai-feedback-modal").fadeIn(300);
  }

  /**
   * Hide feedback modal
   */
  function hideFeedbackModal() {
    $("#aaai-feedback-modal").fadeOut(300);
    resetFeedbackForm();
  }

  /**
   * Reset feedback form
   */
  function resetFeedbackForm() {
    $(".aaai-emotion-button").removeClass("active");
    selectedEmotion = null;
    selectedRating = 0;
    updateStarRating(0);
    $("#feedback-text").val("");
  }

  /**
   * Update star rating display
   */
  function updateStarRating(rating) {
    $(".aaai-star").each(function (index) {
      if (index < rating) {
        $(this).addClass("active");
      } else {
        $(this).removeClass("active");
      }
    });
  }

  /**
   * Highlight stars on hover
   */
  function highlightStars(rating) {
    $(".aaai-star").each(function (index) {
      if (index < rating) {
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
    const feedbackData = {
      action: "aaai_submit_feedback",
      nonce: aaai_ajax.nonce,
      emotion: selectedEmotion,
      rating: selectedRating,
      feedback_text: $("#feedback-text").val(),
    };

    $.ajax({
      url: aaai_ajax.ajax_url,
      type: "POST",
      data: feedbackData,
      success: function (response) {
        if (response.success) {
          showNotification(aaai_ajax.feedback_success, "success");
          hideFeedbackModal();
        } else {
          showNotification(response.data || aaai_ajax.error_text, "error");
        }
      },
      error: function () {
        showNotification(aaai_ajax.network_error, "error");
      },
    });
  }

  /**
   * Show notification
   */
  function showNotification(message, type = "info") {
    // Create notification element if it doesn't exist
    let $notification = $(".aaai-notification");
    if ($notification.length === 0) {
      $notification = $('<div class="aaai-notification"></div>');
      $("body").append($notification);
    }

    $notification
      .removeClass("success error info")
      .addClass(type)
      .text(message)
      .fadeIn(300);

    // Auto-hide after 3 seconds
    setTimeout(() => {
      $notification.fadeOut(300);
    }, 3000);
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
