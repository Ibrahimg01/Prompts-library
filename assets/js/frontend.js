/**
 * Prompts Library Frontend JavaScript
 */

(function($) {
    'use strict';

    const PromptsLibrary = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // View prompt button
            $(document).on('click', '.view-prompt', this.openModal.bind(this));

            // Use prompt button (from card)
            $(document).on('click', '.prompt-card .use-prompt', this.usePromptDirect.bind(this));

            // Use prompt button (from modal)
            $(document).on('click', '.use-prompt-modal', this.usePromptFromModal.bind(this));

            // Copy prompt button
            $(document).on('click', '.copy-prompt', this.copyPrompt.bind(this));

            // Close modal
            $(document).on('click', '.modal-close, .modal-overlay', this.closeModal.bind(this));

            // Prevent modal close when clicking inside modal content
            $(document).on('click', '.modal-content', function(e) {
                e.stopPropagation();
            });

            // Close modal on ESC key
            $(document).keydown(function(e) {
                if (e.key === 'Escape') {
                    PromptsLibrary.closeModal();
                }
            });
        },

        /**
         * Open modal and load prompt details
         */
        openModal: function(e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const promptId = $button.data('prompt-id');

            // Show loading state
            $button.addClass('loading');

            // Get prompt details via AJAX
            $.ajax({
                url: promptsLibrary.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_prompt_details',
                    prompt_id: promptId,
                    nonce: promptsLibrary.nonce
                },
                success: function(response) {
                    if (response.success) {
                        PromptsLibrary.displayModal(response.data);
                    } else {
                        alert(response.data.message || 'Error loading prompt');
                    }
                },
                error: function() {
                    alert('Error loading prompt. Please try again.');
                },
                complete: function() {
                    $button.removeClass('loading');
                }
            });
        },

        /**
         * Display modal with prompt data
         */
        displayModal: function(data) {
            const $modal = $('#prompt-modal');
            
            // Set category badge
            if (data.categories && data.categories.length > 0) {
                const category = data.categories[0];
                $('.modal-category-badge')
                    .text(category.name)
                    .css('background-color', category.color);
            }

            // Set title and description
            $('.modal-title').text(data.title);
            $('.modal-description').text(data.description);

            // Set prompt text
            $('.prompt-text').text(data.prompt_text);

            // Store prompt text for later use
            $modal.data('prompt-text', data.prompt_text);

            // Show modal
            $modal.fadeIn(300);
            $('body').css('overflow', 'hidden');
        },

        /**
         * Close modal
         */
        closeModal: function() {
            $('#prompt-modal').fadeOut(300);
            $('body').css('overflow', 'auto');
        },

        /**
         * Use prompt directly (from card button)
         */
        usePromptDirect: function(e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const promptId = $button.data('prompt-id');

            // Show loading state
            $button.addClass('loading');

            // Get prompt details via AJAX
            $.ajax({
                url: promptsLibrary.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_prompt_details',
                    prompt_id: promptId,
                    nonce: promptsLibrary.nonce
                },
                success: function(response) {
                    if (response.success) {
                        PromptsLibrary.insertPromptToChatbot(response.data.prompt_text);
                    } else {
                        alert(response.data.message || 'Error loading prompt');
                    }
                },
                error: function() {
                    alert('Error loading prompt. Please try again.');
                },
                complete: function() {
                    $button.removeClass('loading');
                }
            });
        },

        /**
         * Use prompt from modal
         */
        usePromptFromModal: function(e) {
            e.preventDefault();
            const $modal = $('#prompt-modal');
            const promptText = $modal.data('prompt-text');

            if (promptText) {
                this.insertPromptToChatbot(promptText);
                this.closeModal();
            }
        },

        /**
         * Insert prompt into chatbot
         */
        insertPromptToChatbot: function(promptText) {
            // Try multiple selectors for different chatbot implementations
            const selectors = [
                'textarea[name="mwai_chat_input"]',
                '.mwai-input textarea',
                '#mwai-chat-input',
                'textarea.mwai-input',
                '.chatbot-input textarea',
                'input[type="text"].mwai-input'
            ];

            let $input = null;
            
            for (let selector of selectors) {
                $input = $(selector);
                if ($input.length > 0) {
                    break;
                }
            }

            if ($input && $input.length > 0) {
                // Set the value
                $input.val(promptText);
                
                // Trigger events to ensure the chatbot recognizes the input
                $input.trigger('input');
                $input.trigger('change');
                $input.focus();

                // Scroll to chatbot
                $('html, body').animate({
                    scrollTop: $input.offset().top - 100
                }, 500);

                // Show success message
                this.showSuccessMessage('Prompt loaded into chatbot!');
            } else {
                // If chatbot not found, copy to clipboard as fallback
                this.copyToClipboard(promptText);
                this.showSuccessMessage('Chatbot input not found. Prompt copied to clipboard!');
            }
        },

        /**
         * Copy prompt to clipboard
         */
        copyPrompt: function(e) {
            e.preventDefault();
            const $modal = $('#prompt-modal');
            const promptText = $modal.data('prompt-text');

            if (promptText) {
                PromptsLibrary.copyToClipboard(promptText);
                PromptsLibrary.showSuccessMessage('Prompt copied to clipboard!');
            }
        },

        /**
         * Copy text to clipboard
         */
        copyToClipboard: function(text) {
            // Create temporary textarea
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            
            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Failed to copy:', err);
            }
            
            $temp.remove();
        },

        /**
         * Show success message
         */
        showSuccessMessage: function(message) {
            // Remove any existing success messages
            $('.copy-success').remove();

            // Create and show new message
            const $success = $('<div class="copy-success">' + message + '</div>');
            $('body').append($success);

            // Remove after 3 seconds
            setTimeout(function() {
                $success.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        PromptsLibrary.init();
    });

})(jQuery);
