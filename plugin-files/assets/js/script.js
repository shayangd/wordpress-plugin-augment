/**
 * AAAI AI Writing Tool - Frontend JavaScript
 * Based on Figma Design Specifications
 */

jQuery(document).ready(function ($) {
  let currentRating = 0;
  let selectedEmotion = "";
  let generatedContent = "";

  // Initialize the tool
  initializeAAAITool();

  function initializeAAAITool() {
    // Bind event handlers
    bindFormEvents();
    bindPotionEvents();
    bindResultsEvents();
    bindFeedbackEvents();
    bindNumberControls();
  }

  function bindFormEvents() {
    // Form submission
    $("#aaai-ai-writing-form").on("submit", function (e) {
      e.preventDefault();
      generateContent();
    });

    // Generate button click
    $(".aaai-generate-button").on("click", function (e) {
      e.preventDefault();
      generateContent();
    });
  }

  function bindPotionEvents() {
    // Creative potion selection
    $(".aaai-potion-card").on("click", function () {
      $(".aaai-potion-card").removeClass("selected");
      $(this).addClass("selected");

      const contentType = $(this).data("type");
      $("#content-type").val(contentType);
    });
  }

  function bindResultsEvents() {
    // Copy content button
    $(document).on("click", ".aaai-copy-button", function () {
      copyToClipboard(generatedContent);
    });

    // Regenerate button
    $(document).on("click", ".aaai-regenerate-button", function () {
      generateContent();
    });

    // Feedback button
    $(document).on("click", ".aaai-feedback-button", function () {
      showFeedbackModal();
    });
  }

  function bindFeedbackEvents() {
    // Star rating
    $(".aaai-star").on("click", function () {
      const rating = $(this).data("rating");
      setRating(rating);
    });

    // Emotion selection
    $(".aaai-emotion-button").on("click", function () {
      $(".aaai-emotion-button").removeClass("selected");
      $(this).addClass("selected");
      selectedEmotion = $(this).data("emotion");
    });

    // Submit feedback
    $("#submit-feedback").on("click", function () {
      submitFeedback();
    });

    // Close modal
    $("#close-feedback, .aaai-feedback-modal").on("click", function (e) {
      if (e.target === this) {
        hideFeedbackModal();
      }
    });
  }

  function bindNumberControls() {
    // Number input controls
    $(".aaai-number-control").on("click", function () {
      const $input = $(this).siblings("input");
      const isIncrement = $(this).hasClass("increment");
      const currentValue = parseInt($input.val()) || 0;
      const min = parseInt($input.attr("min")) || 0;
      const max = parseInt($input.attr("max")) || 5000;

      let newValue = isIncrement ? currentValue + 100 : currentValue - 100;
      newValue = Math.max(min, Math.min(max, newValue));

      $input.val(newValue);
    });
  }

  function generateContent() {
    const $button = $(".aaai-generate-button");
    const $form = $("#aaai-ai-writing-form");

    // Get form data
    const formData = {
      action: "generate_ai_content",
      nonce: aaai_ai_writing_ajax.nonce,
      topic: $("#topic").val(),
      keyword: $("#keyword").val(),
      wordcount: $("#wordcount").val(),
      tone: $("#tone").val(),
      llm: $("#llm").val(),
      content_type: $("#content-type").val() || "blog_post",
    };

    // Validate form
    if (!validateForm(formData)) {
      return;
    }

    // Update button state to loading
    setButtonLoading($button, true);

    // Clear previous results
    hideResults();
    clearMessages();

    // Make AJAX request
    $.ajax({
      url: aaai_ai_writing_ajax.ajax_url,
      type: "POST",
      data: formData,
      success: function (response) {
        if (response.success) {
          generatedContent = response.data.content;
          showResults(generatedContent);
          showMessage("Content generated successfully!", "success");
        } else {
          showMessage(
            response.data || aaai_ai_writing_ajax.error_text,
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        showMessage(
          "Network error. Please check your connection and try again.",
          "error"
        );
      },
      complete: function () {
        setButtonLoading($button, false);
      },
    });
  }

  function validateForm(formData) {
    // Clear previous messages
    clearMessages();

    // Validate topic
    if (!formData.topic.trim()) {
      showMessage("Please enter a topic to generate content.", "error");
      $("#topic").focus();
      return false;
    }

    if (formData.topic.length < 5) {
      showMessage(
        "Topic is too short. Please provide at least 5 characters.",
        "error"
      );
      $("#topic").focus();
      return false;
    }

    // Validate word count
    const wordcount = parseInt(formData.wordcount);
    if (wordcount < 50 || wordcount > 5000) {
      showMessage("Word count must be between 50 and 5000.", "error");
      $("#wordcount").focus();
      return false;
    }

    return true;
  }

  function setButtonLoading($button, isLoading) {
    if (isLoading) {
      $button
        .prop("disabled", true)
        .addClass("loading")
        .html(
          '<span class="aaai-spinner"></span>' +
            aaai_ai_writing_ajax.generating_text
        );
    } else {
      $button
        .prop("disabled", false)
        .removeClass("loading")
        .text("Generate Text");
    }
  }

  function showResults(content) {
    const $resultsSection = $(".aaai-results-section");
    const $resultsContent = $(".aaai-results-content");

    $resultsContent.html(content);
    $resultsSection.addClass("show");

    // Scroll to results
    $("html, body").animate(
      {
        scrollTop: $resultsSection.offset().top - 20,
      },
      500
    );
  }

  function hideResults() {
    $(".aaai-results-section").removeClass("show");
  }

  function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard
        .writeText(text)
        .then(function () {
          showMessage(aaai_ai_writing_ajax.copy_success, "success");
        })
        .catch(function (err) {
          console.error("Failed to copy: ", err);
          fallbackCopyTextToClipboard(text);
        });
    } else {
      fallbackCopyTextToClipboard(text);
    }
  }

  function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      const successful = document.execCommand("copy");
      if (successful) {
        showMessage(aaai_ai_writing_ajax.copy_success, "success");
      } else {
        showMessage("Failed to copy content.", "error");
      }
    } catch (err) {
      console.error("Fallback: Oops, unable to copy", err);
      showMessage("Failed to copy content.", "error");
    }

    document.body.removeChild(textArea);
  }

  function showFeedbackModal() {
    $(".aaai-feedback-modal").addClass("show");
    resetFeedbackForm();
  }

  function hideFeedbackModal() {
    $(".aaai-feedback-modal").removeClass("show");
  }

  function resetFeedbackForm() {
    currentRating = 0;
    selectedEmotion = "";
    $(".aaai-star").removeClass("filled");
    $(".aaai-emotion-button").removeClass("selected");
    $("#feedback-text").val("");
  }

  function setRating(rating) {
    currentRating = rating;
    $(".aaai-star").each(function (index) {
      if (index < rating) {
        $(this).addClass("filled");
      } else {
        $(this).removeClass("filled");
      }
    });
  }

  function submitFeedback() {
    const feedbackText = $("#feedback-text").val();

    if (currentRating === 0) {
      showMessage("Please select a rating.", "error");
      return;
    }

    const formData = {
      action: "submit_feedback",
      nonce: aaai_ai_writing_ajax.nonce,
      rating: currentRating,
      emotion: selectedEmotion,
      feedback_text: feedbackText,
    };

    $.ajax({
      url: aaai_ai_writing_ajax.ajax_url,
      type: "POST",
      data: formData,
      success: function (response) {
        if (response.success) {
          showMessage(aaai_ai_writing_ajax.feedback_success, "success");
          hideFeedbackModal();
        } else {
          showMessage(response.data || "Failed to submit feedback.", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Feedback AJAX Error:", error);
        showMessage("Network error. Please try again.", "error");
      },
    });
  }

  function showMessage(message, type) {
    // Remove existing messages
    $(".aaai-message").remove();

    const $message = $(
      '<div class="aaai-message ' + type + '">' + message + "</div>"
    );
    $(".aaai-ai-writing-tool").prepend($message);

    // Auto-hide success messages after 5 seconds
    if (type === "success") {
      setTimeout(function () {
        $message.fadeOut(function () {
          $(this).remove();
        });
      }, 5000);
    }
  }

  function clearMessages() {
    $(".aaai-message").remove();
  }
});
